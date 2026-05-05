<?php
class Itens_model extends CI_Model {

    public function userItem($id_item,$id_personagem) {
        #echo $id_item;
        #echo "<br>";
        #echo $id_personagem;

        $attr_personagem = $this->padrao_model->get_by_matriz('id_personagem',$id_personagem,'rpg_personagens_atributos')->row();
        $life_now = $attr_personagem->life;
        $life_total = $attr_personagem->life_total;

        // LIFE +20
        if($id_item == 3){
            $new_life = $life_now + 20;
            if($new_life > $life_total){
                $new_life = $life_total;
            }
            $up_attr = [
                'life' => $new_life
            ];
            $this->db->where('id_personagem',$id_personagem);
            $this->db->update('rpg_personagens_atributos' , $up_attr);
        } // x item 3

        // remove item
        $this->db->where('id_personagem',$id_personagem);
        $this->db->where('item_id',$id_item);
        $this->db->limit(1);
        $this->db->delete('rpg_user_inventory');



        $response = [
            'item_id' => $id_item,
            'id_personagem' => $id_personagem,
        ];
        echo json_encode($response);
        #$this->db->where('id_mapa',$id_mapa);
        #return $this->db->get('rpg_locations')->result_array();
    }


    
}
