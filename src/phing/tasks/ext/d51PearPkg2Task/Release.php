<?php

require_once 'phing/tasks/ext/d51PearPkg2Task/Release/Install.php';

class d51PearPkg2Task_Release
{
    private $_install = array();
    public function __construct()
    {
        
    }
    
    public function createInstall()
    {
        $install = new d51PearPkg2Task_Release_Install();
        $this->_install[] = $install;
        return $install;
    }
    
    public function __get($key)
    {
        switch ($key) {
            case 'install' :
                $real_key = "_{$key}";
                return $this->$real_key;
        }
    }
}
