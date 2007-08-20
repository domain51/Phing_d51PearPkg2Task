<?php

class d51PearPkg2Task_Dependencies_PHP
{
    private $_minimum_version = false;
    private $_maximum_version = false;
    private $_exclude_version = false;
    
    public function __construct()
    {
        
    }
    
    public function __get($key)
    {
        static $valid = array(
            'minimum_version',
            'maximum_version',
            'exclude_version',
        );
        
        if (in_array($key, $valid)) {
            $real_key = '_' . $key;
            return $this->$real_key;
        }
    }
    
    public function setMinimum_version($minimum_version)
    {
        $this->_minimum_version = $minimum_version;
    }
    
    public function setMaximum_version($maximum_version)
    {
        $this->_maximum_version = $maximum_version;
    }
    
    public function setExclude_versions($exclude_version)
    {
        $this->_exclude_version = explode(',', $exclude_version);
    }
}