<?php
//=============================================================================
// Relationship Endpoints - Factory Pattern
//=============================================================================	
/**
 * Relationships are expressed using the following terms:
 * outgoing_status: Your relationship to the user. Can be "follows", "requested", "none". 
 * incoming_status: A user's relationship to you. Can be "followed_by", "requested_by", "blocked_by_you", "none".
 */
class Relationship {
	
	protected $request;

	public function __construct(Request $request){
		$this->request = $request;
	}

	/**
     * Get the list of users this user follows.
     *
     * @required scope	relationships
	 * @param  int  	$id			User id.
     * @return object
    */	
	public function get_user_follows($id = 0,$params=null) {
		return $this->request->set_function('users/'.$id.'/follows')->call($params);
	}
	
	/**
     * Get the list of users this user is followed by.
     *
     * @required scope	relationships
	 * @param  int  	$id			User id.
     * @return object
    */	
	public function get_user_followed_by($id = 0,$params=null) {
		return $this->request->set_function('users/'.$id.'/followed-by')->call($params);
	}
	
	/**
     * List the users who have requested this user's permission to follow.
     *
     * @required scope	relationships
	 * @return object
    */	
	public function get_user_requested_by(){
		return $this->request->set_function('users/self/requested-by')->call();
	}
	
	/**
     * Get information about a relationship to another user.
     *
     * @required scope	relationships
	 * @param  int  	$id					User id.
	 * @return object
    */	
	public function get_relationship($id = 0) {
		return $this->request->set_function('users/'.$id.'/relationship')->call();
	}
	
	/**
     * Modify the relationship between the current user and the target user.
     *
     * @required scope	relationships
	 * @param  int  	$id					User id.
	 * @param  string  	$action				One of follow/unfollow/block/unblock/approve/deny.
	 * @return object
    */	
	public function set_relationship($id = 0, $action) {
		return $this->request->set_function('users/'.$id.'/relationship')
		->call_post(array('action' => $action));
	}
}
?>