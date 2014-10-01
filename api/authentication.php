<?php
//
// FACTORY PATTERN
class Authentication {

	const API_OAUTH_URL = 'https://api.instagram.com/oauth/authorize';
	const API_OAUTH_TOKEN_URL = 'https://api.instagram.com/oauth/access_token';
	
	protected $client_info;
	protected $code;
	protected $params;
	protected $data;
	private $_scopes = array('basic', 'likes', 'comments', 'relationships');

	public function __construct(ClientInfo $client_info){
		$this->client_info = $client_info;
		isset($_GET['code'])? $this->receive() : $this->authorize();	
	}

	public function authorize(){
		$str_scopes = implode("+",$this->_scopes);
		$client_id = $this->client_info->get_client_id();
		$redirect_uri = $this->client_info->get_redirect_uri();
		$location = self::API_OAUTH_URL.'?client_id='.$client_id.'&redirect_uri='.$redirect_uri.'&scope='.$str_scopes.'&response_type=code';
		header("Location: ".$location."");
		exit;		
	}

	public function receive(){
		$this->code = $_GET['code'];
		$this->params = array(
			'client_id'		=> $this->client_info->get_client_id(),
			'client_secret'	=> $this->client_info->get_client_secret(),
			'grant_type'	=> 'authorization_code',
			'redirect_uri'	=> $this->client_info->get_redirect_uri(),
			'code'			=> $this->code
		);

		$this->request();
		$this->store_access_token();
	}


	public function request() {
		$apiHost = self::API_OAUTH_TOKEN_URL;
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, $apiHost);
		curl_setopt($ch, CURLOPT_POST, count($this->params));
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($this->params));
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		
		$this->data = curl_exec($ch);
		curl_close($ch);
	}

	private function store_access_token(){
		$decode = json_decode($this->data);

		$_SESSION['username'] = $decode->user->username;
		$_SESSION['bio'] = $decode->user->bio;
		$_SESSION['website'] = $decode->user->website;
		//$_SESSION['profile'] = $decode->user->profile;
		$_SESSION['full_name'] = $decode->user->full_name;
		$_SESSION['id'] = $decode->user->id;
	  	$_SESSION['access_token'] = $decode->access_token;
	  	
	  	header("Location: ".$this->client_info->get_website_url()."/index.php");
		exit;
	}
}

//
// Registry Pattern
abstract class AuthRegistry{
    
    protected static $stored = array();
	
	public static function get(){
		return self::$stored;
	}

    public static function set($key, $value){
        self::$stored[$key] = $value;
    }

    public static function get_by($key){
        return self::$stored[$key];
    }

    public static function is_set($key){
		return array_key_exists($key, self::$stored) ? "true" : "false";
	}
}
?>