<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Lista de espera da clínica: níveis 1–4, sempre dentro da própria conta (raiz da árvore).
class Lista_espera extends CI_Controller {

	private $usuario;

	public function __construct()
	{
		parent::__construct();
		$this->load->library('session');
		$this->load->helper(array('form', 'url', 'lista_espera'));
		$this->load->model('adm/usuarios_model');
		$this->load->model('padrao_model');
		$this->load->model('Lista_espera_model', 'lista_espera_model');
		$this->usuarios_model->verSession();

		$this->usuario = $this->padrao_model->get_usuario_logado();
		$nivel = $this->usuario ? (int)$this->usuario->nivel : 0;
		if($nivel < 1 || $nivel > 4){
			show_error('Acesso restrito.', 403);
		}
	}

	private function nivel()
	{
		return (int)$this->usuario->nivel;
	}

	// Conta do logado; para o admin (1), a conta de ?conta=<id de usuário da clínica>.
	private function conta_atual()
	{
		if($this->nivel() === 1){
			$ref = (int)$this->input->get('conta');
			if($ref <= 0){ $ref = (int)$this->input->post('conta'); }
			return $ref > 0 ? $this->lista_espera_model->conta_raiz($ref) : 0;
		}
		return $this->lista_espera_model->conta_raiz((int)$this->usuario->id);
	}

	private function pode_conta($conta)
	{
		return $conta > 0 && ($this->nivel() === 1 || $conta === $this->lista_espera_model->conta_raiz((int)$this->usuario->id));
	}

	private function voltar_url()
	{
		$v = (string)$this->input->post('voltar');
		return preg_match('#^adm/[a-z0-9_/?=&-]*$#i', $v) ? $v : 'adm/lista_espera';
	}

	private function prestadores()
	{
		if($this->nivel() === 1){
			return $this->db->query('SELECT id, nome FROM usuarios WHERE nivel = 3 ORDER BY nome ASC')->result();
		}
		$ids = $this->padrao_model->get_visible_prestador_ids($this->usuario);
		return $this->db->query('SELECT id, nome FROM usuarios WHERE nivel = 3 AND id IN ('.$this->padrao_model->ids_to_sql_in($ids).') ORDER BY nome ASC')->result();
	}

	private function paciente_valido($id_paciente)
	{
		$id_paciente = (int)$id_paciente;
		if($id_paciente <= 1 || !$this->padrao_model->can_access_usuario($id_paciente)){ return null; }
		$p = $this->db->query('SELECT id, nome, nivel FROM usuarios WHERE id = ? LIMIT 1', array($id_paciente))->row();
		return ($p && (int)$p->nivel === 5) ? $p : null;
	}

	public function index()
	{
		$aba = (string)$this->input->get('aba');
		if(!in_array($aba, array('aguardando', 'agendado', 'removido'), true)){ $aba = 'aguardando'; }
		$dados['schema_ok'] = $this->lista_espera_model->disponivel();
		$dados['eh_admin'] = $this->nivel() === 1;
		$dados['aba'] = $aba;
		$dados['paciente_pre'] = null;
		$dados['editar'] = null;
		$pre = (int)$this->input->get('paciente');
		if($pre > 0){ $dados['paciente_pre'] = $this->paciente_valido($pre); }
		$conta = $this->conta_atual();
		if($conta <= 0 && $dados['paciente_pre']){ $conta = $this->lista_espera_model->conta_raiz((int)$dados['paciente_pre']->id); }
		$dados['conta'] = $conta;
		$dados['itens'] = ($dados['schema_ok'] && $this->pode_conta($conta)) ? $this->lista_espera_model->listar($conta, $aba) : array();
		$ed = (int)$this->input->get('editar');
		if($ed > 0 && $dados['schema_ok']){
			$row = $this->lista_espera_model->buscar($ed);
			if($row && $row->status === 'aguardando' && $this->pode_conta((int)$row->id_conta) && $this->paciente_valido($row->id_paciente)){
				$dados['editar'] = $row;
			}
		}
		$dados['prestadores'] = $this->prestadores();
		$dados['flash_ok'] = $this->session->flashdata('le_ok');
		$dados['flash_erro'] = $this->session->flashdata('le_erro');
		$this->load->view('adm/lista_espera/index', $dados);
	}

	public function salvar()
	{
		if($this->input->method() !== 'post'){ show_404(); return; }
		if(!$this->lista_espera_model->disponivel()){ show_error('Lista de espera indisponível.', 503); return; }
		$id = (int)$this->input->post('id');
		$atual = $id > 0 ? $this->lista_espera_model->buscar($id) : null;
		$post = $this->input->post(NULL, true);
		if($atual){ $post['id_paciente'] = (int)$atual->id_paciente; }
		$n = utec_le_normalizar($post);
		if($n['erros']){
			$this->session->set_flashdata('le_erro', implode(' ', $n['erros']));
			redirect($this->voltar_url()); return;
		}
		$d = $n['dados'];
		if(!$this->paciente_valido($d['id_paciente'])){ show_error('Acesso negado ao paciente selecionado.', 403); return; }
		if($d['id_prestador'] !== null){
			$pr = $this->db->query('SELECT nivel FROM usuarios WHERE id = ? LIMIT 1', array($d['id_prestador']))->row();
			if(!$pr || (int)$pr->nivel !== 3 || !$this->padrao_model->can_access_usuario($d['id_prestador'])){
				$this->session->set_flashdata('le_erro', 'Profissional inválido.');
				redirect($this->voltar_url()); return;
			}
		}
		$conta = $this->lista_espera_model->conta_raiz($d['id_paciente']);
		if(!$this->pode_conta($conta)){ show_error('Acesso negado.', 403); return; }
		if($id > 0){
			if(!$atual || (int)$atual->id_conta !== $conta){ show_404(); return; }
			$r = $this->lista_espera_model->atualizar($id, $d, (int)$this->usuario->id);
		}else{
			$r = $this->lista_espera_model->adicionar($d, $conta, (int)$this->usuario->id);
		}
		$this->session->set_flashdata($r['ok'] ? 'le_ok' : 'le_erro', $r['ok'] ? 'Lista de espera atualizada.' : $r['erro']);
		redirect($this->voltar_url());
	}

	public function remover($id = 0)
	{
		if($this->input->method() !== 'post'){ show_404(); return; }
		$row = $this->lista_espera_model->buscar((int)$id);
		if(!$row){ show_404(); return; }
		if(!$this->pode_conta((int)$row->id_conta) || !$this->paciente_valido($row->id_paciente)){ show_error('Acesso negado.', 403); return; }
		$ok = $this->lista_espera_model->remover((int)$id, (string)$this->input->post('motivo'), (int)$this->usuario->id);
		$this->session->set_flashdata($ok ? 'le_ok' : 'le_erro', $ok ? 'Paciente removido da lista de espera.' : 'Escolha o motivo para remover.');
		redirect($this->voltar_url());
	}

	public function vaga($id_vaga = 0)
	{
		$vaga = $this->lista_espera_model->buscar_vaga((int)$id_vaga);
		if(!$vaga){ show_404(); return; }
		if(!$this->pode_conta((int)$vaga->id_conta)){ show_error('Acesso negado.', 403); return; }
		$dados['vaga'] = $vaga;
		$dados['ocupada'] = $this->lista_espera_model->horario_ocupado((int)$vaga->id_prestador, $vaga->data_agenda, $vaga->hora_agenda);
		$dados['passada'] = strtotime($vaga->data_agenda.' '.$vaga->hora_agenda.':00') <= time();
		$dados['itens'] = utec_le_ordenar(
			$this->lista_espera_model->aguardando_para((int)$vaga->id_conta, (int)$vaga->id_prestador),
			array('id_prestador' => (int)$vaga->id_prestador, 'data_agenda' => $vaga->data_agenda, 'hora_agenda' => $vaga->hora_agenda)
		);
		$this->load->view('adm/lista_espera/vaga', $dados);
	}
}
