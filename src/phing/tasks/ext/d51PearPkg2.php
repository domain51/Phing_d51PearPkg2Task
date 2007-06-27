<?php
/**
 * This file contains {@link d51PearPkg2}
 */

require_once 'PEAR/PackageFileManager2.php';

class d51PearPkg2 extends Task
{
    private $_name = null;
    private $_summary = null;
    private $_description = null;
    private $_channel = null;
    private $_maintainers = array();
    private $_date = null;
    private $_license = null;
    private $_version = null;
    private $_stability = null;
    private $_release = null;
    private $_notes = null;
    
    private $_directory = null;
    private $_options = array(
        'baseinstalldir' => null,
        'packagedirectory' => null,
        'filelistgenerator' => null,
        'ignore' => array(),
        'installexceptions' => array(),
        'dir_roles' => array(),
        'exceptions' => array(),
    );
    
    
    
    /**
     * Handles initialization of this task
     */
    public function init()
    {
        $this->createDate()->addText(date('Y-m-d'));
    }
    
    /**
     * Main entry point for d51pearpkg2 task
     */
    public function main()
    {
        $package = new PEAR_PackageFileManager2();
        $this->_initOptions();
        $package->setOptions($this->_options);
        
        $package->setPackage((string)$this->_name);
        $package->setSummary((string)$this->_summary);
        
        // TODO: make these less hacky - this is what I come up with at 9:30 at night
        $desc = str_replace("\n", '\n', (string)$this->_description);
        $desc = preg_replace('/\s{2,}/', '', $desc);
        $desc = str_replace('\n', ' ', $desc);
        $package->setDescription($desc);
        
        $package->setChannel((string)$this->_channel);
        $package->setAPIVersion($this->_version->api);
        $package->setReleaseVersion($this->_version->release);
        $package->setAPIStability($this->_stability->api);
        $package->setReleaseStability($this->_stability->release);
        
        $notes = str_replace("\n", '\n', (string)$this->_notes);
        $notes = preg_replace('/\s{2,}/', '', $notes);
        $notes = str_replace('\n', ' ', $notes);
        
        $package->setNotes($notes);
        // TODO: allow different types
        $package->setPackageType('php');
        $package->addRelease();
        $package->setPhpDep('5.2.3');
        $package->setPearinstallerDep('1.6.0');
        
        foreach ($this->_maintainers as $maintainer) {
            $package->addMaintainer(
                $maintainer->role,
                $maintainer->user,
                $maintainer->name,
                $maintainer->email,
                $maintainer->active
            );
        }
        
        $package->setLicense($this->_license->license, $this->_license->uri);
        $package->generateContents();
        $e = $package->writePackageFile();
        if (PEAR::isError($e)) {
            throw new d51PearPkg2_Exception(
                'unable to write package.xml file: ' . $e->getMessage()
            );
        }
    }
    
    private function _initOptions()
    {
        if (empty($this->_options['baseinstalldir'])) {
            $this->setBaseInstallDir($this->_name);
        }
        
        $this->_options['packagedirectory'] = $this->_directory->getAbsolutePath();
        
        if (empty($this->_options['filelistgenerator'])) {
            $this->_options['filelistgenerator'] = 'file';
        }
        
        // setup the ignore array in the anticipated format
        foreach ($this->_options['ignore'] as $key => $ignore) {
            $this->_options[$key] = (string)$ignore;
        }
        
        // setup all KeyedContainer objects as associative arrays
        $keyed_values = array(
            'installexceptions',
            'dir_roles',
            'exceptions',
        );
        foreach ($keyed_values as $value_name) {
            foreach ($this->_options[$value_name] as $key => $obj) {
                $this->_options[$value_name][$obj->key] = $obj->value;
                unset($this->_options[$value_name][$key]);
            }
        }
        
    }
  
  // 'dir_roles' => array('tutorials' => 'doc'),
  // 'exceptions' => array('README' => 'doc', // README would be data, now is doc
  //                       'PHPLICENSE.txt' => 'doc'))); // same for the license
    
    public function setBaseInstallDir($base_install_dir)
    {
        $this->_options['baseinstalldir'] = $base_install_dir;
    }
    
    public function setDir(PhingFile $dir)
    {
        $this->_directory = $dir;
    }
    
    public function setFileListGenerator($file_list_generator)
    {
        $accepted_values = array('file', 'cvs');
        if (!in_array($file_list_generator, $accepted_values)) {
            throw new d51PearPkg2_Exception(
                'unknown file list generator supplied: ' . $file_list_generator
            );
        }
        
        $this->_options['filelistgenerator'] = $file_list_generator;
    }
    
    public function createInstallExceptions()
    {
        $exception = new d51PearPkg2_KeyedContainer();
        $this->_options['installexceptions'][] = $exception;
        return $exception;
    }
    
    public function createDirRoles()
    {
        $dir_role = new d51PearPkg2_KeyedContainer();
        $this->_options['dir_roles'][] = $dir_roles;
        return $dir_role;
    }
    
    public function createIgnore()
    {
        $ignore = new d51PearPkg2_TextContainer();
        $this->_options['ignore'][] = $ignore;
        return $ignore;
    }
    
    
    /**
     * Handle <name> element.
     *
     * Note that only one <name> element can be present
     *
     * @return d51PearPkg2_TextContainer
     */
    public function createName()
    {
        $this->_insureOnlyOnePresent('name');
        $this->_name = new d51PearPkg2_TextContainer();
        return $this->_name;
    }
    
    /**
     * Handle the <summary> element
     *
     * Note that only one <summary> element can be present
     *
     * @return d51PearPkg2_TextContainer
     */
    public function createSummary()
    {
        $this->_insureOnlyOnePresent('summary');
        $this->_summary = new d51PearPkg2_TextContainer();
        return $this->_summary;
    }
    
    /**
     * Handle the <description> element
     *
     * Note that only one <description> element can be present
     *
     * @return d51PearPkg2_TextContainer
     */
    public function createDescription()
    {
        $this->_insureOnlyOnePresent('description');
        $this->_description = new d51PearPkg2_TextContainer();
        return $this->_description;
    }
    
    public function createChannel()
    {
        $this->_insureOnlyOnePresent('channel');
        $this->_channel= new d51PearPkg2_TextContainer();
        return $this->_channel;
    }
    
    public function createLead()
    {
        $lead = new d51PearPkg2_Maintainer_Lead();
        $this->_maintainers[] = $lead;
        return $lead;
    }
    
    public function createDeveloper()
    {
        $developer = new d51PearPkg2_Maintainer_Developer();
        $this->_maintainers[] = $developer;
        return $developer;
    }
    
    public function createContributor()
    {
        $contributor = new d51PearPkg2_Maintainer_Contributor();
        $this->_maintainers[] = $contributor;
        return $contributor;
    }
    
    public function createHelper()
    {
        $helper = new d51PearPkg2_Maintainer_Helper();
        $this->_maintainers[] = $helper;
        return $helper;
    }
    
    public function createDate()
    {
        $this->_date = new d51PearPkg2_TextContainer();
        return $this->_date;
    }
    
    public function createLicense()
    {
        $this->_license = new d51PearPkg2_License();
        return $this->_license;
    }
    
    public function createVersion()
    {
        $this->_version = new d51PearPkg2_Version();
        return $this->_version;
    }
    
    public function createStability()
    {
        $this->_stability = new d51PearPkg2_Stability();
        return $this->_stability;
    }
    
    public function createNotes()
    {
        $this->_notes = new d51PearPkg2_TextContainer();
        return $this->_notes;
    }
    
    /**
     * Utility method to insure a value is only set once
     *
     * @throws d51PearPkg2_Exception
     */
    private function _insureOnlyOnePresent($key)
    {
        $real_key = "_{$key}";
        if (is_null($this->$real_key)) {
            return;
        }
        
        throw new d51PearPkg2_Exception(
            "<{$key}> can only be called once"
        );
    }
}


class d51PearPkg2_Exception extends Exception { }

class d51PearPkg2_TextContainer
{
    private $_value = '';
    
    public function addText($value)
    {
        $this->_value = $value;
    }
    
    public function __toString()
    {
        return $this->_value;
    }
}

class d51PearPkg2_KeyedContainer
{
    private $_key = '';
    private $_value = '';
    public function setKey($key)
    {
        $this->_key = $key;
    }
    
    public function setValue($value)
    {
        $this->_value = $value;
    }
    
    public function addText($value)
    {
        $this->setValue($value);
    }
    
    public function __get($key)
    {
        switch ($key) {
            case 'key' :
                return $this->_key;
            
            case 'value' :
                return $this->_value;
        }
    }
}


abstract class d51PearPkg2_Iterator implements Iterator
{
    protected $_stack = array();
    public function current()
    {
        return current($this->_stack);
    }
    
    public function key()
    {
        return key($this->_stack);
    }
    
    public function next()
    {
        return next($this->_stack);
    }
    
    public function rewind()
    {
        reset($this->_stack);
    }
    
    public function valid()
    {
        return current($this->_stack) !== false;
    }
}

/**
 * Handle the <maintainer> element within <maintainers>
 *
 * @see d51PearPkg2_Maintainers
 */
class d51PearPkg2_Maintainer 
{
    private $_name = null;
    private $_user = null;
    private $_email = null;
    private $_active = 'yes';
    private $_role = null;
    
    public function setRole($role)
    {
        $this->_role = $role;
    }
    
    public function setName($name)
    {
        $this->_name = $name;
    }
    
    public function setUser($user)
    {
        $this->_user = $user;
    }
    
    public function setEmail($email)
    {
        $this->_email = $email;
    }
    
    public function setActive($active)
    {
        $this->_active = $active;
    }
    
    public function __get($key)
    {
        switch ($key)
        {
            case 'role' :
            case 'name' :
            case 'user' :
            case 'email' :
            case 'active' :
                $real_key = '_' . $key;
                return $this->$real_key;
        }
    }
}

class d51PearPkg2_Maintainer_Lead extends d51PearPkg2_Maintainer
{
    public function __construct()
    {
        $this->setRole('lead');
    }
}

class d51PearPkg2_Maintainer_Developer extends d51PearPkg2_Maintainer
{
    public function __construct()
    {
        $this->setRole('developer');
    }
}

class d51PearPkg2_Maintainer_Contributor extends d51PearPkg2_Maintainer
{
    public function __construct()
    {
        $this->setRole('contributor');
    }
}

class d51PearPkg2_Maintainer_Helper extends d51PearPkg2_Maintainer
{
    public function __construct()
    {
        $this->setRole('helper');
    }
}

class d51PearPkg2_License
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
    
    public function setUri($uri)
    {
        $this->_uri = $uri;
    }
    
    /**
     * This allows for <license license="mylicense" />
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
     */
    public function addText($license)
    {
        $this->setLicense($license);
    }
    
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

class d51PearPkg2_Version
{
    private $_release = null;
    private $_api = null;
    
    public function setRelease($release)
    {
        $this->_release = $release;
    }
    
    public function setApi($api)
    {
        $this->_api = $api;
    }
    
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

class d51PearPkg2_Stability
{
    private $_release = null;
    private $_api = null;
    
    public function setRelease($release)
    {
        $this->_release = $release;
    }
    
    public function setApi($api)
    {
        $this->_api = $api;
    }
    
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