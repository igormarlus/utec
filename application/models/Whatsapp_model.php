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
               AND status_envio = 'enviado'"
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
