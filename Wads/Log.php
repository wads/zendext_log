<?php
 /**
  * Wads Library
  *
  * @category   Wads
  * @package    Wads_Log
  * @copyright  Copyright (c) 2008 wads. (wads@gmail.com)
  * @license    http://framework.zend.com/license/new-bsd     New BSD License
  */

/** Zend_Log */
require_once 'Zend/Log.php';

/** Zend_Loader */
require_once 'Zend/Loader.php';

 /**
  * Logger class
  *
  * @category   Wads
  * @package    Wads_Log
  * @copyright  Copyright (c) 2008 wads. (wads@gmail.com)
  */
class Wads_Log
{
    /**
     * Loaded log file configure
     *
     * @var array
     */
    private $_logConfig = null;

    /**
     * Configrue file path for log file
     *
     * @var string
     */
    //private $_logConfigFile = APP_ROOT_DIR . '/conf/xml/log_config.xml';
    private $_logConfigFile = null;

    /**
     * Default log file configure.
     *
     * @var array
     */
    private $_defaultLogConfig = array(
                'file' => array(
                    'path' => '/tmp/wads_log_file.log',
                    'permission' => '0666'
                ),
                'priority' => 'info'
            );
    
    private function __construct($type, array $opt=null) {
        
        // check option
        if($opt !== null) {
            $this->_checkOptions($opt);
        }

        try {
            // load log file config
            $this->_loadLogConfig();
            //$conf = new Zend_Config_Xml($this->_logConfig, $type);

            //if(empty($conf->rotate)) {
            //    $conf->rotate->type = 'default';
            //}
            //$logRotateClass = Wads_Log_Rotate::factory($conf->rotate);
            //$logFileClass = Wads_Log_File::factory($conf);
            $logFileClass = Wads_Log_File::getInstance($this->_logConfig);

            // 以下は各ファイル、ローテートクラスでやっているはず
            //$date = date('Ym');
            //$auth_file			= sprintf(self::LOG_FILEPATH_AUTH, $date);

            // 失敗したらthrow Exception
            //if (!file_exists($auth_file)) {
			//touch($auth_file); 
			//chmod($auth_file, 0666);
            //}

            // create log writer class
            $logWriterClass = new Zend_Log_Writer_Stream($logFileClass->getFileName());

            // create log class
            $logClass = new Zend_Log($logWriterClass);

            // create log filter class
            if(!isset($this->_logConfig['priority'])) {
                $this->_logConfig['priority'] = 'DEBUG';
            }
            
            $constName = 'Zend_Log::' . strtoupper($this->_logConfig['priority']);
            if(($priority = constant($constName)) === null) {
                $priority = constant('Zend_Log::DEBUG');
            }
            
            $logFilterClass = new Zend_Log_Filter_Priority($priority);
            $logClass->addFilter($logFilterClass);
            
    	} catch(Exception $e) {
            require_once 'Wads/Exception.php';
            throw new Wads_Exception($e->getMessage());
        }

        return $logClass;
	}

    /**
     * Disallow copying
     */
    private function __clone()
    {
    }

	/**
	 * Returns Specified Log object
	 *
     * @param string $type
     * @param array $opt
	 * @return Zend_Log
	 */
    public static function getInstance($type, array $opt = null) {
        // instances
	static $log = array();

        // normalize argument
        if(!is_string($type)) {
            if(is_scalar($type)) {
                $type = (string)$type;
            } else {
                require_once 'Wads/Exception.php';
                throw new Wads_Exception('Argument $type is need to be string.');
            }
        }

        if(!isset($log[$type])) {
             $log[$type] = new Wads_Log($type, $opt);
	}
        
	return $log[$type];
    }

    /**
     * check options
     *
     * @param array $opt
     */
    private function _checkOptions(array $opt) {
        foreach($opt as $key => $val) {
            switch($key) {
            case 'conf_file':
                $this->_logConfig = $val;
                break;
            default:
            }
        }
    }

    /**
     * load log config from file or array
     */
    private function _loadogConfig() {
        if($this->_logConfigFile !== null) {
            $this->_logConfig = new Zend_Config_Xml($this->_logConfig, $type);
            $this->_logConfig->toArray();
        }

        if($this->_logConfig === null) {
            $this->_logConfig = $this->_defaultLogConfig;
        }
    }
}
