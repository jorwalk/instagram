<?php
/**
 * class RequestFactory
 */
// PHP Pattern - Creational / Simple Factory
class RequestFactory
{
	public function __construct()
    {
        if(isset($_GET)):
        	$get = new GetRequest();
        	$get->params($_GET);
    	endif;

    	if(isset($_POST)):
        	$post = new PostRequest();
        	$post->params($_POST);
    	endif;
    }
}

/**
 * RequestInterface is a handle of params
 */
interface RequestInterface
{
    /**
     * @param array $params
     */
    public static function params($params);
}

/**
 * $_GET is a get request
 */
class GetRequest implements RequestInterface
{
    /**
     * @param array $params
     */
    public static function params($params)
    {
    	foreach($params as $key => $value):
    		RequestRegistry::set($key,$value);
    	endforeach;
    }
}

/**
 * $_POST is a post request
 */
class PostRequest implements RequestInterface
{
    /**
     * @param array $params
     */
    public static function params($params)
    {
    	foreach($params as $key => $value):
    		RequestRegistry::set($key,$value);
    	endforeach;
    }
}

/**
 * class Registry
 */
// PHP Pattern - Structural / Registry
abstract class RequestRegistry
{
    protected static $store = array();

    public static function set($key, $value)
    {
        self::$store[$key] = $value;
    }

    public static function get(){
    	return self::$store;
    }

    public static function get_by($key = null)
    {
        $stored = array_key_exists($key, self::$store) ? self::$store[$key] : false;
        return $stored;
    }

    
}
$factory_request = new RequestFactory();
?>