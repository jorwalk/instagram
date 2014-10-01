<?php
// Client Info
// FLUENT INTERFACE PATTERN
class ClientInfo {

	protected $client_id;
	protected $client_secret;
	protected $website_url;
	protected $redirect_uri;

	public function set_client_id($client_id){
		$this->client_id = $client_id;
		return $this;
	}

	public function get_client_id(){
		return $this->client_id;
	}

	public function set_client_secret($client_secret){
		$this->client_secret = $client_secret;
		return $this;
	}

	public function get_client_secret(){
		return $this->client_secret;
	}

	public function set_website_url($website_url){
		$this->website_url = $website_url;
		return $this;
	}

	public function get_website_url(){
		return $this->website_url;
	}

	public function set_redirect_uri($redirect_uri){
		$this->redirect_uri = $redirect_uri;
		return $this;
	}

	public function get_redirect_uri(){
		return $this->redirect_uri;
	}
}
?>