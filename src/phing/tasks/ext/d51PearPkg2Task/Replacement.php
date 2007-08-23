<?php
/**
 * This file contains {@link d51PearPkg2Task_Replacement} and
 * {@link d51PearPkg2Task_Replacement_MissingAttributeException}
 *
 *
 * @license http://www.gnu.org/licenses/lgpl.html LGPL
 * 
 * @package phing.tasks.ext
 * 
 * @subpackage d51pearkpkg2
 */

/**
 * Acts as a container for &lt;replacement> elements within &lt;d51pearpkg2>
 */
class d51PearPkg2Task_Replacement extends ProjectComponent
{
    private $_path = null;
    private $_type = null;
    private $_from = null;
    private $_to = null;
    
    public function __construct()
    {
        
    }
    
    /**
     * Handle path attribute
     *
     * @param string $path
     */
    public function setPath($path)
    {
        $this->_path = $path;
    }
    
    
    /**
     * Handle type attribute
     *
     * @param string $type
     */
    public function setType($type)
    {
        $this->_type = $type;
    }
    
    
    /**
     * Handle from attribute
     *
     * @param string $from
     */
    public function setFrom($from)
    {
        $this->_from = $from;
    }
    
    
    /**
     * Handle to attribute
     *
     * @param string $to
     */
    public function setTo($to)
    {
        $this->_to = $to;
    }
    
    
    /**
     * Returns true if this &lt;replacement> element has been properly
     * initialized, otherwise false.
     *
     * @return bool
     */
    public function isValid()
    {
        $failed = array();
        $required_attributes = array('path', 'type', 'from', 'to');
        foreach ($required_attributes as $required_attribute) {
            $real_key = "_{$required_attribute}";
            if (is_null($this->$real_key)) {
                $this->log("{$required_attribute} attribute not set");
                $failed[] = $required_attribute;
            }
        }
        
        if (count($failed) > 0) {
            throw new d51PearPkg2Task_Replacement_MissingAttributeException(
                implode(', ', $failed) . ' attributes not set'
            );
        }
    }
    
    
    /**
     * Magic method to expose properties as read-only, public properties
     *
     *
     * @param string $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        switch ($key) {
            case 'path' :
            case 'type' :
            case 'from' :
            case 'to' :
                $key = "_{$key}";
                return $this->$key;
        }
    }
    
    
    /**
     * Log a $message with "[d51pearpkg2-replacement]" prefix
     *
     * @param string $message
     */
    public function log($message)
    {
        parent::log('[d51pearpkg2-replacement] ' . $message);
    }
}

/**
 * Exception to throw when an attribute is missing
 */
class d51PearPkg2Task_Replacement_MissingAttributeException extends Exception { }