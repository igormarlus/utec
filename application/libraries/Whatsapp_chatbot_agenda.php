<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * Remarcacao e cancelamento automaticos pelo chatbot (perfil paciente).
 * Whatsapp_chatbot encaminha para ca a escolha da consulta, os cliques rem:/can:
 * e o texto do motivo na sessao agenda_cancelar/motivo.
 * Retorna o resultado do envio ou, para o chatbot tratar:
 *   ['fallback' => 'prazo|sem_grade|sem_vaga', 'acao' => ..., 'id_agendamento' => ...]
 *   ['expirado' => true]
 */
class Whatsapp_chatbot_agenda {

    protected $CI;
    public $agora = null;

    public function __construct($ci = null)
    {
        $this->CI = $ci ?: get_instance();
        if (!isset($this->CI->whatsapp_model)) {
            $this->CI->load->model('Whatsapp_model', 'whatsapp_model');
        }
        if (!isset($this->CI->disponibilidade_model)) {
            $this->CI->load->model('Disponibilidade_model', 'disponibilidade_model');
        }
        if (!isset($this->CI->notificacoes_model)) {
            $this->CI->load->model('Notificacoes_model', 'notificacoes_model');
        }
        if (!isset($this->CI->whatsapp_agendamento)) {
            $this->CI->load->library('whatsapp_agendamento');
        }
        if (isset($this->CI->load)) {
            $this->CI->load->helper('whatsapp_agendamento');
        }
    }

    public function iniciar($perfil, $acao, $agendamento, $evento)
    {
        $acao = $acao === 'cancelar' ? 'cancelar' : 'remarcar';
        if (!utec_whatsapp_agenda_antecedencia_ok($agendamento->data_agenda, $agendamento->hora_agenda, $this->agora())) {
            return $this->fallback('prazo', $acao, $agendamento);
        }
        if ($acao === 'cancelar') {
            $this->salvar_sessao($perfil, 'agenda_cancelar', 'motivo', ['id_agendamento' => (int)$agendamento->id], $evento);
            return $this->pedir_motivo($perfil, $agendamento);
        }
        return $this->enviar_dias($perfil, $agendamento, $evento, '');
    }

    public function processar_clique($perfil, $evento, $idEvento)
    {
        $clique = utec_whatsapp_agenda_parse_id(utec_whatsapp_read($evento, 'payload', ''));
        if (!$clique) {
            return ['expirado' => true];
        }
        $agendamento = $this->CI->whatsapp_model->obter_agendamento_chatbot(
            $clique['id_agendamento'], $perfil['perfil'], $perfil['id_usuario'], $perfil['tenant_id']
        );
        if ($agendamento && $clique['fluxo'] === 'cancelar' && $clique['acao'] === 'confirmar' && (string)utec_whatsapp_read($agendamento, 'status', '') === '3') {
            $this->CI->whatsapp_model->limpar_sessao_chatbot($perfil['telefone']);
            return $this->texto($perfil, 'Essa consulta já está cancelada.', (int)$agendamento->id);
        }
        if (!$this->elegivel($agendamento, $perfil)) {
            return ['expirado' => true];
        }
        if (!utec_whatsapp_agenda_antecedencia_ok($agendamento->data_agenda, $agendamento->hora_agenda, $this->agora())) {
            return $this->fallback('prazo', $clique['fluxo'], $agendamento);
        }

        if ($clique['fluxo'] === 'cancelar') {
            if ($clique['acao'] === 'sem_motivo') {
                $this->salvar_sessao($perfil, 'agenda_cancelar', 'confirmar', ['id_agendamento' => (int)$agendamento->id, 'motivo' => ''], $evento);
                return $this->pedir_confirmacao_cancelamento($perfil, $agendamento);
            }
            return $this->confirmar_cancelamento($perfil, $agendamento, $idEvento);
        }

        switch ($clique['acao']) {
            case 'dia':
                return $this->enviar_horarios($perfil, $agendamento, $clique['data'], 1, $evento, '');
            case 'pagina':
                return $this->enviar_horarios($perfil, $agendamento, $clique['data'], $clique['pagina'], $evento, '');
            case 'dias':
                return $this->enviar_dias($perfil, $agendamento, $evento, '');
            case 'hora':
                $this->salvar_sessao($perfil, 'agenda_remarcar', 'confirmar', ['id_agendamento' => (int)$agendamento->id, 'data' => $clique['data'], 'hora' => $clique['hora']], $evento);
                return $this->botoes($perfil, utec_whatsapp_agenda_texto_confirmar_remarcacao($clique['data'], $clique['hora'], utec_whatsapp_read($agendamento, 'prestador_nome', '')), [
                    ['id' => utec_whatsapp_agenda_id_confirmar_remarcacao($agendamento->id, $clique['data'], $clique['hora']), 'title' => 'Confirmar'],
                    ['id' => utec_whatsapp_agenda_id_outros_dias($agendamento->id), 'title' => 'Outro dia'],
                    ['id' => 'chat:paciente:voltar', 'title' => 'Voltar'],
                ], (int)$agendamento->id);
            case 'confirmar':
                return $this->confirmar_remarcacao($perfil, $agendamento, $clique, $evento, $idEvento);
        }
        return ['expirado' => true];
    }

    public function receber_motivo($perfil, $sessao, $evento)
    {
        $dados = json_decode((string)utec_whatsapp_read($sessao, 'dados_json', '{}'), true);
        $dados = is_array($dados) ? $dados : [];
        $agendamento = $this->CI->whatsapp_model->obter_agendamento_chatbot(
            (int)utec_whatsapp_read($dados, 'id_agendamento', 0), $perfil['perfil'], $perfil['id_usuario'], $perfil['tenant_id']
        );
        if (!$this->elegivel($agendamento, $perfil)) {
            return ['expirado' => true];
        }
        $motivo = trim((string)utec_whatsapp_read($evento, 'text', ''));
        if ($this->tamanho($motivo) < 3) {
            return $this->pedir_motivo($perfil, $agendamento);
        }
        $this->salvar_sessao($perfil, 'agenda_cancelar', 'confirmar', ['id_agendamento' => (int)$agendamento->id, 'motivo' => $motivo], $evento);
        return $this->pedir_confirmacao_cancelamento($perfil, $agendamento);
    }

    protected function enviar_dias($perfil, $agendamento, $evento, $prefixo)
    {
        $resposta = $this->CI->disponibilidade_model->dias_com_vaga(
            (int)$agendamento->id_prestador, $this->minimo(), 30, 10, (int)$agendamento->id
        );
        if (empty($resposta['tem_grade'])) {
            return $this->fallback('sem_grade', 'remarcar', $agendamento);
        }
        if (empty($resposta['dias'])) {
            return $this->fallback('sem_vaga', 'remarcar', $agendamento);
        }
        $rows = [];
        foreach ($resposta['dias'] as $dia) {
            $qtd = (int)$dia['qtd'];
            $rows[] = [
                'id' => utec_whatsapp_agenda_id_dia($agendamento->id, $dia['data']),
                'title' => utec_whatsapp_agenda_rotulo_dia($dia['data']),
                'description' => $qtd === 1 ? '1 horário livre' : $qtd.' horários livres',
            ];
        }
        $this->salvar_sessao($perfil, 'agenda_remarcar', 'dia', ['id_agendamento' => (int)$agendamento->id], $evento);
        $corpo = trim($prefixo.' Escolha o novo dia da consulta.');
        return $this->lista($perfil, $corpo, 'Ver dias', $rows, (int)$agendamento->id);
    }

    protected function enviar_horarios($perfil, $agendamento, $data, $pagina, $evento, $prefixo)
    {
        $resposta = $this->CI->disponibilidade_model->horarios_livres(
            (int)$agendamento->id_prestador, $data, (int)$agendamento->id, $this->minimo()
        );
        $livres = isset($resposta['livres']) ? $resposta['livres'] : [];
        $paginado = utec_whatsapp_agenda_paginar_horarios($livres, $pagina);
        if (empty($paginado['itens'])) {
            return $this->enviar_dias($perfil, $agendamento, $evento, 'Esse dia não tem mais horários livres.');
        }
        $rows = [];
        foreach ($paginado['itens'] as $hora) {
            $rows[] = ['id' => utec_whatsapp_agenda_id_hora($agendamento->id, $data, $hora), 'title' => $hora];
        }
        if ($paginado['tem_mais']) {
            $rows[] = ['id' => utec_whatsapp_agenda_id_pagina($agendamento->id, $data, (int)$pagina + 1), 'title' => 'Ver mais horários'];
        }
        $this->salvar_sessao($perfil, 'agenda_remarcar', 'hora', ['id_agendamento' => (int)$agendamento->id, 'data' => $data], $evento);
        $corpo = trim($prefixo.' Horários livres em '.utec_whatsapp_agenda_rotulo_dia($data).'.');
        return $this->lista($perfil, $corpo, 'Ver horários', $rows, (int)$agendamento->id);
    }

    protected function confirmar_remarcacao($perfil, $agendamento, $clique, $evento, $idEvento)
    {
        $r = $this->CI->whatsapp_model->remarcar_agendamento_chatbot(
            (int)$agendamento->id, (int)$perfil['id_usuario'], $clique['data'], $clique['hora'], $this->minimo(), $perfil['telefone']
        );
        if (empty($r['ok'])) {
            $falha = (string)utec_whatsapp_read($r, 'motivo_falha', 'erro');
            if ($falha === 'ocupado') {
                return $this->enviar_horarios($perfil, $agendamento, $clique['data'], 1, $evento, 'Esse horário acabou de ser ocupado. Escolha outro.');
            }
            if ($falha === 'prazo') {
                return $this->fallback('prazo', 'remarcar', $agendamento);
            }
            if ($falha === 'erro') {
                return $this->texto($perfil, 'Não foi possível remarcar agora. Tente novamente em instantes.', (int)$agendamento->id);
            }
            return ['expirado' => true];
        }

        $this->CI->whatsapp_model->limpar_sessao_chatbot($perfil['telefone']);
        $novo = utec_whatsapp_read($r, 'agendamento', $agendamento);
        $prestador = utec_whatsapp_read($agendamento, 'prestador_nome', '');
        if (!empty($r['ja_estava'])) {
            return $this->texto($perfil, 'Sua consulta já está marcada para '.utec_whatsapp_agenda_quando($novo->data_agenda, $novo->hora_agenda, $prestador).'.', (int)$agendamento->id);
        }
        $resposta = $this->texto($perfil, utec_whatsapp_agenda_texto_remarcado($novo->data_agenda, $novo->hora_agenda, $prestador), (int)$agendamento->id);
        $this->avisar_equipe('remarcar', $agendamento, $r, '', $idEvento);
        return $resposta;
    }

    protected function confirmar_cancelamento($perfil, $agendamento, $idEvento)
    {
        $motivo = '';
        $sessao = $this->CI->whatsapp_model->obter_sessao_chatbot($perfil['telefone']);
        if ($sessao && utec_whatsapp_read($sessao, 'fluxo', '') === 'agenda_cancelar') {
            $dados = json_decode((string)utec_whatsapp_read($sessao, 'dados_json', '{}'), true);
            if (is_array($dados) && (int)utec_whatsapp_read($dados, 'id_agendamento', 0) === (int)$agendamento->id) {
                $motivo = trim((string)utec_whatsapp_read($dados, 'motivo', ''));
            }
        }

        $r = $this->CI->whatsapp_model->cancelar_agendamento_chatbot((int)$agendamento->id, (int)$perfil['id_usuario'], $this->minimo(), $perfil['telefone']);
        if (empty($r['ok'])) {
            $falha = (string)utec_whatsapp_read($r, 'motivo_falha', 'erro');
            if ($falha === 'prazo') {
                return $this->fallback('prazo', 'cancelar', $agendamento);
            }
            if ($falha === 'erro') {
                return $this->texto($perfil, 'Não foi possível cancelar agora. Tente novamente em instantes.', (int)$agendamento->id);
            }
            return ['expirado' => true];
        }

        $this->CI->whatsapp_model->limpar_sessao_chatbot($perfil['telefone']);
        if (!empty($r['ja_estava'])) {
            return $this->texto($perfil, 'Essa consulta já está cancelada.', (int)$agendamento->id);
        }
        $resposta = $this->texto($perfil, 'Consulta cancelada. Se quiser remarcar depois, é só chamar aqui.', (int)$agendamento->id);
        $this->avisar_equipe('cancelar', $agendamento, $r, $motivo, $idEvento);
        return $resposta;
    }

    protected function avisar_equipe($acao, $agendamento, $r, $motivo, $idEvento)
    {
        $anterior = utec_whatsapp_read($r, 'anterior', null) ?: $agendamento;
        $novo = utec_whatsapp_read($r, 'agendamento', null) ?: $agendamento;
        $contexto = [
            'tenant_id' => (int)utec_whatsapp_read($anterior, 'tenant_id', 0),
            'id_agendamento' => (int)$agendamento->id,
            'id_paciente' => (int)utec_whatsapp_read($agendamento, 'id_paciente', 0),
            'id_user' => (int)utec_whatsapp_read($agendamento, 'id_user', 0),
            'id_prestador' => (int)utec_whatsapp_read($agendamento, 'id_prestador', 0),
            'paciente_nome' => utec_whatsapp_read($agendamento, 'paciente_nome', ''),
            'id_whatsapp_notificacao' => (int)utec_whatsapp_read($r, 'id_log', 0),
            'data_anterior' => substr((string)utec_whatsapp_read($anterior, 'data_agenda', ''), 0, 10),
            'hora_anterior' => substr((string)utec_whatsapp_read($anterior, 'hora_agenda', ''), 0, 5),
        ];
        $dados = [
            'data_anterior' => $contexto['data_anterior'],
            'hora_anterior' => $contexto['hora_anterior'],
            'data_nova' => substr((string)utec_whatsapp_read($novo, 'data_agenda', ''), 0, 10),
            'hora_nova' => substr((string)utec_whatsapp_read($novo, 'hora_agenda', ''), 0, 5),
            'motivo' => (string)$motivo,
        ];
        try {
            $this->CI->notificacoes_model->criar_aviso_chatbot_agenda($contexto, $acao, $dados, (int)$idEvento);
        } catch (Throwable $e) {
            $this->log('aviso interno falhou: '.$e->getMessage());
        }
        try {
            $this->CI->whatsapp_agendamento->notificar_equipe($contexto, $acao);
        } catch (Throwable $e) {
            $this->log('whatsapp equipe falhou: '.$e->getMessage());
        }
    }

    protected function pedir_motivo($perfil, $agendamento)
    {
        return $this->botoes($perfil, 'Se quiser, conte em uma mensagem o motivo do cancelamento. Ou toque em "Prefiro não informar".', [
            ['id' => utec_whatsapp_agenda_id_sem_motivo($agendamento->id), 'title' => 'Prefiro não informar'],
            ['id' => 'chat:paciente:voltar', 'title' => 'Voltar'],
        ], (int)$agendamento->id);
    }

    protected function pedir_confirmacao_cancelamento($perfil, $agendamento)
    {
        return $this->botoes($perfil, utec_whatsapp_agenda_texto_confirmar_cancelamento($agendamento->data_agenda, $agendamento->hora_agenda, utec_whatsapp_read($agendamento, 'prestador_nome', '')), [
            ['id' => utec_whatsapp_agenda_id_confirmar_cancelamento($agendamento->id), 'title' => 'Sim, cancelar'],
            ['id' => 'chat:paciente:voltar', 'title' => 'Voltar'],
        ], (int)$agendamento->id);
    }

    protected function fallback($motivo, $acao, $agendamento)
    {
        return ['fallback' => $motivo, 'acao' => $acao === 'cancelar' ? 'cancelar' : 'remarcar', 'id_agendamento' => (int)$agendamento->id];
    }

    protected function elegivel($agendamento, $perfil)
    {
        return $perfil['perfil'] === 'paciente' && $agendamento && (string)utec_whatsapp_read($agendamento, 'status', '') === '0';
    }

    protected function salvar_sessao($perfil, $fluxo, $etapa, $dados, $evento)
    {
        return $this->CI->whatsapp_model->salvar_sessao_chatbot(
            $perfil['telefone'], $perfil['perfil'], $perfil['id_usuario'], $perfil['tenant_id'], $fluxo, $etapa, $dados,
            utec_whatsapp_read($evento, 'event_at', null), utec_whatsapp_read($evento, 'message_id', '')
        );
    }

    protected function lista($perfil, $corpo, $botao, $rows, $idAgendamento)
    {
        return $this->enviar($perfil, utec_whatsapp_payload_lista('', 'Remarcar consulta', $corpo, $botao, [['rows' => $rows]]), $idAgendamento);
    }

    protected function botoes($perfil, $corpo, $botoes, $idAgendamento)
    {
        return $this->enviar($perfil, utec_whatsapp_payload_botoes('', '', $corpo, $botoes), $idAgendamento);
    }

    protected function texto($perfil, $texto, $idAgendamento)
    {
        return $this->enviar($perfil, utec_whatsapp_payload_texto('', $texto), $idAgendamento);
    }

    protected function enviar($perfil, $payload, $idAgendamento)
    {
        if (empty($payload)) {
            return ['processado' => false, 'reason' => 'payload_invalido', 'id_agendamento' => (int)$idAgendamento];
        }
        $envio = $this->CI->whatsapp_agendamento->enviar_chatbot($perfil['telefone'], $payload);
        return [
            'processado' => !empty($envio['sent']),
            'reason' => !empty($envio['reason']) ? $envio['reason'] : 'api_error',
            'wamid' => trim((string)utec_whatsapp_read($envio, 'wamid', '')),
            'error' => trim((string)utec_whatsapp_read($envio, 'error', '')),
            'id_agendamento' => (int)$idAgendamento,
        ];
    }

    protected function agora()
    {
        return $this->agora !== null ? (int)$this->agora : time();
    }

    protected function minimo()
    {
        return utec_whatsapp_agenda_minimo_datetime($this->agora());
    }

    protected function tamanho($texto)
    {
        return function_exists('mb_strlen') ? mb_strlen($texto, 'UTF-8') : strlen($texto);
    }

    protected function log($mensagem)
    {
        if (function_exists('log_message')) {
            log_message('error', '[whatsapp_chatbot_agenda] '.$mensagem);
        }
    }
}
