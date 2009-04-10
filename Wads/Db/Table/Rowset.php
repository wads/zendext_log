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
#require_once 'Zend/Db/Table/Row/Abstract.php';

 /**
  * Abstract DB Result Row class
  *
  * @category   Wads
  * @package    Wads_Db
  * @copyright  Copyright (c) 2008 wads. (wads@gmail.com)
  */
class Wads_Db_Table_Rowset
{
    /**
     * @param Zend_Db_Table_Row_Abstract
     */
    //    protected $_row = null;

    /**
     * constructor
     *
     * @param Zend_Db_Table_Row_Abstract $row
     */
/*    protected function __construct(Zend_Db_Table_Row_Abstract $row = null) {
        if($row !== null) {
            $this->_row = $row;
        }
    }
*/
    /**
     * Returns row class
     *
     * @return Zend_Db_Table_Row
     * @throws Wads_Db_Table_Exception
     */
/*   protected function _getRow() {
        if($this->_row === null) {
            require_once 'Wads/Db/Table/Exception.php';
            throw new Wads_Db_Table_Exception('Row class is not set.');
        }
        
        return $this->_row;
    }
*/
 }
