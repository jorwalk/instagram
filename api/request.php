<?php
//=============================================================================
// Request -- Factory Pattern
//=============================================================================
class Request {

	const API_URL = 'https://api.instagram.com/v1/';
	const DEBUG = false;

	protected $function;
	protected $params;

	public function set_function($function){
		$this->function = $function;
		return $this;
	}

	public function get_function(){
		return $this->function;
	}

	public function set_params($params){
		$this->params = $params;
		return $this;
	}

	public function get_params(){
		return $this->params;
	}

	public function call($params = null) {

		if (isset($params) && is_array($params)) {
			$params = '&'.http_build_query($params);
		}

		// call for instagram
		$authMethod = '?access_token='.AuthRegistry::get_by('access_token');
		$apiCall = self::API_URL.$this->function.$authMethod.$params;

		// initial cURL
		$ch = curl_init();
		
		// set options
		curl_setopt($ch, CURLOPT_URL, $apiCall);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		
		// set options - SSL
		$this->_ssl_verify($ch);

		// set options - Debug
		if(self::DEBUG){ $this->_verbose_header($ch); }
		
		$data = curl_exec($ch);
		curl_close($ch);
		
		return json_decode($data);
	}


	public function call_post($params = null) {
		
		if (isset($params) && is_array($params)) {
			$params = '&'.http_build_query($params);
		} else {
			$params = null;
		}
		
		// call for instagram
		$authMethod = '?access_token='.AuthRegistry::get_by('access_token');
		$apiCall = self::API_URL.$this->function.$authMethod;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $apiCall);
		curl_setopt($ch, CURLOPT_POST, count($params));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		// set options - SSL
		$this->_ssl_verify($ch);

		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);

		$data = curl_exec($ch);

		curl_close($ch);
		return json_decode($data);
	}

	private function _ssl_verify($ch){
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	}

	private function _verbose_header($ch){
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_HEADER, 1);
	}
}
?>