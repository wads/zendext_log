<?php
 /**
  * Zend Framework extensional component
  *
  * @category   Wads
  * @package    Wads_Log
  * @copyright  Copyright (c) 2008-2009 wads. (wads@gmail.com)
  * @license    http://framework.zend.com/license/new-bsd     New BSD License
  */

/** Wads_Log_RotationFile_Policy_Abstract */
require_once 'Wads/Log/RotationFile/Policy/Abstract.php';

 /**
  * @category   Wads
  * @package    Wads_Log
  * @copyright  Copyright (c) 2008-2009 wads. (wads@gmail.com)
  */
class Wads_Log_RotationFile
{
    /**
     * Rotation Policy class
     *
     * @var Wads_Log_RotationFile_Policy_Abstract
     */
    protected $_rotationPolicyClass = null;

    /**
     * Current log file name
     *
     * @var string
     */
    protected $_logFile = null;

    /**
     * constructor
     *
     * @param  string $logFile
     * @param  Wads_Log_RotationFile_Abstract $rotation
     * @throws Wads_Log_Exception
     */    
    public function __construct($logFile, array $opt = null) {
        // check arguments
        if(!is_string($logFile)) {
            require_once 'Wads/Log/Exception.php';
            throw new Wads_Log_Exception('Log file name is not corrected.');
        }

        if(!isset($opt['file'])) {
            $opt['file'] = $logFile;
        }

        // create policy class
        if(isset($opt['policy'])) {
            $policy = $opt['policy'];
            unset($opt['policy']);
        } else {
            $policy = 'size';
        }

        $policy_class = 'Wads_Log_RotationFile_Policy_' . ucfirst($policy);
        Zend_Loader::loadClass($policy_class);

        $this->_rotationPolicyClass = new $policy_class($opt);
    }

    /**
     * Initialization class
     */
    public function _init() {
        $this->_rollOver();
    }

    /**
     * Returns rotation policy class
     *
     * @return Wads_Log_RotationFile_Policy_Abstract   
     */
    public function getRotationPolicy() {
        return $this->_rotationPolicyClass;
    }

    /**
     * Sets rotation policy class
     *
     * @param Wads_Log_RotationFile_Policy_Abstract
     */
    public function setRotationPolicy(Wads_Log_RotationFile_Policy_Abstract $rotationPolicyClass) {
        $this->_rotationPolicyClass = $rotationPolicyClass;
    }

    /**
     * Returns log file name
     *
     * @return string   
     */
    public function getLogFileName() {
        return $this->_logFile;
    }

    /**
     * Sets log file
     *
     * @param string
     */
    public function setLogFileName($logFile) {
        if(is_string($logFile)) {
            $this->_logFile = $logFile;
        }
    }

    public function rollOver(Wads_Log_RotationFile_Writer_Interface $writer) {
        if($this->_rollOver()) {
            $writer->update($this->_logFile);
        }
    }
    
    /**
     * RollOver log file
     *
     * @param resource $fp
     * @param string $mode
     * @returns boolean
     * @throws Wads_Log_Exception
     */
    protected function _rollOver() {
        if(!$this->_rotationPolicyClass->trigger()) {
            return false;
        }

        $this->_logFile = $this->_rotationPolicyClass->rollOver();
        
        return true;
    }
}