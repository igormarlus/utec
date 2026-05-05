<?php
class Armas_model extends CI_Model {
    public function get_all_armas($id_user=1,$id_personagem,$type="") {
        #$this->db->where('user_id',$id_user);
        #return $this->db->get('rpg_user_inventory')->result_array();
        $type = utf8_encode($type);
        $where_type = "";

        if($type != ""){
            $where_type = " AND i.type = '".$type."'";
        }

        if($type == "consumo"){
            $where_type = " AND i.type <> 'arma'";
        }

        #echo $where_type;
        #echo "<br><br>";

        $qr = $this->db->query("SELECT ui.id, ui.user_id, ui.item_id, i.icon, i.top, i.left, i.name, i.type FROM rpg_user_inventory as ui 
            INNER JOIN rpg_items as i ON ui.item_id = i.id
            WHERE ui.user_id = $id_user AND ui.id_personagem = $id_personagem $where_type");
        return $qr->result_array();
    }
}
