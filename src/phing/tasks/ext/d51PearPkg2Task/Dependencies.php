<?php

require_once 'phing/tasks/ext/d51PearPkg2Task/Dependencies/Extension.php';
require_once 'phing/tasks/ext/d51PearPkg2Task/Dependencies/Group.php';
require_once 'phing/tasks/ext/d51PearPkg2Task/Dependencies/Package.php';

class d51PearPkg2Task_Dependencies
{
    private $_groups = array();
    private $_packages = array();
    private $_extensions = array();
    
    public function __construct()
    {
        
    }
    
    public function __get($key)
    {
        switch ($key) {
            case 'groups' :
                return $this->_groups;
            case 'packages' :
                return $this->_packages;
            case 'extensions' :
                return $this->_extensions;
        }
    }
    
    public function createPackage()
    {
        $package = new d51PearPkg2Task_Dependencies_Package();
        $this->_packages[] = $package;
        return $package;
    }
    
    public function createExtension()
    {
        $extension = new d51PearPkg2Task_Dependencies_Extension();
        $this->_extensions[] = $extension;
        return $extension;
    }
    
    public function createGroup()
    {
        $group = new d51PearPkg2Task_Dependencies_Group();
        $this->_groups[] = $group;
        return $group;
    }
    
}