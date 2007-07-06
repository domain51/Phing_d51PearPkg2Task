<?php

/**
 *
 * @see d51PearPkg2
 * @todo set version to default version of package
 */
class d51PearPkg2Task_Changelog
{
    private $_package = null;
    
    private $_version = null;
    private $_api_version = null;
    private $_stability = null;
    private $_date = null;
    private $_license = null;
    private $_contents = '';
    
    public function setVersion($version)
    {
        $this->_version = $version;
    }
    
    public function setApi_version($api_version)
    {
        $this->_api_version = $api_version;
    }
    
    public function setStability($stability)
    {
        $this->_stability = $stability;
    }
    
    public function setDate($date)
    {
        $this->_date = date('Y-m-d', strtotime($date));
    }
    
    public function setLicense($license)
    {
        $this->_license = $license;
    }
    
    public function setContents($contents)
    {
        $this->addText($contents);
    }
    
    public function addText($contents)
    {
        $this->_contents = $contents;
    }
    
    public function __get($key)
    {
        static $valid = array(
            'version',
            'api_version',
            'stability',
            'license',
            'date',
            'contents',
        );
        
        if (in_array($key, $valid)) {
            $real_key = '_' . $key;
            return $this->$real_key;
        }
    }
    
    public function __set($key, $value)
    {
        switch ($key) {
            case 'package' :
                if (!$value instanceof PEAR_PackageFile_v2) {
                    throw new d51PearPkg2Task_Exception(
                        'package must be of a PEAR_PackageFile_v2 type'
                    );
                }
                $this->_package = $value;
                break;
        }
    }
    
    /**
     */
    public function toArray()
    {
        if (is_null($this->_package)) {
            throw new d51PearPkg2Task_Exception(
                'setPackage() must be called on changelog prior to creating the changelog array'
            );
        }
        
        return array(
            'version' =>
                array(
                    'release' => is_null($this->version) ?
                        $this->_package->getVersion() :
                        $this->version,
                    'api' => is_null($this->api_version) ?
                        $this->_package->getVersion('api') :
                        $this->api_version,
                ),
            'stability' => is_null($this->stability) ?
                $this->_package->getStability() :
                $this->stability,
            'date' => is_null($this->date) ?
                $this->_package->getDate() :
                $this->date,
            'license' => is_null($this->license) ?
                $this->_package->getLicense(true) :
                $this->license,
            'notes' => $this->contents,
        );
    }
}
