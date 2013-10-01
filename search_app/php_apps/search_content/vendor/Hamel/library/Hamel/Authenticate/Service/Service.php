<?php
/**
 * Byblio.
 * User authentication service extension - stores variable info across php session
 * @copyright 2013 Byblio.com
 * @author: Paul A. Oliver
 *
 */

namespace Hamel\Authenticate\Service;

use Zend\Authentication\AuthenticationService as AuthenticationService;
use Zend\Authentication\Storage\Session as SessionStorage;
use Hamel\Sessionmanagement\Upload as SessionMgtUpload;
use Hamel\Sessionmanagement\Download as SessionMgtDownload;
use Hamel\Db\General as DbGeneral;
use Zend\Session\Container as SessionContainer;
use Hamel\CountryAccess\General as CAGeneral;

// called on every page by layout view
// uses the PHP $SESSION variable to persist variable user information (Zend Authentication service does not allow changes to Identity).
class Service extends AuthenticationService{
	
	public $variableUserInfo;
	protected $_unknownSession;
	protected $_unknownUserInfo;
	private $_unknownNamespace;
	protected $_loggedIn;
	private $_numCharsInVKey;
	
	// constructor
	public function __construct(){
		
		// set name space for logged in users
		$this->setStorage(new SessionStorage('byblioAuth'));
		
		// namespace for unknown users
		$this->_unknownNamespace = 'ByblioUnknown';
		
		
		// create user variable in session if not exist
		if(!key_exists('hamelAuthUserInfo', $_SESSION)){
			$_SESSION['hamelAuthUserInfo'] = array();
		}
		
		// ref
		$this->variableUserInfo = &$_SESSION['hamelAuthUserInfo'];
		
		// set min variables if not present:
		
		// personal user account libraries
		if(!key_exists('userLibraries', $this->variableUserInfo)){
			$this->variableUserInfo['userLibraries'] = array();
		}
		
		// group account libraries
		if(!key_exists('groupLibraries', $this->variableUserInfo)){
			$this->variableUserInfo['groupLibraries'] = array();
		}
		
		// user info
		if(!key_exists('userInfo', $this->variableUserInfo)){
			$this->variableUserInfo['userInfo'] = array();
		}
		
		// context keys
		if(!key_exists('contextKeys', $this->variableUserInfo)){
			$this->variableUserInfo['contextKeys'] = array();
		}
		
		// num chars in verification keys
		$this->_numCharsInVKey = 10;
	}
	
	
	
	
	
	
	// checks to see if the current session info matches the session info stored in the db
	// the info  in the db is the most current
	public function checkSameBrowser(){
		
		// defaault
		$sameBrowser = false;
		
		// get current session info
		$userInfo = $this->getIdentity();
		$username = $userInfo['username'];
	
		$dbSessionId = $userInfo['dbSessionId']; // the db id tied to this session
		
		// php session id
		$phpSessionId = session_id();
		
		$httpUserAgent = $_SERVER['HTTP_USER_AGENT'];
		
		$inputList = array();
		$inputList['dbSessionId'] = $dbSessionId;
		$inputList['phpSessionId'] = $phpSessionId;
		$inputList['username'] = $username;
		$sessionMgtDownload = new SessionMgtDownload();
		$checkResult = $sessionMgtDownload->userVerifySession($inputList, true);
		
		// if this session info does not match the current in the db
		if($checkResult){
			$sameBrowser = true;
		}
		
		// return
		return $sameBrowser;
	}
	
	
	
	
	// closes the existing unknonw session
	public function closeUnknownSession($dbSessionId){
		
			// close
			$inputList = array('dbSessionId'=>$dbSessionId);
			$sessionMgtUpload = new SessionMgtUpload();
			$sessionMgtUpload->userUnknownEndSession($inputList, true);
	}
	
	
	
	// log unknown user
	public function logUnknowUser($controller){
		
		// regenrate php session id
		session_regenerate_id(true);
		$phpSessionId = session_id();
			
		// get ip address
		$dbGeneral = new DbGeneral();
		$ipAddress = $dbGeneral->getRealIpAddr();
			
		// get HTTP user agent
		$httpUserAgent = $_SERVER['HTTP_USER_AGENT'];
			
		// record session info in db
		$sessionMgtUpload = new SessionMgtUpload();
		$inputList = array('phpSessionId'=>$phpSessionId, 'ipAddress'=>$ipAddress, 'httpUserAgent'=>$httpUserAgent);
		$dbSessionId = $sessionMgtUpload->userUnknownStartSession($inputList, true);
			
		// create a random key to tie to this session id (this key changes troughout the session)
		$hamelKey = $this->_createVerificationKey();

		// get country code of access
		$translate = $controller->getServiceLocator()->get('viewhelpermanager')->get('translate');
		$countryAccess = new CAGeneral($translate, false, 'user_unknown');
		$countryInfo = $countryAccess->getBrowserAccessCountry(false);// use actual contry of browser
		
		// session storage info for unknown user
		$this->_unknownSession = new SessionContainer($this->_unknownNamespace);
		$currentSessionInfo = $this->_unknownSession->getManager()->getStorage();
		
		// clear all existing data
		$currentSessionInfo->clear();
		
		
		
		// add to storage
		$currentSessionInfo->dbSessionId = $dbSessionId;
		$currentSessionInfo->ipAddress = $ipAddress;
		$currentSessionInfo->phpSessionId = $phpSessionId;
		$currentSessionInfo->httpUserAgent = $httpUserAgent;
		$currentSessionInfo->hamelKey = $hamelKey;
		$currentSessionInfo->countryInfo = $countryInfo;
		
		// record
		$this->_unknownUserInfo = array(
				'dbSessionId' =>$dbSessionId,
				'ipAddress' =>$ipAddress,
				'phpSessionId' =>$phpSessionId,
				'httpUserAgent' =>$httpUserAgent,
				'hamelKey' =>$hamelKey,
				'countryInfo' =>$countryInfo,
				);
	}
	
	
	// returns unknown user info
	public function getUnknownUserInfo(){
		
		// if already exists
		if(!is_array($this->_unknownUserInfo)){
			
			// retrieve from session storage
			$unknownSession = new SessionContainer($this->_unknownNamespace);
			$currentSessionInfo = $unknownSession->getManager()->getStorage();
			
			// add to storage
			$dbSessionId = $currentSessionInfo->dbSessionId;
			$ipAddress = $currentSessionInfo->ipAddress;
			$phpSessionId = $currentSessionInfo->phpSessionId;
			$httpUserAgent = $currentSessionInfo->httpUserAgent;
			$hamelKey = $currentSessionInfo->hamelKey;
			$countryInfo = $currentSessionInfo->countryInfo;
			
			// record
			$this->_unknownUserInfo = array(
					'dbSessionId' =>$dbSessionId,
					'ipAddress' =>$ipAddress,
					'phpSessionId' =>$phpSessionId,
					'httpUserAgent' =>$httpUserAgent,
					'hamelKey' =>$hamelKey,
					'countryInfo' =>$countryInfo,
			);
			
		}
		
		// return info
		$returnInfo = $this->_unknownUserInfo;
		
		// return
		return $returnInfo;
	}
	
	
	// verifies unknown session
	public function verifyUnknownSession(){
		
		// default
		$verified = false;
		
		// session strorage info for unknown user
		$this->_unknownSession = new SessionContainer($this->_unknownNamespace);
		$currentSessionInfo = $this->_unknownSession->getManager()->getStorage();
		
		// get session info
		$dbSessionId = $currentSessionInfo->dbSessionId;
		$phpSessionId = $currentSessionInfo->phpSessionId;
		$httpUserAgent = $currentSessionInfo->httpUserAgent;
			
		// current info
		$c_phpSessionId = session_id();
		$c_httpUserAgent = $_SERVER['HTTP_USER_AGENT'];
			
		// if not same browser settings
		if($c_phpSessionId != $phpSessionId || $c_httpUserAgent != $httpUserAgent){
			$verified = false;
		} else {
			// check validity of db id
			$inputList = array();
			$inputList['dbSessionId'] = $dbSessionId;
			$inputList['phpSessionId'] = $phpSessionId;
			$inputList['httpUserAgent'] = $httpUserAgent;
			$sessionMgtDownload = new SessionMgtDownload();
			$verified = $sessionMgtDownload->userUnknownVerifySession($inputList, true);
		}
		
		return $verified;
	}
	
	
	
	
	
	// creates a new context-value pair. This is checked on receipt of ajax requests to verify authenticity.
	// the value (but not the context) is sent to the client
	// context and value stored in session 
	public function getVerificationValues($contextList){
		
		// list of keys
		$returnInfo = array();
		
		// context information
		if(is_array($contextList)){
				
			// logged in?
			if($this->hasIdentity()){
				// flag
				$loggedIn = true;
				
				// get session-fixed user info
				$userInfo = $this->getIdentity();
				// get db sesion id
				$dbSessionId = $userInfo['dbSessionId'];
				$phpSessionId = $userInfo['phpSessionId'];
				
			} else {
				// flag
				$loggedIn = false;
				
				// session strorage info for unknown user
				$this->_unknownSession = new SessionContainer($this->_unknownNamespace);
				$currentSessionInfo = $this->_unknownSession->getManager()->getStorage();
				
				// ensure context list exists
				if(!is_array($currentSessionInfo->contextKeys)){
					$currentSessionInfo->contextKeys = array();
				}
				
				// get db sesion id
				$dbSessionId = $currentSessionInfo->dbSessionId;
				$phpSessionId = $currentSessionInfo->phpSessionId;
				
			}
			
			// create keys
			foreach($contextList as $context){
				
				// new key
				$key = $this->_createVerificationKey();
				
				// record/ replace
				if($loggedIn){
					$this->variableUserInfo['contextKeys'][$context] = $key;
				} else {
					$currentSessionInfo->contextKeys[$context] = $key;
				}
				
				// record in return list
				$returnInfo[$context] = $key;
			}
		}
		
		// add session ids
		$returnInfo['dbSessionId'] = $dbSessionId;
		$returnInfo['phpSessionId'] = $phpSessionId;
		
		return $returnInfo;
		
	}
		
	
	// verifies given context info, including core session info (dbSessionID and php session id)
	public function validateRequest($context = null, $inputList = null){
		
		// defaults
		$verified = false;
		
		if(!is_array($inputList)){
			$inputList = array('alpha'=>"", 'beta'=>"", 'gama'=>"");
		}
		
		// logged in?
		if($this->hasIdentity()){
			// flag
			$loggedIn = true;
		} else {
			// flag
			$loggedIn = false;
		}
		
		if($loggedIn){
				
			// get session-fixed user info
			$userInfo = $this->getIdentity();
				
			// core info
			$dbSessionId = $userInfo['dbSessionId'];
			$phpSessionId = $userInfo['phpSessionId'];
				
			// context value
			$verificationValue = $this->variableUserInfo['contextKeys'][$context];
				
		} else {
				
			// session strorage info for unknown user
			$this->_unknownSession = new SessionContainer($this->_unknownNamespace);
			$currentSessionInfo = $this->_unknownSession->getManager()->getStorage();
				
			// get core session info
			$dbSessionId = $currentSessionInfo->dbSessionId;
			$phpSessionId = $currentSessionInfo->phpSessionId;
				
			// context value
			if(is_array($currentSessionInfo->contextKeys)){
				$contextKeys = $currentSessionInfo->contextKeys;
				$verificationValue = $contextKeys[$context];
			}
		
		}
		
		if($verificationValue == $inputList['alpha']){
			if($dbSessionId == $inputList['beta']){
				if($phpSessionId == $inputList['gama']){
					$verified = true;
				}
			}
		}
		
		return $verified;
	}
	
	
	// creates an random key used for verifying ajax and form requests
	private function _createVerificationKey(){
	
		$chars = "abcdefghijkmnopqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ023456789";
		srand((double)microtime()*1000000);
		$i = 0;
		$key = "";
	
		while ($i < $this->_numCharsInVKey) {
			$num = rand() % 33;
			$tmp = substr($chars, $num, 1);
			$key = $key . $tmp;
			$i++;
		}
	
		return $key;
	}
	
	
	// logs out known user
	public function logout(){
		
		$userLoggedIn = $this->hasIdentity();
		if($userLoggedIn){
			
			// get session-fixed user info
			$userInfo = $this->getIdentity();
			
			// username
			$username = $userInfo['username'];	
			
			// clear identity
			$this->clearIdentity();
			
			// clear varaible session info
			unset($_SESSION['hamelAuthUserInfo']);
			
			// update db
			$sessionMgtUpload = new SessionMgtUpload();
			$sessionMgtUpload->userEndSession(array('username'=>$username), true);
			
		}
	}
	
	
	
	
}