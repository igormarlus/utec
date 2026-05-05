<?php
class Locations_model extends CI_Model {
    public function get_all_locations($id_mapa=1) {
        $this->db->where('id_mapa',$id_mapa);
        return $this->db->get('rpg_locations')->result_array();
    }

    public function get_location($id_mapa=1) {
        $this->db->where('id',$id_mapa);
        return $this->db->get('rpg_locations')->result_array();
    }

    public function get_last_location($id_personagem) {
        $this->db->where('id_personagem',$id_personagem);
        $this->db->order_by('id','desc');
        $this->db->limit(1);
        return $this->db->get('rpg_progress')->result_array();
    }

    
}
