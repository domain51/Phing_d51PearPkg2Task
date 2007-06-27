<?php
/**
 * This file contains {@link d51PearPkg2}
 * 
 * 
 * @license http://www.gnu.org/licenses/lgpl.html LGPL
 * 
 * @package phing.tasks.ext
 * 
 * @subpackage Domain51 Extensions
 * 
 */

/**##@+
 * Load require class
 *
 * @ignore
 */
require_once 'PEAR/PackageFileManager2.php';
require_once 'phing/tasks/ext/d51PearPkg2/Exception.php';
require_once 'phing/tasks/ext/d51PearPkg2/KeyedContainer.php';
require_once 'phing/tasks/ext/d51PearPkg2/License.php';
require_once 'phing/tasks/ext/d51PearPkg2/Maintainer.php';
require_once 'phing/tasks/ext/d51PearPkg2/Stability.php';
require_once 'phing/tasks/ext/d51PearPkg2/TextContainer.php';
require_once 'phing/tasks/ext/d51PearPkg2/Version.php';
/**##@-*/

/**
 * This class provides the d51pearpkg2 task for phing.
 */
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
    
    /**
     * Handles the baseinstalldir attribute of d51pearpkg2
     *
     * This corresponds to the baseinstalldir option present in 
     * {@link PEAR_PackageFileManager2::setOptions}.
     *
     *
     * @param string $base_install_dir
     */
    public function setBaseInstallDir($base_install_dir)
    {
        $this->_options['baseinstalldir'] = $base_install_dir;
    }
    
    /**
     * Handles the dir attribute of d51pearpkg2
     *
     * This is used to set the default directory in which to base the package generation on
     *
     *
     * @param PhingFile $dir
     */
    public function setDir(PhingFile $dir)
    {
        $this->_directory = $dir;
    }
    
    /**
     * Handles the the filelistgenerator attribute of d51pearpkg2
     *
     * This corresponds to the filelistgenerator option present in
     * {@link PEAR_PackageFileManager2::setOptions}.
     *
     * This can only be 'file', or 'cvs' as of this version
     *
     *
     * @param string $file_list_generator
     */
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
    
    /**
     * Handle &lt;installexceptions&gt; elements
     * 
     * This corresponds to the ignore options present in
     * {@link PEAR_PackageFileManager2::setOptions}.
     *
     *
     * @return d51PearPkg2_KeyedContainer
     */
    public function createInstallExceptions()
    {
        $exception = new d51PearPkg2_KeyedContainer();
        $this->_options['installexceptions'][] = $exception;
        return $exception;
    }
    
    /**
     * Handle &lt;dirrole&gt; elements
     * 
     * This corresponds to the ignore options present in
     * {@link PEAR_PackageFileManager2::setOptions}.
     *
     *
     * @return d51PearPkg2_KeyedContainer
     */
    public function createDirRoles()
    {
        $dir_role = new d51PearPkg2_KeyedContainer();
        $this->_options['dir_roles'][] = $dir_roles;
        return $dir_role;
    }
    
    /**
     * Handle &lt;ignore&gt; elements
     *
     * This corresponds to the ignore options present in
     * {@link PEAR_PackageFileManager2::setOptions}.
     *
     *
     * @return d51PearPkg2_TextContainer
     */
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
     * Handle the &lt;description&gt; element
     *
     * Note that only one description element can be present
     *
     *
     * @return d51PearPkg2_TextContainer
     */
    public function createDescription()
    {
        $this->_insureOnlyOnePresent('description');
        $this->_description = new d51PearPkg2_TextContainer();
        return $this->_description;
    }
    
    /**
     * Handle &lt;channel&gt; element
     *
     * Note that only one description element can be present
     *
     * 
     * @return d51PearPkg2_TextContainer
     */
    public function createChannel()
    {
        $this->_insureOnlyOnePresent('channel');
        $this->_channel= new d51PearPkg2_TextContainer();
        return $this->_channel;
    }
    
    /**
     * Handle &lt;lead&gt; elements
     *
     * Note that the returned {@link d51PearPkg2_Maintainer} object will already have a role of
     * lead when this method is invoked
     *
     *
     * @return d51PearPkg2_Maintainer
     */
    public function createLead()
    {
        $lead = new d51PearPkg2_Maintainer();
        $lead->setRole('lead');
        $this->_maintainers[] = $lead;
        return $lead;
    }
    
    /**
     * Handle &lt;developer&gt; elements
     *
     * Note that the returned {@link d51PearPkg2_Maintainer} object will already have a role of
     * developer when this method is invoked
     *
     *
     * @return d51PearPkg2_Maintainer
     */
    public function createDeveloper()
    {
        $developer = new d51PearPkg2_Maintainer();
        $developer->setRole('developer');
        $this->_maintainers[] = $developer;
        return $developer;
    }
    
    /**
     * Handle &lt;contributor&gt; elements
     *
     * Note that the returned {@link d51PearPkg2_Maintainer} object will already have a role of
     * contributor when this method is invoked
     *
     *
     * @return d51PearPkg2_Maintainer
     */
    public function createContributor()
    {
        $contributor = new d51PearPkg2_Maintainer();
        $contributor->setRole('contributor');
        $this->_maintainers[] = $contributor;
        return $contributor;
    }
    
    /**
     * Handle &lt;helper&gt; elements
     *
     * Note that the returned {@link d51PearPkg2_Maintainer} object will already have a role of
     * helper when this method is invoked
     *
     *
     * @return d51PearPkg2_Maintainer
     */
    public function createHelper()
    {
        $helper = new d51PearPkg2_Maintainer();
        $helper->setRole('helper');
        $this->_maintainers[] = $helper;
        return $helper;
    }
    
    /**
     * Handle &lt;date&gt; element
     *
     * Note that currently only one date object is maintained per package, this may change
     * in future verions
     * 
     *
     * @return d51PearPkg2_TextContainer
     */
    public function createDate()
    {
        $this->_date = new d51PearPkg2_TextContainer();
        return $this->_date;
    }
    
    /**
     * Handle &lt;license&gt; element
     *
     * Note that currently only one license object is maintained per package, this may change
     * in future versions
     *
     *
     * @return d51PearPkg2_License
     */
    public function createLicense()
    {
        $this->_license = new d51PearPkg2_License();
        return $this->_license;
    }
    
    /**
     * Handle &lt;version&gt; element
     *
     * Note that currently only one version object is maintained per package, this may change
     * in future versions
     *
     *
     * @return d51PearPkg2_Version
     */
    public function createVersion()
    {
        $this->_version = new d51PearPkg2_Version();
        return $this->_version;
    }
    
    /**
     * Handle &lt;stability&gt; element
     * 
     * Note that currently only one stability object is maintained per package, this may change
     * in future versions
     * 
     * 
     * @return d51PearPkg2_Stability
     */
    public function createStability()
    {
        $this->_stability = new d51PearPkg2_Stability();
        return $this->_stability;
    }
    
    /**
     * Handle &lt;notes&gt; element
     * 
     * Note that currently only one notes object is maintained per package, this may change
     * in future versions
     *
     * 
     * @return d51PearPkg2_TextContainer
     */
    public function createNotes()
    {
        $this->_notes = new d51PearPkg2_TextContainer();
        return $this->_notes;
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
