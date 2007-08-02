<?php
/**
 * This file contains {@link d51PearPkg2}
 * 
 * 
 * @license http://www.gnu.org/licenses/lgpl.html LGPL
 * 
 * @package phing.tasks.ext
 * 
 * @subpackage d51pearkpkg2
 * 
 */

/**##@+
 * Load require class
 *
 * @ignore
 */
require_once 'PEAR/PackageFileManager2.php';
require_once 'phing/tasks/ext/d51PearPkg2Task/Changelog.php';
require_once 'phing/tasks/ext/d51PearPkg2Task/Dependencies.php';
require_once 'phing/tasks/ext/d51PearPkg2Task/Description.php';
require_once 'phing/tasks/ext/d51PearPkg2Task/Exception.php';
require_once 'phing/tasks/ext/d51PearPkg2Task/KeyedContainer.php';
require_once 'phing/tasks/ext/d51PearPkg2Task/License.php';
require_once 'phing/tasks/ext/d51PearPkg2Task/Maintainer.php';
require_once 'phing/tasks/ext/d51PearPkg2Task/Stability.php';
require_once 'phing/tasks/ext/d51PearPkg2Task/TextContainer.php';
require_once 'phing/tasks/ext/d51PearPkg2Task/Version.php';
/**##@-*/

/**
 * This class provides the d51pearpkg2 task for phing.
 */
class d51PearPkg2Task extends Task
{
    protected $taskName = 'd51pearpkg2';
    
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
    private $_dependencies = null;
    private $_changelogs = array();
    private $_replacements = array();
    private $_releases = array();
    
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
        
        $desc = preg_replace("/^({$this->_description->indention_type}{{$this->_description->indentions_to_remove}}|\t)+/m", '', (string)$this->_description);
        $package->setDescription($desc);
        
        $package->setChannel((string)$this->_channel);
        $package->setAPIVersion($this->_version->api);
        $package->setReleaseVersion($this->_version->release);
        $package->setAPIStability($this->_stability->api);
        $package->setReleaseStability($this->_stability->release);
        
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
        
        // handle dependencies
        if (!empty($this->_dependencies)) {
            $this->log('adding dependencies');
            if (count($this->_dependencies->groups) > 0) {
                $this->log('found dependency groups');
                foreach ($this->_dependencies->groups as $group) {
                    $this->log("adding [{$group->name}] :: [{$group->hint}]");
                    $package->addDependencyGroup($group->name, $group->hint);
                    foreach ($group->packages as $sub_package) {
                        $package->addGroupPackageDepWithChannel(
                            'subpackage',
                            $group->name,
                            $sub_package->name,
                            $sub_package->channel,
                            '0.0.1'
                        );
                    }
                }
            }
            if (count($this->_dependencies->packages) > 0) {
                $this->log('found dependencies');
                foreach ($this->_dependencies->packages as $dependency) {
                    $this->log("adding following dependency: {$dependency->channel}/{$dependency->name}");
                    $package->addPackageDepWithChannel(
                        $dependency->type,
                        $dependency->name,
                        $dependency->channel,
                        $dependency->minimum_version,
                        $dependency->maximum_version,
                        $dependency->recommended_version,
                        $dependency->exclude_version,
                        $dependency->providesextension,
                        $dependency->nodefault
                    );
                }
            }
            
            if (count($this->_dependencies->extensions) > 0) {
                $this->log('adding extension dependencies');
                foreach ($this->_dependencies->extensions as $extension) {
                    $this->log("adding ext dependency for: {$extension->name}");
                    $package->addExtensionDep(
                        $extension->type,
                        $extension->name,
                        $extension->minimum_version,
                        $extension->maximum_version,
                        $extension->recommended_version,
                        $extension->extension
                    );
                }
            }
        }
        
        foreach ($this->_changelogs as $changelog) {
            $this->log("adding changelog for prior release [{$changelog->version}]");
            $changelog->package = $package;
            $package->setChangelogEntry(
                $changelog->version,
                $changelog->toArray()
            );
            
            if (is_null($this->_notes) && $package->getVersion() == $changelog->version) {
                $this->log("no package notes specified, using changelog entry");
                $this->_notes = $changelog->contents;
            }
        }
        
        foreach ($this->_replacements as $replacement) {
            $replacement->isValid();
            
            $package->addReplacement(
                $replacement->path,
                $replacement->type,
                $replacement->from,
                $replacement->to
            );
        }
        
        foreach ($this->_releases as $release) {
            $this->log('adding new release');
            $package->addRelease();
            foreach ($release->install as $install) {
                $this->log("installing [{$install->name}] as [{$install->as}]");
                $package->addInstallAs($install->name, $install->as);
            }
        }
        
        $notes = preg_replace("/^( {4}|\t)+/m", '', (string)$this->_notes);
        $package->setNotes($notes);

        
        $package->setLicense($this->_license->license, $this->_license->uri);
        $package->generateContents();
        $e = $package->writePackageFile();
        if (PEAR::isError($e)) {
            throw new d51PearPkg2Task_Exception(
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
            throw new d51PearPkg2Task_Exception(
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
     * @return d51PearPkg2Task_KeyedContainer
     */
    public function createInstallExceptions()
    {
        $exception = new d51PearPkg2Task_KeyedContainer();
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
     * @return d51PearPkg2Task_KeyedContainer
     */
    public function createDirRoles()
    {
        $dir_role = new d51PearPkg2Task_KeyedContainer();
        $this->_options['dir_roles'][] = $dir_role;
        return $dir_role;
    }
    
    /**
     * Handle &lt;ignore&gt; elements
     *
     * This corresponds to the ignore options present in
     * {@link PEAR_PackageFileManager2::setOptions}.
     *
     *
     * @return d51PearPkg2Task_TextContainer
     */
    public function createIgnore()
    {
        $ignore = new d51PearPkg2Task_TextContainer();
        $this->_options['ignore'][] = $ignore;
        return $ignore;
    }
    
    
    /**
     * Handle <name> element.
     *
     * Note that only one <name> element can be present
     *
     *
     * @return d51PearPkg2Task_TextContainer
     */
    public function createName()
    {
        $this->_insureOnlyOnePresent('name');
        $this->_name = new d51PearPkg2Task_TextContainer();
        return $this->_name;
    }
    
    /**
     * Handle the <summary> element
     *
     * Note that only one <summary> element can be present
     *
     *
     * @return d51PearPkg2Task_TextContainer
     */
    public function createSummary()
    {
        $this->_insureOnlyOnePresent('summary');
        $this->_summary = new d51PearPkg2Task_TextContainer();
        return $this->_summary;
    }
    
    /**
     * Handle the &lt;description&gt; element
     *
     * Note that only one description element can be present
     *
     *
     * @return d51PearPkg2Task_TextContainer
     */
    public function createDescription()
    {
        $this->_insureOnlyOnePresent('description');
        $this->_description = new d51PearPkg2Task_Description();
        return $this->_description;
    }
    
    /**
     * Handle &lt;channel&gt; element
     *
     * Note that only one description element can be present
     *
     * 
     * @return d51PearPkg2Task_TextContainer
     */
    public function createChannel()
    {
        $this->_insureOnlyOnePresent('channel');
        $this->_channel= new d51PearPkg2Task_TextContainer();
        return $this->_channel;
    }
    
    /**
     * Handle &lt;lead&gt; elements
     *
     * Note that the returned {@link d51PearPkg2Task_Maintainer} object will already have a role of
     * lead when this method is invoked
     *
     *
     * @return d51PearPkg2Task_Maintainer
     */
    public function createLead()
    {
        $lead = new d51PearPkg2Task_Maintainer();
        $lead->setRole('lead');
        $this->_maintainers[] = $lead;
        return $lead;
    }
    
    /**
     * Handle &lt;developer&gt; elements
     *
     * Note that the returned {@link d51PearPkg2Task_Maintainer} object will already have a role of
     * developer when this method is invoked
     *
     *
     * @return d51PearPkg2Task_Maintainer
     */
    public function createDeveloper()
    {
        $developer = new d51PearPkg2Task_Maintainer();
        $developer->setRole('developer');
        $this->_maintainers[] = $developer;
        return $developer;
    }
    
    /**
     * Handle &lt;contributor&gt; elements
     *
     * Note that the returned {@link d51PearPkg2Task_Maintainer} object will already have a role of
     * contributor when this method is invoked
     *
     *
     * @return d51PearPkg2Task_Maintainer
     */
    public function createContributor()
    {
        $contributor = new d51PearPkg2Task_Maintainer();
        $contributor->setRole('contributor');
        $this->_maintainers[] = $contributor;
        return $contributor;
    }
    
    /**
     * Handle &lt;helper&gt; elements
     *
     * Note that the returned {@link d51PearPkg2Task_Maintainer} object will already have a role of
     * helper when this method is invoked
     *
     *
     * @return d51PearPkg2Task_Maintainer
     */
    public function createHelper()
    {
        $helper = new d51PearPkg2Task_Maintainer();
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
     * @return d51PearPkg2Task_TextContainer
     */
    public function createDate()
    {
        $this->_date = new d51PearPkg2Task_TextContainer();
        return $this->_date;
    }
    
    /**
     * Handle &lt;license&gt; element
     *
     * Note that currently only one license object is maintained per package, this may change
     * in future versions
     *
     *
     * @return d51PearPkg2Task_License
     */
    public function createLicense()
    {
        $this->_license = new d51PearPkg2Task_License();
        return $this->_license;
    }
    
    /**
     * Handle &lt;version&gt; element
     *
     * Note that currently only one version object is maintained per package, this may change
     * in future versions
     *
     *
     * @return d51PearPkg2Task_Version
     */
    public function createVersion()
    {
        $this->_version = new d51PearPkg2Task_Version();
        return $this->_version;
    }
    
    /**
     * Handle &lt;stability&gt; element
     * 
     * Note that currently only one stability object is maintained per package, this may change
     * in future versions
     * 
     * 
     * @return d51PearPkg2Task_Stability
     */
    public function createStability()
    {
        $this->_stability = new d51PearPkg2Task_Stability();
        return $this->_stability;
    }
    
    /**
     * Handle &lt;notes&gt; element
     * 
     * Note that currently only one notes object is maintained per package, this may change
     * in future versions
     *
     * 
     * @return d51PearPkg2Task_TextContainer
     */
    public function createNotes()
    {
        $this->_notes = new d51PearPkg2Task_TextContainer();
        return $this->_notes;
    }
    
    /**
     * Handle &lt;dependencies> element
     *
     */
    public function createDependencies()
    {
        $this->_dependencies = new d51PearPkg2Task_Dependencies();
        return $this->_dependencies;
    }
    
    public function createChangelog()
    {
        $changelog = new d51PearPkg2Task_Changelog();
        $this->_changelogs[] = $changelog;
        return $changelog;
    }
    
    public function createReplacement()
    {
        require_once 'd51PearPkg2Task/Replacement.php';
        $replacement = new d51PearPkg2Task_Replacement();
        $this->_replacements[] = $replacement;
        return $replacement;
    }
    
    public function createRelease()
    {
        require_once 'd51PearPkg2Task/Release.php';
        $release = new d51PearPkg2Task_Release();
        $this->_releases[] = $release;
        return $release;
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
        
        throw new d51PearPkg2Task_Exception(
            "<{$key}> can only be called once"
        );
    }
}
