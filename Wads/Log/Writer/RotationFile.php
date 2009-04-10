<?php
 /**
  * Zend Framework extensional component
  *
  * @category   Wads
  * @package    Wads_Log
  * @copyright  Copyright (c) 2008-2009 wads. (wads@gmail.com)
  * @license    http://framework.zend.com/license/new-bsd     New BSD License
  */
/** Zend_Log_Writer_Abstract */
require_once 'Zend/Log/Writer/Abstract.php';

/** Zend_Log_Formatter_Simple */
require_once 'Zend/Log/Formatter/Simple.php';

/** Wads_Log_RotationFile_Writer_Interface */
require_once 'Wads/Log/RotationFile/Writer/Interface.php';

/** Wads_Log_RotationFile */
require_once 'Wads/Log/RotationFile.php';

 /**
  * @category   Wads
  * @package    Wads_Log
  * @copyright  Copyright (c) 2008-2009 wads. (wads@gmail.com)
  */
class Wads_Log_Writer_RotationFile extends Zend_Log_Writer_Abstract
                 implements Wads_Log_RotationFile_Writer_Interface
{
    /**
     * File pointer
     *
     * @var resource
     */
    protected $_fp = null;

    /**
     * File append mode
     *
     * @var string
     */
    protected $_mode = null;

    /**
     * File name
     *
     * @var string
     */
    protected $_fname = null;

    /**
     * Rotation class
     *
     * @var Wads_Log_RotationFile
     */
    protected $_rotationClass = null;

    /**
     * constructor
     *
     * @param string $file
     * @param string $mode
     * @param Wads_Log_Rotation $rotationClass
     */
    public function __construct($file, $mode = 'a', Wads_Log_RotationFile $rotationClass = null) {
        if(!is_string($file)) {
            require_once 'Wads/Log/Writer/Exception.php';
            throw new Zend_Log_Writer_Exception("File name is not valid");
        }
        $this->_fname = $file;

        if(!preg_match('/^[awx]+?$/', $mode)) {
            require_once 'Wads/Log/Writer/Exception.php';
            throw new Zend_Log_Writer_Exception("File name is not valid");
        }
        $this->_mode = $mode;
        
        $this->startup();

        $this->_formatter = new Zend_Log_Formatter_Simple();

        if($rotationClass !== null) {
            $this->_rotationClass = $rotationClass;
        }
    }

    /**
     * Set a new log lotation class
     *
     * @param Wads_Log_RotationFile_Abstract
     */
    public function setRotationClass(Wads_Log_RotationFile $rotationClass) {
        $this->_rotationClass = $rotationClass;
    }

    /**
     * Returns log lotation class
     *
     * @return Wads_Log_RotationFile_Abstract
     */
    public function getRotationClass() {
        return $this->_rotationClass;
    }
    
    /**
     * Startup Writer
     */
    public function startup() {
        $this->_openLogFile();
    }
    
    /**
     * Shutdown writer
     */
    public function shutdown() {
        $this->_closeLogFile();
    }
    
    /**
     * Implements interface (Wads_Log_RotationFile_Writer_Interface)
     *
     * @param $file
     */
    public function update($file) {
        $this->shutdown();
        $this->_fname = $file;
        $this->startup();
    }

    /**
     * Write a message to the log
     *
     * @param array $event
     * @throws Wads_Log_Exception
     */
    protected function _write($event) {
        // do rotation file
        if($this->_rotationClass !== null) {
            try {
                $this->_rotationClass->rollOver($this);
            } catch (Wads_Log_Exception $e) {
                $this->shutdown();
                throw $e;
            }
        }

        // write log message
        $line = $this->_formatter->format($event);
        
        if(false === @fwrite($this->_fp, $line)) {
            require_once 'Wads/Log/Writer/Exception.php';
            throw new Zend_Log_Exception('Unable to write to file');
        }
    }

    /**
     * Opne log file
     * Move to writer
     *
     * @throws Wads_Log_Exception
     */
    protected function _openLogFile() {
        $this->_closeLogFile();

        if(! $this->_fp = @fopen($this->_fname, $this->_mode, false)) {
            require_once 'Wads/Log/Exception.php';
            throw new Wads_Log_Exception('Failed open log file.');
        }
    }

    /**
     * Close log file
     * Move to writer	
     */
    protected function _closeLogFile() {
        if(is_resource($this->_fp)) {
            fclose($this->_fp);
        }
    }
}