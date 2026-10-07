<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Aviso de vaga aberta para a lista de espera. Chamada após cancelamento/remarcação gravados.
// Nunca lança exceção nem bloqueia quem chamou: qualquer falha vira log [lista_espera].
class Lista_espera_vagas {

	protected $CI;

	public function __construct(){
		$this->CI =& get_instance();
	}

	public function vaga_do_agendamento($id_agendamento, $origem){
		try {
			$ag = $this->CI->db->query(
				'SELECT id, id_prestador, data_agenda, hora_agenda FROM agendamentos WHERE id = ? LIMIT 1',
				array((int)$id_agendamento)
			)->row();
			if(!$ag){ return false; }
			return $this->processar((int)$ag->id_prestador, (string)$ag->data_agenda, (string)$ag->hora_agenda, (string)$origem, (int)$ag->id);
		} catch (Throwable $e) {
			log_message('error', '[lista_espera] vaga_do_agendamento '.(int)$id_agendamento.': '.$e->getMessage());
			return false;
		}
	}

	public function vaga_aberta($id_prestador, $data, $hora, $origem, $id_agendamento_origem){
		try {
			return $this->processar((int)$id_prestador, (string)$data, (string)$hora, (string)$origem, (int)$id_agendamento_origem);
		} catch (Throwable $e) {
			log_message('error', '[lista_espera] vaga_aberta prestador='.(int)$id_prestador.': '.$e->getMessage());
			return false;
		}
	}

	protected function processar($id_prestador, $data, $hora, $origem, $id_agendamento_origem){
		$this->CI->load->model('Lista_espera_model', 'lista_espera_model');
		$m = $this->CI->lista_espera_model;
		if(!$m->disponivel() || $id_prestador <= 0){ return false; }
		$data = substr($data, 0, 10);
		$hora = substr($hora, 0, 5);
		$conta = $m->conta_raiz($id_prestador);
		if($conta <= 0){ return false; }
		$qtd = $m->contar_aguardando_para($conta, $id_prestador);
		if(!utec_le_deve_avisar(array('data_agenda' => $data, 'hora_agenda' => $hora), date('Y-m-d H:i:s'), $qtd)){ return false; }
		if($m->horario_ocupado($id_prestador, $data, $hora)){ return false; }
		$id_vaga = $m->registrar_vaga(array(
			'id_conta' => $conta, 'id_prestador' => $id_prestador, 'data_agenda' => $data, 'hora_agenda' => $hora,
			'origem' => $origem, 'id_agendamento_origem' => $id_agendamento_origem,
		));
		if($id_vaga <= 0){ return false; }
		$prest = $this->CI->db->query('SELECT * FROM usuarios WHERE id = ? LIMIT 1', array($id_prestador))->row();
		$nome = $prest ? (string)$prest->nome : '';
		$tenant = ($prest && isset($prest->tenant_id)) ? (int)$prest->tenant_id : 0;
		$this->CI->load->model('Notificacoes_model', 'notificacoes_model');
		$ok = $this->CI->notificacoes_model->criar_aviso_lista_espera(
			$id_vaga,
			$m->destinatarios_conta($conta, $id_prestador),
			'Abriu vaga — lista de espera',
			utec_le_mensagem_aviso($nome, $data, $hora, $qtd),
			'adm/lista_espera/vaga/'.$id_vaga,
			$id_agendamento_origem,
			$tenant
		);
		log_message('info', '[lista_espera] vaga '.$id_vaga.' origem='.$origem.' prestador='.$id_prestador.' fila='.$qtd.' aviso='.($ok ? 'ok' : 'falha'));
		return (bool)$ok;
	}
}
