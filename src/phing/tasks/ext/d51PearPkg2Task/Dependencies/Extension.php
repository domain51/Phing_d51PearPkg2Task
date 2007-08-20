<?php

/**
 * @todo automatically assume this package's channel is the same as the main channel
 */
class d51PearPkg2Task_Dependencies_Extension
{
    private $_name = '';
    private $_type = 'required';
    private $_minimum_version = false;
    private $_maximum_version = false;
    private $_recommended_version = false;
    private $_exclude_version = false;
    
    public function __construct()
    {
        
    }
    
    public function __get($key)
    {
        static $valid = array(
            'name',
            'type',
            'minimum_version',
            'maximum_version',
            'recommended_version',
            'exclude_version',
        );
        
        if (in_array($key, $valid)) {
            $real_key = '_' . $key;
            return $this->$real_key;
        }
    }
    
    public function setName($name)
    {
        $this->_name = $name;
    }
    
    public function setType($type)
    {
        $this->_type = $type;
    }
    
    public function setMinimum_version($minimum_version)
    {
        $this->_minimum_version = $minimum_version;
    }
    
    public function setMaximum_version($maximum_version)
    {
        $this->_maximum_version = $maximum_version;
    }
    
    public function setRecommended_version($recommended_version)
    {
        $this->_recommended_version = $recommended_version;
    }
    
    public function setExclude_versions($exclude_version)
    {
        $this->_exclude_version = explode(',', $exclude_version);
    }
}