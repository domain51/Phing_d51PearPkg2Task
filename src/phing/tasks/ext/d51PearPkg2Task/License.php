<?php
/**
 * This file contains {@link d51PearPkg2Task_License}
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
 * Acts as the container for stability elements within a d51pearpkg2 task
 *
 * @see d51PearPkg2, d51PearPkg2::createLicense()
 */
class d51PearPkg2Task_License
{
    private $_uri = null;
    private $_license = null;
    private $_known_license_uris = array(
        'GPL' => 'http://www.gnu.org/copyleft/gpl.html',
        'LGPL' => 'http://www.gnu.org/licenses/lgpl.html',
        'MIT' => 'http://www.opensource.org/licenses/mit-license.php',
        'New BSD' => 'http://www.opensource.org/licenses/bsd-license.php',
        'PHP License' => 'http://www.php.net/license/3_01.txt',
    );
    
    /**
     * Handles the uri attribute
     *
     * @param string $uri
     */
    public function setUri($uri)
    {
        $this->_uri = $uri;
    }
    
    /**
     * This allows for &lt;license license="mylicense" /&gt;
     *
     * This mehthod will attempt to guess map the requested license with a known URI.  To insure
     * the proper license URI is used, however, it is suggested that you specify the uri attribute.
     *
     * @param string $license
     */
    public function setLicense($license)
    {
        $this->_license = $license;
        
        // only attempt to set the URI if none has been set
        if (!is_null($this->_uri)) {
            return;
        }
        
        if (isset($this->_known_license_uris[$license])) {
            $this->setUri($this->_known_license_uris[$license]);
        }
    }
    
    /**
     * This allows for <license>My License</license>
     *
     * @param string $license
     *
     * @see setLicense()
     */
    public function addText($license)
    {
        $this->setLicense($license);
    }
    
    /**
     * This allows public, read-only access to the license and uri properties
     *
     * @param string $key Either 'license' or 'uri'
     *
     * @return string
     */
    public function __get($key)
    {
        switch ($key) {
            case 'license' :
                return $this->_license;
            
            case 'uri' :
                return $this->_uri;
        }
    }
}
