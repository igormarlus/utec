<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notificacoes_model extends CI_Model {

    private $table = 'notificacoes_usuarios';

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('whatsapp_agendamento');
    }

    public function criar_resposta_agendamento($contexto, $acao)
    {
        $tipo = utec_notificacoes_tipo_resposta_agendamento($acao);
        if ($tipo === '' || !$this->tabela_possui_campos([
            'tenant_id', 'id_usuario_destino', 'id_agendamento',
            'id_whatsapp_notificacao', 'tipo', 'titulo', 'mensagem',
            'url', 'lida', 'criado_em'
        ])) {
            return false;
        }

        $idAgendamento = (int)utec_whatsapp_read($contexto, 'id_agendamento', 0);
        $idWhatsappNotificacao = (int)utec_whatsapp_read($contexto, 'id_whatsapp_notificacao', 0);
        $idPaciente = (int)utec_whatsapp_read($contexto, 'id_paciente', 0);
        if ($idAgendamento <= 0 || $idWhatsappNotificacao <= 0 || $idPaciente <= 0) {
            return false;
        }

        $destinatarios = utec_notificacoes_destinatarios_agendamento(
            (int)utec_whatsapp_read($contexto, 'id_user', 0),
            (int)utec_whatsapp_read($contexto, 'id_prestador', 0)
        );
        if (empty($destinatarios)) {
            return true;
        }

        $titulo = strtolower(trim((string)$acao)) === 'confirmar'
            ? 'Confirmacao de agendamento'
            : 'Cancelamento de agendamento';
        $mensagem = utec_notificacoes_mensagem_resposta_agendamento(
            utec_whatsapp_read($contexto, 'paciente_nome', ''),
            $acao
        );
        $url = 'adm/usuarios/prontuario/' . $idPaciente . '/' . $idAgendamento;
        $tenantId = (int)utec_whatsapp_read($contexto, 'tenant_id', 0);

        foreach ($destinatarios as $idUsuario) {
            $sql = "INSERT IGNORE INTO `{$this->table}`\n"
                . "(tenant_id, id_usuario_destino, id_agendamento, id_whatsapp_notificacao, tipo, titulo, mensagem, url, lida, criado_em) VALUES ("
                . $tenantId . ', '
                . (int)$idUsuario . ', '
                . $idAgendamento . ', '
                . $idWhatsappNotificacao . ', '
                . $this->db->escape($tipo) . ', '
                . $this->db->escape($titulo) . ', '
                . $this->db->escape($mensagem) . ', '
                . $this->db->escape($url) . ", 0, '" . date('Y-m-d H:i:s') . "')";

            if ($this->db->query($sql) === false) {
                return false;
            }
        }

        return true;
    }

    public function listar_nao_lidas($id_usuario, $limite = 8)
    {
        $idUsuario = (int)$id_usuario;
        if ($idUsuario <= 0 || !$this->tabela_possui_campos(['id', 'id_usuario_destino', 'lida', 'criado_em'])) {
            return [];
        }

        $limite = max(1, min(30, (int)$limite));
        $query = $this->db->query(
            "SELECT * FROM `{$this->table}` WHERE id_usuario_destino = {$idUsuario} AND lida = 0 ORDER BY criado_em DESC LIMIT {$limite}"
        );

        return $query ? $query->result() : [];
    }

    public function contar_nao_lidas($id_usuario)
    {
        $idUsuario = (int)$id_usuario;
        if ($idUsuario <= 0 || !$this->tabela_possui_campos(['id', 'id_usuario_destino', 'lida'])) {
            return 0;
        }

        $query = $this->db->query(
            "SELECT COUNT(id) AS total FROM `{$this->table}` WHERE id_usuario_destino = {$idUsuario} AND lida = 0"
        );

        return $query && $query->num_rows() ? (int)$query->row()->total : 0;
    }

    public function abrir_para_usuario($id, $id_usuario)
    {
        $id = (int)$id;
        $idUsuario = (int)$id_usuario;
        if ($id <= 0 || $idUsuario <= 0 || !$this->tabela_possui_campos(['id', 'id_usuario_destino', 'lida', 'lida_em'])) {
            return null;
        }

        $query = $this->db->query(
            "SELECT * FROM `{$this->table}` WHERE id = {$id} AND id_usuario_destino = {$idUsuario} LIMIT 1"
        );
        if (!$query || !$query->num_rows()) {
            return null;
        }

        $notificacao = $query->row();
        if (!(int)$notificacao->lida) {
            $dados = ['lida' => 1, 'lida_em' => date('Y-m-d H:i:s')];
            $this->db->where('id', $id);
            $this->db->where('id_usuario_destino', $idUsuario);
            if (!$this->db->update($this->table, $dados)) {
                return null;
            }
            $notificacao->lida = 1;
            $notificacao->lida_em = $dados['lida_em'];
        }

        return $notificacao;
    }

    private function tabela_possui_campos($campos)
    {
        if (!$this->db->table_exists($this->table)) {
            return false;
        }

        foreach ($campos as $campo) {
            if (!$this->db->field_exists($campo, $this->table)) {
                return false;
            }
        }

        return true;
    }
}
