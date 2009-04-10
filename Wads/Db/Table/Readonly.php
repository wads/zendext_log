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
 * @see Wads_Db_Table_Writable
 */
require_once 'Wads/Db/Table/Writable.php';

 /**
  * Abstract Table Access class. Allows only Read
  *
  * @category   Wads
  * @package    Wads_Db
  * @copyright  Copyright (c) 2008 wads. (wads@gmail.com)
  */
class Wads_Db_Table_Readonly extends Wads_Db_Table_Writable
{
    /**
     * Disallows insert this class
     */
    public function insert(array $data) {
        require_once 'Wads/Db/Table/Exception.php';
        throw new Wads_Db_Table_Exception('Tried insert in readonly table class');
    }

    /**
     * Disallows update this class
     */
    public function update(array $data, $where){
        require_once 'Wads/Db/Table/Exception.php';
        throw new Wads_Db_Table_Exception('Tried update in readonly table class');
    }

    /**
     * Disallows delete this class
     */
    public function delete($where) {
        require_once 'Wads/Db/Table/Exception.php';
        throw new Wads_Db_Table_Exception('Tried delete in readonly table class');
    }
}
