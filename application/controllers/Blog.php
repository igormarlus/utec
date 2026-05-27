<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Blog extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('padrao_model');
        $this->load->model('adm/blog_model');
        $this->padrao_model->indexador();
    }

    public function index() {
        $dados['posts']      = $this->blog_model->get_posts(1);
        $dados['categorias'] = $this->blog_model->get_categorias();
        $this->load->view('public/blog/index', $dados);
    }

    public function post($slug) {
        $dados['post'] = $this->blog_model->get_post_by_slug($slug);
        if (!$dados['post']) show_404();
        $dados['recentes'] = $this->blog_model->get_recentes(4, $dados['post']->id);
        $this->load->view('public/blog/post', $dados);
    }
}
