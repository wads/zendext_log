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
 * @see Zend_Db_Table_Row_Abstract
 */
require_once 'Zend/Db/Table/Row/Abstract.php';

 /**
  * Abstract DB Result Row class
  *
  * @category   Wads
  * @package    Wads_Db
  * @copyright  Copyright (c) 2008 wads. (wads@gmail.com)
  */
class Wads_Db_Table_Row
{
    /**
     * @param Zend_Db_Table_Row_Abstract
     */
    protected $_row = null;

    /**
     * constructor
     *
     * @param Zend_Db_Table_Row_Abstract $row
     */
    protected function __construct(Zend_Db_Table_Row_Abstract $row = null) {
        if($row !== null) {
            $this->_row = $row;
        }
    }

    /**
     * Transform actural DB table name from simple name
     *
     * @param string $columnName
     * @returns string
     */
    protected function _transformColumnName($columnName) {
        $needle = strtolower(get_class($this)) . '_';
        if(strpos($columnName, $needle) !== 0) {
            $columnName = $needle . $columnName;
        }

        return $columnName;
    }
    
    /**
     * Retrieve row field value
     *
     * @param  string $columnName The user-specified column name.
     * @return string             The corresponding column value.
     * @throws Zend_Db_Table_Row_Exception
     */
    public function __get($columnName) {
        //$columnName = $this->_transformColumnName($columnName);
        return $this->_getRow()->__get($columnName);
    }

    /**
     * Set row field value
     *
     * @param  string $columnName The column key.
     * @param  mixed  $value      The value for the property.
     * @return void
     * @throws Zend_Db_Table_Row_Exception
     */
    public function __set($columnName, $value) {
        //$columnName = $this->_transformColumnName($columnName);
        return $this->_getRow()->__set($columnName, $value);
    }

/*
    public function __call() {
    }
 */

    /**
     * Returns row class
     *
     * @return Zend_Db_Table_Row
     * @throws Wads_Db_Table_Exception
     */
    protected function _getRow() {
        if($this->_row === null) {
            require_once 'Wads/Db/Table/Exception.php';
            throw new Wads_Db_Table_Exception('Row class is not set.');
        }
        
        return $this->_row;
    }

    /**
     * Saves the properties to the database.
     *
     * @see Zend_Db_Table_Row
     */
    public function save() {
        return $this->_getRow()->save();
    }
 }
