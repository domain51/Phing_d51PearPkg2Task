<?php
/**
 * This file contains {@link d51PearPkg2Task_Release}
 * 
 * 
 * @license http://www.gnu.org/licenses/lgpl.html LGPL
 * 
 * @package phing.tasks.ext
 * 
 * @subpackage d51pearkpkg2
 * 
 */
/**
 * Load required classes
 * @ignore
 */
require_once 'phing/tasks/ext/d51PearPkg2Task/Release/Install.php';

/**
 * Object to handle <release> elements
 *
 * @see d51PaerPkg2Task::createRelease()
 */
class d51PearPkg2Task_Release
{
    private $_install = array();
    
    /**
     * Handle instantiation
     * 
     * @ignore
     */
    public function __construct()
    {
        
    }
    
    /**
     * Creates a {@link d51PearPkg2Task_Release_Install} object to
     * handle <install> elements
     *
     * @return d51PearPkg2Task_Release_Install
     */
    public function createInstall()
    {
        $install = new d51PearPkg2Task_Release_Install();
        $this->_install[] = $install;
        return $install;
    }
    
    
    /**
     * Magic method to expose following properties as read-only:
     * 
     * install: Contains an array of <install> elements
     *
     * @return mixed
     */
    public function __get($key)
    {
        switch ($key) {
            case 'install' :
                $real_key = "_{$key}";
                return $this->$real_key;
        }
    }
}
