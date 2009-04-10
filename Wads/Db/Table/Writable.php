<?php
 /**
  * Wads Library
  *
  * @category   Wads
  * @package    Wads_Db
  * @copyright  Copyright (c) 2008 wads. (wads@gmail.com)
  * @license    http://framework.zend.com/license/new-bsd     New BSD License
  */
require_once 'Zend/Db/Table/Abstract.php';

 /**
  * Abstract Table Access class. Allows CRUD
  *
  * @category   Wads
  * @package    Wads_Db
  * @copyright  Copyright (c) 2008 wads. (wads@gmail.com)
  */
class Wads_Db_Table_Writable extends Zend_Db_Table_Abstract
{
    // primary key
	protected $_primary = 'id';
}
