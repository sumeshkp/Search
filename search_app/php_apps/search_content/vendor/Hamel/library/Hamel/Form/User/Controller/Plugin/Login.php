<?php

/**
 * Byblio
 * for logging in user from main menu.
 * Called in every controller in the app.
 * @copyright 2013 Byblio.com
 * @author: Paul A. Oliver
 *
 */


namespace Hamel\Form\User\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Hamel\Form\User\ValidateFilter\LoginFormValidate;
use Hamel\Db\General as dbGeneral;
use Useraccount\Model\Login as HamelLogin;
use Hamel\Form\User\LoginForm;
use Zend\Form\FormInterface;
use Zend\Mvc\Router;


class Login extends AbstractPlugin{

	protected $loginForm;
	protected $loginFormCaptcha;
	protected $loginFormName;
	protected $loginFormCaptchaName;
	private $_successGoToRouteName;
	private $_successGoToRouteSegments;
	
	
	// sets the route to go to on successfull login
	// only sets if is a vlid route
	public function setLoginSuccessGoToRoute($e, $route, $routeSegments){
		
		// record
		$this->_successGoToRouteName = $route;
		$this->_successGoToRouteSegments = $routeSegments;
	}
	
	
	// sets the name of the forms
	public function setFormNames($formLocation){
		
		// set form names
		switch($formLocation){
			case 'mainMenu':
				$this->loginFormName = 'form_user-menu-login';
				$this->loginFormCaptchaName = 'form_user-menu-login-captcha';
			break;
			
			default:
				$this->loginFormName = 'form_user-login';
				$this->loginFormCaptchaName = 'form_user-login-captcha';
			break;
				
		}
		
	}
	
	
	// create form and check if submitted
	public function getCheckLogin($e){
		
		// ref controller
		$controller = $e->getTarget();
		
		// set forms
		$this->loginForm = $this->_getLoginForm($controller);
		$this->loginFormCaptcha = $this->_getLoginFormCaptcha($controller);
		
		// check for login attempt
		$loginInfo = $this->_checkForlogin($e);
		
		// return info
		$returnInfo = array();
		$returnInfo['form'] = $this->loginForm;
		$returnInfo['formCaptcha'] = $this->loginFormCaptcha;
		$returnInfo['info'] = $loginInfo;
		
		
		
		// return
		return $returnInfo;
	}
	
	
	
	// create login form (without Captcha)
	private function _getLoginForm($controller){
		
		// ref translator
		$translate = $controller->getServiceLocator()->get('viewhelpermanager')->get('translate');
		
		// log in form
		$inputs = array();
		$inputs['formName'] = $this->loginFormName;
		$inputs['placeholder'] = array('email'=>$translate('Email'), 'password'=>$translate('Password'));
		$inputs['buttonValues'] = array('submit'=>$translate('Log in'));
		$loginForm  = new LoginForm($inputs);
		
		// return
		return $loginForm;
	}
	
	
	// create login form (with Captcha)
	private function _getLoginFormCaptcha($controller){
		
		// ref translator
		$translate = $controller->getServiceLocator()->get('viewhelpermanager')->get('translate');
		
		// log in form
		$inputs = array();
		$inputs['formName'] = $this->loginFormCaptchaName;
		$inputs['placeholder'] = array('email'=>$translate('Email'), 'password'=>$translate('Password'), 'captcha'=>'Security code');
		$inputs['buttonValues'] = array('submit'=>$translate('Log in'));
		$inputs['addCaptcha'] = true;
		$loginForm  = new LoginForm($inputs);
		
		// return
		return $loginForm;
	}
	
	
	// called by login form in main menu (in layout)
	private function _checkForlogin($e){
		
		// ref controller
		$controller = $e->getTarget();
		
		// form posted?
		$request = $controller->getRequest();
		
		// flags
		$formSubmitted_login = false;
		$loginFailed = false;
		$loginAcNotActive = false;
		$loginAcLocked = false;
		$loginEmail = "";
		$loginNextTimeStr = "";
		$failedLoginInfo = array();
		$loginOK = false;
		
		if($request->isPost()){ // if user posted form
			
			// flag
			$formValid = false;
			
			// name of posted form
			$postedFormName = $request->getPost('formname');
			
			if($postedFormName == $this->loginFormName) { // user clicked on log in (no captcha)
				
				// validate
				$loginValidate = new LoginFormValidate();
				$this->loginForm->setInputFilter($loginValidate->getInputFilter());
				$this->loginForm->setData($request->getPost());
				
				// flag
				$formSubmitted_login = true;
				$formValid = $this->loginForm->isValid();
				
				// set the email value of this form into the captcha form, so that email is entered for the user if we show the captcha form
				$loginEmail = $this->loginForm->get('login-email')->getValue();
				$this->loginFormCaptcha->get('login-email')->setvalue($loginEmail);
				
			}
			
			if($postedFormName == $this->loginFormCaptchaName) { // user clicked on log in, captcha
				
				// validate
				$loginValidate = new LoginFormValidate();
				$this->loginFormCaptcha->setInputFilter($loginValidate->getInputFilter());
				
				$this->loginFormCaptcha->setData($request->getPost());
								
				// flag
				$formSubmitted_login = true;
				$formValid = $this->loginFormCaptcha->isValid();
				
				// set the flag to enforce use of captcha form (until user successfully logs in)
				$failedLoginInfo['useCaptcha'] = true;
			}
			
			
			// ref translator
			$translate = $controller->getServiceLocator()->get('viewhelpermanager')->get('translate');
				

			if($formValid){
				
				// process
				$loginInfo = $this->_processLogin($e, $request, $translate);
				
				// results
				$loginOK = $loginInfo['loginOK'];
				
				if(!$loginOK){
					
					// failure reason
					$loginAcNotActive = $loginInfo['accountNotActive'];
					$loginAcLocked = $loginInfo['accountLocked'];
					
					// 
					if(!($loginAcNotActive || $loginAcLocked)){
						// flag
						$loginFailed = true;
						
						// failure info
						$loginNextTimeStr = $loginInfo['nextTimeStr'];
						$failedLoginInfo = $loginInfo['failedLoginInfo'];
						
					}else{
						// failure info
						$loginNextTimeStr = $loginInfo['nextTimeStr'];
						$failedLoginInfo = $loginInfo['failedLoginInfo'];
					}
					
					// login email
					$loginEmail = $loginInfo['email'];
					
					
				} else { // sucessful login
								
					// user info
					$userInfo = $loginInfo['userInfo'];
					
					// update check for login plugin
					$controller->checkForLogin->userInfo = $userInfo;
					$controller->checkForLogin->loggedIn = true;
					
					
					// target route
					$routeName = $this->_successGoToRouteName;
					
					if($routeName !=""){
						if($routeName == 'use_route_segments'){
							
							// redirect url
							$routeSegments = $this->_successGoToRouteSegments;
							
							// segments given
							if(is_array($routeSegments)){
								$route = $routeSegments['route'];
								$segments = $routeSegments['segments'];
								
								// default: go to home page
								$controller->redirect()->toRoute($route, $segments);
							} else {
								
								// redirect using uri
								$controller->redirect()->toRoute('home');
							}

						} else {
							// redirect using route name
							$controller->redirect()->toRoute($routeName);
						}
					}

				}

			}
	
		}
	
		
		// return info
		$rtn = array(
				'loginFailed' =>$loginFailed,
				'loginSuccess' =>$loginOK,
				'accountLocked' =>$loginAcLocked,
				'accountNotActive' =>$loginAcNotActive,
				'loginEmail' =>$loginEmail,
				'loginNextTimeStr' =>$loginNextTimeStr,
				'formSubmitted' =>$formSubmitted_login,
				'failedLoginInfo' =>$failedLoginInfo,
		);
		 
		return $rtn;
		
	}


	

	
	// acts on user submitted login form
	private function _processLogin($e, $request, $translate){
		
		// raw values
		$values = $request->getPost();
		
		// clean
		$dbGeneral = new dbGeneral();
		$cleanInputList = $dbGeneral->mysqlHTML_cleanInputOutput(
				array('email'=>$values['login-email'],
						'password'=>$values['login-password'],
				)
				, array()
		);
		
		$email = $cleanInputList['email'];
		$password = $cleanInputList['password'];
		
		// attempt to login
		$authList = array();
		$authList['email'] = $email;
		$authList['password'] = $password;
		$authList['pwdScrambled'] = false;
		$hamelLogin = new HamelLogin($e, $translate, 'userlogin', $authList);
		$loginInfo = $hamelLogin->login();
		
		// record cleaned email
		$loginInfo['email'] = $email;
		
		return $loginInfo;
		
	}
	
	
	
	
}