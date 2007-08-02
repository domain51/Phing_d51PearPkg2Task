<?php

class d51PearPkg2Task_Replacement extends ProjectComponent
{
    private $_path = null;
    private $_type = null;
    private $_from = null;
    private $_to = null;
    
    public function __construct()
    {
        
    }
    
    public function setPath($path)
    {
        $this->_path = $path;
    }
    
    public function setType($type)
    {
        $this->_type = $type;
    }
    
    public function setFrom($from)
    {
        $this->_from = $from;
    }
    
    public function setTo($to)
    {
        $this->_to = $to;
    }
    
    public function isValid()
    {
        $failed = array();
        $required_attributes = array('path', 'type', 'from', 'to');
        foreach ($required_attributes as $required_attribute) {
            $real_key = "_{$required_attribute}";
            if (is_null($this->$real_key)) {
                $this->log("{$required_attribute} attribute not set");
                $failed[] = $required_attribute;
            }
        }
        
        if (count($failed) > 0) {
            throw new d51PearPkg2Task_Replacement_MissingAttributeException(
                implode(', ', $failed) . ' attributes not set'
            );
        }
    }
    
    public function __get($key)
    {
        switch ($key) {
            case 'path' :
            case 'type' :
            case 'from' :
            case 'to' :
                $key = "_{$key}";
                return $this->$key;
        }
    }
    
    public function log($message)
    {
        parent::log('[d51pearpkg2-replacement] ' . $message);
    }
}

class d51PearPkg2Task_Replacement_MissingAttributeException extends Exception { }