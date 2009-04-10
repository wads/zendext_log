<?php
 /**
  * Zend Framework extensional component
  *
  * @category   Wads
  * @package    Wads_Log
  * @copyright  Copyright (c) 2008-2009 wads. (wads@gmail.com)
  * @license    http://framework.zend.com/license/new-bsd     New BSD License
  */

 /**
  * @category   Wads
  * @package    Wads_Log
  * @copyright  Copyright (c) 2008-2009 wads. (wads@gmail.com)
  */
interface Wads_Log_RotationFile_Writer_Interface
{
    /**
     * Calls when rotation log file has finished.
     * Updates writer class
     *
     * @param string $file
     */
    public function update($file);
}