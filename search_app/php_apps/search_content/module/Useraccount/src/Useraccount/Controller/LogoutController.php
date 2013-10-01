<?php
/**
 * Byblio
 * logout - redirects to home page
 * @copyright 2013 Byblio.com
 * @author: Paul A. Oliver
 *
 */


namespace Useraccount\Controller;

use Zend\Mvc\Controller\AbstractActionController;


class LogoutController extends AbstractActionController{
	
	public function logoutAction(){
		
		// auth service
		$auth = $this->checkForLogin->auth;
		
		// clear identity and log out of db
		$auth->logout();
		
		// redirect
		// go to library home page
		$this->redirect()->toRoute('home');
		
	}


	
}