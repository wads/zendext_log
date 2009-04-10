<?php
/**
 * @see Zend_Auth_Storage_Interface
 */
require_once 'Zend/Auth/Storage/Interface.php';


/**
 * @copyright  wads (wadslab@gmail.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Wads_Auth_Storage_Cookie implements Zend_Auth_Storage_Interface
{
    /**
     * Default cookie name
     */
    const COOKIENAME_DEFAULT = 'Wads_Auth_Cookie';

    /**
     * @var string
     */
    protected $_name = "";
 
    /**
     * @var string
     */
    protected $_value = "";
 
    /**
     * @var int
     */
    protected $_expire = 0;
 
    /**
     * @var string
     */
    protected $_path = "/"; 
 
    /**
     * @var string
     */
    protected $_domain = "";
 
    /**
     * @var boolean
     */
    protected $_secure = false;
 
    /**
     * @var boolean
     */
    protected $_httponly = false;
    
    /**
     * @var int
     */
    protected $_expiry_term = 0;
 
    /**
     * constructor
     * 
     * @param string $name cookie name
     * @param string|array|Zend_Config  $option cookie option
     * @throws Zend_Auth_Storage_Exception
     */
    public function __construct($name, $option = null) {
        if(empty($name)) {
            require 'Zend/Auth/Storage/Exception.php';
            throw new Zend_Auth_Storage_Exception('Cookie Name cannot be empty.');
        }
        $this->_loadFromGlobal($name);
        
        if (is_string($option)) {
            $this->setValue($option);
        } else if (is_array($option)) {
            $this->_setCookieParams($option);
        } else if ($option instanceof Zend_Config) {
            $this->_setCookieParams($option);
        } else {
            require 'Zend/Auth/Storage/Exception.php';
            throw new Zend_Auth_Storage_Exception('Cookie Option value type need to string, array or Zend_Config.');
        }
    }
 
    /**
     * Load cookie from $_COOKIE
     * 
     * @param string $name cookie name
     */
    protected function _loadFromGlobal($name) {
        if(!is_string($name)) {
            $name = (string)$name;
        }
        $this->_name = $name;
        
        if(isset($_COOKIE[$this->_name])) {
            $this->setValue($_COOKIE[$this->_name]);
        }
    }
    
    public function __get($name) {
        $func = "get".ucfirst($name);
 
        if(method_exists($this, $func)) {
            return $func();
        }
 
        return null;
    }
 
    public function __set($name, $value) {
        $func = "get".ucfirst($name);
 
        if(method_exists($this, $func)) {
            return $func($value);
        } else {
            require_once 'Zend/Auth/Storage/Exception.php';
            throw new Zend_Auth_Storage_Exception('Cannot set the this value .');
        }
    }
 
    public function __toString(){
        return "{$this->_name}={$this->_value}";
    }
 
    /**
     * Defined by Zend_Auth_Storage_Interface
     *
     * Returns true if and only if storage is empty
     *
     * @throws Zend_Auth_Storage_Exception If it is impossible to determine whether storage is empty
     * @return boolean
     */
    public function isEmpty() {
        return ($this->_value === null);
    }
 
    /**
     * Defined by Zend_Auth_Storage_Interface
     *
     * Returns the contents of storage
     *
     * Behavior is undefined when storage is empty.
     *
     * @throws Zend_Auth_Storage_Exception If reading contents from storage is impossible
     * @return mixed
     */
    public function read() {
        return $this->_value;
    }
 
    /**
     * Defined by Zend_Auth_Storage_Interface
     *
     * Writes $contents to storage
     *
     * @param  mixed $contents
     * @throws Zend_Auth_Storage_Exception If writing $contents to storage is impossible
     * @return void
     */
    public function write($contents) {
        if(headers_sent()) {
            require_once 'Zend/Auth/Storage/Exception.php';
            throw new Zend_Auth_Storage_Exception('Cannot write Cookie because headers have already been sent.');
        }
 
        if(!is_array($contents)) {
            $contents = array('value' => $contents);
            $this->_setCookieParams($contents);
        }

        // set expiry time
        if($this->_expiry_term === 0) {
            $this->_expire = 0;
        } else {
            $this->_expire = time()+$this->_expiry_term;
        }
        
        $this->_setcookie($this->_name, $this->_value, $this->_expire, $this->_path, $this->_domain, $this->_secure, $this->_httponly);
    }
 
    /**
     * Defined by Zend_Auth_Storage_Interface
     *
     * Clears contents from storage
     *
     * @throws Zend_Auth_Storage_Exception If clearing contents from storage is impossible
     * @return void
     */
    public function clear() {
        $this->_setcookie($this->_name, "", time()-3600, $this->_path, $this->_domain, $this->_secure, $this->_httponly);
    }
 
    protected function _setCookieParams($params) {
        if ($params instanceof Zend_Config) {
            $params = $params->toArray();
        } elseif (!is_array($params)) {
            require_once 'Zend/Auth/Storage/Exception.php';
            throw new Zend_Auth_Storage_Exception('setCookieParams expects either an array or a Zend_Config object .');
        }
 
        foreach($params as $key => $value) {
            $method = 'set' . ucfirst($key);
            if(method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }
 
    public function setName($name) {
       if (!$name = (string)$name) {
            require_once 'Zend/Auth/Storage/Exception.php';
            throw new Zend_Auth_Storage_Exception('Cookies must have a name');
        }
 
        if (preg_match("/[=,; \t\r\n\013\014]/", $name)) {
            require_once 'Zend/Auth/Storage/Exception.php';
            throw new Zend_Auth_Storage_Exception("Cookie name cannot contain these characters: =,; \\t\\r\\n\\013\\014 ({$name})");
        }
 
        $this->_name = $name;
    }
 
    public function getName() {
        return $this->_name;
    }
 
    public function setValue($value) {
        if (is_bool($value)) {
            $value = ($value) ? "1" : "0";
        } else if (!is_string($contents)) {
            $value = (string)$value;
        }
        $this->_value = $value;
    }
 
    public function getValue() {
        return $this->_value;
    }
 
    public function getExpiryTime() {
        return $this->_expire;
    }
    
    public function getExpire() {
        return $this->getExpiryTime();
    }
 
    public function isExpired($now = null) {
        if ($now === null) $now = time();
        if (is_int($this->_expires) && $this->_expires < $now) {
            return true;
        } else {
            return false;
        }
    }
 
    public function isExpire() {
        return (time() < $this->getExpiryTime());
    }
    
    public function setExpiryTerm($term = null) {
        if(!is_numeric($term)) {
            require_once 'Zend/Auth/Storage/Exception.php';
            throw new Zend_Auth_Storage_Exception('Expiry Time need to numeric.');
        }
        $this->_expiry_term = (int)$term;
    }
    
    public function getExpiryTerm() {
        return $this->_expiry_term;
    }
    
    public function setPath($path) {
        $this->_path = $path;
    }
 
    public function getPath() {
        return $this->_path;
    }
 
    public function setDomain($domain) {
        $this->_domain = $domain;
    }
 
    public function getDomain() {
        return $this->_domain;
    }
 
    public function setSecure($secure) {
        $this->_secure = ($secure) ? true : false;
    }
 
    protected function getSecure() {
        return $this->isSecure();
    }
 
    public function isSecure() {
        return $this->_secure;
    }
 
    public function setHttponly($httponly) {
        if (version_compare(PHP_VERSION, '5.2.0', '>=')) {
            $this->_httponly = ($httponly) ? true : false;
        }
    }
 
    protected function getHttponly() {
        if (version_compare(PHP_VERSION, '5.2.0', '>=')) {
            return $this->isHttponly();
        }
    }
 
    public function isHttponly() {
        if (version_compare(PHP_VERSION, '5.2.0', '>=')) {
            return $this->_httponly;
        }
    }
 
    protected final function _setcookie($name, $value, $expire, $path, $domain, $secure, $httponly){
        if (version_compare(PHP_VERSION, '5.2.0', '>=')) {
           setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);
        } else {
            setcookie($name, $value, $expire, $path, $domain, $secure);
        }
    }
}