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
class Wads_Log_RotationFile_Policy_Datetime extends Wads_Log_RotationFile_Policy_Abstract
{
    const LOG_NAME_PATTERN_FILE = '%file%';

    const LOG_NAME_PATTERN_DATE = '%date%';
    
    /**
     * File Name pattern.
     * This pattern replaces '%file%' as log file name. and replades
     * '%date%' as $_dateFormat.
     *
     * @var string
     */
    protected $_fileNamePattern = '%file%_%date%.log';

    /**
     * Inserts $_fileNamePattern in %date% pattern.
     * This property follows date() function argument format
     *
     * @var string
     */
    protected $_dateFormat = 'Ymd';

    /**
     * Current opne log file
     *
     * @var string
     */
    protected $_currentOpenLogFile = null;
    
    /**
     * constructor
     *
     * @param array $opt
     */
    public function __construct(array $opt = null) {
        if($opt !== null) {
            $this->_setOptions($opt);
        }
    }

    /**
     * Set each options from array
     *
     * @param array $opt
     */
    protected function _setOptions(array $opt) {
        foreach($opt as $name=>$val) {
            switch($name) {
            case 'file':
                $this->setBaseFileName($val);
                break;
            case 'nameformat':
                $this->setFileNamePattern($val);
                break;
            case 'dateformat':
                $this->setDateFormat($val);
                break;
            default:
                break;
            }
        }
    }

    /**
     * Sets log file base name
     *
     * @param string $file
     */
    public function setBaseFileName($file) {
	parent::setFileName($file);
    }

    /**
     * Returns log file base name
     *
     * @returns string
     */
    public function getBaseFileName() {
        return $this->_fileName;
    }

    /**
     * Returns log file name
     *
     * @returns string
     */
    public function getFileName() {
        return $this->_getLogFileName();
    }

    
    /**
     * Sets file name pattern
     *
     * @param string $pattern
     */
    public function setFileNamePattern($pattern) {
        if(is_string($pattern) && preg_match('/%date%/', $pattern)) {
            $this->_fileNamePattern = $pattern;
        }
    }

    /**
     * Returns file name pattern
     *
     * @returns string
     */
    public function getFileNamePattern() {
        return $this->_fileNamePattern;
    }

    /**
     * Sets date format
     *
     * @param string $format
     */
    public function setDateFormat($format) {
        if(is_string($format)) {
            $this->_dateFormat = $format;
        }
    }

    /**
     * Returns date format
     *
     * @returns string
     */
    public function getDateFormat() {
        return $this->_dateFormat;
    }
    
    /**
     * Implements abstract function.
     * Check log file whether need to trigger rotation.
     *
     * @param string $logFile
     * @return boolean
     */
    public function trigger() {
       if($this->_fileName === null) {
            require_once 'Wads/Log/RotationFile/Policy/Exception.php';
            throw new Wads_Log_RotationFile_Policy_Exception('Trriger error because the log file name is not unknown.');
       }
            
        return $this->_trigger($this->_fileName);
    }

    /**
     * Returns if trigger rolling log file
     *
     * @param string $file
     * @return boolean
     */
    protected function _trigger($logFile) {
        if($this->_currentOpenLogFile === null) {
	    return true;
	}

	return !($this->_currentOpenLogFile == $this->_getLogFileName());
    }

    /**
     * Implements abstract function.
     *
     * @returns string
     * @throws Wads_Log_RotationFile_Policy_Exception
     */
    public function rollover() {
	$this->_currentOpenLogFile = $this->_getLogFileName();
	return $this->_currentOpenLogFile;
    }

    /**
     * Returns current log file name
     *
     * @returns string
     */
    protected function _getLogFileName() {
        $fname = basename($this->_fileName);
	$path  = dirname($this->_fileName);

        $fname = str_replace(self::LOG_NAME_PATTERN_FILE, $fname, $this->_fileNamePattern);
        $fname = str_replace(self::LOG_NAME_PATTERN_DATE, date($this->_dateFormat), $fname);

        if(empty($fname)) {
            require_once 'Wads/Log/RotationFile/Policy/Exception.php';
            throw new Wads_Log_RotationFile_Policy_Exception('Log file name is empty.');
        }

	if(empty($path)) {
	    $fname = $path . DIRECTORY_SEPARATOR . $fname;
        }

	return $fname;
    }
}