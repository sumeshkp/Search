<?php
/**
 Application/module.php
 Effectively the application bootstrap
 
 */

namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\View\Helper\AbstractHelper;
use Zend\Validator\AbstractValidator;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Hamel\Authenticate\Checkforlogin;
use Hamel\UISettings\General as UISettingsGeneral;
//use Zend\

class Module implements AutoloaderProviderInterface, ConfigProviderInterface {
	
	private $_hostPublicFolder;
	private $_publicFolder;
	
	
    public function onBootstrap(MvcEvent $e){

        // route info helpers
        $this->_setRouteInfoHelpers($e);
       
        // event manager
        $eventManager = $e->getApplication()->getEventManager();
        
        // attach main menu login form
        $eventManager->attach('route', array($this, 'setMainMenuLoginForm'), 2);
        
        // route listener
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        	
        // locale
        $this->_setLocale($e);
        
        // language direction
        $this->_setLanguageDirection($e);
        
        // host public folder name
        $this->_setHostPublicFolderName();
        
        // set css core folder
        $this->_setCSSCoreFolder($e);
       
        // set path to public folder
        $this->_setPublicFolder();
        
        // set images folder
        $this->_setImagesPath($e);
        
        // set icons folder
        $this->_setIconsPath($e);
        
        // email path to public folder
        $this->_setEmailHTTPPath($e);
        
        // file paths
        $this->_setSitePublicDirectoryFilePath();
        
        // set logo paths
        $this->_setLogoImgHTML($e);
        
        // set login check plugin
        $this->_setLoggedInCheck($e);
     
        
        // set translator for form validators
        $this->_setFormValidatorTranslator($e);
       
       // set UI cookie info
       $this->_setUISettingsInfo($e);
       
       $this->_setDateTimeFormat();
     
    }

    
    

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
    	
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
    
   
    
    
    private function _setDateTimeFormat(){
    	
    	//UTC format
    	$dtFmt = 'Y-m-d\TH:i:s\Z';
    	
    	defined('DATETIME_FORMAT') || define('DATETIME_FORMAT', $dtFmt);
    			
    }
    
    
   
    // sets up and captures login form used in main menu
    public function setMainMenuLoginForm(MvcEvent $e){
        
    	$application = $e->getApplication();
    	$sm = $application->getServiceManager();
    	$sharedManager = $application->getEventManager()->getSharedManager();
     
        $router = $sm->get('router');
    	$request = $sm->get('request');
     
    	$viewModel = $application->getMvcEvent()->getViewModel();
    	
    	$matchedRoute = $router->match($request);
    	if(null !== $matchedRoute){
           $sharedManager->attach('Zend\Mvc\Controller\AbstractActionController','dispatch', 
                function($e) use ($sm, $viewModel){
                	$loginManager = $sm->get('ControllerPluginManager')->get('loginPlugin');   
                	$loginManager->setFormNames('mainMenu');  
                	$loginInfo = $loginManager->getCheckLogin($e);  
                	$viewModel->menuLoginForm = $loginInfo;
                	
           		},2
           );
        }
        
        
    }
    
    
    // sets login check into all controllers. Needs to be called from within controller
    // this checks if a user is logged in from two different places (if so, will kick out both)
    /// sets logged in flag and user info into all views
    private function _setLoggedInCheck($e){
    	
    	$application = $e->getApplication();
    	$sm = $application->getServiceManager();
    	

    	// inject
    	$sharedManager = $application->getEventManager()->getSharedManager();
    
        $router = $sm->get('router');
    	$request = $sm->get('request');
     
    	$matchedRoute = $router->match($request);
    	if(null !== $matchedRoute){
    		$sharedManager->attach('Zend\Mvc\Controller\AbstractActionController','dispatch', 
                function($e) use ($sm){
                	// controller
                	$controller = $e->getTarget();
                	// instantiate class
    				$checkForLogin = $sm->get('ControllerPluginManager')->get('checkForLogin', $controller);
    				// add to controller
    				$controller->checkForLogin = $checkForLogin;
    				
    				
    				
    				// auth
    				$auth = $checkForLogin->auth;

    				$userLibraries = $auth->variableUserInfo['userLibraries'];
    				$groupLibraries = $auth->variableUserInfo['groupLibraries'];
    				$groupAcInfo = $auth->variableUserInfo['groupAcInfo'];
    				$homeURIInfo = $auth->variableUserInfo['homeURIInfo'];
    				
    				// get logged in
			        $loggedIn = $checkForLogin->loggedIn;
			        // user info
			        $userInfo = $checkForLogin->userInfo;
    				
			        // add to all views
			        $viewModel = $e->getViewModel();
			        $viewModel->setVariable('loggedIn', $loggedIn);
			        $viewModel->setVariable('userInfo', $userInfo);
			        $viewModel->setVariable('userLibraries', $userLibraries);
			        $viewModel->setVariable('groupLibraries', $groupLibraries);
			        $viewModel->setVariable('groupAcInfo', $groupAcInfo);
			        $viewModel->setVariable('homeURIInfo', $homeURIInfo);
			      
           		},2
           );
           
           
           
           
        }
    		
    }
    
    
    
   
    // set the public folder name
    private function _setPublicFolder(){
    	
    	$this->_publicFolder =  'site_main_public';
    	defined('PUBLIC_FOLDER') || define('PUBLIC_FOLDER', $this->_publicFolder);
    
    }
    
    // set the HTML folder path to images
    private function _setImagesPath($e){
    	
    	// base path
     	$r= $e->getApplication()->getRequest();
     	$bp = $r->getBasePath();
     	
     	// images path
     	$path = $bp ."/" .$this->_publicFolder .'/images';
    	
    	// define
    	defined('IMAGES_PATH') || define('IMAGES_PATH', $path);
    
    }
    
    // set the http path to the public folder
    private function _setEmailHTTPPath($e){
    	
    	$request = $e->getApplication()->getRequest();
    	$uri = $request->getUri();
	    $scheme = $uri->getScheme();
	    $host = $uri->getHost();
	    $base = sprintf('%s://%s', $scheme, $host);
	    
     	// images path
     	$path = $base ."/" .$this->_publicFolder;
    	
    	// define
    	defined('EMAIL_PUBLIC_PATH') || define('EMAIL_PUBLIC_PATH', $path);
    
    }
    
    // set the HTML icons folder path
    private function _setIconsPath($e){
    	
    	// base path
     	$r= $e->getApplication()->getRequest();
     	$bp = $r->getBasePath();
     	
     	// images path
     	$path = $bp ."/" .$this->_publicFolder .'/icons';
    	
    	// define
    	defined('ICONS_PATH') || define('ICONS_PATH', $path);
    
    }
    
    
    // sets FILE path to the public folder for the Main site on the host server
    private function _setSitePublicDirectoryFilePath(){
    
    	// path
    	$filePath = realpath(dirname(__DIR__) ."/../../../" .$this->_hostPublicFolder ."/" .PUBLIC_FOLDER);
    	
    	defined('SITE_PUBLIC_FILEPATH') || define('SITE_PUBLIC_FILEPATH', $filePath);
    
    }
    
    
    // sets the host public folder name
    private function _setHostPublicFolderName(){
    	
    	// set folder name
    	if(substr_count($_SERVER['HTTP_HOST'], 'localhost')>0){
    		$hostPublicFolder = "www_publicFolder";
    	} else {
    		 
    		if(substr_count($_SERVER['HTTP_HOST'], 'byblio')>0){ // united hosting
    			$hostPublicFolder = "www";
    		}
    		 
    		//     		if(substr_count($_SERVER['HTTP_HOST'], 'byblio')>0){ // dreamhost
    		//     			$hostPublicFolder = "byblio";
    		//     		}
    	   
    	}
    	
    	// record local
    	$this->_hostPublicFolder = $hostPublicFolder;
    	
    	// record global
    	defined('HOST_PUBLIC_FOLDER') || define('HOST_PUBLIC_FOLDER', $hostPublicFolder);
    }
    
    
    
    // set the route info helpers
    private function _setRouteInfoHelpers($e){
    	
    	// controller name. Call in view script as $name = $this->controllerName();
    	$e->getApplication()->getServiceManager()->get('viewhelpermanager')->setFactory('controllerName', function($sm) use ($e) {
        	$viewHelper = new View\ControllerName($e->getRouteMatch());
        	return $viewHelper;
        });
        
    	// roure name. Call in view script as $name = $this->routeName();
    	$e->getApplication()->getServiceManager()->get('viewhelpermanager')->setFactory('routeName', function($sm) use ($e) {
        	$viewHelper = new View\RouteName($e->getRouteMatch());
        	return $viewHelper;
        });
        
    
    }
    
    // sets locale based upon browser settings, with en_gb as default
    private function _setLocale($e){
    	
    	// current locale
    	$sm = $e->getApplication()->getServiceManager();
    	$translator = $sm->get('translator');
    	$locale = $translator->getLocale();
    	
    	// record in service manager
    	$sm->setService('language_locale', $locale);
    	
    }
    
   
    // ui info - cookie, update etc.
    private function _setUISettingsInfo($e){
    	
    	// cookie info
    	$uiGeneral = new UISettingsGeneral();

    	// record in service manager
    	$sm = $e->getApplication()->getServiceManager();
    	$sm->setService('ui_settingsInfo', $uiGeneral);
    	
    }
    
   
  
    // sets logo html
    private function _setLogoImgHTML($e){
    	
    	$e->getApplication()->getServiceManager()->get('viewhelpermanager')->setFactory('logoHTML', function($logoType){
        	$viewHelper = new Logo\HeaderImgHTML($logoType);
        	return $viewHelper;
        });
    	
    }
    
    
    // sets the language direction flag
    private function _setLanguageDirection($e){
    	 
    	// current locale
    	$sm = $e->getApplication()->getServiceManager();
        $translator = $sm->get('translator');
        $locale = $translator->getLocale();
    
        // list of languages written from right to left (i18n)
        $rlList = array('ar', 'iw', ); /// current: arabic, hebrew
        
        // core language of current locale
        $lang = mb_substr($locale, 0, 2);
        
        // if an rtl language
        if(in_array($lang, $rlList)){
        	$dir = 'rtl';
        } else {
        	$dir = 'ltr';
        }
        
    	// record in service manager
    	$sm->setService('language_direction', $dir);
    	
    }
    
    // sets the same translator for all form validator classes
    private function _setFormValidatorTranslator($e){
    	
    	// current translator
//     	$sm = $e->getApplication()->getServiceManager();
//     	$translator = $sm->get('translator');
    	
//     	// set
//     	AbstractValidator::setDefaultTranslator($translator);
    }
    
    // sets the core css folder - LR for languages reading left to right, and RL for vice-versa
    private function _setCSSCoreFolder($e){
    	 
    	// current language direction
    	$sm = $e->getApplication()->getServiceManager();
        $ld = $sm->get('language_direction');
    	
        switch($ld){
        	case 'rtl':
        		$csFolder = 'rtl';
        	break;
        	
        	case 'ltr':
        	default:
        		$csFolder = 'ltr';  		
        	break;	
        }
    	
        // record in service manager
        $sm->setService('css_coreFolder', $csFolder);
        
    }
}
