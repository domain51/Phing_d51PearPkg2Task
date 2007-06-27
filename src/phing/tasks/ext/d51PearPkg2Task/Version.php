<?php
/**
 * This file contains {@link d51PearPkg2_Version}
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
 * Acts as the container for version elements within a d51pearpkg2 task
 *
 * @see d51PearPkg2
 */
class d51PearPkg2_Version
{
    private $_release = null;
    private $_api = null;
    
    /**
     * Handles the release attribute
     *
     * @param string $release
     */
    public function setRelease($release)
    {
        $this->_release = $release;
    }
    
    /**
     * Handles the API attribute
     *
     * @param string $api
     */
    public function setApi($api)
    {
        $this->_api = $api;
    }
    
    /**
     * Allows for release and api to accessed as public properties
     *
     * @param string $key Either 'release' or 'api'
     *
     * @return string
     */
    public function __get($key)
    {
        switch($key) {
            case 'release' :
                return $this->_release;
            
            case 'api' :
                return $this->_api;
        }
    }
}
