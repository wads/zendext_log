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
class Wads_Log_RotationFile_Policy_Size extends Wads_Log_RotationFile_Policy_Abstract
{
    /**
     * Max log file size(default 10M)
     *
     * @var int
     */
    protected $_maxFileSize = 10485760;

    /**
     * Max backup index
     *
     * @var int
     */
    protected $_maxBackupIndex = 1;
    
    /**
     * constructor
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
                $this->setFileName($val);
                break;
            case 'size':
                $this->setMaxFileSize($val);
                break;
            case 'backupindex':
                $this->setMaxBackupIndex($val);
                break;
            default:
                break;
            }
        }
    }

    /**
     * Sets max file size
     *
     * @param int $size
     */
    public function setMaxFileSize($size) {
        $maxFileSize = null;
        $numpart = substr($size,0, strlen($size) -2);
        $suffix  = strtoupper(substr($size, -2));

        switch ($suffix) {
            case 'KB':
                $maxFileSize = (int)((int)$numpart * 1024);
                break;
            case 'MB':
                $maxFileSize = (int)((int)$numpart * 1024 * 1024);
                break;
            case 'GB':
                $maxFileSize = (int)((int)$numpart * 1024 * 1024 * 1024);
                break;
            default:
                if (is_numeric($size)) {
                    $maxFileSize = (int)$size;
                }
        }
        
        if ($maxFileSize !== null && $maxFileSize > 0) {
            $this->_maxFileSize = $maxFileSize;
        }
    }

    /**
     * Returns max file size
     *
     * @return int
     */
    public function getMaxFileSize() {
        return $this->_maxFileSize;
    }

    /**
     * Sets max backup index
     *
     * @param int $maxBackupIndex
     */
    public function setMaxBackupIndex($maxBackupIndex) {
        if (is_numeric($maxBackupIndex) && $maxBackupIndex >= 0) {
            $this->_maxBackupIndex = (int)$maxBackupIndex;
        }
    }

    /**
     * Returns max backup index
     *
     * @return int
     */
    public function getMaxBackupIndex() {
        return $this->_maxBackupIndex;
    }
    
    /**
     * Implements abstract function.
     *
     * @param string $logFile
     * @return boolean
     */
    public function trigger() {
        if($this->_fileName === null) {
            require_once 'Wads/Log/RotationFile/Policy/Exception.php';
            throw new Wads_Log_RotationFile_Policy_Exception('Trriger error because the log file name is unknown.');
        }
                
        clearstatcache();
        
        if(!file_exists($this->_fileName)) {
            return false;
        }

        // If file size over 2GB, this result may not be corrected. 
        return ($this->_maxFileSize <= filesize($this->_fileName));
    }
    
    /**
     * Implements abstract function.
     *     
     * @return string
     * @throws Wads_Log_RotationFile_Policy_Exception
     */
    public function rollOver() {
        if($this->_fileName === null) {
            require_once 'Wads/Log/RotationFile/Policy/Exception.php';
            throw new Wads_Log_RotationFile_Policy_Exception('Roleover error because the log file name is unknown.');
        }

        return $this->_rollOver();

    }

    protected function _rollOver() {
        clearstatcache();
        
        if($this->_maxBackupIndex > 0) {
            // roll over current log file
            $file = $this->_fileName . '.' . $this->_maxBackupIndex;
            if(file_exists($file)) {
                unlink($file);
            }

            for($i = $this->_maxBackupIndex - 1; $i > 0; $i--) {
                $file = $this->_fileName . "." . $i;
                if(is_readable($file)) {
                    $target = $this->_fileName . '.' . ($i+1);
                    rename($file, $target);
                }
            }

            // rename current log file
            $target = $this->_fileName . '.1';
            rename($this->_fileName, $target);
        }
        
        if(file_exists($this->_fileName)) {
            unlink($this->_fileName);
        }
                
        return $this->_fileName;
    }
}
