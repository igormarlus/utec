<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class BlogAdmin extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('padrao_model');
        $this->load->model('adm/usuarios_model');
        $this->load->model('adm/blog_model');
        $this->usuarios_model->verSession();
        $usuario = $this->padrao_model->get_usuario_logado();
        if ((int)$usuario->nivel !== 1) {
            redirect('adm/atendimento');
        }
    }

    public function index() {
        $dados['posts']   = $this->blog_model->get_posts();
        $dados['dd_user'] = $this->padrao_model->get_usuario_logado();
        $this->load->view('adm/blog/lista', $dados);
    }

    public function novo() {
        $dados['post']       = null;
        $dados['categorias'] = $this->blog_model->get_categorias();
        $dados['dd_user']    = $this->padrao_model->get_usuario_logado();
        $dados['salvo']      = false;
        $this->load->view('adm/blog/form', $dados);
    }

    public function salvar() {
        $data = $this->_post_data();
        $id   = $this->blog_model->salvar($data);
        redirect('adm/blog/editar/' . $id . '?salvo=1');
    }

    public function editar($id) {
        $dados['post'] = $this->blog_model->get_post((int)$id);
        if (!$dados['post']) show_404();
        $dados['categorias'] = $this->blog_model->get_categorias();
        $dados['dd_user']    = $this->padrao_model->get_usuario_logado();
        $dados['salvo']      = $this->input->get('salvo');
        $this->load->view('adm/blog/form', $dados);
    }

    public function atualizar($id) {
        $data = $this->_post_data();
        $this->blog_model->atualizar((int)$id, $data);
        redirect('adm/blog/editar/' . $id . '?salvo=1');
    }

    public function publicar($id) {
        $this->blog_model->toggle_publicado((int)$id);
        redirect('adm/blog');
    }

    public function excluir($id) {
        $this->blog_model->excluir((int)$id);
        redirect('adm/blog');
    }

    private function _post_data() {
        return [
            'id_categoria'   => (int)$this->input->post('id_categoria') ?: null,
            'titulo'         => $this->input->post('titulo'),
            'slug'           => $this->input->post('slug'),
            'resumo'         => $this->input->post('resumo'),
            'conteudo'       => $this->input->post('conteudo', false),
            'meta_titulo'    => $this->input->post('meta_titulo')    ?: null,
            'meta_descricao' => $this->input->post('meta_descricao') ?: null,
            'imagem_capa'    => $this->input->post('imagem_capa')    ?: null,
            'autor'          => $this->input->post('autor')          ?: 'UTecnologia Saúde',
            'tempo_leitura'  => (int)$this->input->post('tempo_leitura') ?: 5,
            'publicado'      => $this->input->post('publicado') ? 1 : 0,
        ];
    }
}
