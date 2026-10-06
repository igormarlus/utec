<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ficha extends CI_Controller {

	private $usuario;

	public function __construct()
	{
		parent::__construct();
		$this->load->library('session');
		$this->load->helper(array('form', 'url', 'ficha_paciente'));
		$this->load->model('adm/usuarios_model');
		$this->load->model('padrao_model');
		$this->load->model('Ficha_paciente_model', 'ficha_model');
		$this->usuarios_model->verSession();

		$this->usuario = $this->padrao_model->get_usuario_logado();
		$nivel = $this->usuario ? (int)$this->usuario->nivel : 0;
		if($nivel < 1 || $nivel > 4){
			show_error('Acesso restrito.', 403);
		}
	}

	private function pode_editar_saude()
	{
		return in_array((int)$this->usuario->nivel, array(1, 2, 3), true);
	}

	public function paciente($id_paciente = 0)
	{
		$id = (int)$id_paciente;
		if($id <= 1){ show_404(); return; }
		if(!$this->padrao_model->can_access_usuario($id)){
			show_error('Acesso negado ao paciente selecionado.', 403);
			return;
		}
		$alvo = $this->db->query("SELECT id, nome, nivel FROM usuarios WHERE id = ? LIMIT 1", array($id))->row();
		if(!$alvo || (int)$alvo->nivel !== 5){ show_404(); return; }

		$pode_saude = $this->pode_editar_saude();

		if($this->input->method() === 'post'){
			if(!$this->ficha_model->disponivel()){
				$this->session->set_flashdata('ficha_erro', 'As tabelas da ficha ainda não foram criadas.');
				redirect('adm/ficha/paciente/'.$id);
				return;
			}
			$r = utec_ficha_normalizar($this->input->post(), $pode_saude);
			if(!empty($r['erros'])){
				// Sem redirect e sem flash/sessao: dados de saude nao podem ir para arquivos de sessao.
				$ficha = array_merge($this->ficha_model->obter($id), $r['dados']);
				foreach(array('responsavel_cpf', 'responsavel_telefone', 'emergencia_telefone') as $k){
					if($ficha[$k] === ''){ $ficha[$k] = (string)$this->input->post($k); }
				}
				$this->load->view('adm/ficha/paciente', $this->dados_formulario($alvo, $ficha, implode(' ', $r['erros'])));
				return;
			}
			$antes = $this->ficha_model->obter($id);
			$mudou = $pode_saude && utec_ficha_saude_mudou($antes, $r['dados']);
			if($this->ficha_model->salvar($id, $r['dados'], (int)$this->usuario->id, $mudou)){
				$this->session->set_flashdata('ficha_ok', 'Ficha do paciente atualizada.');
				redirect('adm/atendimento/prontuario/'.$id);
			}else{
				$this->session->set_flashdata('ficha_erro', 'Não foi possível salvar a ficha.');
				redirect('adm/ficha/paciente/'.$id);
			}
			return;
		}

		$this->load->view('adm/ficha/paciente', $this->dados_formulario($alvo, $this->ficha_model->obter($id), $this->session->flashdata('ficha_erro')));
	}

	private function dados_formulario($alvo, $ficha, $flash_erro)
	{
		return array(
			'paciente' => $alvo,
			'ficha' => $ficha,
			'schema_ok' => $this->ficha_model->disponivel(),
			'pode_editar_saude' => $this->pode_editar_saude(),
			'opcoes_sexo' => utec_ficha_opcoes_sexo(),
			'opcoes_estado_civil' => utec_ficha_opcoes_estado_civil(),
			'opcoes_tipo_sanguineo' => utec_ficha_opcoes_tipo_sanguineo(),
			'flash_erro' => $flash_erro,
		);
	}
}
