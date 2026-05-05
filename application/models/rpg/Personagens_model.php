<?php
class Personagens_model extends CI_Model {
    public function get_dd($id_pers=1) {
        #$this->db->where('id',$id_pers);
        #return $this->db->get('rpg_personagens')->result_array();

        $qr = $this->db->query("SELECT p.nome, p.descricao, p.status, p.img, a.forca, a.destreza, a.defesa, a.life, a.mana, a.status, a.nivel, a.life_total, a.xp FROM rpg_personagens p
                INNER JOIN rpg_personagens_atributos a ON p.id = a.id_personagem
                WHERE p.id = ".$id_pers."
                ");
        return $qr->result_array();
    }
}
