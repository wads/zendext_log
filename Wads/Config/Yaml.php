<?php
/**
 * @see Zend_Config.php
 */
require_once 'Zend/Config.php';

/**
 * @copyright  wads (wadslab@gmail.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Wads_Config_Yaml extends Zend_Config
{
    /**
     * String that symbol extended section
     */
    protected $_extendsSection = ';extends';
 
    /**
     * constructor
     *
     * @param string $filename
     * @param mixed $section
     * @param boolean $allowModifications
     * @throws Zend_Config_Exception
     */
    public function __construct($filename, $section = null, $allowModifications = false)
    {
        if (empty($filename)) {
            /** @see Zend_Config_Exception */
            require_once 'Zend/Config/Exception.php';
            throw new Zend_Config_Exception('Filename is not set');
        }
 
        $yamlArray = $this->_parse_yaml_file($filename);
 
        $preProcessedArray = array();
        foreach ($yamlArray as $key => $data)
        {
            $bits = explode('< ', $key);
            $numberOfBits = count($bits);
            $thisSection = trim($bits[0]);
            switch (count($bits)) {
                case 1:
                    $preProcessedArray[$thisSection] = $data;
                    break;
 
                case 2:
                    $extendedSection = trim($bits[1]);
                    if(!is_array($data)) {
                        $data = array($data);
                    }
                    $preProcessedArray[$thisSection] = array_merge(array($this->_extendsSection=>$extendedSection), $data);
                    break;
 
                default:
                    /** @see Zend_Config_Exception */
                    require_once 'Zend/Config/Exception.php';
                    throw new Zend_Config_Exception("Section '$thisSection' may not extend multiple sections in $filename");
            }
        }
 
        if (null === $section) {
            $dataArray = array();
            foreach ($preProcessedArray as $sectionName => $sectionData) {
                $dataArray[$sectionName] = $this->_processExtends($preProcessedArray, $sectionName);
            }
            parent::__construct($dataArray, $allowModifications);
        } elseif (is_array($section)) {
            $dataArray = array();
            foreach ($section as $sectionName) {
                if (!isset($preProcessedArray[$sectionName])) {
                    /** @see Zend_Config_Exception */
                    require_once 'Zend/Config/Exception.php';
                    throw new Zend_Config_Exception("Section '$sectionName' cannot be found in $filename");
                }
                $dataArray = array_merge($this->_processExtends($preProcessedArray, $sectionName), $dataArray);
 
            }
            parent::__construct($dataArray, $allowModifications);
        } else {
            if (!isset($preProcessedArray[$section])) {
                /** @see Zend_Config_Exception */
                require_once 'Zend/Config/Exception.php';
                throw new Zend_Config_Exception("Section '$section' cannot be found in $filename");
            }
            parent::__construct($this->_processExtends($preProcessedArray, $section), $allowModifications);
        }
 
        $this->_loadedSection = $section;
    }
 
    /**
     * Loads a YAML file and parse data to PHP array
     *
     * @param string $filename
     * @throws Zend_Config_Exception
     * @return array
     */
    protected function _parse_yaml_file($filename)
    {
        if (extension_loaded('syck') && in_array('syck_load', get_extension_funcs('syck'))) {
            $yamlArray = syck_load(file_get_contents($filename));
            return $yamlArray;
        } elseif (class_exists('spyc') && is_callable(array('Spyc', 'YAMLLoad'))) {
            $yamlArray = Spyc::YAMLLoad($filename);
            return $yamlArray;
        }
 
        require_once 'Zend/Config/Exception.php';
        throw new Zend_Config_Exception('YAML loader function not found');
    }
 
 
    /**
     * Helper function to process each element in the section and handle
     * the "extends" inheritance keyword.
     *
     * @param array $yamlArray
     * @param string $section
     * @param array $config
     * @throws Zend_Config_Exception
     * @return array
     */
    protected function _processExtends($yamlArray, $section, $config = array())
    {
        $thisSection = $yamlArray[$section];
 
        if(!is_array($thisSection)) {
            return $thisSection;
        }
 
        foreach ($thisSection as $key => $value) {
            if (strtolower($key) == $this->_extendsSection) {
                if (isset($yamlArray[$value])) {
                    $this->_assertValidExtend($section, $value);
                    $config = $this->_processExtends($yamlArray, $value, $config);
                } else {
                    /** @see Zend_Config_Exception */
                    require_once 'Zend/Config/Exception.php';
                    throw new Zend_Config_Exception("Section '$section' cannot be found");
                }
            } else {
                if(is_int($key) && array_key_exists($key, $config)) {
                    $config[] = $value;
                } else {
                    $config[$key] = $value;
                }
            }
        }
        return $config;
    }
}