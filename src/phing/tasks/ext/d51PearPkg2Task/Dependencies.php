<?php

require_once 'phing/tasks/ext/d51PearPkg2Task/Dependencies/Extension.php';
require_once 'phing/tasks/ext/d51PearPkg2Task/Dependencies/Group.php';
require_once 'phing/tasks/ext/d51PearPkg2Task/Dependencies/Package.php';
require_once 'phing/tasks/ext/d51PearPkg2Task/Dependencies/PHP.php';
require_once 'phing/tasks/ext/d51PearPkg2Task/Dependencies/PEAR.php';

class d51PearPkg2Task_Dependencies
{
    private $_groups = array();
    private $_packages = array();
    private $_extensions = array();
    private $_php = false;
    private $_pear = false;
    
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
            case 'php' :
                return $this->_php;
            case 'pear' :
                return $this->_pear;
        }
    }
    
    public function createPackage()
    {
        $package = new d51PearPkg2Task_Dependencies_Package();
        $this->_packages[] = $package;
        return $package;
    }
    
    public function createPHP()
    {
        $php = new d51PearPkg2Task_Dependencies_PHP();
        $this->_php = $php;
        return $php;
    }
    
    public function createPEAR()
    {
        $pear = new d51PearPkg2Task_Dependencies_PEAR();
        $this->_pear = $pear;
        return $pear;
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