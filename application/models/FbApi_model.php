<?
class FbApi_model extends CI_Model{
	
	
	function _construct()
	{
		// Call the Model constructor
		parent::_construct();
	}

	## API CONVERSAO FACEBOOK
	public function send_event($tipo_evento="Lead")
    {
        $pixel_id = "8069804829802981";
        $access_token = "EAAbqi2nysUIBO19TdXTLXKuJn5TnXh0ZAL8DwZAeWSkBFWwutu4UHfIygQPDsZBP1Vsmet8vEaM2G61tb6esvWdudumscMIY6LkVhBYzLjJfKPHE98exJh4DrWi2ZCA9lZCl5OXUQdn36O2DIIg8fy80Ca0YzcEdW5Swh7fZCvCrZB3u9uZBsuFtaJtIRZBGnunJZCUgZDZD";

        $user_email = "visitante_".$_SERVER['REMOTE_ADDR']."@chatbot-whatsapp-br.com.br"; // Por exemplo, de um sistema de login
        $client_ip = $ip = $_SERVER['REMOTE_ADDR'];
        $client_user_agent = $_SERVER['HTTP_USER_AGENT'];

        // Gerar o hash do e-mail no backend
        $hashed_email = hash('sha256', strtolower(trim($user_email)));

        $event_data = [
            "data" => [
                [
                    "event_name" => $tipo_evento,
                    "event_time" => time(),
                    "user_data" => [
                        "em" => $hashed_email,
                        "client_ip_address" => $client_ip,
                        "client_user_agent" => $client_user_agent,
                    ],
                    "custom_data" => [
                        "value" => 100.00,
                        "currency" => "USD",
                    ],
                ],
            ],
            "access_token" => $access_token,
        ];

        // Enviar para a API do Facebook
        $url = "https://graph.facebook.com/v17.0/{$pixel_id}/events";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($event_data));
        $response = curl_exec($ch);
        curl_close($ch);

        #echo $response;
        $dd_response = [
        	'id_user' => ($this->session->userdata('id')) ?? 0,
        	'tipo' => $tipo_evento,
        	'ip' => $client_ip,
        	'response' => $response
        ];

        $this->db->insert('api_conv_fb' , $dd_response);
    }


} // fecha class
?>
