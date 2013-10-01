<?php
/**
 * Byblio
 * user activating account link
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

class ActivateController extends AbstractActionController{
	
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
    	$activateInfo = $this->_activateUserlogin($inputList);
    	
    	// results
    	$activatedUser = $activateInfo['activatedUser'];
    	$alreadyActivated = $activateInfo['alreadyActivated'];
    	$firstName = $activateInfo['firstName'];
		
    	
		// record flags
		$viewInfo['activatedUser'] = $activatedUser;
		$viewInfo['alreadyActivated'] = $alreadyActivated;
		$viewInfo['firstName'] = $inputList['firstName'];
		
		
		 // return
		$rtn = new ViewModel($viewInfo);
		 
		return $rtn;
		
	}

	
	// attempts to activate user and log them in, returns boolean
	private function _activateUserlogin($infoList){
		
		// return
		$activatedUser = false;
		$alreadyActivated = false;
		
		// check registration details
		$usermanagementDownload = new UsermanagementDownload();
		$userInfo = $usermanagementDownload->getRegistrationDetails($infoList, true);
		
		if(is_array($userInfo)){
			$username = "";
				
			if(key_exists('username', $userInfo)){
				$username = $userInfo['username'];
			}
				
			// set flag if have correct details
			if($username !=""){
				
				// clear registration details
				$infoList['username'] = $username;
				$usermanagementUpload = new UsermanagementUpload();
				$usermanagementUpload->deleteRegistrationToComplete($infoList, true);
		
				// activate user account
				$userInfo['status'] = 1;
				$activatedUser = $usermanagementUpload->updateStatus($userInfo, true);
		
				// log user in
				$authList = array();
				$authList['username'] = $username;
				$hamelLogin = new HamelLogin('userActivateAccount', $authList);
				$loginInfo = $hamelLogin->login();
				
			}
		} else {
			// no info, so assume already actived
			$alreadyActivated = true;
		}
		
		$returnInfo = array('activatedUser'=>$activatedUser, 'alreadyActivated'=>$alreadyActivated);
		return $returnInfo;
		
	}
	
	
}