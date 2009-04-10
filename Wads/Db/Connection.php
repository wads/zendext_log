<?php
 /**
  * Wads Library
  *
  * @category   Wads
  * @package    Wads_Db
  * @copyright  Copyright (c) 2008 wads. (wads@gmail.com)
  * @license    http://framework.zend.com/license/new-bsd     New BSD License
  */

 /**
  * Databese connection management class for MySQL
  *
  * @category   Wads
  * @package    Wads_Db
  * @copyright  Copyright (c) 2008 wads. (wads@gmail.com)
  */
class Wads_Db_Connection
{
    /**
     * Database configuration file path
     *
     * @var string
     */
    protected static $_db_config_file = null;

    /**
     * Database configuration parameter
     *
     * @var array
     */     
    protected static $_db_config;

    /**
     * Database handle
     *
     * @var Zend_Db_Adapter_Abstract
     */
  	private static $_dbh = null;
	
	/**
	 * Use getAdapter
	 */
	private function __construct() {
	}
	
	/**
	 * Disallow Object copy
	 */
    private function __clone() {
    }
    
	/**
	 * Returns Zend_Db_Adapter_Abstract object
	 *
	 * @param string  $schema DB schema name
	 * @param boolean $master If true, connect to the master DB. If not,
	 *                        connect to the slave DB.
	 * 
	 * @return Zend_Db_Adapter_Abstract
	 * @throw Wads_Db_Exception
	 */
	public static function getAdapter($schema, $master=false, $conf=null) {
		if(!is_bool($master)) {
	        throw new Wads_Db_Exception('Argument $master need to be boolean value.');
	    }

        if($conf !== null) {
            self::$_db_config_file = $conf;
        }
	    
	    $type = $master ? 'master' : 'slave';
	    
	    if(!(self::$_dbh[$schema][$type] instanceof Zend_Db_Adapter_Abstract)) {
	        $func = '_get' . ucfirst($type) . 'Config';
	        $conf = self::$func($schema);
	        if($conf === null) {
	            throw new Wads_Db_Exception('Specified Database not found.');
	        }
	        self::$_dbh[$schema][$type] = self::_getAdapter($conf);
	    }
	    return self::$_dbh[$schema][$type];
	}

	/**
	 * Create and Returns Zend_Db_Adapter_Abstract object
	 *
	 * @param array $db_param : parameter array intend to connect
	 * @return Zend_Db_Adapter_Abstract
	 */
	private static function _getAdapter($conf) {
		$params = array('host'		=> $conf['host'],
						'username'	=> $conf['username'],
						'password'	=> $conf['password'],
						'dbname'	=> $conf['dbname']);
    	return Zend_Db::factory($conf['dbkind'], $params);
	}

	/**
	 * Returns master db config
	 *
	 * @return array
	 */
	private static function _getMasterConfig($schema) {
	    self::_loadDbConfig($schema);
	    
	    if(!isset(self::$_db_config[$schema]->master)) {
	        return null;
	    }
	    
	    return self::$_db_config[$schema]->master->toArray();
	}

	/**
	 * Returns slave db config
	 *
	 * @param int $no : index of slave db config
	 * @return array
	 */
	private static function _getSlaveConfig($schema) {
	    self::_loadDbConfig($schema);


        // use no replication.
        if(!(self::$_db_config[$schema]->slave instanceof Zend_Config)) {
            return self::_getMasterConfig($schema);
        }

	    //$db_conf_num = self::$_db_config[$schema]->slave->count();
        $confs = self::$_db_config[$schema]->slave->toArray();
        if(is_int(key($confs))) {
            // use 2 or more slave database
            $slave_num = count($confs) -  1;
            $i = self::_getSlaveIndex($slave_num);
            echo $i;
            if(!isset(self::$_db_config[$schema]->slave->$i)) {
		        return null;
            }

            return self::$_db_config[$schema]->slave->$i->toArray();
        } else {
            // use only one slave database
            return self::$_db_config[$schema]->slave->toArray();            
        }
	}
	
	/**
	 * Returns Slave index based on something rule
	 *
	 * @return int
	 */
	protected static function _getSlaveIndex($max_slave_num) {
        /* now index is random */
        if(!is_numeric($max_slave_num) || $max_slave_num <= 0) {
		    return new Wads_Db_Exception('Slave Index need to numeric value and over 0');
		}

		return mt_rand(0, $max_slave_num);
	}
	
	private static function _loadDbConfig($schema){
	    if(self::$_db_config[$schema] === null) {
            try {
                $conf = new Zend_Config_Xml(self::$_db_config_file, $schema);
            } catch (Zend_Config_Exception $e) {
                throw new Wads_Db_Exception($e->getMessage());
            }
            self::$_db_config[$schema] = $conf;
	    }
	    return true;
	}
}