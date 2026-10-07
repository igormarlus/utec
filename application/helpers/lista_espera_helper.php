<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Lista de espera: funções puras (sem banco). Testes em tests/lista_espera_helper_test.php.

if(!function_exists('utec_le_turnos')){
	function utec_le_turnos(){
		return array('' => 'Tanto faz', 'manha' => 'Manhã', 'tarde' => 'Tarde', 'noite' => 'Noite');
	}
}

if(!function_exists('utec_le_motivos_saida')){
	function utec_le_motivos_saida(){
		return array('desistiu' => 'Desistiu', 'conseguiu_horario' => 'Conseguiu horário', 'outro' => 'Outro');
	}
}

if(!function_exists('utec_le_dias_rotulos')){
	function utec_le_dias_rotulos(){
		return array(0 => 'Dom', 1 => 'Seg', 2 => 'Ter', 3 => 'Qua', 4 => 'Qui', 5 => 'Sex', 6 => 'Sáb');
	}
}

if(!function_exists('utec_le_turno_da_hora')){
	function utec_le_turno_da_hora($hora){
		if(!preg_match('/^(\d{2}):(\d{2})/', (string)$hora, $m)){ return ''; }
		$h = (int)$m[1];
		if($h > 23 || (int)$m[2] > 59){ return ''; }
		if($h < 12){ return 'manha'; }
		if($h < 18){ return 'tarde'; }
		return 'noite';
	}
}

if(!function_exists('utec_le_dias_normalizar')){
	function utec_le_dias_normalizar($dias){
		if(!is_array($dias)){
			$dias = ($dias === null || $dias === '') ? array() : explode(',', (string)$dias);
		}
		$ok = array();
		foreach($dias as $d){
			$d = trim((string)$d);
			if($d !== '' && ctype_digit($d) && (int)$d <= 6){ $ok[(int)$d] = (int)$d; }
		}
		ksort($ok);
		return implode(',', $ok);
	}
}

if(!function_exists('utec_le_data_valida')){
	function utec_le_data_valida($data){
		if(!preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', (string)$data, $m)){ return false; }
		return checkdate((int)$m[2], (int)$m[3], (int)$m[1]);
	}
}

if(!function_exists('utec_le_ler')){
	function utec_le_ler($origem, $chave, $padrao = null){
		$origem = (array)$origem;
		return array_key_exists($chave, $origem) ? $origem[$chave] : $padrao;
	}
}

if(!function_exists('utec_le_normalizar')){
	function utec_le_normalizar($post){
		$post = (array)$post;
		$erros = array();
		$id_paciente = (int)utec_le_ler($post, 'id_paciente', 0);
		if($id_paciente <= 0){ $erros[] = 'Selecione o paciente.'; }
		$id_prestador = (int)utec_le_ler($post, 'id_prestador', 0);
		$turno = (string)utec_le_ler($post, 'turno', '');
		if(!array_key_exists($turno, utec_le_turnos())){ $turno = ''; }
		$a_partir = trim((string)utec_le_ler($post, 'a_partir_de', ''));
		$obs = trim((string)preg_replace('/\s+/u', ' ', (string)utec_le_ler($post, 'observacao', '')));
		$obs = function_exists('mb_substr') ? mb_substr($obs, 0, 500, 'UTF-8') : substr($obs, 0, 500);
		return array(
			'dados' => array(
				'id_paciente' => $id_paciente,
				'id_prestador' => $id_prestador > 0 ? $id_prestador : null,
				'turno' => $turno,
				'dias_semana' => utec_le_dias_normalizar(utec_le_ler($post, 'dias_semana', array())),
				'a_partir_de' => utec_le_data_valida($a_partir) ? $a_partir : null,
				'observacao' => $obs !== '' ? $obs : null,
			),
			'erros' => $erros,
		);
	}
}

if(!function_exists('utec_le_compativel')){
	function utec_le_compativel($entrada, $vaga){
		$data = substr((string)utec_le_ler($vaga, 'data_agenda', ''), 0, 10);
		if(!utec_le_data_valida($data)){ return false; }
		$prest = (int)utec_le_ler($entrada, 'id_prestador', 0);
		if($prest > 0 && $prest !== (int)utec_le_ler($vaga, 'id_prestador', 0)){ return false; }
		$turno = (string)utec_le_ler($entrada, 'turno', '');
		if($turno !== '' && $turno !== utec_le_turno_da_hora(utec_le_ler($vaga, 'hora_agenda', ''))){ return false; }
		$dias = utec_le_dias_normalizar(utec_le_ler($entrada, 'dias_semana', ''));
		if($dias !== '' && !in_array(date('w', strtotime($data)), explode(',', $dias), true)){ return false; }
		$a_partir = substr((string)utec_le_ler($entrada, 'a_partir_de', ''), 0, 10);
		if(utec_le_data_valida($a_partir) && $a_partir > $data){ return false; }
		return true;
	}
}

if(!function_exists('utec_le_ordenar')){
	function utec_le_ordenar($entradas, $vaga){
		$lista = array();
		foreach($entradas as $e){
			$lista[] = array(
				'entrada' => $e,
				'compativel' => utec_le_compativel($e, $vaga),
				'criado_em' => (string)utec_le_ler($e, 'criado_em', ''),
				'id' => (int)utec_le_ler($e, 'id', 0),
			);
		}
		usort($lista, function($x, $y){
			if($x['compativel'] !== $y['compativel']){ return $x['compativel'] ? -1 : 1; }
			$c = strcmp($x['criado_em'], $y['criado_em']);
			return $c !== 0 ? $c : $x['id'] - $y['id'];
		});
		$saida = array();
		foreach($lista as $l){ $saida[] = array('entrada' => $l['entrada'], 'compativel' => $l['compativel']); }
		return $saida;
	}
}

if(!function_exists('utec_le_deve_avisar')){
	function utec_le_deve_avisar($vaga, $agora, $qtd_aguardando){
		$data = substr((string)utec_le_ler($vaga, 'data_agenda', ''), 0, 10);
		$hora = substr((string)utec_le_ler($vaga, 'hora_agenda', ''), 0, 5);
		if((int)$qtd_aguardando <= 0 || !utec_le_data_valida($data) || utec_le_turno_da_hora($hora) === ''){ return false; }
		return strtotime($data.' '.$hora.':00') > strtotime((string)$agora);
	}
}

if(!function_exists('utec_le_dias_espera')){
	function utec_le_dias_espera($criado_em, $hoje){
		$de = substr((string)$criado_em, 0, 10);
		$ate = substr((string)$hoje, 0, 10);
		if(!utec_le_data_valida($de) || !utec_le_data_valida($ate)){ return 0; }
		return max(0, (int)floor((strtotime($ate) - strtotime($de)) / 86400));
	}
}

if(!function_exists('utec_le_resumo_preferencias')){
	function utec_le_resumo_preferencias($entrada){
		$partes = array();
		$turnos = utec_le_turnos();
		$turno = (string)utec_le_ler($entrada, 'turno', '');
		if($turno !== '' && isset($turnos[$turno])){ $partes[] = $turnos[$turno]; }
		$dias = utec_le_dias_normalizar(utec_le_ler($entrada, 'dias_semana', ''));
		if($dias !== ''){
			$rot = utec_le_dias_rotulos();
			$nomes = array();
			foreach(explode(',', $dias) as $d){ $nomes[] = $rot[(int)$d]; }
			$partes[] = implode(', ', $nomes);
		}
		$a_partir = substr((string)utec_le_ler($entrada, 'a_partir_de', ''), 0, 10);
		if(utec_le_data_valida($a_partir)){ $partes[] = 'a partir de '.date('d/m', strtotime($a_partir)); }
		return $partes ? implode(' · ', $partes) : 'Sem preferência';
	}
}

if(!function_exists('utec_le_mensagem_aviso')){
	function utec_le_mensagem_aviso($prestador_nome, $data, $hora, $qtd){
		$quando = date('d/m', strtotime(substr((string)$data, 0, 10))).' às '.substr((string)$hora, 0, 5);
		$nome = trim((string)$prestador_nome);
		$qtd = (int)$qtd;
		$msg = 'Abriu vaga '.($nome !== '' ? 'com '.$nome.' ' : '').'em '.$quando.'. '
			.$qtd.' '.($qtd === 1 ? 'paciente' : 'pacientes').' na lista de espera.';
		return function_exists('mb_substr') ? mb_substr($msg, 0, 480, 'UTF-8') : substr($msg, 0, 480);
	}
}
