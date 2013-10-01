<?php

/**
 * Byblio
 * for signing up as a registered user or library owner
 * @copyright 2013 Byblio.com
 * @author: Paul A. Oliver
 *
 */


namespace Useraccount\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Hamel\Form\User\SignupForm;
use Hamel\Form\User\SignupLoggedInForm;
use Hamel\Form\User\ValidateFilter\SignupFormValidate;
use Hamel\Form\User\ValidateFilter\SignupLoggedInFormValidate;
use Hamel\Db\General as dbGeneral;
use Useraccount\Model\NewAccount as NewAccount;
use Hamel\Usermanagement\Download as UsermanagementDownload;
use Library\Model\NewLibrary as NewLibrary;
use Useraccount\Model\Login as HamelLogin;
use Useraccount\Model\AccountSignupOptions;
use Hamel\CountryAccess\General as CAGeneral;
use Hamel\AccountManagement\Options as AccountOptions;
use Hamel\Vouchermanagement\Options as VoucherOptions;



class SignupController extends AbstractActionController{
	
	
	
	// landing page for user (any type) to sign up yo byblio.com
	public function homeAction(){
		
		// logo
		$headerLogo = 'blue';
		 
		
		// translator
		$translate = $this->getServiceLocator()->get('viewhelpermanager')->get('translate');
		
		// account options
		$acOptionInfo = $this->_getAccountOptions($translate);
		
		$loggedIn = $this->checkForLogin->loggedIn;
		$userInfo = $this->checkForLogin->userInfo;
		
		// browser title
		if($loggedIn){
			$browserTitle = $translate('New account');
		} else {
			$browserTitle = $translate('Join byblio');
		}
		
		
		// page settings
		$pageSettings = $this->_getJSPageSettings($this);
		
		// account and user ids and names types
		$accountOptions = new AccountOptions($translate);
		$allAccountInfo = $accountOptions->getAccountTypes();
		$allUsertypeInfo = $accountOptions->getUsertypes();
	
		// num chars in voucher
		$voucherOptions = new VoucherOptions();
		$numVoucherChars = $voucherOptions->getNumCodeChars();
		
		// form names & id
		$signupFormLoggedInName = 'form_user-signup-loggedIn';
		$signupFormFullName = 'form_user-signup';
		$signupSubmitId = 'submit_signup';
		
		if($loggedIn){
			
			// sign-up form
			$voucher = array('numChars'=>$numVoucherChars); // voucher info
			$inputs = array();
			$inputs['formName'] = $signupFormLoggedInName;
			$inputs['placeholder'] = array(
					'password'=>$translate('Password (at least 5 characters)'),
					'voucher'=>$translate('Voucher code?')
			);
			$inputs['buttonValues'] = array('submit'=>$translate('Sign up'));
			$inputs['classList'] = array(
					'submit'=>'submit',
					'acOption'=>'acOption',
					'allAcOptions'=>'allAcOptions',
					'allAcSubOptions'=> 'allAcSubOptions',
					'acSubOption' => 'acSubOption',
					'paymentType' => 'paymentType',
			);
			$inputs['acOptionInfo'] = $acOptionInfo['acOptions'];
			$inputs['voucher'] = $voucher;
			$signupForm  = new SignupLoggedInForm($inputs);
			
			
		} else { // use full signup form
			
			// sign-up  form
			$voucher = array('numChars'=>$numVoucherChars); // voucher info
			$inputs = array();
			$inputs['formName'] = $signupFormFullName;
			$inputs['placeholder'] = array(
					'firstName'=>$translate('First name'), 
					'lastName'=>$translate('Last name'), 
					'email'=>$translate('Email'), 
					'password'=>$translate('Password (at least 5 characters)'),
					'voucher'=>$translate('Voucher code?')
					);
			$inputs['buttonValues'] = array('submit'=>$translate('Sign up'));
			$inputs['classList'] = array(
					'submit'=>'submit',
					'acOption'=>'acOption',
					'allAcOptions'=>'allAcOptions',
					'allAcSubOptions'=> 'allAcSubOptions',
					'acSubOption' => 'acSubOption',
					'paymentType' => 'paymentType',
					);
			$inputs['acOptionInfo'] = $acOptionInfo['acOptions'];
			$inputs['voucher'] = $voucher; 
			$signupForm  = new SignupForm($inputs);
		}
		
		// form posted?
		$request = $this->getRequest();
		
		// flags
		$formSubmitted_signup = false;
		$createdNewUser = false;
		$failedToCreateNewUser = false;
		$signupEmailInUse = false;
		$signupFirstName = "";
		$signupEmail = "";
		
		
		
		
		if ($request->isPost()){ // if user posted form
			
			// name of posted form
			$postedFormName = $request->getPost('formname');
			
			if($postedFormName == $signupFormFullName){ // user clicked on sign up (full form)
				
				// flag
				$formSubmitted_signup = true;
				
				$signupValidate = new SignupFormValidate();
				$signupForm->setInputFilter($signupValidate->getInputFilter());
				$signupForm->setData($request->getPost());
					
				if($signupForm->isValid()){
					
					// is t&c box checked?
					$tandc = $signupForm->get('user-signup-tandc');
					$tandcSelected = var_export($tandc->getValue(), true);
					
					// is selection valid?
					$tandcAgreed = true;
					if($tandcSelected == 'false'){
						$tandcAgreed = false;
					}
					
					if($tandcAgreed){ // all values Ok
						// process
						$returnInfo = $this->_processSignUp($request, $translate);	
						
						// record flags
						$signupEmailInUse = $returnInfo['emailInUse'];
						$createdNewUser = $returnInfo['createdNewUser'];
						$signupFirstName = $returnInfo['firstName'];
						$signupEmail = $returnInfo['email'];
						
						// set flag logic
						if($signupEmailInUse){ // email already in use
							$createdNewUser = false;
							$failedToCreateNewUser = false;
							
						} else { // email not in use
							
							// set flag
							if(!$createdNewUser){ // failed to create new user
								$failedToCreateNewUser = true;
							} else {
								$failedToCreateNewUser = false; // ensure state
							}
						}
						
					}
				}
			}
			
			// if user submitted the logged in form
			if($postedFormName == $signupFormLoggedInName){
				
				// flag
				$formSubmitted_signup = true;
				
				$signupValidate = new SignupLoggedInFormValidate();
				$signupForm->setInputFilter($signupValidate->getInputFilter());
				$signupForm->setData($request->getPost());
				
				
			};
			
			
		}
			

		// view model to return
		$rtn = new ViewModel(array(
				'headerLogo' => $headerLogo,
				'browserTitle' => $browserTitle,
				'signupForm' => $signupForm,
				'formSubmitted_signup' =>$formSubmitted_signup,
				'signupEmailInUse' =>$signupEmailInUse,
				'createdNewUser' =>$createdNewUser,
				'failedToCreateNewUser' =>$failedToCreateNewUser,
				'signupFirstName' =>$signupFirstName,
				'signupEmail' =>$signupEmail,
				'acOptionInfo' =>$acOptionInfo,
				'pageSettings' => $pageSettings,
				'allAccountInfo' => $allAccountInfo,
				'allUsertypeInfo' => $allUsertypeInfo,
				'loggedIn' => $loggedIn,
				'userInfo' => $userInfo,

		));
		 
		return $rtn;
		
	}


	// core page settings (does not go to db as this is a non-logged in page)
	private function _getJSPageSettings($controller){
		
		$ajaxRoute = 'useraccount-remote';
		$verificationRoute = $ajaxRoute .'signup-voucher';
		
		// set verification info for ajax calls
		$verfifyInfo = $controller->checkForLogin->auth->getVerificationValues(array($verificationRoute));
		$voucherValue = $verfifyInfo[$verificationRoute];
		$dbSessionId = $verfifyInfo['dbSessionId'];
		$phpSessionId = $verfifyInfo['phpSessionId'];
		
		// voucher access url
		$voucherUrl = $controller->url()->fromRoute($ajaxRoute) .'signup-voucher';
		
		// ajax settings
		$ajaxSettings = array(
				'alpha' => array('voucher'=>$voucherValue),
				'beta' => $dbSessionId,
				'gama'=>$phpSessionId,
				'url' => array('voucher'=>$voucherUrl),
				'voucherInfo'=>array(
						'signup'=>array(
								'entitlement'=>array('summary'=>true, 'info'=>true)
								)
						)
		);
		$pageSettings = array(
				'ajax'=> $ajaxSettings
		);
		
		return $pageSettings;
	}
	
	
	// creates options of the account types, with prices and currency string and labels
	private function _getAccountOptions($translate){
		
		// temp.
		$showAdminAccount = false;
		
		$countryAccess = new CAGeneral($translate);
		$countryInfo = $countryAccess->getBrowserAccessCountry(true); // user admin setting if have
				
		// get names of the users in each account type
		$acOptions = new AccountSignupOptions();
		$acOptionsInfo = $acOptions->getAccountSignupOptions($translate, $countryInfo, $showAdminAccount);

		
		return $acOptionsInfo;
	}
	
	
	
	// acts on user submitted signup form
	private function _processSignUp($request, $translate){
		
		// flags
		$emailInUse = false;
		$createdNewUser = false;
		
		// raw values
		$values = $request->getPost();
		
		// clean
		$dbGeneral = new dbGeneral();
		$cleanInputList = $dbGeneral->mysqlHTML_cleanInputOutput(
				array('email'=>$values['user-signup-email'],
						'password'=>$values['user-signup-password'],
						'firstName'=>$values['user-signup-firstname'],
						'lastName'=>$values['user-signup-lastname'],
						'acType'=>$values['user-signup-acType'],
				)
				, array()
		);
		
		// check if email is in use
		$email = $cleanInputList['email'];
		$mgtDownload = new UsermanagementDownload();
		$emailStatus = $mgtDownload->verifyEmailAddress($email, true);
		if($emailStatus != "not_found"){
			$emailInUse = true;
		}
		
		if(!$emailInUse){
			// create new user
			$viewHelperMgr = $this->getServiceLocator()->get('viewhelpermanager');
			$newAccount = new NewAccount($viewHelperMgr);
			$returnInfo = $newAccount->createNewAccount($cleanInputList, true);
			
			// id
			$userId = $returnInfo['userId'];
			
			
			if($userId !=0){ // created user
				// set flag
				$createdNewUser = true;
				
				// send email
				$emailSent = $newAccount->sendActivationEmail($returnInfo);
				
				// info
				$accountType = $returnInfo['accountType'];
				$firstName = $cleanInputList['firstName'];
				$lastName = $cleanInputList['lastName'];
				
				// username
				$username = $returnInfo['username'];
				
				// create new library - 1
				$inputList = array();
				$inputList['libraryType'] = 'newUser';
				$inputList['accountType'] = $accountType;
				$inputList['firstName'] = $firstName;
				$inputList['lastName'] = $lastName;
				$inputList['username'] = $username;
				$inputList['titleAppendix'] = "(1)";
				
				$newLibrary = new NewLibrary($viewHelperMgr);
				$newLibrary->createLibrary($inputList);
				
				// create new library - 2
				$inputList = array();
				$inputList['libraryType'] = 'newUser';
				$inputList['accountType'] = $accountType;
				$inputList['firstName'] = $firstName;
				$inputList['lastName'] = $lastName;
				$inputList['username'] = $username;
				$inputList['titleAppendix'] = "(2)";
				
				$newLibrary = new NewLibrary($viewHelperMgr);
				$newLibrary->createLibrary($inputList);
				
			} 
			
		} 
		
		// return
		$firstName = $cleanInputList['firstName'];
		$email = $cleanInputList['email'];
		$returnInfo = array('emailInUse'=>$emailInUse, 'createdNewUser'=>$createdNewUser, 'firstName'=>$firstName, 'email'=>$email);
		return $returnInfo;
	}
	
}