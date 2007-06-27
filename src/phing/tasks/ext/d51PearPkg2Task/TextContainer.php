<?php
/**
 * This file contains {@link d51PearPkg2Task_TextContainer}
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
 * This holds simple string data that is present as the nodeValue of any element
 *
 * @see d51PearPkg2
 */
class d51PearPkg2Task_TextContainer
{
    private $_value = '';
    
    /**
     * Handles the nodeValue portion of any element
     *
     * @param string $value
     */
    public function addText($value)
    {
        $this->_value = $value;
    }
    
    /**
     * Returns the value this object represents
     *
     * @return string
     */
    public function __toString()
    {
        return $this->_value;
    }
}
