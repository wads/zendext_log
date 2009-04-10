<?php

require_once 'Zend/Filter/Input.php';

class Wads_Filter_Input extends Zend_Filter_Input
{
    private $_options = array(
        'breakChainOnFailure' => true,
        'inputNamespace'      => 'Wads_Validate'
    );

    public function __construct($filterRules, $validatorRules, array $data = null, array $options = null) {
        if($options === null) {
            $options = $this->_options;
        }
        parent::__construct($filterRules, $validatorRules, $data, $options);
    }
    
    public function getErrorMessages($messages = null) {
        if($messages === null) {
            $messages = $this->getMessages();
        }
    
        $err_msg = array();
        foreach($messages as $name=>$msg) {
            unset($this->_form[$name]);
            $err_msg[] = current($msg);
        }
        
        return $err_msg;
    }
}
