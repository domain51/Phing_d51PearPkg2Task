<?php

class d51PearPkg2Task_Release_Install
{
    private $_as = null;
    private $_name = null;
    
    public function setAs($as)
    {
        $this->_as = $as;
    }
    
    public function setName($name)
    {
        $this->_name = $name;
    }
    
    public function __get($key)
    {
        switch ($key) {
            case 'as' :
            case 'name' :
                $real_key = "_{$key}";
                return $this->$real_key;
        }
    }
}