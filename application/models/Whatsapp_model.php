<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Whatsapp_model extends CI_Model {

    private $table = 'whatsapp_config';
    private $log_table = 'whatsapp_notificacoes';
    private $chatbot_session_table = 'whatsapp_chatbot_sessoes';
    private $chatbot_event_table = 'whatsapp_chatbot_eventos';

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('whatsapp_agendamento');
    }

    public function get_configuracao_atual()
    {
        if (!$this->db->table_exists($this->table)) {
            return null;
        }

        $qr = $this->db->query("SELECT * FROM `{$this->table}` ORDER BY id DESC LIMIT 1");
        return $qr->num_rows() ? $qr->row() : null;
    }

    public function get_configuracao_ativa()
    {
        if (!$this->db->table_exists($this->table)) {
            return null;
        }

        $qr = $this->db->query("SELECT * FROM `{$this->table}` WHERE status = 1 ORDER BY id DESC LIMIT 1");
        return $qr->num_rows() ? $qr->row() : null;
    }

    public function salvar_configuracao($data)
    {
        if (!$this->db->table_exists($this->table)) {
            throw new Exception('A tabela whatsapp_config ainda nao existe no banco.');
        }

        $atual = $this->get_configuracao_atual();
        $id = isset($data['id']) ? (int)$data['id'] : 0;
        if ($id <= 0 && $atual) {
            $id = (int)$atual->id;
        }

        $save = $this->filtrar_colunas($this->table, [
            'nome_conexao' => trim((string)utec_whatsapp_read($data, 'nome_conexao', 'Configuracao principal')),
            'phone_number_id' => trim((string)utec_whatsapp_read($data, 'phone_number_id', '')),
            'waba_id' => trim((string)utec_whatsapp_read($data, 'waba_id', '')),
            'access_token' => trim((string)utec_whatsapp_read($data, 'access_token', '')),
            'app_secret' => trim((string)utec_whatsapp_read($data, 'app_secret', '')),
            'verify_token' => trim((string)utec_whatsapp_read($data, 'verify_token', '')),
            'template_name' => trim((string)utec_whatsapp_read($data, 'template_name', 'confirmacao_consulta')),
            'template_lang' => trim((string)utec_whatsapp_read($data, 'template_lang', 'pt_BR')),
            'numero_remetente' => utec_whatsapp_normalizar_numero(utec_whatsapp_read($data, 'numero_remetente', '')),
            'status' => utec_whatsapp_read($data, 'status', '') ? 1 : 0,
        ]);

        if ($id > 0) {
            $this->db->where('id', $id);
            $this->db->update($this->table, $save);
        } else {
            $this->db->insert($this->table, $save);
            $id = (int)$this->db->insert_id();
        }

        if ((int)utec_whatsapp_read($save, 'status', 0) === 1) {
            $this->db->where('id !=', $id);
            $this->db->update($this->table, $this->filtrar_colunas($this->table, ['status' => 0]));
        }

        return $id;
    }

    public function registrar_log($data)
    {
        if (!$this->db->table_exists($this->log_table)) {
            return false;
        }

        $log = $this->filtrar_colunas($this->log_table, [
            'id_agendamento' => (int)utec_whatsapp_read($data, 'id_agendamento', 0),
            'tenant_id' => (int)utec_whatsapp_read($data, 'tenant_id', 0),
            'telefone_destino' => trim((string)utec_whatsapp_read($data, 'telefone_destino', '')),
            'wamid' => trim((string)utec_whatsapp_read($data, 'wamid', '')),
            'status_envio' => trim((string)utec_whatsapp_read($data, 'status_envio', 'pendente')),
            'erro_detalhe' => trim((string)utec_whatsapp_read($data, 'erro_detalhe', '')),
            'status_confirmacao' => trim((string)utec_whatsapp_read($data, 'status_confirmacao', 'pendente')),
            'tipo_notificacao' => trim((string)utec_whatsapp_read($data, 'tipo_notificacao', 'confirmacao')),
            'criado_em' => date('Y-m-d H:i:s'),
            'respondido_em' => utec_whatsapp_read($data, 'respondido_em', null),
        ]);

        return $this->db->insert($this->log_table, $log);
    }

    public function contar_envios_enviados_por_tenant($tenant_id)
    {
        $tenant_id = (int)$tenant_id;
        if ($tenant_id <= 0 || !$this->db->table_exists($this->log_table)) {
            return 0;
        }

        $qr = $this->db->query(
            "SELECT COUNT(id) AS total
             FROM `{$this->log_table}`
             WHERE tenant_id = {$tenant_id}
               AND wamid <> ''
               AND status_envio IN ('enviado', 'entregue', 'lido', 'erro', 'apagado')"
        );

        return $qr->num_rows() ? (int)$qr->row()->total : 0;
    }

    public function registrar_limite_atingido($tenant_id, $id_agendamento, $telefone_destino, $mensagem, $tipo_notificacao = 'confirmacao')
    {
        return $this->registrar_log([
            'tenant_id' => (int)$tenant_id,
            'id_agendamento' => (int)$id_agendamento,
            'telefone_destino' => trim((string)$telefone_destino),
            'status_envio' => 'limite',
            'erro_detalhe' => trim((string)$mensagem),
            'status_confirmacao' => 'nao_enviado',
            'tipo_notificacao' => trim((string)$tipo_notificacao),
        ]);
    }

    public function resumir_consumo_tenant($tenant_id, $subscription_status)
    {
        $used = $this->contar_envios_enviados_por_tenant($tenant_id);
        return utec_whatsapp_politica_limite($subscription_status, $used);
    }

    public function get_notificacao_por_wamid($wamid)
    {
        $wamid = trim((string)$wamid);
        if ($wamid === '' || !$this->db->table_exists($this->log_table)) {
            return null;
        }

        $qr = $this->db->query(
            "SELECT * FROM `{$this->log_table}` WHERE wamid = ".$this->db->escape($wamid)." ORDER BY id DESC LIMIT 1"
        );

        return $qr->num_rows() ? $qr->row() : null;
    }

    public function get_notificacao_por_agendamento($id_agendamento)
    {
        $id_agendamento = (int)$id_agendamento;
        if ($id_agendamento <= 0 || !$this->db->table_exists($this->log_table)) {
            return null;
        }

        $qr = $this->db->query(
            "SELECT * FROM `{$this->log_table}` WHERE id_agendamento = {$id_agendamento} ORDER BY id DESC LIMIT 1"
        );

        return $qr->num_rows() ? $qr->row() : null;
    }

    public function get_agendamentos_para_lembrete($tipo, $inicio, $fim)
    {
        $tipo = (string)$tipo;
        if (!utec_whatsapp_lembrete_tipo_valido($tipo)
            || !$this->db->table_exists('agendamentos')
            || !$this->db->table_exists($this->log_table)
            || !$this->db->field_exists('tipo_notificacao', $this->log_table)) {
            return [];
        }

        $inicio = $this->db->escape((string)$inicio);
        $fim = $this->db->escape((string)$fim);
        $tipoEscapado = $this->db->escape($tipo);

        $condicaoConfirmado = '';
        $condicaoConfirmacaoRecente = '';
        $condicaoPrestador = '';
        if ($tipo === 'lembrete_paciente') {
            $condicaoConfirmado =
                " AND NOT EXISTS (SELECT 1 FROM `{$this->log_table}` wc"
                . " WHERE wc.id_agendamento = a.id AND wc.status_confirmacao = 'confirmado')";

            $cutoffConfirmacao = $this->db->escape(date('Y-m-d H:i:s', time() - 3 * 3600));
            $condicaoConfirmacaoRecente =
                " AND NOT EXISTS (SELECT 1 FROM `{$this->log_table}` wr"
                . " WHERE wr.id_agendamento = a.id AND wr.tipo_notificacao = 'confirmacao'"
                . " AND wr.criado_em >= {$cutoffConfirmacao})";
        } elseif ($tipo === 'lembrete_profissional') {
            $condicaoPrestador = ' AND a.id_prestador > 0';
        }

        $sql =
            "SELECT a.id\n"
            . "FROM `agendamentos` a\n"
            . "WHERE a.status = 0\n"
            . "  AND TIMESTAMP(a.data_agenda, a.hora_agenda) >= {$inicio}\n"
            . "  AND TIMESTAMP(a.data_agenda, a.hora_agenda) <= {$fim}\n"
            . "  AND NOT EXISTS (SELECT 1 FROM `{$this->log_table}` wn"
            . " WHERE wn.id_agendamento = a.id AND wn.tipo_notificacao = {$tipoEscapado})\n"
            . $condicaoConfirmado
            . $condicaoConfirmacaoRecente
            . $condicaoPrestador
            . "\nORDER BY a.data_agenda ASC, a.hora_agenda ASC, a.id ASC"
            . "\nLIMIT 200";

        $qr = $this->db->query($sql);
        return $qr ? $qr->result() : [];
    }

    public function resolver_notificacao_webhook($wamid, $id_agendamento)
    {
        $wamid = trim((string)$wamid);
        return $wamid !== '' ? $this->get_notificacao_por_wamid($wamid) : $this->get_notificacao_por_agendamento($id_agendamento);
    }

    public function atualizar_status_envio_notificacao($id, $status_meta, $erro_detalhe = '', $event_at = null)
    {
        $id = (int)$id;
        $status_envio = utec_whatsapp_status_envio_meta($status_meta);
        if ($id <= 0 || $status_envio === '' || !$this->db->table_exists($this->log_table)) {
            return false;
        }

        $dados = $this->filtrar_colunas($this->log_table, [
            'status_envio' => $status_envio,
            'erro_detalhe' => trim((string)$erro_detalhe),
            'status_atualizado_em' => $event_at,
        ]);
        if (empty($dados)) {
            return false;
        }

        $this->db->where('id', $id);
        if ($event_at && $this->db->field_exists('status_atualizado_em', $this->log_table)) {
            $this->db->group_start();
            $this->db->where('status_atualizado_em IS NULL', null, false);
            $this->db->or_where('status_atualizado_em <=', $event_at);
            $this->db->group_end();
        }
        return $this->db->update($this->log_table, $dados);
    }

    public function atualizar_confirmacao_notificacao($id, $status_confirmacao)
    {
        $id = (int)$id;
        $status_confirmacao = trim((string)$status_confirmacao);
        if ($id <= 0 || $status_confirmacao === '' || !$this->db->table_exists($this->log_table)) {
            return false;
        }

        $dados = $this->filtrar_colunas($this->log_table, [
            'status_confirmacao' => $status_confirmacao,
            'respondido_em' => date('Y-m-d H:i:s'),
        ]);
        if (empty($dados)) {
            return false;
        }

        $this->db->where('id', $id);
        return $this->db->update($this->log_table, $dados);
    }

    public function cancelar_agendamento_por_webhook($id_agendamento)
    {
        $id_agendamento = (int)$id_agendamento;
        if ($id_agendamento <= 0 || !$this->db->table_exists('agendamentos')) {
            return false;
        }

        $this->db->where('id', $id_agendamento);
        return $this->db->update('agendamentos', ['status' => 3]);
    }

    public function cancelar_notificacao_e_agendamento($id_notificacao, $id_agendamento)
    {
        $this->db->trans_begin();
        $notificacaoAtualizada = $this->atualizar_confirmacao_notificacao($id_notificacao, 'cancelado');
        $agendamentoCancelado = $notificacaoAtualizada && $this->cancelar_agendamento_por_webhook($id_agendamento);

        if (!$notificacaoAtualizada || !$agendamentoCancelado || $this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        }

        return $this->db->trans_commit();
    }

    public function registrar_resposta_webhook($id_notificacao, $acao)
    {
        $resultado = [
            'ok' => false,
            'processado' => false,
            'notificacao' => null,
            'contexto' => null,
        ];
        $idNotificacao = (int)$id_notificacao;
        $acao = strtolower(trim((string)$acao));

        if ($idNotificacao <= 0 || !in_array($acao, ['confirmar', 'cancelar'], true)
            || !$this->tabela_possui_campos($this->log_table, ['id', 'id_agendamento', 'tenant_id', 'telefone_destino', 'status_confirmacao', 'respondido_em'])
            || !$this->tabela_possui_campos('agendamentos', ['id', 'id_paciente', 'id_user', 'id_prestador', 'status'])
            || !$this->tabela_possui_campos('usuarios', ['id', 'nome'])) {
            return $resultado;
        }

        $this->db->trans_begin();
        $query = $this->db->query(
            "SELECT wn.*, a.id_paciente, a.id_user, a.id_prestador, p.nome AS paciente_nome\n"
            . "FROM `{$this->log_table}` wn\n"
            . "INNER JOIN `agendamentos` a ON a.id = wn.id_agendamento\n"
            . "LEFT JOIN `usuarios` p ON p.id = a.id_paciente\n"
            . "WHERE wn.id = {$idNotificacao} LIMIT 1 FOR UPDATE"
        );
        if (!$query || !$query->num_rows()) {
            $this->db->trans_rollback();
            return $resultado;
        }

        $notificacao = $query->row();
        $contexto = [
            'id_agendamento' => (int)$notificacao->id_agendamento,
            'id_paciente' => (int)$notificacao->id_paciente,
            'paciente_nome' => (string)$notificacao->paciente_nome,
            'id_user' => (int)$notificacao->id_user,
            'id_prestador' => (int)$notificacao->id_prestador,
            'tenant_id' => (int)$notificacao->tenant_id,
            'telefone_destino' => (string)$notificacao->telefone_destino,
            'id_whatsapp_notificacao' => (int)$notificacao->id,
        ];
        $resultado['notificacao'] = $notificacao;
        $resultado['contexto'] = $contexto;

        $statusConfirmacao = $acao === 'confirmar' ? 'confirmado' : 'cancelado';
        $statusAtual = (string)$notificacao->status_confirmacao;

        // Nova resposta igual a que ja esta registrada: reentrega da Meta ou clique repetido no mesmo botao.
        if ($statusAtual === $statusConfirmacao) {
            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                return $resultado;
            }
            $this->db->trans_commit();
            $resultado['ok'] = true;
            return $resultado;
        }

        // Transicao valida: pendente -> confirmado/cancelado ou troca entre confirmado <-> cancelado.
        $dados = [
            'status_confirmacao' => $statusConfirmacao,
            'respondido_em' => date('Y-m-d H:i:s'),
        ];
        $this->db->where('id', $idNotificacao);
        $this->db->where('status_confirmacao', $statusAtual);
        $notificacaoAtualizada = $this->db->update($this->log_table, $dados);
        $agendamentoAtualizado = true;

        if ($acao === 'cancelar') {
            $this->db->where('id', (int)$notificacao->id_agendamento);
            $agendamentoAtualizado = $this->db->update('agendamentos', ['status' => 3]);
        } elseif ($statusAtual === 'cancelado') {
            // Reconfirmacao apos cancelamento: reativa o agendamento se ele ainda estava cancelado.
            $this->db->where('id', (int)$notificacao->id_agendamento);
            $this->db->where('status', 3);
            $agendamentoAtualizado = $this->db->update('agendamentos', ['status' => 0]);
        }

        if (!$notificacaoAtualizada || !$agendamentoAtualizado || $this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return $resultado;
        }

        if (!$this->db->trans_commit()) {
            return $resultado;
        }

        $notificacao->status_confirmacao = $statusConfirmacao;
        $notificacao->respondido_em = $dados['respondido_em'];
        $resultado['notificacao'] = $notificacao;
        $resultado['ok'] = true;
        $resultado['processado'] = true;
        return $resultado;
    }

    public function iniciar_evento_chatbot($message_id, $telefone, $tipo = 'mensagem', $entrada = '')
    {
        $message_id = trim((string)$message_id);
        $telefone = utec_whatsapp_normalizar_numero($telefone);
        $tipo = trim((string)$tipo);

        if ($message_id === '' || $telefone === '' || $tipo === ''
            || !$this->tabela_possui_campos($this->chatbot_event_table, ['message_id', 'telefone', 'tipo', 'entrada', 'criado_em'])) {
            return 0;
        }

        $sql = "INSERT IGNORE INTO `{$this->chatbot_event_table}` (`message_id`, `telefone`, `tipo`, `entrada`, `criado_em`) VALUES ("
            .$this->db->escape($message_id).", "
            .$this->db->escape($telefone).", "
            .$this->db->escape($tipo).", "
            .$this->db->escape($this->chatbot_json($entrada)).", NOW())";
        if (!$this->db->query($sql) || (int)$this->db->affected_rows() !== 1) {
            return 0;
        }

        return (int)$this->db->insert_id();
    }

    public function finalizar_evento_chatbot($id_evento, $resultado, $id_sessao = 0, $id_usuario = 0, $id_agendamento = 0)
    {
        $id_evento = (int)$id_evento;
        if ($id_evento <= 0 || !$this->tabela_possui_campos($this->chatbot_event_table, ['id', 'resultado'])) {
            return false;
        }

        $dados = $this->filtrar_colunas($this->chatbot_event_table, [
            'resultado' => $this->chatbot_json($resultado),
            'id_whatsapp_chatbot_sessao' => (int)$id_sessao,
            'id_usuario' => (int)$id_usuario,
            'id_agendamento' => (int)$id_agendamento,
        ]);
        if (empty($dados)) {
            return false;
        }

        $this->db->where('id', $id_evento);
        return $this->db->update($this->chatbot_event_table, $dados);
    }

    public function obter_sessao_chatbot($telefone)
    {
        $telefone = utec_whatsapp_normalizar_numero($telefone);
        if ($telefone === '' || !$this->tabela_possui_campos($this->chatbot_session_table, ['telefone', 'dados_json', 'expira_em'])) {
            return null;
        }

        $query = $this->db->query(
            "SELECT * FROM `{$this->chatbot_session_table}` WHERE telefone = ".$this->db->escape($telefone)." AND expira_em > NOW() LIMIT 1"
        );
        if (!$query || !$query->num_rows()) {
            return null;
        }

        $sessao = $query->row();
        $dados = json_decode((string)$sessao->dados_json, true);
        $sessao->dados = is_array($dados) ? $dados : [];
        return $sessao;
    }

    public function salvar_sessao_chatbot($telefone, $perfil, $id_usuario, $tenant_id, $fluxo, $etapa, $dados = [])
    {
        $telefone = utec_whatsapp_normalizar_numero($telefone);
        $perfil = trim((string)$perfil);
        if ($telefone === '' || $perfil === ''
            || !$this->tabela_possui_campos($this->chatbot_session_table, ['telefone', 'perfil', 'id_usuario', 'tenant_id', 'fluxo', 'etapa', 'dados_json', 'atividade_em', 'expira_em', 'criado_em', 'atualizado_em'])) {
            return false;
        }

        $sql = "INSERT INTO `{$this->chatbot_session_table}` (`telefone`, `perfil`, `id_usuario`, `tenant_id`, `fluxo`, `etapa`, `dados_json`, `atividade_em`, `expira_em`, `criado_em`, `atualizado_em`) VALUES ("
            .$this->db->escape($telefone).", "
            .$this->db->escape($perfil).", "
            .(int)$id_usuario.", "
            .(int)$tenant_id.", "
            .$this->db->escape(trim((string)$fluxo)).", "
            .$this->db->escape(trim((string)$etapa)).", "
            .$this->db->escape($this->chatbot_json($dados)).", NOW(), DATE_ADD(NOW(), INTERVAL 15 MINUTE), NOW(), NOW()) "
            ."ON DUPLICATE KEY UPDATE perfil = VALUES(perfil), id_usuario = VALUES(id_usuario), tenant_id = VALUES(tenant_id), fluxo = VALUES(fluxo), etapa = VALUES(etapa), dados_json = VALUES(dados_json), atividade_em = NOW(), expira_em = DATE_ADD(NOW(), INTERVAL 15 MINUTE), atualizado_em = NOW()";

        return (bool)$this->db->query($sql);
    }

    public function limpar_sessao_chatbot($telefone)
    {
        $telefone = utec_whatsapp_normalizar_numero($telefone);
        if ($telefone === '' || !$this->tabela_possui_campos($this->chatbot_session_table, ['telefone'])) {
            return false;
        }

        $this->db->where('telefone', $telefone);
        return $this->db->delete($this->chatbot_session_table);
    }

    public function resolver_perfil_chatbot($telefone)
    {
        $resultado = ['telefone' => utec_whatsapp_normalizar_numero($telefone), 'perfil' => '', 'id_usuario' => 0, 'tenant_id' => 0];
        if ($resultado['telefone'] === '' || !$this->tabela_possui_campos('usuarios', ['id', 'nivel', 'telefone'])) {
            return $resultado;
        }

        $tenantSelect = $this->db->field_exists('tenant_id', 'usuarios') ? 'tenant_id' : '0 AS tenant_id';
        $telefoneSql = $this->telefone_chatbot_sql('telefone');
        $usuario = $this->db->query(
            "SELECT id, nivel, {$tenantSelect} FROM `usuarios` WHERE nivel BETWEEN 1 AND 4 AND {$telefoneSql} = ".$this->db->escape($resultado['telefone'])." ORDER BY id ASC LIMIT 1"
        );
        if (!$usuario || !$usuario->num_rows()) {
            $usuario = $this->db->query(
                "SELECT id, nivel, {$tenantSelect} FROM `usuarios` WHERE nivel = 5 AND {$telefoneSql} = ".$this->db->escape($resultado['telefone'])." ORDER BY id ASC LIMIT 1"
            );
        }
        if (!$usuario || !$usuario->num_rows()) {
            return $resultado;
        }

        $usuario = $usuario->row();
        $resultado['id_usuario'] = (int)$usuario->id;
        $resultado['tenant_id'] = (int)$usuario->tenant_id;
        $resultado['perfil'] = utec_whatsapp_perfil_por_nivel($usuario->nivel);
        return $resultado;
    }

    public function listar_agendamentos_chatbot($perfil, $id_usuario, $tenant_id = 0)
    {
        $contexto = $this->contexto_chatbot_autorizado($perfil, $id_usuario, $tenant_id);
        if (!$contexto || !$this->tabela_possui_campos('agendamentos', ['id', 'id_paciente', 'id_prestador', 'id_user', 'data_agenda', 'hora_agenda', 'tipo', 'status'])) {
            return [];
        }

        $where = $this->where_agendamento_chatbot($contexto);
        if ($where === '') {
            return [];
        }

        $statusWhatsapp = "'' AS status_whatsapp";
        if ($this->tabela_possui_campos($this->log_table, ['id', 'id_agendamento', 'status_envio', 'status_confirmacao'])) {
            $statusWhatsapp = "COALESCE((SELECT CONCAT_WS('/', wn.status_envio, wn.status_confirmacao) FROM `{$this->log_table}` wn WHERE wn.id_agendamento = a.id ORDER BY wn.id DESC LIMIT 1), '') AS status_whatsapp";
        }
        $query = $this->db->query(
            "SELECT a.id, a.id_paciente, a.id_prestador, a.data_agenda, a.hora_agenda, a.tipo, a.status, p.nome AS paciente_nome, pr.nome AS prestador_nome, {$statusWhatsapp} FROM `agendamentos` a LEFT JOIN `usuarios` p ON p.id = a.id_paciente LEFT JOIN `usuarios` pr ON pr.id = a.id_prestador WHERE {$where} ORDER BY a.data_agenda ASC, a.hora_agenda ASC, a.id ASC"
        );
        return $query ? $query->result() : [];
    }

    public function obter_agendamento_chatbot($id_agendamento, $perfil, $id_usuario, $tenant_id = 0)
    {
        $id_agendamento = (int)$id_agendamento;
        $contexto = $this->contexto_chatbot_autorizado($perfil, $id_usuario, $tenant_id);
        if ($id_agendamento <= 0 || !$contexto || !$this->tabela_possui_campos('agendamentos', ['id', 'id_paciente', 'id_prestador', 'id_user', 'data_agenda', 'hora_agenda', 'tipo', 'status'])) {
            return null;
        }

        $where = $this->where_agendamento_chatbot($contexto);
        if ($where === '') {
            return null;
        }

        $statusWhatsapp = "'' AS status_whatsapp";
        if ($this->tabela_possui_campos($this->log_table, ['id', 'id_agendamento', 'status_envio', 'status_confirmacao'])) {
            $statusWhatsapp = "COALESCE((SELECT CONCAT_WS('/', wn.status_envio, wn.status_confirmacao) FROM `{$this->log_table}` wn WHERE wn.id_agendamento = a.id ORDER BY wn.id DESC LIMIT 1), '') AS status_whatsapp";
        }
        $query = $this->db->query(
            "SELECT a.id, a.id_paciente, a.id_prestador, a.data_agenda, a.hora_agenda, a.tipo, a.status, p.nome AS paciente_nome, pr.nome AS prestador_nome, {$statusWhatsapp} FROM `agendamentos` a LEFT JOIN `usuarios` p ON p.id = a.id_paciente LEFT JOIN `usuarios` pr ON pr.id = a.id_prestador WHERE a.id = {$id_agendamento} AND {$where} LIMIT 1"
        );
        return $query && $query->num_rows() ? $query->row() : null;
    }

    public function obter_plano_chatbot($perfil, $id_usuario, $tenant_id = 0)
    {
        if (!utec_whatsapp_perfil_tem_plano($perfil)
            || !$this->tabela_possui_campos('saas_subscriptions', ['id', 'tenant_id', 'plano_id', 'status'])
            || !$this->tabela_possui_campos('produtos', ['id', 'modelo'])) {
            return null;
        }

        $contexto = $this->contexto_chatbot_autorizado($perfil, $id_usuario, $tenant_id);
        if (!$contexto || (int)$contexto->tenant_id <= 0) {
            return null;
        }

        $dataSelect = $this->db->field_exists('current_period_end', 'saas_subscriptions') ? 's.current_period_end' : 'NULL';
        $query = $this->db->query(
            "SELECT p.modelo, s.status, {$dataSelect} AS data FROM `saas_subscriptions` s INNER JOIN `produtos` p ON p.id = s.plano_id WHERE s.tenant_id = ".(int)$contexto->tenant_id." ORDER BY s.id DESC LIMIT 1"
        );
        return $query && $query->num_rows() ? $query->row() : null;
    }

    public function tabela_log_existe()
    {
        return $this->db->table_exists($this->log_table);
    }

    private function filtrar_colunas($table, $data)
    {
        $filtered = [];
        foreach ($data as $column => $value) {
            if ($this->db->field_exists($column, $table)) {
                $filtered[$column] = $value;
            }
        }
        return $filtered;
    }

    private function tabela_possui_campos($table, $campos)
    {
        if (!$this->db->table_exists($table)) {
            return false;
        }

        foreach ($campos as $campo) {
            if (!$this->db->field_exists($campo, $table)) {
                return false;
            }
        }

        return true;
    }

    private function chatbot_json($valor)
    {
        if (is_string($valor)) {
            return $valor;
        }

        $json = json_encode($valor, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return $json === false ? '{}' : $json;
    }

    private function telefone_chatbot_sql($campo)
    {
        return "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE({$campo}, '+', ''), '(', ''), ')', ''), '-', ''), ' ', '')";
    }

    private function contexto_chatbot_autorizado($perfil, $id_usuario, $tenant_id)
    {
        $perfil = trim((string)$perfil);
        $id_usuario = (int)$id_usuario;
        if ($id_usuario <= 0 || !in_array($perfil, ['paciente', 'profissional', 'atendente', 'admin'], true)
            || !$this->tabela_possui_campos('usuarios', ['id', 'nivel'])) {
            return null;
        }

        $tenantSelect = $this->db->field_exists('tenant_id', 'usuarios') ? 'tenant_id' : '0 AS tenant_id';
        $query = $this->db->query("SELECT *, {$tenantSelect} FROM `usuarios` WHERE id = {$id_usuario} LIMIT 1");
        if (!$query || !$query->num_rows()) {
            return null;
        }

        $usuario = $query->row();
        if (utec_whatsapp_perfil_por_nivel($usuario->nivel) !== $perfil) {
            return null;
        }
        if ((int)$tenant_id > 0 && (int)$usuario->tenant_id > 0 && (int)$tenant_id !== (int)$usuario->tenant_id) {
            return null;
        }

        return $usuario;
    }

    private function where_agendamento_chatbot($usuario)
    {
        if ((int)$usuario->nivel === 1) {
            return '1 = 1';
        }

        if ($usuario->nivel == 5) {
            return 'a.id_paciente = '.(int)$usuario->id;
        }

        if (!in_array((int)$usuario->nivel, [2, 3, 4], true)) {
            return '';
        }

        $this->load->model('Padrao_model', 'padrao_model');
        $ids = $this->padrao_model->get_scope_user_ids($usuario);
        $idsSql = $this->padrao_model->ids_to_sql_in($ids);
        return "(a.id_user IN ({$idsSql}) OR a.id_paciente IN ({$idsSql}) OR a.id_prestador IN ({$idsSql}))";
    }
}
