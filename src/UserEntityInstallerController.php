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

    // Used to display error message on views
    public $message = "";
    
    private $_mainClassNameMapper = null;

    private $_composerPath = "";
    
    private $_mainNamespaces = null;
    
    private $_fullyQualifiedUserEntityNamespace = null;
    
    const __ENTITYMANAGER_NAME__ = "entityManager";
    
    const __GENERATED_CLASS_NAME__ = "User";

    const __ABSTRACT_CLASS_NAME__ = "AbstractUserEntity";
    
    const __USERINTERFACE_FULLNAME__ = "Mouf\\Security\\UserService\\UserDaoInterface";
    
    const __USERDAO_CLASS_NAME__ = "UserDao";
    
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
    	$name = self::__ENTITYMANAGER_NAME__;
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
    				$entitiesNamespace[] = $mn."Model\\Entities\\".self::__GENERATED_CLASS_NAME__;
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
        // TODO : Allow the user to choose the namespace if there is an exception while getting namespaces
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
     * Get all posible filename for a namespace (with the class name at the end)
     */
    protected function getNamespaceFileName($namespace, $isAbsolutePath = true) {
    	$namespace = preg_replace('/\s+/', '', $namespace);
    	$possibleFileNames = $this->_getClassNameMapper()->getPossibleFileNames($namespace);
    	
    	if (empty($possibleFileNames)) {
    		return [];
    	}
    	$fileNameList = [];
    	foreach ($possibleFileNames as $possibleFileName) {
    		$filepath = $possibleFileName;
    		if ($isAbsolutePath) {
    			$filepath =  realpath($this->_getProjectPath())."/".$filepath;
    		}
    		$fileNameList[] = ["path" => $filepath, "exist" => file_exists($this->_getProjectPath()."/".$possibleFileName)];
    	}
    	return $fileNameList;
    }
    
    
    
    /**
     * Action to allow user to select his filename/path for a namespace
     *
     * @Action
     * @Logged
     * @param string $selfedit If true, the name of the component must be a component from the Mouf framework itself (internal use only)
     * @param string $namespace
     */
    public function selectFilename($choosen_namespace, $selfedit = "false") {
    	// Check format of namespace choosen?
        $this->selfedit = $selfedit;
    	if ($selfedit == "true") {
    		$this->moufManager = MoufManager::getMoufManager();
    	} else {
    		$this->moufManager = MoufManager::getMoufManagerHiddenInstance();
    	}
    	$fileNameList = [];
    	$namespace = html_entity_decode($choosen_namespace);
    	try {
    		$fileNameList = $this->getNamespaceFileName($namespace, true);
    	}
    	catch (UserDaoException $e) {
    		// IF there is an error, let the user select his filename
    		$fileNameList = [];
    	}
    	
        $this->fileList = $fileNameList;
        $this->namespace = $namespace;
    	
    	$this->contentBlock->addFile(dirname(__FILE__)."/select_filename.php", 
    			$this);
    	$this->template->toHtml();
    }
    
    /**
     * check if the file exist filename for a namespace / Create the file if possible => missnamed function
     *
     * @Action
     * @Logged
     * @param string $selfedit If true, the name of the component must be a component from the Mouf framework itself (internal use only)
     * @param string $namespace
     */
    public function checkFile($choosen_namespace, $choosen_filename, $selfedit = "false") {
    	$this->selfedit = $selfedit;
    	if ($selfedit == "true") {
    		$this->moufManager = MoufManager::getMoufManager();
    	} else {
    		$this->moufManager = MoufManager::getMoufManagerHiddenInstance();
    	}
    	$namespace = html_entity_decode($choosen_namespace);
		$namespace = preg_replace('/\s+/', '', $namespace);
    	$filename = html_entity_decode($choosen_filename);
    	$filename = preg_replace('/\s+/', '', $filename);
    	
    	if (file_exists($filename)) {
    		$this->message = "The file [".htmlentities($choosen_filename)."] already exists, please select another path";
    		return $this->selectFilename($choosen_namespace, $selfedit);
    	}
    	
    	$this->filename = $filename;
    	$this->namespace = $namespace;
    	$classname = substr(strrchr($namespace, "\\"), 1);
    	$fullnamespace = substr($namespace, 0, strrpos($namespace, "\\", -1));
    	$fileObject = new \SplFileObject($filename, "w+");
    	$fileObject->fwrite("<?php ".PHP_EOL);
    	$fileObject->fwrite("namespace ".$fullnamespace.";".PHP_EOL);
    	$fileObject->fwrite("use ".__NAMESPACE__."\\".self::__ABSTRACT_CLASS_NAME__.";".PHP_EOL);
    	$fileObject->fwrite(file_get_contents(__DIR__."/User.php.tpl"));
    	$fileObject->fwrite("class ".$classname." extends ".self::__ABSTRACT_CLASS_NAME__." {".PHP_EOL);
    	$fileObject->fwrite("}".PHP_EOL);
    	/**if (!copy(__DIR__."/UserEntity.php.tpl", $filename)) {
    		throw new UserDaoException("Error while copying ".__DIR__."/UserEntity.php.tpl into ".$filename);
    	}**/
    	if (!chmod($filename, 0664))
    		throw new UserDaoException("Cannot Chmod ".$filename);
    	$this->createMyInstanceView($choosen_namespace, $selfedit);
    }
    /**
     * 
     *
     * @Action
     * @Logged
     * @param string $selfedit If true, the name of the component must be a component from the Mouf framework itself (internal use only)
     * @param string $namespace
     */
    public function createMyInstanceView($choosen_namespace, $selfedit = "false") {
    	$this->selfedit = $selfedit;
    	if ($selfedit == "true") {
    		$this->moufManager = MoufManager::getMoufManager();
    	} else {
    		$this->moufManager = MoufManager::getMoufManagerHiddenInstance();
    	}
    	$namespace = html_entity_decode($choosen_namespace);
		$namespace = preg_replace('/\s+/', '', $namespace);
    	$this->namespace = $namespace;
    	$this->instanceName = self::__USERINTERFACE_FULLNAME__;
    	$this->entityManagerName = self::__ENTITYMANAGER_NAME__;
    	
    	$this->contentBlock->addFile(dirname(__FILE__)."/select_instancename.php",
    			$this);
    	$this->template->toHtml();
    }

    /**
     * check if the file exist filename for a namespace / Create the file if possible => missnamed function
     *
     * @Action
     * @Logged
     * @param string $selfedit If true, the name of the component must be a component from the Mouf framework itself (internal use only)
     * @param string $namespace
     */
    public function createMyInstance($choosen_namespace, $selfedit = "false") {
    	$this->selfedit = $selfedit;
    	if ($selfedit == "true") {
    		$this->moufManager = MoufManager::getMoufManager();
    	} else {
    		$this->moufManager = MoufManager::getMoufManagerHiddenInstance();
    	}
    	$moufManager = $this->moufManager;
    	$namespace = html_entity_decode($choosen_namespace);
		$namespace = preg_replace('/\s+/', '', $namespace);
		if (!$moufManager->instanceExists(self::__ENTITYMANAGER_NAME__)) {
			$this->message = "No entity manager instance found, supposed name is ".self::__ENTITYMANAGER_NAME__;
			return $this->createMyInstanceView($namespace, $selfedit);
		}
    	if (!$moufManager->instanceExists(self::__USERINTERFACE_FULLNAME__)) {
    		$userDao = $moufManager->createInstance(__NAMESPACE__."\\".self::__USERDAO_CLASS_NAME__);
    		$userDao->setName(self::__USERINTERFACE_FULLNAME__);
    		$userDao->getProperty("entityManager")->setValue($moufManager->getInstanceDescriptor(self::__ENTITYMANAGER_NAME__));
    		
    		// LEt create the repository instance
    		$userDao->getProperty("fullenameClassEntity")->setValue($namespace);
    	
	    	// Let's rewrite the MoufComponents.php file to save the component
	    	$moufManager->rewriteMouf();
	    	$this->skip();
    	}
    	else {
    		$this->message = "Instance's name ".self::__USERINTERFACE_FULLNAME__." already in use, you might skip this installer";
    		return $this->createMyInstanceView($namespace, $selfedit);
    		// TODO Error message
    	}
    }
    
}