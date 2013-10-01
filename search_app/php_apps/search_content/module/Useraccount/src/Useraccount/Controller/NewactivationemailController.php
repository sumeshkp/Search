<?php
/**
 * Byblio
 * for user to request new account activation email
 * @copyright 2013 Byblio.com
 * @author: Paul A. Oliver
 *
 */

namespace Useraccount\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Hamel\Form\User\NewAccountActivationEmailForm as NewActivationForm;
use Hamel\Form\User\ValidateFilter\NewAccountActivationEmailFormValidate as NewActivationFormValidate;
use Useraccount\Model\NewAccount as NewAccount;
use Hamel\Usermanagement\Download as UsermanagementDownload;
use Hamel\Db\General as DbGeneral;

class NewactivationemailController extends AbstractActionController{
	
	// landing page for user to request new email to activate account
	public function homeAction(){
		
		// redirect if user logged in
		$this->checkForLogin->loggedInRedirect();
		
		// logo
		$headerLogo = 'blue';
		 
		// translator
		$translate = $this->getServiceLocator()->get('viewhelpermanager')->get('translate');
		 
		// browesr title
		$browserTitle = $translate('Activate');
		 
		// request new email form
		$newEmailFormName = 'form_user-newAccountActivation';
		$inputs = array();
		$inputs['formName'] = $newEmailFormName;
		$inputs['placeholder'] = array('email'=>$translate('Email'));
		$inputs['buttonValues'] = array('submit'=>$translate('Send me a new email'));
		$newEmailForm  = new NewActivationForm($inputs);
		
		// defaults
		$emailSent =  false;
		$alreadyActivated =  false;
		$emailNotInUse = false;
		$requestEmail = "";
		 
		// form posted?
		$request = $this->getRequest();
		
		if ($request->isPost()){ // if user posted form
				
			// name of posted form
			$postedFormName = $this->getRequest()->getPost('formname');
				
			if($postedFormName == $newEmailFormName) { // user clicked on request new email form
				
				$requestValidate = new NewActivationFormValidate();
				$newEmailForm->setInputFilter($requestValidate->getInputFilter());
				$newEmailForm->setData($request->getPost());
					
				if ($newEmailForm->isValid()){
						
					// process
					$requestInfo = $this->_processRequestNewEmail($request);
					
					// flags
					$emailSent =  $requestInfo['emailSent'];
					$alreadyActivated =  $requestInfo['alreadyActivated'];
					$emailNotInUse = $requestInfo['emailNotInUse'];
					
					// email entered
					$requestEmail = $requestInfo['email'];
				}
			}
		}
		
				
		
		
		
		
		// return view
		$rtn = new ViewModel(array(
				'headerLogo' => $headerLogo,
				'browserTitle' => $browserTitle,
				'newEmailForm' =>$newEmailForm,
				'requestEmail' =>$requestEmail,
				'emailSent' =>$emailSent,
				'alreadyActivated' =>$alreadyActivated,
				'emailNotInUse' =>$emailNotInUse,
		));
		 
		return $rtn;
		
	}

	
	// processes request for new email
	private function _processRequestNewEmail($request){
		
		// defaults
		$emailNotInUse = false;
		$alreadyActivated = false;
		$emailSent = false;
		$email = "";
		
		// raw values
		$values = $request->getPost();
		
		// clean
		$dbGeneral = new dbGeneral();
		$cleanInputList = $dbGeneral->mysqlHTML_cleanInputOutput(
				array('email'=>$values['newAccountActivation-email'],
				)
				, array()
		);
		
		// check if email is in use
		$email = $cleanInputList['email'];
		$mgtDownload = new UsermanagementDownload();
		$username = $mgtDownload->checkUserEmailInDb($email, true);
		if(!$username){
			$emailNotInUse = true;
		}
		
		if($username){
			// check status of account
			$inputList = array();
			$inputList['username'] = $username; 
			$userInfo = $mgtDownload->getUserCoreDetailsByUsername($inputList, true);
			
			// status
			$status = 0;
			if(key_exists('status', $userInfo)){
				$status = $userInfo['status'];
			}
			
			if($status !=0){
				// set flag
				$alreadyActivated = true;
			
			} else {
			
				// send new email
				$viewHelperMgr = $this->getServiceLocator()->get('viewhelpermanager');
				$newAccount = new NewAccount($viewHelperMgr);
				$returnInfo = $newAccount->resendActivationEmail($userInfo, true);
			
				// set flag
				$emailSent = true;
			}
		
		}
		
		// return
		$returnInfo = array('emailNotInUse'=>$emailNotInUse, 'alreadyActivated'=>$alreadyActivated, 'emailSent'=>$emailSent, 'email'=>$email);
		
		return $returnInfo;
		
	}
	

	
	
}