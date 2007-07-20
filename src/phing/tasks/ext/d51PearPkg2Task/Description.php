<?php

require_once 'phing/tasks/ext/d51PearPkg2Task/TextContainer.php';

class d51PearPkg2Task_Description extends d51PearPkg2Task_TextContainer
{
    private $_indentions_to_remove = '4';
    private $_indention_type = 'space';
    
    public function __get($key)
    {
        switch ($key) {
            case 'indentions_to_remove' :
                return $this->_indentions_to_remove;
            
            case 'indention_type' :
                if ($this->_indention_type = 'tab') {
                    return "\t";
                } else {
                    return ' ';
                }
        }
        
        return parent::__get($key);
    }
    
    public function setIndentions_To_Remove($value)
    {
        $this->_indentions_to_remove = (int)$value;
    }
    
    public function setIndention_type($type)
    {
        $this->_indention_type = $type;
    }
}