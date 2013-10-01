<?php
/**
 * Byblio
 * for user resetting password
 * @copyright 2013 Byblio.com
 * @author: Paul A. Oliver
 *
 */


namespace Useraccount\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Hamel\Form\User\ResetpasswordForm;
use Hamel\Form\User\ValidateFilter\ResetpasswordFormValidate;
use Useraccount\Model\ResetPassword;
use Hamel\Db\General as DbGeneral;


class ForgotpwdController extends AbstractActionController{
	
	// landing page for user (any type) login
	public function homeAction(){
		
		// redirect if user logged in
		$this->checkForLogin->loggedInRedirect();
		
		// logo
		$headerLogo = 'blue';
		 
		// translator
		$translate = $this->getServiceLocator()->get('viewhelpermanager')->get('translate');
		 
		// browesr title
		$browserTitle = $translate('Reset password');
		 
		// reset password form
		$resetFormName = 'form_user-resetPassword';
		$inputs = array();
		$inputs['formName'] = $resetFormName;
		$inputs['placeholder'] = array('email'=>$translate('Email'));
		$inputs['buttonValues'] = array('submit'=>$translate('Reset & send email'));
		$resetPasswordForm  = new ResetpasswordForm($inputs);
		
		// form posted?
		$request = $this->getRequest();
		
		// flags
		$formSubmitted_resetPassword = false;
		$resetPasswordEmailNotInUse = false;
		$resetPasswordEmail = "";
		$passwordReset = false;
		
		
		if ($request->isPost()){ // if user posted form
				
			// name of posted form
			$postedFormName = $this->getRequest()->getPost('formname');
				
			if($postedFormName == $resetFormName) { // user clicked on reset form
				
				// flag
				$formSubmitted_resetPassword = true;
				
				$resetValidate = new ResetpasswordFormValidate();
				$resetPasswordForm->setInputFilter($resetValidate->getInputFilter());
				$resetPasswordForm->setData($request->getPost());
					
				if ($resetPasswordForm->isValid()){
						
					// process
					$resetInfo = $this->_processResetPassword($request, $translate);
						
					// results
					$passwordReset = $resetInfo['passwordReset'];
					$resetPasswordEmailNotInUse = $resetInfo['emailNotInUse'];
					$resetPasswordEmail = $resetInfo['email'];
				
				}
						
						
			}
		}
				
				
		$rtn = new ViewModel(array(
				'headerLogo' => $headerLogo,
				'browserTitle' => $browserTitle,
				'resetPasswordForm' => $resetPasswordForm,
				'formSubmitted_resetPassword' => $formSubmitted_resetPassword,
				'resetPasswordEmailNotInUse' => $resetPasswordEmailNotInUse,
				'resetPasswordEmail' => $resetPasswordEmail,
				'passwordReset' => $passwordReset,
		));
		 
		return $rtn;
		
	}

	// processes request to reset password
	private function _processResetPassword($request, $translate){
		
		// defaults
		$emailNotInUse = false;
		
		// view helper mgr
		$viewHelperMgr = $this->getServiceLocator()->get('viewhelpermanager');
		
		// raw values
		$values = $request->getPost();
		
		// clean
		$dbGeneral = new dbGeneral();
		$cleanInputList = $dbGeneral->mysqlHTML_cleanInputOutput(
				array('email'=>$values['resetPassword-email'],		
				)
				, array()
		);
		
		$email = $cleanInputList['email'];
		
		// reset password and send email
		$resetList = array();
		$resetList['email'] = $email;
		$resetPassword = new ResetPassword($viewHelperMgr);
		$resetInfo = $resetPassword->resetPassword($resetList);
		
		
		$emailNotInUse = $resetInfo['emailNotInUse'];
		$passwordReset = $resetInfo['passwordReset'];

		
		// return info
		$returnInfo = array('emailNotInUse'=>$emailNotInUse, 'passwordReset'=>$passwordReset, 'email'=>$email);
		
		// return
		return $returnInfo;
		
		
	}
	
	

	
	

	
}