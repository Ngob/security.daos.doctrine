<?php
namespace Mouf\Security\Userdao;

use Mouf\Actions\InstallUtils;
use Mouf\MoufManager;
use Mouf\Html\Template\TemplateInterface;
use Mouf\Mvc\Splash\Controllers\Controller;
use Mouf\Composer\ClassNameMapper;

use Mouf\MoufUtils;


/**
 * The controller managing the install process.
 *
 * @Component
 */
//TODO ADD support of namespace in UserEntity.php.tpl
class UserEntityInstallerController extends Controller  {
    public $selfedit;

    /**
     * The active MoufManager to be edited/viewed
     *
     * @var MoufManager
     */
    public $moufManager;

    /**
     * The template used by the install process.
     *
     * @var TemplateInterface
     */
    public $template;

    /**
     * The content block the template will be writting into.
     *
     * @var HtmlBlock
     */
    public $contentBlock;

    private $_mainClassNameMapper = null;

    private $_composerPath = "";
    
    private $_mainNamespaces = null;
    
    private $_fullyQualifiedUserEntityNamespace = null;
    const __GENERATED_CLASS_NAME__ = "User";
    
    
    private function _nocomposerAction($selfedit = "false", $calculedComposerPath = "") {
    	if ($selfedit == "true") {
    		$this->moufManager = MoufManager::getMoufManager();
    	} else {
    		$this->moufManager = MoufManager::getMoufManagerHiddenInstance();
    	}
    	echo "no composer file detected";
    	return;
    	$this->contentBlock->addFile(dirname(__FILE__)."/view_install.php", $this);
    	$this->template->toHtml();
    	/**
    	 * TODO
    	 * HERE MANAGE NO COMPOSER.JSON
    	 * Maybe ask for path ?
    	 */
    }
    
    protected function _getClassNameMapper($composerPath = null) {
    	/**
    	 * TODO ??
    	 * Maybe manage when no $composerPath != $this->_composerPath
    	 */
    	if (!empty($this->_mainClassNameMapper))
    		return $this->_mainClassNameMapper;
    	if (empty($composerPath)) {
			$composerPath =  $this->_getProjectPath()."/composer.json";
    	}
    	if (!file_exists($composerPath)) {
    		throw new UserDaoException("Cannot find Composer.json at the path: [".$composerPath."]");
    	}
    	$this->_composerPath = $composerPath;
    	$this->_mainClassNameMapper = ClassNameMapper::createFromComposerFile($composerPath);
    	if (empty($this->_mainClassNameMapper)) {
    		throw new UserDaoException("Cannot load the ClassNameMapper from the composer file: [".$composerPath."]");
    	}
    	return $this->_mainClassNameMapper;
    }
    
    /**
     * 
     * @param string $namespace
     */
    private function _copyUserEntityFile($namespace) {
    	
    }
    /**
     * 
     * @param string $composerPath
     * @throws UserDaoException
     * @return string[]
     */
    protected function _getNamespacesFromComposerFile($composerPath = null) {
    	if (!empty($this->_mainNamespaces) && is_array($this->_mainNamespaces))
    		return $this->_mainNamespaces;
    	try {
    		$classNameMapper = $this->_getClassNameMapper($composerPath);
    	}
    	catch (UserDaoException $e) {
   	 		if (empty($composerPath))
    			throw new UserDaoException("composerPath cannot be null if no namespace already instancied", null, $e);
   	 		throw $e;
    	}
    	$managedNamespaces= $classNameMapper->getManagedNamespaces();
    	if (!isset($managedNamespaces)  || empty($managedNamespaces) || !is_array($managedNamespaces) || count($managedNamespaces) < 1)
    		throw new UserDaoException("No namespace found in the composerfile: ".$composerPath);
    	$this->_mainNamespaces = $managedNamespaces;
    	return $this->_mainNamespaces;
    }
    /**
     * 
     * @param string[] $mainNamespace
     * @throws UserDaoException
     */
    protected function _getCalculedFullyQualifiedUserEntityNameSpace($mainNamespaces = null) {
    	if (!empty($this->_fullyQualifiedUserEntityNamespace) && is_array($this->_fullyQualifiedUserEntityNamespace))
    		return $this->_fullyQualifiedUserEntityNamespace;
    	
		$entitiesNamespace = array();
		$instance = null;
    	$autoloadNamespaces = MoufUtils::getAutoloadNamespaces2();
    	$psrMode = $autoloadNamespaces['psr'];
    	
    	$autoloadDetected = true;
    	$name = "entityManager";
    	if ($this->moufManager->instanceExists($name)){
    		$instance = $this->moufManager->getInstanceDescriptor($name);
    		$entitiesNamespace[] =  $instance->getProperty("entitiesNamespace")->getValue()."\\".self::__GENERATED_CLASS_NAME__;
    	} else{
    		if ($autoloadNamespaces) {
    			if (empty($mainNamespaces)) {
    				try {
    					$mainNamespaces = $this->_getNamespacesFromComposerFile();
    				}
    				catch (UserDaoException $e) {
    					throw new UserDaoException("You must specify a mainNamespace if _fullyQualifiedUserEntityNamespace has not been instancied", null, $e);
    				}
    			}
    			foreach ($mainNamespaces as $mn)
    				$entitiesNamespace[] = $mn."Model\\Entities".self::__GENERATED_CLASS_NAME__;
    		} else {
    			// TODO Throw exception
    			throw new UserDaoException("No Autoload Detected");
    		}
    	}
    	$this->_fullyQualifiedUserEntityNamespace = $entitiesNamespace;
    	return $this->_fullyQualifiedUserEntityNamespace;
    }
    
    protected function _buildFullyQualifiedUserEntityNameSpace($mainNamespace) {
    	$nm = $this->_getCalculedFullyQualifiedUserEntityNameSpace($mainNamespace);
    	if (empty($nm))
    		return false;
    	return true;
    }
    
    protected function _getProjectPath() {
    	return __DIR__."/../../../..";
    }
    
    /**
     * Displays the install screen.
     * 
     * @Action
     * @Logged
     * @param string $selfedit If true, the name of the component must be a component from the Mouf framework itself (internal use only) 
     */
    public function defaultAction($selfedit = "false") {
        $this->selfedit = $selfedit;

        if ($selfedit == "true") {
            $this->moufManager = MoufManager::getMoufManager();
        } else {
            $this->moufManager = MoufManager::getMoufManagerHiddenInstance();
        }
        $calculedComposerPath = $this->_getProjectPath()."/composer.json";
        if (!file_exists($calculedComposerPath)) {
        	$this->nocomposerAction($selfedit, $calculedComposerPath);
        	return;
        }
       	$mainNamespaces = $this->_getNamespacesFromComposerFile($calculedComposerPath);
       	if (!$this->_buildFullyQualifiedUserEntityNameSpace($mainNamespaces)) {
       		throw new UserDaoException("Something wrong happened, cannot determine the cause");
       	}
       	
       // $classNameWrapper = ClassNameMapper::createFromComposerFile(ROOT_PATH."/composer.json");
        $this->contentBlock->addFile(dirname(__FILE__)."/view_install.php", $this);
        $this->template->toHtml();
    }

    /**
     * The user clicked "no". Let's skip the install process.
     * 
     * @Action
     * @Logged
     * @param string $selfedit If true, the name of the component must be a component from the Mouf framework itself (internal use only)
     */
    public function skip($selfedit = "false") {
        InstallUtils::continueInstall($selfedit == "true");
    }

    /**
     * The user clicked "yes". Let's create the instance.
     * 
     * @Action
     * @Logged
     * @param string $selfedit If true, the name of the component must be a component from the Mouf framework itself (internal use only)
     * @param string $choosen_namespace
     */
    public function install($selfedit = "false", $choosen_namespace = null) {
        if ($selfedit == "true") {
            $this->moufManager = MoufManager::getMoufManager();
        } else {
            $this->moufManager = MoufManager::getMoufManagerHiddenInstance();
        }
        
        // Maybeadd support of different composer.json path? ?? 
        $possibleFileNames = $this->_getClassNameMapper()->getPossibleFileNames(html_entity_decode($choosen_namespace));
        if (empty($possibleFileNames)) {
        	throw new UserDaoException("No possible file name");
        }
        $filename = "";
        //$filename = $possibleFileNames;
        //error_log(var_export($possibleFileNames, true));
        foreach ($possibleFileNames as $possibleFileName) {
        	error_log($this->_getProjectPath()."/".$possibleFileName);
        	if (!file_exists($this->_getProjectPath()."/".$possibleFileName)) {
        		$filename = $this->_getProjectPath()."/".$possibleFileName;
        		break;
        	}
        }
        error_log("--------");
        error_log(html_entity_decode($choosen_namespace));
        error_log("filename ".$filename);
        if (empty($filename)) {
        	InstallUtils::continueInstall($selfedit == "true");
        	return;
        }
        
        if (!copy(__DIR__."/UserEntity.php.tpl", $filename)) {
        	throw new UserDaoException("Error while copying ".__DIR__."/UserEntity.php.tpl into ".$filename);
        }
        if (!chmod($filename, 0664))
        	throw new UserDaoException("Cannot Chmod ".$filename);
        InstallUtils::continueInstall($selfedit == 'true');
		return;
    }
}