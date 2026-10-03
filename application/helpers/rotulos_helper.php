<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Funções puras dos rótulos de pacientes (sem CI, sem banco) — testes em tests/rotulos_*.

if(!function_exists('utec_rotulos_paleta')){
	function utec_rotulos_paleta(){
		return array(
			'azul' => '#2563eb', 'verde' => '#16a34a', 'ambar' => '#d97706', 'vermelho' => '#dc2626',
			'roxo' => '#7c3aed', 'rosa' => '#db2777', 'cinza' => '#64748b', 'teal' => '#0d9488',
		);
	}
}

if(!function_exists('utec_rotulos_cor_valida')){
	function utec_rotulos_cor_valida($cor){
		$paleta = utec_rotulos_paleta();
		$cor = (string)$cor;
		return isset($paleta[$cor]) ? $cor : 'cinza';
	}
}

if(!function_exists('utec_rotulos_normalizar_nome')){
	function utec_rotulos_normalizar_nome($nome){
		$n = preg_replace('/\s+/u', ' ', (string)$nome);
		$n = trim((string)$n);
		if(function_exists('mb_substr')){
			$n = mb_substr($n, 0, 40, 'UTF-8');
		}else{
			$n = substr($n, 0, 40);
		}
		return trim($n);
	}
}

if(!function_exists('utec_rotulos_resolver_raiz')){
	// Sobe pela árvore id_user até o estabelecimento (nível 2) ou até não haver pai válido (2, 3 ou 4).
	function utec_rotulos_resolver_raiz($id, callable $buscar){
		$atual = call_user_func($buscar, (int)$id);
		if(!$atual || (int)$atual['nivel'] === 1){ return 0; }
		$raiz = (int)$atual['id'];
		$visitados = array($raiz => true);
		for($i = 0; $i < 10 && (int)$atual['nivel'] !== 2; $i++){
			$pai_id = (int)$atual['id_user'];
			if($pai_id <= 0 || isset($visitados[$pai_id])){ break; }
			$pai = call_user_func($buscar, $pai_id);
			if(!$pai || !in_array((int)$pai['nivel'], array(2, 3, 4), true)){ break; }
			$atual = $pai;
			$raiz = (int)$pai['id'];
			$visitados[$raiz] = true;
		}
		return $raiz;
	}
}

if(!function_exists('utec_rotulos_sugestoes')){
	function utec_rotulos_sugestoes(){
		return array(
			array('VIP', 'roxo', 0),
			array('Retorno pendente', 'ambar', 0),
			array('Convênio', 'azul', 0),
			array('Gestante', 'rosa', 1),
			array('Alérgico', 'vermelho', 1),
		);
	}
}

if(!function_exists('utec_rotulos_ids_validos')){
	function utec_rotulos_ids_validos(array $ids_post, array $ids_catalogo){
		$permitidos = array();
		foreach($ids_catalogo as $c){ $permitidos[(int)$c] = true; }
		$saida = array();
		foreach($ids_post as $v){
			if(!is_scalar($v) || !preg_match('/^\d+$/', (string)$v)){ continue; }
			$id = (int)$v;
			if($id > 0 && isset($permitidos[$id]) && !in_array($id, $saida, true)){
				$saida[] = $id;
			}
		}
		return $saida;
	}
}

if(!function_exists('utec_rotulos_chips_html')){
	function utec_rotulos_chips_html($rotulos, $so_alertas = false){
		$paleta = utec_rotulos_paleta();
		$html = '';
		foreach((array)$rotulos as $r){
			$r = (array)$r;
			$alerta = !empty($r['alerta']) && (int)$r['alerta'] === 1;
			if($so_alertas && !$alerta){ continue; }
			$hex = $paleta[utec_rotulos_cor_valida(isset($r['cor']) ? $r['cor'] : '')];
			$nome = htmlspecialchars(isset($r['nome']) ? (string)$r['nome'] : '', ENT_QUOTES, 'UTF-8');
			$classe = 'ut-rotulo'.($alerta ? ' ut-rotulo-alerta' : '');
			$html .= '<span class="'.$classe.'" style="border-color:'.$hex.';color:'.$hex.';">'.($alerta ? '&#9888; ' : '').$nome.'</span>';
		}
		return $html;
	}
}

if(!function_exists('utec_rotulos_data_attr')){
	function utec_rotulos_data_attr($rotulos){
		$ids = array();
		foreach((array)$rotulos as $r){
			$r = (array)$r;
			if(isset($r['id'])){ $ids[] = (int)$r['id']; }
		}
		return ','.(empty($ids) ? '' : implode(',', $ids).',');
	}
}
