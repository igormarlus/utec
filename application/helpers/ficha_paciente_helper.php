<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Funções puras da ficha do paciente (sem CI, sem banco) — testes em tests/ficha_paciente_*.

if(!function_exists('utec_ficha_opcoes_sexo')){
	function utec_ficha_opcoes_sexo(){
		return array('feminino' => 'Feminino', 'masculino' => 'Masculino', 'outro' => 'Outro', 'nao_informado' => 'Não informado');
	}
}

if(!function_exists('utec_ficha_opcoes_estado_civil')){
	function utec_ficha_opcoes_estado_civil(){
		return array(
			'solteiro' => 'Solteiro(a)', 'casado' => 'Casado(a)', 'uniao_estavel' => 'União estável',
			'divorciado' => 'Divorciado(a)', 'viuvo' => 'Viúvo(a)', 'nao_informado' => 'Não informado',
		);
	}
}

if(!function_exists('utec_ficha_opcoes_tipo_sanguineo')){
	function utec_ficha_opcoes_tipo_sanguineo(){
		$tipos = array('A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-');
		return array_combine($tipos, $tipos);
	}
}

if(!function_exists('utec_ficha_campos_saude')){
	function utec_ficha_campos_saude(){
		return array('tipo_sanguineo', 'alergias', 'medicamentos', 'comorbidades', 'obs_saude');
	}
}

if(!function_exists('utec_ficha_vazia')){
	function utec_ficha_vazia(){
		$chaves = array(
			'nome_social', 'sexo', 'estado_civil', 'responsavel_nome', 'responsavel_parentesco', 'responsavel_telefone',
			'responsavel_cpf', 'emergencia_nome', 'emergencia_parentesco', 'emergencia_telefone', 'tipo_sanguineo',
			'alergias', 'medicamentos', 'comorbidades', 'obs_saude', 'convenio_nome', 'convenio_plano',
			'convenio_carteirinha', 'convenio_validade',
		);
		return array_fill_keys($chaves, '');
	}
}

if(!function_exists('utec_ficha_cpf_valido')){
	function utec_ficha_cpf_valido($cpf){
		$d = preg_replace('/\D/', '', (string)$cpf);
		if(strlen($d) !== 11 || preg_match('/^(\d)\1{10}$/', $d)){ return false; }
		for($t = 9; $t < 11; $t++){
			$soma = 0;
			for($i = 0; $i < $t; $i++){ $soma += (int)$d[$i] * (($t + 1) - $i); }
			$dv = ((10 * $soma) % 11) % 10;
			if((int)$d[$t] !== $dv){ return false; }
		}
		return true;
	}
}

if(!function_exists('utec_ficha_texto_curto')){
	function utec_ficha_texto_curto($v, $max){
		$v = trim((string)preg_replace('/\s+/u', ' ', (string)$v));
		return function_exists('mb_substr') ? trim(mb_substr($v, 0, $max, 'UTF-8')) : trim(substr($v, 0, $max));
	}
}

if(!function_exists('utec_ficha_texto_longo')){
	function utec_ficha_texto_longo($v){
		$v = trim(str_replace(array("\r\n", "\r"), "\n", (string)$v));
		return function_exists('mb_substr') ? trim(mb_substr($v, 0, 2000, 'UTF-8')) : trim(substr($v, 0, 2000));
	}
}

if(!function_exists('utec_ficha_telefone')){
	function utec_ficha_telefone($v){
		$d = preg_replace('/\D/', '', (string)$v);
		return (strlen($d) === 10 || strlen($d) === 11) ? $d : '';
	}
}

if(!function_exists('utec_ficha_normalizar')){
	function utec_ficha_normalizar($post, $pode_editar_saude){
		$post = is_array($post) ? $post : array();
		$g = function($k) use ($post){ return (array_key_exists($k, $post) && is_scalar($post[$k])) ? (string)$post[$k] : ''; };
		$dados = array();
		$erros = array();

		$curtos = array(
			'nome_social' => 120, 'responsavel_nome' => 120, 'responsavel_parentesco' => 40,
			'emergencia_nome' => 120, 'emergencia_parentesco' => 40,
			'convenio_nome' => 80, 'convenio_plano' => 80, 'convenio_carteirinha' => 40,
		);
		foreach($curtos as $k => $max){ $dados[$k] = utec_ficha_texto_curto($g($k), $max); }
		$tel_msgs = array(
			'responsavel_telefone' => 'Telefone do responsável inválido.',
			'emergencia_telefone' => 'Telefone do contato de emergência inválido.',
		);
		foreach($tel_msgs as $k => $msg){
			$dados[$k] = utec_ficha_telefone($g($k));
			if($dados[$k] === '' && preg_match('/\d/', $g($k))){ $erros[] = $msg; }
		}

		$dados['sexo'] = array_key_exists($g('sexo'), utec_ficha_opcoes_sexo()) ? $g('sexo') : '';
		$dados['estado_civil'] = array_key_exists($g('estado_civil'), utec_ficha_opcoes_estado_civil()) ? $g('estado_civil') : '';

		$cpf = preg_replace('/\D/', '', $g('responsavel_cpf'));
		if($cpf === ''){
			$dados['responsavel_cpf'] = '';
		}elseif(utec_ficha_cpf_valido($cpf)){
			$dados['responsavel_cpf'] = $cpf;
		}else{
			$dados['responsavel_cpf'] = '';
			$erros[] = 'CPF do responsável inválido.';
		}

		$val = $g('convenio_validade');
		$dados['convenio_validade'] = (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/D', $val, $m) && checkdate((int)$m[2], (int)$m[3], (int)$m[1])) ? $val : '';

		if($pode_editar_saude){
			$dados['tipo_sanguineo'] = array_key_exists($g('tipo_sanguineo'), utec_ficha_opcoes_tipo_sanguineo()) ? $g('tipo_sanguineo') : '';
			foreach(array('alergias', 'medicamentos', 'comorbidades', 'obs_saude') as $k){
				$dados[$k] = utec_ficha_texto_longo($g($k));
			}
		}

		// ordem canônica das chaves
		$ordenado = array();
		foreach(array_keys(utec_ficha_vazia()) as $k){
			if(array_key_exists($k, $dados)){ $ordenado[$k] = $dados[$k]; }
		}
		return array('dados' => $ordenado, 'erros' => $erros);
	}
}

if(!function_exists('utec_ficha_saude_mudou')){
	function utec_ficha_saude_mudou(array $antes, array $depois){
		foreach(utec_ficha_campos_saude() as $k){
			if(!array_key_exists($k, $depois)){ continue; }
			$a = array_key_exists($k, $antes) ? (string)$antes[$k] : '';
			if($a !== (string)$depois[$k]){ return true; }
		}
		return false;
	}
}

if(!function_exists('utec_ficha_rotulo_opcao')){
	function utec_ficha_rotulo_opcao(array $opcoes, $valor){
		$valor = (string)$valor;
		return ($valor !== '' && array_key_exists($valor, $opcoes)) ? $opcoes[$valor] : 'Não informado';
	}
}

if(!function_exists('utec_ficha_telefone_fmt')){
	function utec_ficha_telefone_fmt($digitos){
		$d = preg_replace('/\D/', '', (string)$digitos);
		if(strlen($d) === 11){ return '('.substr($d, 0, 2).') '.substr($d, 2, 5).'-'.substr($d, 7); }
		if(strlen($d) === 10){ return '('.substr($d, 0, 2).') '.substr($d, 2, 4).'-'.substr($d, 6); }
		return '';
	}
}

if(!function_exists('utec_ficha_data_br')){
	function utec_ficha_data_br($ymd){
		$v = substr((string)$ymd, 0, 10);
		if(!preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $v, $m) || !checkdate((int)$m[2], (int)$m[3], (int)$m[1])){ return ''; }
		return $m[3].'/'.$m[2].'/'.$m[1];
	}
}
