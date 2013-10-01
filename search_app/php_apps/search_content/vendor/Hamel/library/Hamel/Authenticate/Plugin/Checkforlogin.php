<?php
/**
 * Byblio
 * Checks for login and different actions, such as redirect to given page if NOT logged in OR if user is NOT Admin account type
 * @copyright 2013 Byblio.com
 * @author: Paul A. Oliver
 *
 */


namespace Hamel\Authenticate\Plugin;


use Hamel\Authenticate\Service\Service as HAuthenticationService;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Hamel\Db\General as DbGeneral;





class Checkforlogin extends AbstractPlugin{

	
	protected $_loginDefaultNotLoggedInRedirectRoute;
	protected $_loginDefaultLoggedInRedirectRoute;
	protected $_unknownSession;
	
	public $auth;
	public $loggedIn;
	public $userInfo;
	
	// called on every page as is loaded by th applicartion as a plugin
	public function __construct($controller){
	
		// routes
		$this->_loginDefaultNotLoggedInRedirectRoute = 'useraccount-login';
		$this->_loginDefaultLoggedInRedirectRoute = 'home';
		
		
		// user logged in?
		$this->auth = new HAuthenticationService();

		// if user logged in
		if($this->auth->hasIdentity()){
	
			// check if user is logged in form a differnet browser
			$sameBrowser = $this->auth->checkSameBrowser();
					
			// if someone else/ same person has logged in to this account from a different browser AFTER this session was started
			if(!$sameBrowser){
				// logout and redirect
				$this->logoutGoToPublicLibraries($controller);
			}
			
			// record
			$this->userInfo = $this->auth->getIdentity();
			
			// flag
			$this->loggedIn = true;
			
		} else {
			
			// flag
			$this->loggedIn = false;
			
			// set up logging of this unknown user
			$this->_logUnknownUser($controller);
			
			// record user info
			$this->userInfo = $this->auth->getUnknownUserInfo();
		}
		
	}
	
	
	
	
	// logs info for tracking and verifying unknown users
	private function _logUnknownUser($controller){
		
		// verify current session
		$validSession = $this->auth->verifyUnknownSession();
			
		if(!$validSession){
			
			// and log new unknown user session
			$this->auth->logUnknowUser($controller);
		}
		
	
	}
	
	
	
	// logs the user out, then goes to the login page
	// on successful login, takes user to public libraries page
	public function logoutGoToPublicLibraries($controller){
		
		// logout
		$this->auth->logout();
		
		// set redirect route after log in (to public libraries)
		$_SESSION['hamelLoginRedirectRoute'] = 'library-allPublic';
		
		// redirect to login page
		$redirectRoute = $this->_loginDefaultNotLoggedInRedirectRoute;
		$controller->redirect()->toRoute($redirectRoute);
	}
	
	
	
	// redirects if user IS logged in 
	public function loggedInRedirect($redirectRoute = null){
	
		if($this->loggedIn){
			// set route
			if($redirectRoute = "" || $redirectRoute == null){
				$redirectRoute = $this->_loginDefaultLoggedInRedirectRoute;
			}

			// redirect
			$controller = $this->getController();
			$controller->redirect()->toRoute($redirectRoute);
				
		}
	}
	
	
	// redirects if user is NOT logged in 
	public function notLoggedInRedirect($redirectRoute = null){
		
		// current route
		$controller = $this->getController();
		$currentRoute = $controller->getEvent()->getRouteMatch()->getMatchedRouteName();
	
		// store the route from where the user has come
		$_SESSION['hamelLoginRedirectRoute'] = $currentRoute;
		
		if(!$this->loggedIn){
			// set route
			if($redirectRoute = "" || $redirectRoute == null){
				$redirectRoute = $this->_loginDefaultNotLoggedInRedirectRoute;
			}
				
			// redirect
			$controller = $this->getController();
			$controller->redirect()->toRoute($redirectRoute);
				
		}
	}
	
	
	
	// redirects if user is NOT logged in or if NOT ADMIN
	public function notAdminRedirect($redirectRoute = null){
		
		if(!$this->loggedIn){
			
			// current route
			$controller = $this->getController();
			$currentRoute = $controller->getEvent()->getRouteMatch()->getMatchedRouteName();
			
			// store the route from where the user has come
			$_SESSION['hamelLoginRedirectRoute'] = $currentRoute;
			
			// set route
			if($redirectRoute = "" || $redirectRoute == null){
				$redirectRoute = $this->_loginDefaultNotLoggedInRedirectRoute;
			}	
			
			// redirect
			$this->redirect()->toRoute($redirectRoute);
			
		} else { // logged in
			
			// account type and status
			$accountType = $this->userInfo['accountType'];
			$status = $this->userInfo['status'];
			
			// redirect?
			if($status == 0){// account not active
				// set route
				if($redirectRoute = "" || $redirectRoute == null){
					$redirectRoute = 'useraccount-resendActivationEmail';
				}
				// redirect
				$controller = $this->getController();
				$controller->redirect()->toRoute($redirectRoute);
				
			} else {
				if($accountType < 100){// account not admin
					
					// set route
					if($redirectRoute = "" || $redirectRoute == null){
						$redirectRoute = 'home';
					}
					
					// redirect
					$controller = $this->getController();
					$controller->redirect()->toRoute($redirectRoute);
				}
			}
		}
	}

	
	
	
	// returns user info for the logged in user
	public function getUserInfo(){
		
		$returnList = $this->userInfo;
		
		return $returnList;
	}

	
}