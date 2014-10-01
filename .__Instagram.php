<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Instagram class
 *
 * @author		Jordan Walker
 * @version		1.0.0
 * @copyright	Copyright (c), Jordan Walker. All rights reserved.
 * 
 */
class Instagram {

	// Instagram Globals
	const API_URL = 'https://api.instagram.com/v1/';
	const API_OAUTH_URL = 'https://api.instagram.com/oauth/authorize';
	const API_OAUTH_TOKEN_URL = 'https://api.instagram.com/oauth/access_token';
	
	/**
     * The consumer key
     *
     * @var	string
     */
	private $_apikey;
	
	/**
     * The consumer secret
     *
     * @var	string
     */
	private $_apisecret;
	
	/**
     * The consumer secret
     *
     * @var	string
     */
	private $_callbackurl;
	
	/**
     * The consumer secret
     *
     * @var	string
     */
	private $_accesstoken;
	
	/**
     * The array of access scopes
     *
     * @var	array
     */
	private $_scopes = array('basic', 'likes', 'comments', 'relationships');
	
	//=============================================================================
	// Instagram Constructor
	//=============================================================================
	public function Instagram_api(){}
	
	/**
     * Default constructor
     *
     * @param string $consumerKey    The consumer key to use.
     * @param string $consumerSecret The consumer secret to use.
     * @param string $apiCallback 	 The callback url.
     */
	public function init($config) {
		if (true === is_array($config)) {
			// if you want to access user data
			$this->setApiKey($config['apiKey']);
			$this->setApiSecret($config['apiSecret']);
			$this->setApiCallback($config['apiCallback']);
		} else if (true === is_string($config)) {
			// if you only want to access public data
			$this->setApiKey($config);
		} else {
			throw new Exception("Error: init() - Configuration data is missing.");
		}
	}
	
	//=============================================================================
	// Instagram Data Methods
	//=============================================================================
	
	public function getLoginUrl($scope = array('basic')) {
		if (is_array($scope) && count(array_intersect($scope, $this->_scopes)) === count($scope)) {
			return self::API_OAUTH_URL.'?client_id='.$this->getApiKey().'&redirect_uri='.$this->getApiCallback().'&scope='.implode('+', $scope).'&response_type=code';
		} else {
			throw new Exception("Error: getLoginUrl() - The parameter isn't an array or invalid scope permissions used.");
		}
	}

	//=============================================================================
	// User Endpoints
	//=============================================================================	

	/**
     * Get basic information about a user.
     *
     * @param  int  $id	The user id.
     * @return object
     */
	public function getUser($id = 0) {
		return $this->_makeCall('users/'.$id, true);
	}
	
	/**
     * See the authenticated user's feed.
     *
     * @param  string  	$ACCESS_TOKEN		A valid access token. 
     * @param  int  	$COUNT				Count of media to return. 
     * @param  int  	$MIN_ID				Return media later than this min_id. 
     * @param  int  	$MAX_ID				Return media earlier than this max_id.
     * @return object
     */
	public function getUserFeed($limit = 0, $max_id='', $min_id='') {
		return $this->_makeCall('users/self/feed', true, array('count' => $limit, 'min_id' => $min_id, 'max_id' => $max_id));
	}
	
	/**
     * Get the most recent media published by a user.
     *
     * @param  string  	$ACCESS_TOKEN		A valid access token. 
     * @param  int  	$COUNT				Count of media to return. 
     * @param  int  	$MAX_TIMESTAMP		Return media before this UNIX timestamp.
     * @param  int  	$MIN_TIMESTAMP		Return media after this UNIX timestamp.
	 * @param  int  	$MIN_ID				Return media later than this min_id. 
     * @param  int  	$MAX_ID				Return media earlier than this max_id.
     * @return object
     */
	public function getUserMedia($user_id, $array) {
		return $this->_makeCall('users/'.$user_id.'/media/recent', true, $array);
	}
	
	/**
     * See the authenticated user's list of media they've liked. Note that this list is ordered 
	 * by the order in which the user liked the media. Private media is returned as long as the 
	 * authenticated user has permission to view that media. Liked media lists are only available 
	 * for the currently authenticated user.
     *
     * @param  string  	$ACCESS_TOKEN		A valid access token. 
     * @param  int  	$COUNT				Count of media to return. 
     * @param  int  	$MAX_LIKE_ID		Return media liked before this id.
     * @return object
     */
	public function getUserLikes($limit = 0) {
		return $this->_makeCall('users/self/media/liked', true, array('count' => $limit));
	}
	
	/**
     * Search for a user by name.
     *
     * @param  string  	$ACCESS_TOKEN		A valid access token. 
     * @param  string  	$Q					A query string.
     * @param  int  	$COUNT				Number of users to return.
     * @return object
     */	
	public function searchUser($name, $limit = 0) {
		return $this->_makeCall('users/search', false, array('q' => $name, 'count' => $limit));
	}
	
	//=============================================================================
	// Relationship Endpoints
	//=============================================================================	
	/**
	 * Relationships are expressed using the following terms:
	 * outgoing_status: Your relationship to the user. Can be "follows", "requested", "none". 
	 * incoming_status: A user's relationship to you. Can be "followed_by", "requested_by", "blocked_by_you", "none".
	 */
		
	/**
     * Get the list of users this user follows.
     *
     * @required scope	relationships
	 * @param  string	$ACCESS_TOKEN		A valid access token.
	 * @param  int  	$id			User id.
     * @return object
    */	
	public function getUserFollows($id = 0,$params=null) {
		return $this->_makeCall('users/'.$id.'/follows', true,$params);
	}
	
	/**
     * Get the list of users this user is followed by.
     *
     * @required scope	relationships
	 * @param  string  	$ACCESS_TOKEN		A valid access token. 
	 * @param  int  	$id			User id.
     * @return object
    */	
	public function getUserFollowedBy($id = 0,$params=null) {
		return $this->_makeCall('users/'.$id.'/followed-by',true,$params);
	}
	
	/**
     * List the users who have requested this user's permission to follow.
     *
     * @required scope	relationships
	 * @param  string  	$ACCESS_TOKEN		A valid access token. 
	 * @return object
    */	
	public function getUserRequestedBy(){
		return $this->_makeCall('users/self/requested-by', true);
	}
	
	/**
     * List the users who have requested this user's permission to follow.
     *
     * @required scope	relationships
	 * @param  string  	$ACCESS_TOKEN		A valid access token.
	 * @param  int  	$id					User id.
	 * @return object
    */	
	public function getRelationship($id = 0) {
		return $this->_makeCall('users/'.$id.'/relationship', true);
	}
	
	/**
     * Modify the relationship between the current user and the target user.
     *
     * @required scope	relationships
	 * @param  string  	$ACCESS_TOKEN		A valid access token.
	 * @param  int  	$id					User id.
	 * @param  string  	$action				One of follow/unfollow/block/unblock/approve/deny.
	 * @return object
    */	
	public function setRelationship($id = 0, $action) {
		return $this->_postCall('users/'.$id.'/relationship', true, array('action' => $action));
	}
	
	//=============================================================================
	// Media Endpoints
	//=============================================================================	
	
	/**
     * Get information about a media object. Note: if you authenticate with an OAuth Token, you will 
	 * receive the user_has_liked key which quickly tells you whether the current user has liked this media item.
     *
     * @param  string  	$ACCESS_TOKEN		A valid access token.
	 * @param  int  	$id					Media Id.
	 * @return object
    */	
	public function getMedia($id) {
		return $this->_makeCall('media/'.$id, true);
	}
	
	/**
     * Search for media in a given area. The default time span is set to 5 days. The time span must not exceed 7 days. \
	 * Defaults time stamps cover the last 5 days.
     *
	 * @param  string  	$ACCESS_TOKEN		A valid access token.
	 * @param  double  	$LAT				Latitude of the center search coordinate. If used, lng is required.			
	 * @param  double  	$LNG				Longitude of the center search coordinate. If used, lat is required.
	 * @param  double  	$LNG				Longitude of the center search coordinate. If used, lat is required.
	 * @param  int  	$MAX_TIMESTAMP		Return media before this UNIX timestamp.
     * @param  int  	$MIN_TIMESTAMP		Return media after this UNIX timestamp.
	 * @return object
    */	
	public function searchMedia($params) {
		return $this->_makeCall('media/search', true, $params);
	}
	
	/**
     * Get a list of what media is most popular at the moment.
     *
     * @param  string  	$ACCESS_TOKEN		A valid access token.
	 * @return object
    */
	public function getPopularMedia() {
		return $this->_makeCall('media/popular', true);
	}
	
	//=============================================================================
	// Comments Endpoints
	//=============================================================================	
	
	
	public function getComments($media_id){
		return $this->_makeCall('media/'.$media_id.'/comments', true);
	}
	
	public function setComments($media_id, $text) {
		return $this->_postCall('media/'.$media_id.'/comments', true, array('text'=>$text));
	}
	
	public function delComments($media_id, $comment_id) {
		return $this->_deleteCall('media/'.$media_id.'/comments/'.$comment_id, true);
	}
	
	//=============================================================================
	// Like Endpoints
	//=============================================================================	
	
	
	public function getLikes($media_id) {
		return $this->_makeCall('media/'.$media_id.'/likes', true);
	}
	
	public function setLikes($media_id) {
		return $this->_postCall('media/'.$media_id.'/likes', true);
	}
	
	public function delLikes($media_id) {
		return $this->_deleteCall('media/'.$media_id.'/likes', true);
	}
	
	//=============================================================================
	// Tag Endpoints
	//=============================================================================	
	
	
	public function getTag($name) {
		return $this->_makeCall('tags/'.$name, true);
	}
	
	public function getTagMedia($name, $params) {
		return $this->_makeCall('tags/'.$name.'/media/recent', true, $params);
	}
	
	public function searchTags($name) {
		return $this->_makeCall('tags/search', false, array('q' => $name));
	}
	
	//=============================================================================
	// Location Endpoints
	//=============================================================================	
	
	public function getLocation($location_id){
		return $this->_makeCall('locations/'.$location_id, true);
	}
	
	public function getLocationMedia($location_id, $params){
		return $this->_makeCall('locations/'.$location_id.'/media/recent', true, $params);
	}
	
	public function searchLocation($lat, $lng) {
		return $this->_makeCall('locations/search', true, array('lat' => $lat, 'lng' => $lng));
	}
	
	//=============================================================================
	// Geographies Endpoints
	//=============================================================================		
	public function getGeographies($geo_id){
		return $this->_makeCall('geographies/'.$geo_id.'/media/recent', false);
	}
	
	//=============================================================================
	// Instagram Subscriptions Methods
	//=============================================================================
	
	public function getSubscriptions() {
		$params = array('client_secret'=>$this->getApiSecret());
		return $this->_makeCall('subscriptions', false, $params);
	}
	
	public function setSubscription($params) {
		return $this->_postCall('subscriptions/', false, $params);
	}
	
	public function delSubscription($params) {
		return $this->_delCall('subscriptions/', false, $params);
	}
	
	//=============================================================================
	// Instagram Authorization
	//=============================================================================
	
	public function getOAuthToken($code, $token = false) {
		$apiData = array(
		'grant_type'      => 'authorization_code',
		'client_id'       => $this->getApiKey(),
		'client_secret'   => $this->getApiSecret(),
		'redirect_uri'    => $this->getApiCallback(),
		'code'            => $code
		);
		
		$result = $this->_makeOAuthCall($apiData);
		return (false === $token) ? $result : $result->access_token;
	}
	
	//=============================================================================
	// Instagram Caller Methods
	//=============================================================================
	
	private function _postCall($function,$auth = false, $params = null) {
		if (false === $auth) {
			// if the call doesn't requires authentication
			$authMethod = '?client_id='.$this->getApiKey();
		} else {
			// if the call needs a authenticated user
			if (true === isset($this->_accesstoken)) {
				$authMethod = '?access_token='.$this->getAccessToken();
			} else {
				throw new Exception("Error: _makeCall() | $function - This method requires an authenticated users access token.");
			}
		}
	
		if (isset($params) && is_array($params)) {
			$params = '&'.http_build_query($params);
		} else {
			$params = null;
		}
		
		$apiCall = self::API_URL.$function.$authMethod.$params;
	
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $apiCall);
		curl_setopt($ch, CURLOPT_POST, count($params));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		
		$jsonData = curl_exec($ch);
		curl_close($ch);
		
		return json_decode($jsonData);
	}
	
	private function _makeCall($function, $auth = false, $params = null) {
		if (false === $auth) {
			// if the call doesn't requires authentication
			$authMethod = '?client_id='.$this->getApiKey();
		} else {
			// if the call needs a authenticated user
			if (true === isset($this->_accesstoken)) {
				$authMethod = '?access_token='.$this->getAccessToken();
			} else {
				throw new Exception("Error: _makeCall() | $function - This method requires an authenticated users access token.");
			}
		}
	
		if (isset($params) && is_array($params)) {
			$params = '&'.http_build_query($params);
		} else {
			$params = null;
		}
		
		// call for platform REST server requests
		$curl = curl_init();
		// Set some options - we are passing in a useragent too here
		curl_setopt_array($curl, array(
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_URL => 'http://www.jordanwalker.net/api/request/instagram/',
			CURLOPT_USERAGENT => 'Instagram API',
			CURLOPT_POST => 1,
			CURLOPT_POSTFIELDS => array(
				'call' => self::API_URL.$function.$authMethod.$params,
				'time' => time()
			)
		));
		// Send the request & save response to $resp
		$resp = curl_exec($curl);
		// Close request to clear up some resources
		curl_close($curl);
		
		// call for instagram
		$apiCall = self::API_URL.$function.$authMethod.$params;
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $apiCall);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		
		$jsonData = curl_exec($ch);
		curl_close($ch);
		
		return json_decode($jsonData);
	}
	
	private function _deleteCall($function, $auth = false, $params = null) {
		if (false === $auth) {
			// if the call doesn't requires authentication
			$authMethod = '?client_id='.$this->getApiKey();
		} else {
			// if the call needs a authenticated user
			if (true === isset($this->_accesstoken)) {
				$authMethod = '?access_token='.$this->getAccessToken();
			} else {
				throw new Exception("Error: _makeCall() | $function - This method requires an authenticated users access token.");
			}
		}
	
		if (isset($params) && is_array($params)) {
			$params = '&'.http_build_query($params);
		} else {
			$params = null;
		}
		
		$apiCall = self::API_URL.$function.$authMethod.$params;
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $apiCall);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
		
		$jsonData = curl_exec($ch);
		curl_close($ch);
		
		return json_decode($jsonData);
	}
	
	private function _makeOAuthCall($apiData) {
		$apiHost = self::API_OAUTH_TOKEN_URL;
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $apiHost);
		curl_setopt($ch, CURLOPT_POST, count($apiData));
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($apiData));
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		
		$jsonData = curl_exec($ch);
		curl_close($ch);
		
		return json_decode($jsonData);
	}
	
	//=============================================================================
	// Instagram Util Methods
	//=============================================================================
	
	public function setAccessToken($data) {
		(true === is_object($data)) ? $token = $data->access_token : $token = $data;
		$this->_accesstoken = $token;
	}
	
	public function getAccessToken() {
		return $this->_accesstoken;
	}

	public function setApiKey($apiKey) {
		$this->_apikey = $apiKey;
	}
	
	public function getApiKey() {
		return $this->_apikey;
	}
	
	public function setApiSecret($apiSecret) {
		$this->_apisecret = $apiSecret;
	}
	
	public function getApiSecret() {
		return $this->_apisecret;
	}

	public function setApiCallback($apiCallback) {
		$this->_callbackurl = $apiCallback;
	}
	
	public function getApiCallback() {
		return $this->_callbackurl;
	}
}