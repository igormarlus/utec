<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Whatsapp_chatbot {

    protected $CI;

    public function __construct($ci = null)
    {
        $this->CI = $ci ?: get_instance();
        if (!isset($this->CI->whatsapp_model)) {
            $this->CI->load->model('Whatsapp_model', 'whatsapp_model');
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

    public function processar($evento)
    {
        $telefone = utec_whatsapp_normalizar_numero(utec_whatsapp_read($evento, 'from', ''));
        $messageId = trim((string)utec_whatsapp_read($evento, 'message_id', ''));
        $payload = trim((string)utec_whatsapp_read($evento, 'payload', ''));
        $texto = trim((string)utec_whatsapp_read($evento, 'text', ''));
        $entrada = $payload !== '' ? $payload : $texto;
        $inicio = $this->CI->whatsapp_model->iniciar_evento_chatbot(
            $messageId, $telefone, trim((string)utec_whatsapp_read($evento, 'message_type', 'mensagem')), $entrada
        );
        if (!in_array(utec_whatsapp_read($inicio, 'status', ''), ['processavel', 'reclaim'], true)) {
            return ['processado' => false, 'reason' => utec_whatsapp_read($inicio, 'status', 'invalido')];
        }

        $idEvento = (int)utec_whatsapp_read($inicio, 'id_evento', 0);
        $token = trim((string)utec_whatsapp_read($inicio, 'token_processamento', ''));
        $perfil = $this->CI->whatsapp_model->resolver_perfil_chatbot($telefone);
        $resultado = ['processado' => false, 'reason' => 'unknown'];
        $idSessao = 0;
        $idUsuario = (int)utec_whatsapp_read($perfil, 'id_usuario', 0);
        $idAgendamento = 0;

        try {
            if ($telefone === '' || $idUsuario <= 0 || trim((string)utec_whatsapp_read($perfil, 'perfil', '')) === '') {
                $resultado = $this->responder_nao_cadastrado($telefone);
            } else {
                $sessao = $this->CI->whatsapp_model->obter_sessao_chatbot($telefone);
                $idSessao = (int)utec_whatsapp_read($sessao, 'id', 0);
                if ($this->sessao_motivo_aberta($sessao)) {
                    $resultado = $this->processar_sessao_motivo($perfil, $sessao, $evento, $idEvento);
                } else {
                    $resultado = $this->processar_comando($perfil, $evento);
                }
                $idAgendamento = (int)utec_whatsapp_read($resultado, 'id_agendamento', 0);
            }
        } finally {
            $this->CI->whatsapp_model->finalizar_evento_chatbot($idEvento, $resultado, $idSessao, $idUsuario, $idAgendamento, $token);
        }

        return $resultado;
    }

    protected function processar_comando($perfil, $evento)
    {
        $comando = $this->extrair_comando($perfil['perfil'], $evento);
        if (!$comando) {
            return $this->responder_menu($perfil);
        }
        if ($comando['nome'] === 'voltar') {
            $this->CI->whatsapp_model->limpar_sessao_chatbot($perfil['telefone']);
            return $this->responder_menu($perfil);
        }
        if ($comando['nome'] === 'suporte') {
            return $this->responder_texto($perfil['telefone'], utec_whatsapp_chatbot_texto_suporte().' '.utec_whatsapp_chatbot_url_suporte());
        }
        if ($comando['nome'] === 'plano') {
            return $this->responder_plano($perfil);
        }
        if ($comando['nome'] === 'atendimento') {
            return $this->responder_texto($perfil['telefone'], 'Para assuntos sobre atendimento, fale com o dev. '.utec_whatsapp_chatbot_url_suporte());
        }
        if (in_array($comando['nome'], ['cancelar', 'remarcar'], true)) {
            return $this->iniciar_solicitacao($perfil, $comando, $evento);
        }
        if ($comando['nome'] === 'consulta') {
            return $this->responder_consulta($perfil, $comando['id_agendamento']);
        }
        return $this->responder_agenda($perfil, $comando['nome']);
    }

    protected function processar_sessao_motivo($perfil, $sessao, $evento, $idEvento)
    {
        $comando = $this->extrair_comando($perfil['perfil'], $evento);
        if ($comando && $comando['nome'] === 'voltar') {
            $this->CI->whatsapp_model->limpar_sessao_chatbot($perfil['telefone']);
            return $this->responder_menu($perfil);
        }
        if ($comando) {
            return $this->responder_texto($perfil['telefone'], 'Envie o motivo com pelo menos 3 caracteres ou escolha Voltar.');
        }

        $motivo = trim((string)utec_whatsapp_read($evento, 'text', ''));
        if ($this->tamanho_texto($motivo) < 3) {
            return $this->responder_texto($perfil['telefone'], 'Informe um motivo com pelo menos 3 caracteres.');
        }

        $dados = json_decode((string)utec_whatsapp_read($sessao, 'dados_json', '{}'), true);
        $dados = is_array($dados) ? $dados : [];
        $idAgendamento = (int)utec_whatsapp_read($dados, 'id_agendamento', 0);
        $acao = trim((string)utec_whatsapp_read($dados, 'acao', ''));
        $agendamento = $this->CI->whatsapp_model->obter_agendamento_chatbot($idAgendamento, $perfil['perfil'], $perfil['id_usuario'], $perfil['tenant_id']);
        if (!$this->agendamento_paciente_valido($agendamento, $perfil['perfil'])) {
            $this->CI->whatsapp_model->limpar_sessao_chatbot($perfil['telefone']);
            return $this->responder_texto($perfil['telefone'], 'Nao foi possivel localizar uma consulta elegivel. Escolha outra opcao no menu.');
        }

        $contexto = [
            'tenant_id' => $perfil['tenant_id'],
            'id_agendamento' => (int)$agendamento->id,
            'id_paciente' => (int)utec_whatsapp_read($agendamento, 'id_paciente', $perfil['id_usuario']),
            'id_user' => (int)utec_whatsapp_read($agendamento, 'id_user', 0),
            'id_prestador' => (int)utec_whatsapp_read($agendamento, 'id_prestador', 0),
            'paciente_nome' => utec_whatsapp_read($agendamento, 'paciente_nome', ''),
        ];
        if (!$this->CI->notificacoes_model->criar_solicitacao_chatbot($contexto, $acao, $motivo, $idEvento)) {
            return $this->responder_texto($perfil['telefone'], 'Nao foi possivel registrar sua solicitacao. Tente novamente mais tarde.');
        }

        $this->CI->whatsapp_model->limpar_sessao_chatbot($perfil['telefone']);
        $resultado = $this->responder_texto($perfil['telefone'], 'Recebemos sua solicitacao. A equipe vai analisar o pedido.');
        $resultado['id_agendamento'] = (int)$agendamento->id;
        return $resultado;
    }

    protected function iniciar_solicitacao($perfil, $comando, $evento)
    {
        $idAgendamento = (int)$comando['id_agendamento'];
        if ($idAgendamento <= 0) {
            return $this->responder_lista_consultas($perfil, $comando['nome']);
        }
        $agendamento = $this->CI->whatsapp_model->obter_agendamento_chatbot($idAgendamento, $perfil['perfil'], $perfil['id_usuario'], $perfil['tenant_id']);
        if (!$this->agendamento_paciente_valido($agendamento, $perfil['perfil'])) {
            return $this->responder_texto($perfil['telefone'], 'Consulta nao encontrada ou indisponivel para esta solicitacao.');
        }

        $this->CI->whatsapp_model->salvar_sessao_chatbot(
            $perfil['telefone'], $perfil['perfil'], $perfil['id_usuario'], $perfil['tenant_id'], 'solicitacao', 'motivo',
            ['acao' => $comando['nome'] === 'remarcar' ? 'remarcacao' : 'cancelamento', 'id_agendamento' => $idAgendamento],
            utec_whatsapp_read($evento, 'event_at', null), utec_whatsapp_read($evento, 'message_id', '')
        );
        $resultado = $this->responder_texto($perfil['telefone'], 'Informe o motivo da solicitacao com pelo menos 3 caracteres.');
        $resultado['id_agendamento'] = $idAgendamento;
        return $resultado;
    }

    protected function responder_agenda($perfil, $comando)
    {
        $agenda = $this->filtrar_agenda($this->CI->whatsapp_model->listar_agendamentos_chatbot($perfil['perfil'], $perfil['id_usuario'], $perfil['tenant_id']), $comando);
        $linhas = [];
        foreach (array_slice($agenda, 0, 10) as $agendamento) {
            $nome = $perfil['perfil'] === 'paciente' ? utec_whatsapp_read($agendamento, 'prestador_nome', '') : utec_whatsapp_read($agendamento, 'paciente_nome', '');
            $linhas[] = utec_whatsapp_formatar_hora_br(utec_whatsapp_read($agendamento, 'hora_agenda', '')).' - '.trim((string)$nome).' - '.utec_whatsapp_status_chatbot($agendamento);
        }
        return $this->responder_texto($perfil['telefone'], empty($linhas) ? 'Nenhum agendamento encontrado.' : implode("\n", $linhas));
    }

    protected function responder_lista_consultas($perfil, $acao)
    {
        $agenda = $this->filtrar_agenda($this->CI->whatsapp_model->listar_agendamentos_chatbot($perfil['perfil'], $perfil['id_usuario'], $perfil['tenant_id']), 'proximas');
        $rows = [];
        foreach (array_slice($agenda, 0, 10) as $agendamento) {
            if (!$this->agendamento_paciente_valido($agendamento, $perfil['perfil'])) {
                continue;
            }
            $rows[] = ['id' => 'chat:paciente:'.$acao.':'.(int)$agendamento->id, 'title' => utec_whatsapp_formatar_hora_br($agendamento->hora_agenda).' - '.utec_whatsapp_read($agendamento, 'prestador_nome', ''), 'description' => utec_whatsapp_status_chatbot($agendamento)];
        }
        if (empty($rows)) {
            return $this->responder_texto($perfil['telefone'], 'Nenhuma consulta elegivel foi encontrada.');
        }
        return $this->responder_payload($perfil['telefone'], utec_whatsapp_payload_lista('', 'Consultas', 'Escolha uma consulta.', 'Ver consultas', [['rows' => $rows]]));
    }

    protected function responder_consulta($perfil, $idAgendamento)
    {
        if ((int)$idAgendamento <= 0) {
            return $this->responder_lista_consultas($perfil, 'consulta');
        }
        $agendamento = $this->CI->whatsapp_model->obter_agendamento_chatbot($idAgendamento, $perfil['perfil'], $perfil['id_usuario'], $perfil['tenant_id']);
        if (!$this->agendamento_paciente_valido($agendamento, $perfil['perfil'])) {
            return $this->responder_texto($perfil['telefone'], 'Consulta nao encontrada.');
        }
        $texto = utec_whatsapp_formatar_data_br($agendamento->data_agenda).' '.utec_whatsapp_formatar_hora_br($agendamento->hora_agenda).' - '.utec_whatsapp_read($agendamento, 'prestador_nome', '').' - '.utec_whatsapp_status_chatbot($agendamento);
        $resultado = $this->responder_texto($perfil['telefone'], $texto);
        $resultado['id_agendamento'] = (int)$agendamento->id;
        return $resultado;
    }

    protected function responder_plano($perfil)
    {
        if (!utec_whatsapp_perfil_tem_plano($perfil['perfil'])) {
            return ['processado' => false, 'reason' => 'comando_invalido'];
        }
        $plano = $this->CI->whatsapp_model->obter_plano_chatbot($perfil['perfil'], $perfil['id_usuario'], $perfil['tenant_id']);
        if (!$plano) {
            return $this->responder_texto($perfil['telefone'], 'Nenhuma informacao de plano esta disponivel.');
        }
        return $this->responder_texto($perfil['telefone'], 'Plano: '.utec_whatsapp_read($plano, 'modelo', 'Ativo')."\nStatus: ".utec_whatsapp_read($plano, 'status', ''));
    }

    protected function responder_menu($perfil)
    {
        $rows = [];
        foreach ($this->comandos_permitidos($perfil['perfil']) as $comando) {
            $rows[] = ['id' => 'chat:'.$perfil['perfil'].':'.$comando, 'title' => ucfirst(str_replace('_', ' ', $comando))];
        }
        return $this->responder_payload($perfil['telefone'], utec_whatsapp_payload_lista('', 'Atendimento', 'Escolha uma opcao.', 'Menu', [['title' => 'Opcoes', 'rows' => $rows]]));
    }

    protected function responder_nao_cadastrado($telefone)
    {
        $url = function_exists('base_url') ? base_url() : 'https://utecnologia.com.br/';
        return $this->responder_texto($telefone, 'Seu numero nao esta cadastrado. Acesse '.$url.' para entrar em contato.');
    }

    protected function responder_texto($telefone, $texto)
    {
        return $this->responder_payload($telefone, utec_whatsapp_payload_texto('', $texto));
    }

    protected function responder_payload($telefone, $payload)
    {
        if (empty($payload)) {
            return ['processado' => false, 'reason' => 'payload_invalido'];
        }
        $envio = $this->CI->whatsapp_agendamento->enviar_chatbot($telefone, $payload);
        return ['processado' => !empty($envio['sent']), 'reason' => !empty($envio['reason']) ? $envio['reason'] : 'api_error'];
    }

    protected function extrair_comando($perfil, $evento)
    {
        $entrada = trim((string)utec_whatsapp_read($evento, 'payload', ''));
        if ($entrada === '') {
            $entrada = strtolower(trim((string)utec_whatsapp_read($evento, 'text', '')));
            return in_array($entrada, $this->comandos_permitidos($perfil), true) ? ['nome' => $entrada, 'id_agendamento' => 0] : null;
        }
        if (!preg_match('/^chat:([a-z_]+):([a-z_]+)(?::(\d+))?$/', $entrada, $matches) || $matches[1] !== $perfil || !in_array($matches[2], $this->comandos_permitidos($perfil), true)) {
            return null;
        }
        return ['nome' => $matches[2], 'id_agendamento' => isset($matches[3]) ? (int)$matches[3] : 0];
    }

    protected function comandos_permitidos($perfil)
    {
        $comandos = [
            'paciente' => ['proximas', 'consulta', 'cancelar', 'remarcar', 'atendimento', 'voltar'],
            'profissional' => ['agenda_hoje', 'amanha', 'pendencias', 'plano', 'suporte', 'voltar'],
            'admin' => ['agenda', 'pedencias', 'cancelamentos', 'plano', 'suporte', 'voltar'],
            'atendente' => ['agenda_hoje', 'amanha', 'pendencias', 'suporte', 'voltar'],
        ];
        return isset($comandos[$perfil]) ? $comandos[$perfil] : [];
    }

    protected function sessao_motivo_aberta($sessao)
    {
        return $sessao && utec_whatsapp_read($sessao, 'fluxo', '') === 'solicitacao' && utec_whatsapp_read($sessao, 'etapa', '') === 'motivo';
    }

    protected function agendamento_paciente_valido($agendamento, $perfil)
    {
        return $perfil === 'paciente' && $agendamento && (string)utec_whatsapp_read($agendamento, 'status', '') !== '3';
    }

    protected function filtrar_agenda($agenda, $comando)
    {
        $agenda = is_array($agenda) ? $agenda : [];
        $hoje = date('Y-m-d');
        $amanha = date('Y-m-d', strtotime('+1 day'));
        $filtrada = [];
        foreach ($agenda as $agendamento) {
            $data = substr((string)utec_whatsapp_read($agendamento, 'data_agenda', ''), 0, 10);
            $status = utec_whatsapp_status_chatbot($agendamento);
            if (in_array($comando, ['proximas', 'consulta'], true) && ($data < $hoje || $status === '❌ cancelado')) {
                continue;
            }
            if ($comando === 'agenda_hoje' && $data !== $hoje) {
                continue;
            }
            if ($comando === 'amanha' && $data !== $amanha) {
                continue;
            }
            if (in_array($comando, ['pendencias', 'pedencias'], true) && $status !== '⏳ pendente') {
                continue;
            }
            if ($comando === 'cancelamentos' && $status !== '❌ cancelado') {
                continue;
            }
            $filtrada[] = $agendamento;
        }
        return $filtrada;
    }

    protected function tamanho_texto($texto)
    {
        if (function_exists('mb_strlen')) {
            return mb_strlen($texto, 'UTF-8');
        }
        $caracteres = preg_split('//u', $texto, -1, PREG_SPLIT_NO_EMPTY);
        return is_array($caracteres) ? count($caracteres) : strlen($texto);
    }
}
