<?php
/**
 * Byblio
 * user activating reset password link
 * @copyright 2013 Byblio.com
 * @author: Paul A. Oliver
 *
 */

namespace Useraccount\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Hamel\Usermanagement\Download as UsermanagementDownload;
use Hamel\Usermanagement\Upload as UsermanagementUpload;
use Hamel\Db\General as DbGeneral;
use Useraccount\Model\Login as HamelLogin;

class ActivatenewpasswordController extends AbstractActionController{
	
	// landing page from activation email
	public function homeAction(){
		
		// redirect if user logged in
		$this->checkForLogin->loggedInRedirect();
		
		// logo
		$headerLogo = 'blue';
		 
		// translator
		$translate = $this->getServiceLocator()->get('viewhelpermanager')->get('translate');
		 
		// browesr title
		$browserTitle = $translate('Activate');
		
		// view array
		$viewInfo = array('headerLogo' => $headerLogo, 'browserTitle' => $browserTitle);
				
		// flag
		$activatedUser = false;
		
		// get values from uri
		$id1 = $this->params()->fromRoute('id1', 0);
		$id2 = $this->params()->fromRoute('id2', 1);
		$id3 = $this->params()->fromRoute('id3', 2);
		$id4 = $this->params()->fromRoute('id4', 3);
		
		// clean inputs
		$inputList = array();
		$inputList['usernameScramble'] = $id1;
    	$inputList['activationKey'] = $id4;
    	$inputList['firstName'] = $id2;
    	$inputList['lastName'] = $id3;
    	$dbGeneral = new DbGeneral();
    	$inputList = $dbGeneral->mysqlHTML_cleanInputOutput($inputList, array());
    	
		
		// activate
    	$activateInfo = $this->_activateResetLogin($inputList);
    	
    	// results
    	$activatedNewPassword = $activateInfo['activatedNewPassword'];
    	$alreadyActivated = $activateInfo['alreadyActivated'];
    	$firstName = $activateInfo['firstName'];
		
    	
		// record flags
		$viewInfo['activatedNewPassword'] = $activatedNewPassword;
		$viewInfo['alreadyActivated'] = $alreadyActivated;
		$viewInfo['firstName'] = $inputList['firstName'];
		
		
		 // return
		$rtn = new ViewModel($viewInfo);
		 
		return $rtn;
		
	}

	
	// attempts to activate new password and log user in
	private function _activateResetLogin($infoList){
		
		// defaults
		$activatedNewPassword = false;
		$alreadyActivated = false;
		$firstName = "";
		
		// check password reset details
		$usermanagementDownload = new UsermanagementDownload();
		$userInfo = $usermanagementDownload->getPasswordResetDetails($infoList, true);
		
		if(is_array($userInfo)){
			$username = "";
			$encPassword = "";
				
			if(key_exists('username', $userInfo)){
				$username = $userInfo['username'];
			}
			if(key_exists('password', $userInfo)){
				$encPassword = $userInfo['password'];
			}
			if(key_exists('firstName', $userInfo)){
				$firstName = $userInfo['firstName'];
			}
				
			// set flag if have correct details
			if($username !="" && $encPassword !=""){
				
				// clear password reset details
				$inputList = array();
				$inputList['username'] = $username;
				$usermanagementUpload = new UsermanagementUpload();
				$usermanagementUpload->deleteNewUserPwdResetToComplete($inputList, true);
		
				// set new password
				$inputList = array();
				$inputList['username'] = $username;
				$inputList['password'] = $encPassword;
				$activatedUser = $usermanagementUpload->updatePassword($userInfo, true);
		
				// log user in
				$authList = array();
				$authList['username'] = $username;
				$hamelLogin = new HamelLogin('userResetPassword', $authList);
				$loginInfo = $hamelLogin->login();
				
				// set flag
				$activatedNewPassword = true;
			}
		} else {
			// no info, so assume already actived
			$alreadyActivated = true;
		}
		
		$returnInfo = array('firstName'=>$firstName, 'activatedNewPassword'=>$activatedNewPassword, 'alreadyActivated'=>$alreadyActivated);
		return $returnInfo;
		
	}
	
	
}