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
abstract class Wads_Log_RotationFile_Policy_Abstract
{
    /**
     * Log file name.
     *
     * @param string
     */
    protected $_fileName = null;
    
    /**
     * Sets file name
     *
     * @param string $name
     */
    public function setFileName($name) {
        if(is_string($name)) {
            $this->_fileName = $name;
        }
    }

    /**
     * Returns file name pattern
     *
     * @returns string
     */
    public function getFileName() {
        return $this->_fileName;
    }

    /**
     * Check log file whether need to trigger rotation.
     *
     * @return boolean
     * @throws Wads_Log_RotationFile_Policy_Exception
     */
    abstract public function trigger();

    /**
     * Rollover Log file.
     * If succeed, returns new log file name.
     *
     * @return string
     * @throws Wads_Log_RotationFile_Policy_Exception
     */
    abstract public function rollOver();
}