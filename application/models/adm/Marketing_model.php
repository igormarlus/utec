<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Marketing_model extends CI_Model {

	public function __construct(){
		parent::__construct();
		$this->load->model('padrao_model');
	}

	private function normaliza_filtros($f){
		if(!is_array($f)){ $f = array(); }
		$hoje = date('Y-m-d');
		$ini  = isset($f['start_date']) ? (string)$f['start_date'] : '';
		$fim  = isset($f['end_date'])   ? (string)$f['end_date']   : '';

		$d1 = DateTime::createFromFormat('Y-m-d', $ini);
		$d2 = DateTime::createFromFormat('Y-m-d', $fim);
		if(!$d1 || $d1->format('Y-m-d') !== $ini){ $ini = date('Y-m-d', strtotime('-29 days')); }
		if(!$d2 || $d2->format('Y-m-d') !== $fim){ $fim = $hoje; }
		if($ini > $fim){ $tmp = $ini; $ini = $fim; $fim = $tmp; }

		return array(
			'start'        => $ini.' 00:00:00',
			'end'          => $fim.' 23:59:59',
			'start_date'   => $ini,
			'end_date'     => $fim,
			'source'       => isset($f['source']) ? preg_replace('/[^a-z0-9_\-]/i', '', (string)$f['source']) : '',
			'landing_page' => isset($f['landing_page']) ? (string)$f['landing_page'] : '',
			'converted'    => (isset($f['converted']) && $f['converted'] !== '') ? (int)(bool)$f['converted'] : null,
		);
	}

	private function aplica_where($f){
		$this->db->where('r.created_at >=', $f['start']);
		$this->db->where('r.created_at <=', $f['end']);
		if($f['source'] !== ''){ $this->db->where('r.ai_source', $f['source']); }
		if($f['landing_page'] !== ''){ $this->db->like('r.landing_page', $f['landing_page']); }
		if($f['converted'] !== null){ $this->db->where('r.converted', $f['converted']); }
	}

	private function count_since($ts){
		$this->db->from('ai_referrals');
		$this->db->where('created_at >=', $ts);
		return $this->db->count_all_results();
	}

	public function get_summary($filtros){
		$f = $this->normaliza_filtros($filtros);

		$this->db->from('ai_referrals r');
		$this->aplica_where($f);
		$acessos = $this->db->count_all_results();

		$this->db->from('ai_referrals r');
		$this->aplica_where($f);
		$this->db->where('r.converted', 1);
		$conversoes = $this->db->count_all_results();

		$this->db->select('COALESCE(SUM(c.conversion_value),0) AS receita', false);
		$this->db->from('ai_conversions c');
		$this->db->where('c.created_at >=', $f['start']);
		$this->db->where('c.created_at <=', $f['end']);
		$this->db->where('c.conversion_type', 'assinatura');
		$this->db->where('c.conversion_value >', 0);
		if($f['source'] !== ''){ $this->db->where('c.ai_source', $f['source']); }
		$receita = (float)$this->db->get()->row()->receita;

		return array(
			'acessos_hoje'    => $this->count_since(date('Y-m-d').' 00:00:00'),
			'acessos_7d'      => $this->count_since(date('Y-m-d 00:00:00', strtotime('-6 days'))),
			'acessos_30d'     => $this->count_since(date('Y-m-d 00:00:00', strtotime('-29 days'))),
			'acessos_periodo' => $acessos,
			'conversoes'      => $conversoes,
			'taxa_conversao'  => $acessos > 0 ? round($conversoes / $acessos * 100, 2) : 0,
			'receita'         => $receita,
			'periodo'         => array($f['start_date'], $f['end_date']),
		);
	}

	public function get_by_source($filtros){
		$f = $this->normaliza_filtros($filtros);
		$this->db->select('r.ai_source, COUNT(*) AS total', false);
		$this->db->from('ai_referrals r');
		$this->aplica_where($f);
		$this->db->group_by('r.ai_source');
		$this->db->order_by('total', 'DESC');
		$rows = $this->db->get()->result();

		$mapa = array();
		foreach($rows as $row){ $mapa[$row->ai_source ? $row->ai_source : 'outros'] = (int)$row->total; }

		$saida = array();
		foreach($this->padrao_model->ai_sources_list() as $slug){
			$saida[] = array('source' => $slug, 'total' => isset($mapa[$slug]) ? $mapa[$slug] : 0);
		}
		return $saida;
	}

	public function get_landing_pages($filtros){
		$f = $this->normaliza_filtros($filtros);
		$this->db->select('r.landing_page, COUNT(*) AS acessos, SUM(r.converted) AS conversoes', false);
		$this->db->from('ai_referrals r');
		$this->aplica_where($f);
		$this->db->group_by('r.landing_page');
		$this->db->order_by('acessos', 'DESC');
		$this->db->limit(50);
		return $this->db->get()->result();
	}

	public function get_conversion_by_source($filtros){
		$f = $this->normaliza_filtros($filtros);
		$this->db->select("r.ai_source,
			COUNT(*) AS visitas,
			SUM(r.converted) AS conversoes,
			ROUND(SUM(r.converted) / COUNT(*) * 100, 2) AS taxa,
			COALESCE(SUM(r.conversion_value),0) AS receita", false);
		$this->db->from('ai_referrals r');
		$this->aplica_where($f);
		$this->db->group_by('r.ai_source');
		$this->db->order_by('visitas', 'DESC');
		return $this->db->get()->result();
	}

	public function get_timeline($filtros){
		$f = $this->normaliza_filtros($filtros);
		$this->db->select('DATE(r.created_at) AS dia, COUNT(*) AS acessos, SUM(r.converted) AS conversoes', false);
		$this->db->from('ai_referrals r');
		$this->aplica_where($f);
		$this->db->group_by('dia');
		$this->db->order_by('dia', 'ASC');
		$rows = $this->db->get()->result();

		$mapa = array();
		foreach($rows as $row){ $mapa[$row->dia] = $row; }

		$saida = array();
		$cursor = strtotime($f['start_date']);
		$limite = strtotime($f['end_date']);
		while($cursor <= $limite){
			$d = date('Y-m-d', $cursor);
			$saida[] = array(
				'dia'        => $d,
				'acessos'    => isset($mapa[$d]) ? (int)$mapa[$d]->acessos : 0,
				'conversoes' => isset($mapa[$d]) ? (int)$mapa[$d]->conversoes : 0,
			);
			$cursor = strtotime('+1 day', $cursor);
		}
		return $saida;
	}
}
