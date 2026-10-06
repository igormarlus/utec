<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Funções puras do tempo de atendimento (check-in, espera, atraso, duração) — testes em tests/tempo_atendimento_*.

if(!function_exists('utec_tempo_campos_zerados')){
	function utec_tempo_campos_zerados(){
		return array('chegada_em' => null, 'chegada_por' => null, 'inicio_atendimento_em' => null, 'fim_atendimento_em' => null);
	}
}

if(!function_exists('utec_tempo_campos_transicao')){
	function utec_tempo_campos_transicao($status_atual, $status_novo, $agora){
		$a = (int)$status_atual;
		$n = (int)$status_novo;
		if($a === 0 && $n === 1){ return array('inicio_atendimento_em' => $agora, 'fim_atendimento_em' => null); }
		if($a === 1 && $n === 2){ return array('fim_atendimento_em' => $agora); }
		if($a === 2 && $n === 0){ return array('inicio_atendimento_em' => null, 'fim_atendimento_em' => null); }
		if($a === 3 && $n === 0){ return utec_tempo_campos_zerados(); }
		return array();
	}
}

if(!function_exists('utec_tempo_ts')){
	function utec_tempo_ts($v){
		$v = trim((string)$v);
		if($v === '' || strpos($v, '0000-00-00') === 0){ return null; }
		$t = strtotime($v);
		return $t === false ? null : $t;
	}
}

if(!function_exists('utec_tempo_minutos_entre')){
	function utec_tempo_minutos_entre($de, $ate){
		$a = utec_tempo_ts($de);
		$b = utec_tempo_ts($ate);
		if($a === null || $b === null){ return null; }
		return (int)(($b - $a) / 60);
	}
}

if(!function_exists('utec_tempo_metricas')){
	function utec_tempo_metricas($ag){
		$ag = (array)$ag;
		$g = function($k) use ($ag){ return (array_key_exists($k, $ag) && $ag[$k] !== null) ? (string)$ag[$k] : ''; };
		$marcado = ($g('data_agenda') !== '' && $g('hora_agenda') !== '') ? substr($g('data_agenda'), 0, 10).' '.$g('hora_agenda') : '';
		$espera = utec_tempo_minutos_entre($g('chegada_em'), $g('inicio_atendimento_em'));
		if($espera !== null && ($espera < 0 || $espera > 480)){ $espera = null; }
		$atraso = utec_tempo_minutos_entre($marcado, $g('inicio_atendimento_em'));
		if($atraso !== null && abs($atraso) > 480){ $atraso = null; }
		$duracao = utec_tempo_minutos_entre($g('inicio_atendimento_em'), $g('fim_atendimento_em'));
		if($duracao !== null && ($duracao < 0 || $duracao > 480)){ $duracao = null; }
		return array('espera' => $espera, 'atraso' => $atraso, 'duracao' => $duracao);
	}
}

if(!function_exists('utec_tempo_formatar')){
	function utec_tempo_formatar($min){
		if($min === null || $min === ''){ return ''; }
		$m = (int)$min;
		$abs = abs($m);
		$txt = $abs >= 60 ? (int)floor($abs / 60).' h '.str_pad((string)($abs % 60), 2, '0', STR_PAD_LEFT).' min' : $abs.' min';
		return ($m < 0 ? '-' : '').$txt;
	}
}

if(!function_exists('utec_tempo_resumo_agenda')){
	function utec_tempo_resumo_agenda($ag){
		$arr = (array)$ag;
		$partes = array();
		$chegada = utec_tempo_ts(array_key_exists('chegada_em', $arr) ? $arr['chegada_em'] : '');
		if($chegada !== null){ $partes[] = 'Chegou '.date('H:i', $chegada); }
		$m = utec_tempo_metricas($arr);
		if($m['espera'] !== null){ $partes[] = 'esperou '.utec_tempo_formatar($m['espera']); }
		if($m['duracao'] !== null){ $partes[] = 'consulta '.utec_tempo_formatar($m['duracao']); }
		return implode(' · ', $partes);
	}
}

if(!function_exists('utec_tempo_pode_checkin')){
	function utec_tempo_pode_checkin($ag, $hoje){
		$ag = (array)$ag;
		if(!array_key_exists('status', $ag) || (int)$ag['status'] !== 0){ return false; }
		if(!array_key_exists('data_agenda', $ag) || substr((string)$ag['data_agenda'], 0, 10) !== (string)$hoje){ return false; }
		return utec_tempo_ts(array_key_exists('chegada_em', $ag) ? $ag['chegada_em'] : '') === null;
	}
}

if(!function_exists('utec_tempo_pode_desfazer_checkin')){
	function utec_tempo_pode_desfazer_checkin($ag){
		$ag = (array)$ag;
		$chegou = utec_tempo_ts(array_key_exists('chegada_em', $ag) ? $ag['chegada_em'] : '') !== null;
		$iniciou = utec_tempo_ts(array_key_exists('inicio_atendimento_em', $ag) ? $ag['inicio_atendimento_em'] : '') !== null;
		return $chegou && !$iniciou;
	}
}

if(!function_exists('utec_tempo_sql_agregados')){
	function utec_tempo_sql_agregados($alias){
		$a = preg_replace('/[^a-z_]/i', '', (string)$alias);
		if($a === ''){ $a = 'a'; }
		$esp = 'TIMESTAMPDIFF(MINUTE, '.$a.'.chegada_em, '.$a.'.inicio_atendimento_em)';
		$atr = "TIMESTAMPDIFF(MINUTE, CONCAT(".$a.".data_agenda, ' ', ".$a.".hora_agenda), ".$a.".inicio_atendimento_em)";
		$dur = 'TIMESTAMPDIFF(MINUTE, '.$a.'.inicio_atendimento_em, '.$a.'.fim_atendimento_em)';
		return 'AVG(CASE WHEN '.$esp.' BETWEEN 0 AND 480 THEN '.$esp.' END) AS espera_media, '
			.'COUNT(CASE WHEN '.$esp.' BETWEEN 0 AND 480 THEN 1 END) AS espera_n, '
			.'AVG(CASE WHEN '.$atr.' BETWEEN -480 AND 480 THEN '.$atr.' END) AS atraso_medio, '
			.'COUNT(CASE WHEN '.$atr.' BETWEEN -480 AND 480 THEN 1 END) AS atraso_n, '
			.'AVG(CASE WHEN '.$dur.' BETWEEN 0 AND 480 THEN '.$dur.' END) AS duracao_media, '
			.'COUNT(CASE WHEN '.$dur.' BETWEEN 0 AND 480 THEN 1 END) AS duracao_n';
	}
}
