<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {

function __construct()
	{
		parent::__construct();
		$this->load->library('session');
		$this->load->helper(array('form','url'));
		$this->load->model('adm/usuarios_model');

		//$this->usuarios_model->verSession();

} // fecha fn USER

	function Index(){
		#echo "Opa 1";
		$this->load->view('adm/login');
	}

	
	
	function logar() {
		$this->usuarios_model->logar();
	}

	function logar_como($id_user){
		// somente admin (nivel 1) pode usar esta função
		$this->load->library('session');
		if($this->session->userdata('nivel') != 1){
			redirect('admin');
		}
		$id_user = (int)$id_user;
		$qr = $this->db->query("SELECT * FROM usuarios WHERE id = $id_user");
		if($qr->num_rows() == 0){ redirect('adm/usuarios'); }
		$dd = $qr->row();
		$this->session->set_userdata([
			'usr'   => true,
			'id'    => $dd->id,
			'nome'  => $dd->nome,
			'nivel' => $dd->nivel,
			'login' => $dd->login
		]);
		if($dd->nivel == 2 || $dd->nivel == 3 || $dd->nivel == 4){
			redirect('adm/atendimento');
		}
		redirect('adm/usuarios');
	}

	function esqueceuSenha(){
		$this->load->view('adm/esqueceu-senha');
	}
	
	
	function sitemap_publi(){
		#$id_categoria = "24";
		#$this->db->where('privacidade','0');
		// ultima atualizacao 14/08/2014 -> prox $inicio = 3510
		#$this->db->limit(5000, 2778);#limit,inicio
		$qr = $this->db->get("posts");
		echo "<textarea>";
		$a = 0;
		foreach($qr->result() as $dd){ $a++;
			echo "
			<url>
				<loc>http://www.frguia.com.br/artigos/de/".url_title($dd->titulo)."/".$dd->id."</loc>
				<lastmod>2015-02-06</lastmod>
				<changefreq>weekly</changefreq>
				<priority>0.8</priority>
			</url>";
		}
		echo "</textarea>";
	}	

#### MIGRAÇÃO DE TABELAS
	function set_campos_produtos(){
		#echo "Ops";
		#return false;
		$qr = $this->db->get('produtos');
		foreach($qr->result() as $dd){
			echo $dd->nome;
			$dd_new = array(
				'modelo' => $dd->nome,
				'especificacoes' => $dd->descricao
			);
			$this->db->where('id',$dd->id);
			$this->db->update('produtos' , $dd_new);
			echo "<br />";
		}
	}
	
	
}
