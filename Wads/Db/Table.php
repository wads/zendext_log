<?php
 /**
  * Wads Library
  *
  * @category   Wads
  * @package    Wads_Db
  * @copyright  Copyright (c) 2008 wads. (wads@gmail.com)
  * @license    http://framework.zend.com/license/new-bsd     New BSD License
  */

/** Wads_Db_Connection */
require_once 'Wads/Db/Connection.php';

/** Wads_Db_Table_Writable */
require_once 'Wads/Db/Table/Writable.php';

/** Wads_Db_Table_Readonly */
require_once 'Wads/Db/Table/Readonly.php';

 /**
  * Databese Table load class
  *
  * @category   Wads
  * @package    Wads_Db
  * @copyright  Copyright (c) 2008 wads. (wads@gmail.com)
  */
class Wads_Db_Table
{
    /**
     * Db config file
     *
     * @var string
     */
    protected static $_conf_file = null;
    
    /**
     * Prefix of Table Access Class
     *
     * @var string
     */
    protected static $_class_prefix = 'Wads_Db_Table_';

    /**
     * Sub Prefix of Readonly Table Access Class
     *
     * @var string
     */
    protected static $_readonly_suffix = '_Readonly';

   /**
     * Sub Prefix of Writable Table Access Class
     *
     * @var string
     */
    protected static $_writable_suffix = '_Writable';

    /**
     * Factory method. Returns Db Handle
     *
     * @param string  $dbName     Db schema name
     * @param string  $tableName  Db table name
     * @param boolean $isWritable Whether if Db uses as writable access
     * @return Zend_Db_Table_Abstract
     * @throw Wads_Db_Exception
     */
    public static function factory($dbName, $tableName, $isWritable = false, $opt = null) {
        if(is_array($opt)) {
            self::_setOption($opt);
        }
        
        $dbSuffix   = self::_strToClassSuffix($dbName);
        $tableName  = self::_strToClassSuffix($tableName);
        $typeSuffix = $isWritable ? self::$_writable_suffix : self::$_readonly_suffix;

        $className  = self::$_class_prefix . $dbSuffix . $typeSuffix . $tableName;
        $opt = array('db' => self::_getDbHandler($dbName, $isWritable, self::$_conf_file));

        return new $className($opt);
    }

    protected function _setOption(array $opt) {
        foreach($opt as $name=>$value) {
            switch($name) {
            case 'conf' :
                self::$_conf_file = (string)$value;
                break;
            case 'prefix':
                self::$_class_prefix = self::_strToClassPrefix((string)$value);
                break;
            case 'readonly_prefix':
                self::$_readonly_suffix = self::_strToClassSuffix((string)$value);
                break;
            case 'writable_suffix':
                self::$_writable_suffix = self::_strToClassSuffix((string)$value);
                break;
            default:
                // no process
            }
        }
    }

    /**
     * Returns Db Handle
     *
     * @param string  $dbName     Daba Base name
     * @param boolean $isWritable Whether if Db uses as writable access
     * @return Zend_Db_Adapter_Abstract
     * @throw Wads_Db_Exception
     */
    protected static function _getDbHandler($dbName, $isWritable, $conf = null) {
        return Wads_Db_Connection::getAdapter($dbName, $isWritable, $conf);
    }

    protected static function _strToClassPrefix($str) {
        if(empty($str)) {
            return '';
        }

        return trim($str, '_');
    }

    protected static function _strToClassSuffix($str) {
        if(empty($str)) {
            return '';
        }
        
        return '_' . self::_strToClassName($str);
    }

    protected static function _strToClassName($str) {
        $str = trim($str, '_');

        if(strpos($str, '_') !== FALSE) {
            $elems = explode('_', $str);

            $ret = '';
            foreach($elems as $elem) {
                $ret .= ucfirst(strtolower($elem));
            }
        } else {
            $ret = ucfirst($str);
        }

        return $ret;
    }
}