<?php
class Lib_Router
{
    public function setup($conf_file) {
        $front = Zend_Controller_Front::getInstance();
        $conf  = new Zend_Config_Xml($conf_file);
        
        foreach($conf as $name=>$value) {
            try {
                $route = $this->_getLoad($value);
            } catch(Exception $e) {
                throw new Exception('Failed Setup Router : ' . $e->getMessage());
            }
            $front->getRouter()->addRoute($name, $route);
        }
    }
    
    private function _getLoad(Zend_Config $conf) {
        $defaults = array();
        $reqs = array();
        
        if(!empty($conf->defaults)) {
            foreach($conf->defaults as $name=>$value) {
                $defaults[$name] = $value;
            }
        }
        if(!empty($conf->reqs)) {
            foreach($conf->reqs as $name=>$index) {
                $reqs[$index] = $name;
            }
        }
        
        $router_class = 'Zend_Controller_Router_Route';
        if(!empty($conf->class)) {
            $router_class .= "_{$conf->class}";
        }
        
        Zend_Loader::loadClass($router_class);
        return new $router_class($conf->rule, $defaults, $reqs);
    }
}