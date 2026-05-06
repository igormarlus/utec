<?
class Padrao_model extends CI_Model{
	
	
	function _construct()
	{
		// Call the Model constructor
		parent::_construct();
	}

// pedar dados da tabela atraves do ID
// pedar dados da tabela atraves do ID
function get_by_id($id,$tabela){
	$this->db->where(array('id' => $id));
	$qr = $this->db->get($tabela);
	return $qr;
	}

function get_usuario_logado(){
	$id = (int)$this->session->userdata('id');
	if($id <= 0){
		return null;
	}
	$qr = $this->db->query("SELECT * FROM usuarios WHERE id = ".$id." LIMIT 1");
	return $qr->num_rows() ? $qr->row() : null;
}

function get_scope_user_ids($usuario=null){
	if(!$usuario){
		$usuario = $this->get_usuario_logado();
	}
	if(!$usuario){
		return [];
	}

	$nivel = (int)$usuario->nivel;
	if($nivel === 1){
		return [];
	}

	if($nivel === 2 || $nivel === 3){
		return $this->expand_user_tree_ids([$usuario->id]);
	}

	if($nivel === 4){
		$parent_id = (int)$usuario->id_user;
		if($parent_id <= 0){
			return $this->expand_user_tree_ids([$usuario->id]);
		}
		$peers = [$usuario->id];
		$qr_peers = $this->db->query("SELECT id FROM usuarios WHERE nivel = 4 AND id_user = ".$parent_id);
		foreach($qr_peers->result() as $peer){
			$peers[] = (int)$peer->id;
		}
		return $this->expand_user_tree_ids($peers);
	}

	return [$usuario->id];
}

function expand_user_tree_ids($root_ids=array()){
	$pending = [];
	foreach($root_ids as $root_id){
		$root_id = (int)$root_id;
		if($root_id > 0){
			$pending[] = $root_id;
		}
	}

	$seen = [];
	while(!empty($pending)){
		$current = array_shift($pending);
		if(isset($seen[$current])){
			continue;
		}
		$seen[$current] = $current;

		$qr_children = $this->db->query("SELECT id FROM usuarios WHERE id_user = ".$current);
		foreach($qr_children->result() as $child){
			$child_id = (int)$child->id;
			if($child_id > 0 && !isset($seen[$child_id])){
				$pending[] = $child_id;
			}
		}
	}

	return array_values($seen);
}

function ids_to_sql_in($ids=array()){
	$ids = array_values(array_unique(array_map('intval', $ids)));
	$ids = array_filter($ids, function($id){ return $id > 0; });
	return count($ids) ? implode(',', $ids) : '0';
}

function can_access_usuario($target_user_id,$usuario=null){
	$target_user_id = (int)$target_user_id;
	if($target_user_id <= 0){
		return false;
	}
	if(!$usuario){
		$usuario = $this->get_usuario_logado();
	}
	if(!$usuario){
		return false;
	}
	if((int)$usuario->nivel === 1){
		return true;
	}
	$scope_ids = $this->get_scope_user_ids($usuario);
	return in_array($target_user_id, $scope_ids);
}

function can_access_agendamento($agendamento_id,$usuario=null){
	$agendamento_id = (int)$agendamento_id;
	if($agendamento_id <= 0){
		return false;
	}
	if(!$usuario){
		$usuario = $this->get_usuario_logado();
	}
	if(!$usuario){
		return false;
	}
	if((int)$usuario->nivel === 1){
		return true;
	}
	$scope_ids = $this->get_scope_user_ids($usuario);
	$scope_sql = $this->ids_to_sql_in($scope_ids);
	$qr = $this->db->query(
		"SELECT id FROM agendamentos 
		WHERE id = ".$agendamento_id." 
		  AND (id_user IN (".$scope_sql.") OR id_paciente IN (".$scope_sql.") OR id_prestador IN (".$scope_sql."))
		LIMIT 1"
	);
	return $qr->num_rows() > 0;
}

function get_visible_prestador_ids($usuario=null){
	if(!$usuario){
		$usuario = $this->get_usuario_logado();
	}
	if(!$usuario){
		return [];
	}
	$nivel = (int)$usuario->nivel;
	if($nivel === 1){
		return [];
	}
	if($nivel === 3){
		return [(int)$usuario->id];
	}
	if($nivel === 2){
		$scope_ids = $this->get_scope_user_ids($usuario);
		$scope_sql = $this->ids_to_sql_in($scope_ids);
		$ids = [];
		$qr = $this->db->query("SELECT id FROM usuarios WHERE nivel = 3 AND id IN (".$scope_sql.")");
		foreach($qr->result() as $row){
			$ids[] = (int)$row->id;
		}
		return $ids;
	}
	if($nivel === 4){
		$parent_id = (int)$usuario->id_user;
		if($parent_id <= 0){
			return [];
		}
		$parent = $this->get_by_id($parent_id, 'usuarios');
		if($parent->num_rows()){
			$parent_row = $parent->row();
			if((int)$parent_row->nivel === 3){
				return [$parent_id];
			}
		}
		$ids = [];
		$qr = $this->db->query("SELECT id FROM usuarios WHERE nivel = 3 AND id_user = ".$parent_id);
		foreach($qr->result() as $row){
			$ids[] = (int)$row->id;
		}
		return $ids;
	}
	return [];
}

function get_by_martix($id_matrix,$id,$tabela,$campo="id",$ord='desc'){
	$this->db->where(array($id_matrix => $id));
	$this->db->order_by($campo,$ord);
	$qr = $this->db->get($tabela);
	return $qr;
	}
	

function get_by_matriz($id_matriz,$id,$tabela,$limit=999,$inicio=0, $campo="id",$ord='desc'){
	$this->db->where(array($id_matriz => $id));
	$this->db->order_by($campo,$ord);
	$this->db->limit($limit,$inicio);
	$qr = $this->db->get($tabela);
	return $qr;
	}

// pegar dados de alguma tabela
function get_qr($tabela,$ord='desc',$campo='id',$limit=999,$inicio=0){
	$this->db->order_by($campo,$ord);
	$this->db->limit($limit,$inicio);
	$qr = $this->db->get($tabela);
	
	return $qr;
	}

// remover dados da tabela atraves do ID
function del_by_id($id,$tabela){
	$this->db->where(array('id' => $id));
	$this->db->delete($tabela);
	}

// fn para tornar url amigavel, paramentro caracter é o que será trocado
function url_amigavel($string,$caracater="-"){
	$url = preg_replace('/[ "\'<>?!()]/', $caracater, $string);
	$url = preg_replace('/-{2,}/', $caracater, $url);
	return $url;
	}


function mes($mes){	
		switch($mes){
			case '1':
			echo "Janeiro";
			break;
			
			case '2':
			echo "Fevereiro";
			break;
			
			case '3':
			echo "Março";
			break;
			
			case '4':
			echo "Abril";
			break;
			
			case '5':
			echo "Maio";
			break;
			
			case '6':
			echo "Junho";
			break;
			
			case '7':
			echo "Julho";
			break;
			
			case '8':
			echo "Agosto";
			break;
			
			case '9':
			echo "Setembro";
			break;
			
			case '10':
			echo "Outrubro";
			break;
			
			case '11':
			echo "Novembro";
			break;
			
			case '12':
			echo "Dezembro";
			break;
		}
											
}

############# INDEXADOR
function indexador(){
		$this->load->library('user_agent');
		
		//$this->agent->is_robot()
		if($this->uri->total_segments() > 3){
			if($this->uri->segment(2) == 'get_odds_only' || $this->uri->segment(2) == 'get_odds_graph' || $this->uri->segment(2) == 'get_odds_sel' || $this->uri->segment(2) == "get_percentual_selecions_light" || $this->uri->segment(2) == "get_odds_api" ){
				return false;
			}	
		}
		if ($this->agent->is_robot()){

			$ip = $this->agent->robot();
	
		}else{
			$ip = $_SERVER['REMOTE_ADDR'];
			$pagina = $_SERVER['SCRIPT_NAME'];

			$ip_pre = substr($ip,0,9);
			if($ip_pre == "66.249.66" ){
				$ip = "bot";
			}

		}

		
		
#		if ($this->agent->is_referral()){

			$refer	= $this->agent->referrer();
			
	
#		}else{
#			$refer = "-";
			#}
		
		$seg = $this->uri->total_segments();
		$pagina = "";
		for($p=0; $p<$seg+1; $p++){
			$pagina .= $this->uri->segment($p)."/";
		}
		
		## verifica se tem POST
		if(!empty($_POST['tipo'])){
			$post = $_POST['tipo'];
		}else{
			$post = "";
		}
		
		## verifica se está logado
		if($this->session->userdata('id')){
			$id_user = $this->session->userdata('id');
			}else{
				$id_user = 0;
				}
		
		
		//mobile
		if($this->agent->is_mobile()){
			$mobile = $this->agent->mobile();
		}else{
			$mobile = 'PC';
		}
		
		
		
		
		
		$dados = array(
		'ip' => $ip,
		'id_user' => $id_user,
		'refer' => $refer,
		'pagina' => $pagina,
		'dispositivo' => $mobile,
		'post' => $post,
		'navegador' => $this->agent->browser()." ".$this->agent->version(),
		'sistema_operacional' => $this->agent->platform()
		);
		if($this->uri->segment(2) == "get_percentual_selecions" ){
			$dados['creditos'] = 1;
		}
		$this->db->insert('acessos' , $dados);
	
	} // x indexador


function get_ips(){
	$qr = $this->db->query("SELECT DISTINCT ip FROM acessos order by dt desc limit 100");
	return $qr;
	}

function get_pags_id($ip){
	$this->db->distinct('refer');
	$this->db->where(array('ip' => $ip));
	$this->db->order_by('dt','asc');
	$qr = $this->db->get('acessos');
	return $qr;
	}

function tempo($dtstring,$time){
	$this->load->helper('date');
	$dtstring = human_to_unix($dtstring);
	$tp = timespan($dtstring,$time);
	$tp_br = str_replace("Days","Dias",$tp);
	return timespan($dtstring,$time);	
	}



################## FUNCOES DE DATA #########################
function converte_data($data){
	if (strstr($data, "/")){//verifica se tem a barra /
	  $d = explode ("/", $data);//tira a barra
	  $invert_data = "$d[2]-$d[1]-$d[0]";//separa as datas $d[2] = ano $d[1] = mes etc...
	  return $invert_data;
	}
	elseif(strstr($data, "-")){
	  $d = explode ("-", $data);
	  $invert_data = "$d[2]/$d[1]/$d[0]";
	  return $invert_data;
	}
		else{
	  	return "Data invalida"; 
	}
}/////////////

function fuso_dt($data,$fusohorario=3,$invert="0",$minsoma=0){
	//$fusohorario     = 3; // como o servidor de hospedagem é a dreamhost pego o fuso para o horario do brasil
	#$hora = substr($data,10,2);
	if($invert=='i'){
		$mes = substr($data,8,2);
		$dia = substr($data,5,2);
		#echo "i: M".$mes." D".$dia;
	}else{
		$dia = substr($data,8,2);
		$mes = substr($data,5,2);
		#echo "sem i: M".$mes." D".$dia;
	}
	$ano = substr($data,0,4);
	$hora = substr($data,11,2);
	if($minsoma == 0){
		$min = substr($data,16,2);
		if($min == ":0"){
			$min = "00";
		}
	}else{
		$min = $min-$minsoma;
		if($min == ":0"){
			$min = "00";
		}
	}
	$sec = substr($data,16,2);

	if($sec == ":0"){
			$sec = "00";
		}




	#$timestamp       = mktime(date("H") - $fusohorario, date("i"), date("s"), date("m"), date("d"), date("Y"));
	$timestamp       = mktime($hora + $fusohorario, $min, $sec, $mes, $dia, $ano);
	$data_hora  = gmdate("d/m/Y H:i:s", $timestamp);
	#echo $data_hora."(".$hora.")";
	#echo $data_hora."(".$hora.")";
	#echo $data_hora."<br>Dia:".$dia."(Hora:".$hora.")"."<br>Mes:".$mes."(min:".$min.")"."<br>ano:".$ano."(seg:".$sec.")"."<br>";

	#echo $data_hora;
	#echo "<br>";
	echo $data;
}


} // fecha class


?>
