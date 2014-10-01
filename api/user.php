<?php 
//=============================================================================
// User Endpoints -- Facade Pattern
//=============================================================================	
class User {

	public $request;

	public function __construct(Request $request){
		$this->request = $request;
	}

	/**
     * Get basic information about a user.
     *
     * @param  int  $id	The user id.
     * @return object
     */
	public function get_user($id = 0) {
		return $this->request->set_function('users/'.$id)->call(false);
	}
	
	/**
     * See the authenticated user's feed.
     *
     * @param  int  	$COUNT				Count of media to return. 
     * @param  int  	$MIN_ID				Return media later than this min_id. 
     * @param  int  	$MAX_ID				Return media earlier than this max_id.
     * @return object
     */
	public function get_self_feed($limit = 0, $max_id='', $min_id='') {
		return $this->request->set_function("users/self/feed")->call(array('count' => $limit, 'min_id' => $min_id, 'max_id' => $max_id));
	}

	/**
     * Search for a user by name.
     *
     * @param  string  	$Q					A query string.
     * @param  int  	$COUNT				Number of users to return.
     * @return object
     */	
	public function search_user($name, $limit = 0) {
		return $this->request->set_function('users/search')->call(array('q' => $name, 'count' => $limit));
	}
}



?>