<?php
/**
 * Byblio login management
 * Attempts to log in a user via user details (email and password) or via activation request
 * If user login and unsuccessful, processes then number on unsuccessful attempts:
 *  if more than 5 (unsucessfull) attempts in less than 24 hours, account is locked against the attempt ip address
 * If correct user login details but account is locked:
 * 	if greater than 24 hours since last attempt, account unlocked and user logged in
 * @copyright 2013 Byblio.com
 * @author: Paul A. Oliver
 *
 */

namespace Useraccount\Model;

use Hamel\Authenticate\Adapter\Adapter as AuthAdapter;
use Hamel\Authenticate\Service\Service as HAuthenticationService;
use Hamel\Db\General as DbGeneral;
use Hamel\Usermanagement\Download as UsermanagementDownload;
use Hamel\Usermanagement\Upload as UsermanagementUpload;


class Login extends AuthAdapter{
	
	private $_authAdapter;
	private $_minUserFailAttemptsTime;
	private $_maxNumFailAttempts;
	private $_minHackerFailAttemptsTime;
	private $_viewModel;
	
	
	// constructor
	public function __construct($e, $translate, $autheticateType, $userInfo){
		
		// instantiate the authentication adapter (stores user info)
		$this->_authAdapter = new AuthAdapter($translate, $autheticateType, $userInfo);
		
		// min allowed time for unsuccessful login attempts (seconds), assumed to be by user
		$this->_minUserFailAttemptsTime = 60*60*24; // 24 hours
		
		// min allowed time for unsuccessful login attempts (seconds), assumed to be by hacker
		$this->_minHackerFailAttemptsTime = 30; // 30 seconds
		
		// max num of unsuccessful attempts before account is locked
		$this->_maxNumFailAttempts = 6;
		
		//  num of unsuccessful attempts when user is warned that account will be locked
		$this->_numFailAttemptsWarn = 3;
		
		// current view model
		$this->_viewModel = $e->getViewModel();
	}
	
	// check login request
	public function login(){
	
		// defaults
		$loginOK = false;
		$accountLocked = false;
		$accountNotActive = false;
		$nextTimeStr = "";
		$failedLoginInfo = array('warn' => false);
		
	
	
		/// instantiate the authentication service
		$auth = new HAuthenticationService();
		$result = $auth->authenticate($this->_authAdapter);
		
		// check result
		$loginOK = $result->isValid();
		

		if(!$loginOK){ // log in failed
			
			// authenticate type
			$autheticateType = $this->_authAdapter->_autheticateType;
			
			switch($autheticateType){
				case 'userlogin': // someone/ something trying to remote login
					
					// get failure reason
					$authResultCode = $result->getCode();
					
					if($authResultCode == -4){
						// get reason failed
						$failedReason = $auth->variableUserInfo['failedReason'];	
						
						
						switch($failedReason){
							case 'accountLocked':
								
								// get ip address
								$dbGeneral = new DbGeneral();
								$ipAddress = $dbGeneral->getRealIpAddr();
								
								// email address
								$email =  $this->_authAdapter->_email;
								
								// get unsuccessful attempt history					
								$usermanagementDownload = new UsermanagementDownload();
			 					$history = $usermanagementDownload->getLoginAttemptHistory(array('ipAddress'=>$ipAddress, 'email'=>$email), true);
								
			 					// info
			 					$firstAttemptTimeStr = $history['firstAttemptTime'];
			 					$firstAttemptTime = strtotime($firstAttemptTimeStr);
			 					
			 					// get time since last attempt
			 					$currentTime = strtotime('now');
			 					$elapsedTime = $currentTime - $firstAttemptTime;
			 					
			 					if($elapsedTime > $this->_minUserFailAttemptsTime){ // ok to clear history
			 						// clear failed attempt history (and do not lock account)
			 						$inputList = array();
			 						$inputList['email'] = $email;
			 						$inputList['ipAddress'] = $ipAddress;
			 						$usermanagementUpload = new UsermanagementUpload();
			 						$usermanagementUpload->unlockAccount($inputList, true);
			 					
			 					} else {
			 						// flag
			 						$accountLocked = true;
			 						
			 						// calc time until ok to try again
			 						$nextTime = $firstAttemptTime + $this->_minUserFailAttemptsTime;
			 						$nextTimeStr = date('H:i \o\n l jS \of F Y', $nextTime);
			 					}
			 					
								
							break;
							
							case 'notActivated':
								// flag
								$accountNotActive = true;
							break;		
						}
						
					}
					
					if($authResultCode == -3){ // incorrect user details, but account not locked nor exceeded attempts
						// get ip address
						$dbGeneral = new DbGeneral();
						$ipAddress = $dbGeneral->getRealIpAddr();
						
						// email address
						$email =  $this->_authAdapter->_email;
						
						// get unsuccessful attempt history
						// update with this failed attempt
						$usermanagementDownload = new UsermanagementDownload();
	 					$history = $usermanagementDownload->getLoginAttemptHistory(array('ipAddress'=>$ipAddress, 'email'=>$email), true);

	 					// info
						$numAttempts = intval($history['numAttempts']);
						$firstAttemptTimeStr = $history['firstAttemptTime'];
						$firstAttemptTime = strtotime($firstAttemptTimeStr);
						
						// get time since last attempt
						$currentTime = strtotime('now');
						$elapsedTime = $currentTime - $firstAttemptTime;
						
						// too many attempts?
						if($numAttempts> $this->_maxNumFailAttempts){
							if($elapsedTime <= $this->_minHackerFailAttemptsTime){
								// ban this ip address
							}
								
							if($elapsedTime <= $this->_minUserFailAttemptsTime){
								// lock email and ip address
								$inputList = array();
								$inputList['email'] = $email;
								$inputList['ipAddress'] = $ipAddress;
								$usermanagementUpload = new UsermanagementUpload();
								$usermanagementUpload->lockAccount($inputList, true);
								
								// set flag
								$accountLocked = true;
								
								// calc time until ok to try again
								$nextTime = $firstAttemptTime + $this->_minUserFailAttemptsTime;
								$nextTimeStr = date('H:i \o\n l jS \of F Y', $nextTime);
							}
							
							
	 					} else { // within number of failed attempts
	 						
	 						// if to give a warning
	 						if($numAttempts >= $this->_numFailAttemptsWarn){
	 							
	 							// warning info
	 							$failedLoginInfo['warn'] = true;
	 							$failedLoginInfo['numAttempts'] = $numAttempts;
	 							$failedLoginInfo['maxNumAttempts'] = $this->_maxNumFailAttempts;
	 								
	 						}
	 						
	 					}
					}
					
				break;
				
				case 'userActivateAccount':
				case 'userResetPassword':
					
					
				break;
			}
			
			
			
			
		} else {
			// logged in
			$userInfo = $auth->getIdentity();
			
			// user variable info
			$userLibraries = $auth->variableUserInfo['userLibraries'];
			$groupLibraries = $auth->variableUserInfo['groupLibraries'];
			$groupAcInfo = $auth->variableUserInfo['groupAcInfo'];
			$homeURIInfo = $auth->variableUserInfo['homeURIInfo'];
			
			// update view variables
			$this->_viewModel->setVariable('loggedIn', true);
			$this->_viewModel->setVariable('userInfo', $userInfo);
			$this->_viewModel->setVariable('userLibraries', $userLibraries);
			$this->_viewModel->setVariable('groupLibraries', $groupLibraries);
			$this->_viewModel->setVariable('groupAcInfo', $groupAcInfo);
			$this->_viewModel->setVariable('homeURIInfo', $homeURIInfo);
			
			
		}
		
		// return info
		$loginInfo = array();
		$loginInfo['loginOK'] = $loginOK;
		$loginInfo['userInfo'] = $userInfo;
		$loginInfo['accountLocked'] = $accountLocked;
		$loginInfo['accountNotActive'] = $accountNotActive;
		$loginInfo['nextTimeStr'] = $nextTimeStr;
		$loginInfo['failedLoginInfo'] = $failedLoginInfo;
		
		return $loginInfo;
		
	}
	
	
	
	
	
		
	
}