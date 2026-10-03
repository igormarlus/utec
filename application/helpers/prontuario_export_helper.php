<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Funções puras da exportação de prontuário (sem CI, sem banco) — testes em tests/prontuario_export_*.

if(!function_exists('utec_pront_rotulos')){
	function utec_pront_rotulos($esp_id){
		$lbl = array(
			'atendimento_inicial' => 'Atendimento Inicial',
			'avaliacao'           => 'Avaliação',
			'reavaliacao'         => 'Reavaliação',
			'ph_inicial'  => 'Descreva a queixa principal, contexto e primeiros registros.',
			'ph_avaliacao'=> 'Registre avaliação clínica, hipóteses e condutas adotadas.',
			'ph_reav'     => 'Registre evolução, retorno ou observações complementares.',
		);
		switch((int)$esp_id){
			case 10: // Fisioterapia
				$lbl['atendimento_inicial'] = 'Queixa / Avaliação Postural';
				$lbl['avaliacao']           = 'Evolução da Sessão / Técnicas Aplicadas';
				$lbl['reavaliacao']         = 'Resposta ao Tratamento / Próxima Sessão';
				$lbl['ph_inicial']   = 'Queixa principal, intensidade de dor, limitações funcionais e achados posturais.';
				$lbl['ph_avaliacao'] = 'Técnicas aplicadas (RPG, PNF, eletroterapia, hidroterapia...), exercícios realizados.';
				$lbl['ph_reav']      = 'Resposta do paciente, evolução do quadro, plano e objetivos para a próxima sessão.';
				break;
			case 36: // Psicologia
				$lbl['atendimento_inicial'] = 'Demanda Apresentada';
				$lbl['avaliacao']           = 'Evolução da Sessão';
				$lbl['reavaliacao']         = 'Observações / Encaminhamentos';
				$lbl['ph_inicial']   = 'Demanda e contexto trazidos pelo paciente nesta sessão.';
				$lbl['ph_avaliacao'] = 'Registro clínico da sessão e intervenções realizadas.';
				$lbl['ph_reav']      = 'Observações, encaminhamentos ou pontos para a próxima sessão.';
				break;
			case 28: // Odontologia
				$lbl['atendimento_inicial'] = 'Queixa / Motivo da Consulta';
				$lbl['avaliacao']           = 'Procedimento(s) Realizado(s)';
				$lbl['reavaliacao']         = 'Prescrição / Retorno';
				$lbl['ph_inicial']   = 'Queixa principal, dente(s) envolvido(s), histórico relevante.';
				$lbl['ph_avaliacao'] = 'Procedimento realizado, dente(s) — numeração FDI, anestesia e material utilizado.';
				$lbl['ph_reav']      = 'Medicação prescrita, orientações pós-operatórias, data de retorno.';
				break;
			case 37: // Psiquiatria
				$lbl['atendimento_inicial'] = 'Queixa Principal / Estado Mental';
				$lbl['avaliacao']           = 'Avaliação / Hipótese Diagnóstica';
				$lbl['reavaliacao']         = 'Conduta / Ajuste Terapêutico';
				$lbl['ph_inicial']   = 'Queixa principal, humor, sono, apetite, pensamento e comportamento.';
				$lbl['ph_avaliacao'] = 'Hipótese diagnóstica (CID), exame do estado mental, raciocínio clínico.';
				$lbl['ph_reav']      = 'Conduta adotada, ajuste de medicação, orientações, retorno.';
				break;
			case 27: // Nutrição
				$lbl['atendimento_inicial'] = 'Queixa / Anamnese Alimentar';
				$lbl['avaliacao']           = 'Avaliação Nutricional / Condutas';
				$lbl['reavaliacao']         = 'Evolução / Plano Alimentar';
				$lbl['ph_inicial']   = 'Queixa principal, hábitos alimentares, intolerâncias, histórico de saúde.';
				$lbl['ph_avaliacao'] = 'Avaliação antropométrica, diagnóstico nutricional, condutas adotadas.';
				$lbl['ph_reav']      = 'Evolução do quadro, ajustes no plano alimentar, metas para o próximo retorno.';
				break;
			case 33: // Pediatria
				$lbl['atendimento_inicial'] = 'Queixa / Dados do Responsável';
				$lbl['avaliacao']           = 'Exame Físico / Hipóteses';
				$lbl['reavaliacao']         = 'Conduta / Retorno';
				$lbl['ph_inicial']   = 'Queixa relatada pelo responsável, histórico de saúde e desenvolvimento da criança.';
				$lbl['ph_avaliacao'] = 'Exame físico, curva de crescimento, hipóteses diagnósticas.';
				$lbl['ph_reav']      = 'Conduta, prescrição, orientações ao responsável, data de retorno.';
				break;
			case 14: // Ginecologia e Obstetrícia
				$lbl['atendimento_inicial'] = 'Queixa / Anamnese Ginecológica';
				$lbl['avaliacao']           = 'Exame Físico / Hipóteses';
				$lbl['reavaliacao']         = 'Conduta / Retorno';
				$lbl['ph_inicial']   = 'Queixa principal, ciclo menstrual, DUM, histórico obstétrico.';
				$lbl['ph_avaliacao'] = 'Exame físico, hipóteses diagnósticas, exames solicitados.';
				$lbl['ph_reav']      = 'Conduta, prescrição, orientações, retorno.';
				break;
			case 11: // Fonoaudiologia
				$lbl['atendimento_inicial'] = 'Queixa / Avaliação Fonoaudiológica';
				$lbl['avaliacao']           = 'Evolução da Sessão / Técnicas';
				$lbl['reavaliacao']         = 'Resposta / Próxima Sessão';
				$lbl['ph_inicial']   = 'Queixa principal, histórico de linguagem, deglutição ou voz.';
				$lbl['ph_avaliacao'] = 'Técnicas aplicadas, exercícios realizados, progresso observado.';
				$lbl['ph_reav']      = 'Resposta do paciente, orientações, plano para próxima sessão.';
				break;
			case 3: // Cardiologia
				$lbl['atendimento_inicial'] = 'Queixa Cardiovascular';
				$lbl['avaliacao']           = 'Exame Físico / Hipóteses';
				$lbl['reavaliacao']         = 'Conduta / Ajuste Terapêutico';
				$lbl['ph_inicial']   = 'Queixa principal (dor torácica, dispneia, palpitações...), PA, FC.';
				$lbl['ph_avaliacao'] = 'Ausculta, hipóteses, ECG, exames solicitados.';
				$lbl['ph_reav']      = 'Conduta, ajuste de medicação, exames de retorno.';
				break;
			case 29: // Oftalmologia
				$lbl['atendimento_inicial'] = 'Queixa / Motivo da Consulta';
				$lbl['avaliacao']           = 'Exame Ocular / Achados';
				$lbl['reavaliacao']         = 'Conduta / Prescrição / Retorno';
				$lbl['ph_inicial']   = 'Queixa principal, tempo de evolução, antecedentes oculares e sistêmicos relevantes.';
				$lbl['ph_avaliacao'] = 'Acuidade visual, biomicroscopia, fundoscopia, PIO e demais achados.';
				$lbl['ph_reav']      = 'Conduta adotada, prescrição de óculos/lentes, medicação ocular, orientações e retorno.';
				break;
		}
		return $lbl;
	}
}

if(!function_exists('utec_pront_status_texto')){
	function utec_pront_status_texto($status){
		$mapa = array(1 => 'Em atendimento', 2 => 'Finalizado', 3 => 'Cancelado');
		$s = (int)$status;
		return isset($mapa[$s]) ? $mapa[$s] : 'Pendente';
	}
}

if(!function_exists('utec_pront_status_exame_texto')){
	function utec_pront_status_exame_texto($status){
		$mapa = array(1 => 'Solicitado', 2 => 'Entregue');
		$s = (int)$status;
		return isset($mapa[$s]) ? $mapa[$s] : 'Pendente';
	}
}

if(!function_exists('utec_pront_data_valida')){
	function utec_pront_data_valida($ymd){
		if(!preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', (string)$ymd, $m)){ return false; }
		return checkdate((int)$m[2], (int)$m[3], (int)$m[1]);
	}
}

if(!function_exists('utec_pront_data_br')){
	function utec_pront_data_br($ymd){
		$d = substr((string)$ymd, 0, 10);
		if(!utec_pront_data_valida($d)){ return ''; }
		return substr($d, 8, 2).'/'.substr($d, 5, 2).'/'.substr($d, 0, 4);
	}
}

if(!function_exists('utec_pront_normalizar_periodo')){
	function utec_pront_normalizar_periodo($de, $ate){
		$de  = utec_pront_data_valida($de) ? (string)$de : '';
		$ate = utec_pront_data_valida($ate) ? (string)$ate : '';
		if($de !== '' && $ate !== '' && $de > $ate){
			$tmp = $de; $de = $ate; $ate = $tmp;
		}
		return array($de, $ate);
	}
}

if(!function_exists('utec_pront_montar_extras')){
	function utec_pront_montar_extras($raw_json, array $labels_por_chave){
		if(!is_string($raw_json) || trim($raw_json) === ''){ return array(); }
		$dec = json_decode($raw_json, true);
		if(!is_array($dec)){ return array(); }
		$saida = array();
		foreach($dec as $chave => $valor){
			if(is_array($valor)){
				$valor = implode(', ', array_map('strval', $valor));
			}
			$valor = trim((string)$valor);
			if($valor === ''){ continue; }
			$rotulo = isset($labels_por_chave[$chave]) ? $labels_por_chave[$chave] : (string)$chave;
			$saida[] = array($rotulo, $valor);
		}
		return $saida;
	}
}

if(!function_exists('utec_pront_extras_texto')){
	function utec_pront_extras_texto(array $extras){
		$partes = array();
		foreach($extras as $par){
			$partes[] = $par[0].': '.$par[1];
		}
		return implode(' | ', $partes);
	}
}

if(!function_exists('utec_pront_linhas_tabulares')){
	function utec_pront_linhas_tabulares(array $dados){
		$atend = array(array('Data', 'Hora', 'Status', 'Profissional', 'Especialidade', 'Atendimento inicial', 'Avaliação', 'Reavaliação', 'Campos extras'));
		$lista = isset($dados['atendimentos']) ? $dados['atendimentos'] : array();
		foreach($lista as $a){
			$atend[] = array(
				utec_pront_data_br($a['data']),
				(string)$a['hora'],
				(string)$a['status_texto'],
				(string)$a['profissional'],
				(string)$a['especialidade'],
				(string)$a['atendimento_inicial'],
				(string)$a['avaliacao'],
				(string)$a['reavaliacao'],
				utec_pront_extras_texto(isset($a['extras']) ? $a['extras'] : array()),
			);
		}
		$exames = array(array('Data', 'Exame', 'Status', 'Profissional', 'Observação'));
		$lista_ex = isset($dados['exames']) ? $dados['exames'] : array();
		foreach($lista_ex as $e){
			$exames[] = array(
				utec_pront_data_br($e['data']),
				(string)$e['exame'],
				(string)$e['status_texto'],
				(string)$e['profissional'],
				(string)$e['obs'],
			);
		}
		return array('Atendimentos' => $atend, 'Exames' => $exames);
	}
}

if(!function_exists('utec_pront_celula_segura')){
	function utec_pront_celula_segura($v){
		$v = (string)$v;
		if($v !== '' && strpos("=+-@\t\r", $v[0]) !== false){
			return "'".$v;
		}
		return $v;
	}
}

if(!function_exists('utec_pront_csv')){
	function utec_pront_csv(array $abas){
		$blocos = array();
		foreach($abas as $linhas){
			$txt = '';
			foreach($linhas as $linha){
				$celulas = array();
				foreach($linha as $c){
					$c = utec_pront_celula_segura($c);
					if(strpbrk($c, ";\"\r\n") !== false){
						$c = '"'.str_replace('"', '""', $c).'"';
					}
					$celulas[] = $c;
				}
				$txt .= implode(';', $celulas)."\r\n";
			}
			$blocos[] = $txt;
		}
		return "\xEF\xBB\xBF".implode("\r\n", $blocos);
	}
}

if(!function_exists('utec_pront_nome_arquivo')){
	function utec_pront_nome_arquivo($id, $ext, $ymd){
		return 'prontuario-'.(int)$id.'-'.str_replace('-', '', substr((string)$ymd, 0, 10)).'.'.$ext;
	}
}
