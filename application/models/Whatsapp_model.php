<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Whatsapp_model extends CI_Model {

    private $table = 'whatsapp_config';
    private $log_table = 'whatsapp_notificacoes';

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

    public function registrar_limite_atingido($tenant_id, $id_agendamento, $telefone_destino, $mensagem)
    {
        return $this->registrar_log([
            'tenant_id' => (int)$tenant_id,
            'id_agendamento' => (int)$id_agendamento,
            'telefone_destino' => trim((string)$telefone_destino),
            'status_envio' => 'limite',
            'erro_detalhe' => trim((string)$mensagem),
            'status_confirmacao' => 'nao_enviado',
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
}
