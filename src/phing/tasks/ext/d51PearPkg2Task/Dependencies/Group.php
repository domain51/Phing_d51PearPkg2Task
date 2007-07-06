<?php

require_once 'phing/tasks/ext/d51PearPkg2Task/Dependencies/Package.php';

class d51PearPkg2Task_Dependencies_Group
{
    private $_name = '';
    private $_hint = '';
    private $_packages = array();
    
    public function __construct()
    {
        
    }
    
    public function __get($key)
    {
        switch ($key) {
            case 'name' :
                return $this->_name;
            
            case 'hint' :
                return $this->_hint;
            
            case 'packages' :
                return $this->_packages;
        }
    }
    
    public function createPackage()
    {
        $package = new d51PearPkg2Task_Dependencies_Package();
        $this->_packages[] = $package;
        return $package;
    }
    
    public function setName($name)
    {
        $this->_name = $name;
    }
    
    public function setHint($hint)
    {
        $this->_hint = $hint;
    }
    
}