<?php

/**
 * Byblio.
 * Authentication adapter used to verify user on log in.
 * Load core user (if successful log in) and stores across PHP session.
 * @copyright 2013 Byblio.com
 * @author: Paul A. Oliver
 *
 */

namespace Hamel\Authenticate\Adapter;
use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Result as AuthResult;
use Hamel\Authenticate\Service\Service as HAuthenticationService;
use Hamel\Usermanagement\Upload as UsermanagementUpload;
use Hamel\Usermanagement\Download as UsermanagementDownload;
use Hamel\AccountManagement\Options as AccountOptions;
use Hamel\Librarymanagement\Download as LibrarymanagementDownload;
use Hamel\Db\General as DbGeneral;
use Hamel\AccountManagement\Download as AcManagementDownload;
use Hamel\CountryAccess\General as CAGeneral;
use Hamel\Sessionmanagement\Upload as SessionMgtUpload;



// authenticates user log in
class Adapter implements AdapterInterface
{
	// array containing authenticated user record
	protected $_sessionInfo;
	
	// user info used to log in
	protected $_email;
	protected $_password;
	protected $_pwdScrambled; 
	protected $_username; 
	protected $_translate;

	
	
	// constructor
	public function __construct($translate, $autheticateType, $userInfo){
		
		if(is_array($userInfo)){
			
			switch($autheticateType){
				case 'userlogin':
					
					// defaults
					$email ="";
					$password = "";
					$pwdScrambled = false;
					
					// record user details
					if(key_exists('email', $userInfo)){
						$email = trim(mb_strtolower($userInfo['email'])); // NOTE: lowercase email stored
					}
					if(key_exists('password', $userInfo)){
						$password = trim($userInfo['password']);
					}
					if(key_exists('pwdScrambled', $userInfo)){
						$pwdScrambled = trim($userInfo['pwdScrambled']);
					}
					
					// store info
					$this->_email = $email;
					$this->_password = $password;
					$this->_pwdScrambled = $pwdScrambled; // flag if password is encrypted or not
					
				break;
					
				case 'userActivateAccount':
				case 'userResetPassword':
					// defaults:
					$username ="";
					
					if(key_exists('username', $userInfo)){
						$username = $userInfo['username'];
					}
					
					// record info
					$this->_username = $username;
	
				break;
			}
				
		}
			
		// record authentication type
		$this->_autheticateType = $autheticateType;
		
		// ref translate
		$this->_translate = $translate;
	}
	
	

	
	// queries db.
	public function authenticate(){
		
		// default
		$logResult = -1;
		$authResult = -4;
		$username = "";
		
		// user management
		$usermanagementDownload = new UsermanagementDownload();
		$usermanagementUpload = new UsermanagementUpload();
		
		
		$autheticateType = $this->_autheticateType;
		
		switch($autheticateType){
			case 'userlogin':
		
				// read vals
				$email= $this->_email;
				$password = $this->_password;
				$pwdScrambled = $this->_pwdScrambled;
				
				// check if email is verified
				$emailVerified = $usermanagementDownload->verifyEmailAddress($email, false);
				
				switch($emailVerified){
					case 'not_found':
						// set failure flag
						$failedReason = 'emailUnknown';
						
						// set results code
						$authResult = -1; // FAILURE_IDENTITY_NOT_FOUND
					break;
					
					case 'not_verified':
						// set failure flag
						$failedReason = 'emailNotVerified';
						
						// set results code
						$authResult = -3; // FAILURE_CREDENTIAL_INVALID
					break;
					
					default:
					
						// check email and password
						$inputList = array();
						$inputList['password'] = $password;
						$inputList['email'] = $email;
						$inputList['pwdScrambled'] = $pwdScrambled;
						$emailPwdMatch = $usermanagementDownload->checkPasswordEmailMatch($inputList, false);
				
						
						if($emailPwdMatch){
							// set username
							$username = $emailVerified;
							
							// set auth result code
							$authResult = 1; // success
								
						} else { // password and email not match
		
							// set results code
							$authResult = -3; // FAILURE_CREDENTIAL_INVALID
						}
					break;
				}
				
				
			break;
			
			case 'userActivateAccount':
			case 'userResetPassword':
				// read the vals
				$username = $this->_username;
				
				// set auth result code
				$authResult = 1; // success
					
			break;
		}
				

		if($authResult == 1){ // login details ok
			
			// collect core user info and put into $this->_resultArray.
			$userInfo = $usermanagementDownload->getUserCoreDetails(array('username'=>$username), true);
			
			// get account status
			$accountStatus = $userInfo['accountStatus'];
			
			switch($accountStatus){
				case 'ac_locked': // account locked
					// auth result code
					$authResult = -4; // FAILURE_UNCATEGORIZED
					// record info
					$failedReason = 'accountLocked';
				break;
				
				case 'u_registered': // registered but not activated (registered by user)
				case 'b_registered': // (registered by byblio admin)
				case 'gac_registered': // (registered by group account admin)
					// auth result code
					$authResult = -4; // FAILURE_UNCATEGORIZED
					// record info
					$failedReason = 'notActivated';
				break;
				
				case 'u_activated': // registered and activated (activated by user)
				case 'b_activated': // (activated by byblio admin)
				case 'gac_activated': // (activated by group account admin)
					
					// nothing
					
				break;
				
				case 'u_closed': // account closed (closed by user)
				case 'b_closed': // (closed by byblio admin)
				case 'gac_closed': // (closed by group account admin)
					// auth result code
					$authResult = -4; // FAILURE_UNCATEGORIZED
					// record info
					$failedReason = 'closed';
				break;
				
				case 'u_suspended': // account suspended (suspended by user)
				case 'b_suspended': // (suspended by byblio admin)
				case 'gac_suspended': // (suspended by group account admin)
					// auth result code
					$authResult = -4; // FAILURE_UNCATEGORIZED
					// record info
					$failedReason = 'suspended';
				break;
			}
				
			
			
			if($authResult == 1){ // login ok and account active
	
				// clear any failed login history
				$usermanagementUpload->clearLoginAttemptHistory(array('email'=>$email), true);
				
				// get library ids of which this user is owner/ manager/ has access to (private and group account)
				$librarymanagementDownload = new LibrarymanagementDownload();
				$libraryInfo = $librarymanagementDownload->getUserAllLibraryMembership(array('username'=>$username), true);
				$userLibraries = $libraryInfo['user'];
				$groupLibraries = $libraryInfo['group'];
					
				// get group account membership info
				$acManagementDownload = new AcManagementDownload();
				$groupAcInfo = $acManagementDownload->getUserAllGroupCoreInfo(array('username'=>$username), true);
				
				
				// get ip address
				$dbGeneral = new DbGeneral();
				$ipAddress = $dbGeneral->getRealIpAddr();
				
				// regenrate php session id
				session_regenerate_id(true);
				$phpSessionId = session_id();
				
				// get HTTP user agent
				$httpUserAgent = $_SERVER['HTTP_USER_AGENT'];
				
				// record session info
				$sessionMgtUpload = new SessionMgtUpload();
				$inputList = array('username'=>$username, 'phpSessionId'=>$phpSessionId, 'ipAddress'=>$ipAddress, 'httpUserAgent'=>$httpUserAgent);
				$dbSessionId = $sessionMgtUpload->userStartSession($inputList, true);
				
				// uri of user's personal home page
				$homeURIInfo = $usermanagementDownload->getUserHomeURI(array('username'=>$username), true);
				
				// all core info
				$firstName = $userInfo['firstName'];
				$lastName = $userInfo['lastName'];
				$accountType = $userInfo['accountType'];

				// get clientside id of account type
				$accountOptions = new AccountOptions($this->_translate);
				$allAccountInfo = $accountOptions->getAccountTypes();
				$accountTypes = $allAccountInfo['byType'];
				$acTypeId = $accountTypes[$accountType]['id'];
				
				// get country code of access
				$translate = $this->_translate;
				$countryAccess = new CAGeneral($translate, true, $accountType);
				$browserCountryInfo = $countryAccess->getBrowserAccessCountry(false);// use actual contry of browser
				
				
				// set session info (cannot be modified during session)
				$this->_sessionInfo['firstName'] = $firstName;
				$this->_sessionInfo['lastName'] = $lastName;
				$this->_sessionInfo['username'] = $username;
				$this->_sessionInfo['email'] = $this->_email; // email used to log in
				$this->_sessionInfo['acTypeId'] = $acTypeId; // email used to log in
				$this->_sessionInfo['dbSessionId'] = $dbSessionId; // the db id tied to this session
				$this->_sessionInfo['phpSessionId'] = $phpSessionId;
				$this->_sessionInfo['countryInfo'] = $browserCountryInfo;
				
				// create hamel authentication service
				$auth = new HAuthenticationService();
					
				// variable info:
				$auth->variableUserInfo['userLibraries'] = $userLibraries; // library details
				$auth->variableUserInfo['groupLibraries'] = $groupLibraries;
				$auth->variableUserInfo['groupAcInfo'] = $groupAcInfo; // group info
				$auth->variableUserInfo['homeURIInfo'] = $homeURIInfo; // home page uri
				
				
			} else { // login not alowed
				
				// create hamel authentication service
				$auth = new HAuthenticationService();
					
				// library details
				$auth->variableUserInfo['failedReason'] = $failedReason;
				
			}
			
			
		}
		
		// create return result
		$authResult = new AuthResult($authResult, $this->_sessionInfo, array());

		return $authResult;
			
	}
	
	

	
}



