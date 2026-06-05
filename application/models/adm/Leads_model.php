<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Leads_model extends CI_Model {

    public function get_leads($filtros = [], $limit = 50, $offset = 0) {
        $this->db->from('leads_capturados');

        if (!empty($filtros['status'])) {
            $this->db->where('status_abordagem', $filtros['status']);
        }
        if (!empty($filtros['especialidade'])) {
            $this->db->where('especialidade', $filtros['especialidade']);
        }
        if (!empty($filtros['cidade'])) {
            $this->db->where('cidade', $filtros['cidade']);
        }
        if (!empty($filtros['tipo'])) {
            $this->db->where('tipo', $filtros['tipo']);
        }
        if (!empty($filtros['busca'])) {
            $busca = $filtros['busca'];
            $this->db->group_start();
            $this->db->like('nome', $busca);
            $this->db->or_like('email', $busca);
            $this->db->or_like('telefone', $busca);
            $this->db->group_end();
        }

        $this->db->order_by('id', 'DESC');
        $this->db->limit($limit, $offset);
        return $this->db->get()->result();
    }

    public function count_leads($filtros = []) {
        $this->db->from('leads_capturados');
        if (!empty($filtros['status'])) {
            $this->db->where('status_abordagem', $filtros['status']);
        }
        if (!empty($filtros['especialidade'])) {
            $this->db->where('especialidade', $filtros['especialidade']);
        }
        if (!empty($filtros['cidade'])) {
            $this->db->where('cidade', $filtros['cidade']);
        }
        if (!empty($filtros['tipo'])) {
            $this->db->where('tipo', $filtros['tipo']);
        }
        if (!empty($filtros['busca'])) {
            $busca = $filtros['busca'];
            $this->db->group_start();
            $this->db->like('nome', $busca);
            $this->db->or_like('email', $busca);
            $this->db->or_like('telefone', $busca);
            $this->db->group_end();
        }
        return $this->db->count_all_results();
    }

    public function get_by_id($id) {
        return $this->db->get_where('leads_capturados', ['id' => (int)$id])->row();
    }

    public function atualizar_status($id, $status, $observacoes = '') {
        $this->db->where('id', (int)$id);
        return $this->db->update('leads_capturados', [
            'status_abordagem' => $status,
            'observacoes'      => $observacoes,
        ]);
    }

    public function get_stats() {
        $stats = [];

        // Total
        $stats['total'] = $this->db->count_all('leads_capturados');

        // Por status
        $q = $this->db->select('status_abordagem, COUNT(id) as qtd')
                       ->group_by('status_abordagem')
                       ->get('leads_capturados');
        $stats['por_status'] = $q->result();

        // Com telefone
        $this->db->where('telefone !=', '');
        $stats['com_telefone'] = $this->db->count_all_results('leads_capturados');

        // Com email
        $this->db->where('email !=', '');
        $stats['com_email'] = $this->db->count_all_results('leads_capturados');

        // Por especialidade (top 5)
        $stats['por_especialidade'] = $this->db
            ->select('especialidade, COUNT(id) as qtd')
            ->group_by('especialidade')
            ->order_by('qtd', 'DESC')
            ->limit(5)
            ->get('leads_capturados')->result();

        return $stats;
    }

    public function get_especialidades() {
        return $this->db->select("DISTINCT (especialidade)")->order_by('especialidade')->get('leads_capturados')->result();
    }

    public function get_cidades() {
        return $this->db->select('DISTINCT (cidade)')->order_by('cidade')->get('leads_capturados')->result();
    }
}
