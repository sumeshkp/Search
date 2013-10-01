<?php
/**
 * Byblio
 * Login page - used as redirect if user lands on secure page when not logged in
 * @copyright 2013 Byblio.com
 * @author: Paul A. Oliver
 *
 */


namespace Useraccount\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;



class LoginController extends AbstractActionController{
	
	// landing page for user (any type) login
	public function homeAction(){
		
		// redirect if user logged in
		$this->checkForLogin->loggedInRedirect();
		
		// logo
		$headerLogo = 'blue';
		 
		// translator
		$translate = $this->getServiceLocator()->get('viewhelpermanager')->get('translate');
		 
		// browesr title
		$browserTitle = $translate('Log in');
		 
		
		// redirect route on sucessful login
		$redirectRoute = $_SESSION['hamelLoginRedirectRoute'];
		if($redirectRoute == "" || !$redirectRoute){
			$redirectRoute = 'home';
		}
		
		// redirect path on sucessful login
		$redirectRouteSegments = $_SESSION['hamelLoginRedirectRouteSegments'];
		
		
		// log in form (html and check for log in)
		$e = $this->getEvent();
		$loginManager = $this->plugin('loginPlugin');
		$loginManager->setFormNames('signUp');
		$loginManager->setLoginSuccessGoToRoute($e, $redirectRoute, $redirectRouteSegments); // go here if successful
		$loginAllInfo = $loginManager->getCheckLogin($e);

		
		
				
		$rtn = new ViewModel(array(
				'headerLogo' => $headerLogo,
				'browserTitle' => $browserTitle,
				'loginAllInfo' =>$loginAllInfo,
		));
		 
		return $rtn;
		
	}


	
}