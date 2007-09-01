<?php
/**
 * This file contains {@link d51PearPkg2Task_Frontend}
 * 
 * @license 	http://www.gnu.org/licenses/lgpl.html LGPL
 * @package		phing.tasks.ext
 * @subpackage 	d51pearkpkg2
 * 
 */

/**
 * Load super class
 * @ignore
 */
require_once 'PEAR/Frontend.php';

/**
 * Serves as an interface to the PEAR packager's frontend to capture
 * any output and redirect it through the phing log object.
 */
class d51PearPkg2Task_Frontend extends PEAR_Frontend
{
    private $_caller;
    
    /**
     * Handle instantiation
     * 
     * @param d51PearPkg2Task $caller
     */
    public function __construct(d51PearPkg2Task $caller)
    {
        $this->_caller = $caller;
    }
    
    /**
     * Logs any output via the caller
     * 
     * @param string $msg
     */
    public function log($msg)
    {
        $this->_caller->log($msg);
    }
}
