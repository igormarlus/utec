<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Blog_model extends CI_Model {

    public function get_posts($publicado = null) {
        $this->db->select('p.*, c.nome as categoria_nome')
                 ->from('blog_posts p')
                 ->join('blog_categorias c', 'c.id = p.id_categoria', 'left')
                 ->order_by('p.criado_em', 'DESC');
        if ($publicado !== null) {
            $this->db->where('p.publicado', (int)$publicado);
        }
        return $this->db->get()->result();
    }

    public function get_post($id) {
        return $this->db->select('p.*, c.nome as categoria_nome, c.slug as categoria_slug')
                        ->from('blog_posts p')
                        ->join('blog_categorias c', 'c.id = p.id_categoria', 'left')
                        ->where('p.id', (int)$id)
                        ->get()->row();
    }

    public function get_post_by_slug($slug) {
        return $this->db->select('p.*, c.nome as categoria_nome, c.slug as categoria_slug')
                        ->from('blog_posts p')
                        ->join('blog_categorias c', 'c.id = p.id_categoria', 'left')
                        ->where('p.slug', $slug)
                        ->where('p.publicado', 1)
                        ->get()->row();
    }

    public function get_recentes($limite = 5, $excluir_id = null) {
        $this->db->where('publicado', 1)->order_by('publicado_em', 'DESC')->limit($limite);
        if ($excluir_id) $this->db->where('id !=', (int)$excluir_id);
        return $this->db->get('blog_posts')->result();
    }

    public function get_categorias() {
        return $this->db->order_by('nome', 'ASC')->get('blog_categorias')->result();
    }

    public function salvar($data) {
        $data['slug'] = $this->_slug_unico($data['slug'] ?: $data['titulo']);
        $data['criado_em'] = date('Y-m-d H:i:s');
        if (!empty($data['publicado'])) {
            $data['publicado_em'] = date('Y-m-d H:i:s');
        }
        $this->db->insert('blog_posts', $data);
        return $this->db->insert_id();
    }

    public function atualizar($id, $data) {
        $post = $this->get_post($id);
        if ($data['slug'] !== $post->slug) {
            $data['slug'] = $this->_slug_unico($data['slug'], $id);
        }
        if (!empty($data['publicado']) && empty($post->publicado_em)) {
            $data['publicado_em'] = date('Y-m-d H:i:s');
        }
        $this->db->where('id', (int)$id)->update('blog_posts', $data);
        return $this->db->affected_rows();
    }

    public function toggle_publicado($id) {
        $post = $this->get_post($id);
        $novo = $post->publicado ? 0 : 1;
        $upd  = ['publicado' => $novo];
        if ($novo === 1 && empty($post->publicado_em)) {
            $upd['publicado_em'] = date('Y-m-d H:i:s');
        }
        $this->db->where('id', (int)$id)->update('blog_posts', $upd);
        return $novo;
    }

    public function excluir($id) {
        $this->db->where('id', (int)$id)->delete('blog_posts');
        return $this->db->affected_rows();
    }

    private function _slug_unico($texto, $excluir_id = null) {
        $slug = strtolower(trim($texto));
        $slug = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $slug);
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        $slug = preg_replace('/[\s-]+/', '-', $slug);
        $slug = trim($slug, '-');
        $base = $slug;
        $i    = 1;
        while (true) {
            $this->db->where('slug', $slug);
            if ($excluir_id) $this->db->where('id !=', (int)$excluir_id);
            if ($this->db->count_all_results('blog_posts') === 0) break;
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }
}
