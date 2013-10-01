<?php

/**
 * Byblio User upload functions
 * @copyright 2013 Byblio.com
 * @author: Paul A. Oliver
 * 
*/

namespace Hamel\Usermanagement;

use Hamel\Db\General as DbGeneral;
use Hamel\Usermanagement\Download as UsermanagementDownload;

class Upload{
	
	private $_numCharsInRegKey;
	private $_dbGeneral;
	
	
	public function __construct(){
		// create db instance
		$this->_dbGeneral = new DbGeneral();
	
		// number of chars/digits in registration activation key
		$this->_numCharsInRegKey=20;
	}
	
	
	
	
	// NOTE: upload encrypted password only
	function createNewUser($infoList, $inputCleaned){
		
		if(!$inputCleaned){// clean strings
			$inputList = $this->_dbGeneral->mysqlHTML_cleanInputOutput($inputList, array());
			$inputCleaned = true;
		}
		
		$firstName = $infoList['firstName'];
		$lastName = $infoList['lastName'];
		$username = $infoList['username'];
		$usernameScramble = $infoList['usernameScramble'];
		$email = $infoList['email'];
		$pwd = $infoList['password'];
		$accountType = $infoList['accountType'];
		$status = $infoList['status'];
		$activationKey = $infoList['activationKey'];
		$userFileId = $infoList['userFileId'];
	
		$userId = 0; // default (user not created) 

		$mgtDownload = new UsermanagementDownload();
		
		// open db connection
		$my_db = $this->_dbGeneral->mysql_i_OpenDB('user');
	
		if(!is_int($my_db)) {
			
			// convert email to lower case
			$email= strtolower($email);
			
			// check to see if the email already exists
			$emailAlreadyExists = $mgtDownload->checkUserEmailInDb($email, $inputCleaned); 
			
			if(!$emailAlreadyExists){
				
				// NOTES:	
				// In 'prepare': the number of ?marks must equal the number of input parameters in the MySQL stored function.
				// In 'bind-param': the first parameter is a single string, where each char indicates the type of PHP variable being passed to the MySQL function.
				// i = integer; s = string; d = double, b = blob. This does NOT correspond to the type of variable in the MySQL function, it refer to the PHP.
				// The number of chars in this string must equal the number of input parameters in the MySQL stored function.
				$stmt= $my_db->prepare("SELECT UserAddUser(?, ?, ?, ?, ?, ?, ?, ?)") or die($my_db->error); 
				$stmt->bind_param('ssssssss', $firstName, $lastName, $email, $username, $userFileId, $pwd, $accountType, $status) or die($stmt->error);
				$stmt->execute() or die($stmt->error); 
				$stmt->bind_result($userId);   
				$stmt->fetch(); 
				$this->_dbGeneral->mysql_i_CloseDB($my_db);
				
				if($status == 0){// if new user is inactive, requires user to activate registration, so store info in db
					$inputList = array('username'=>$username, 'password'=>$pwd, 'activationKey'=>$activationKey, 'usernameScramble'=>$usernameScramble);
					$this->addNewRegistrationToComplete($inputList, $inputCleaned);
				}
			}
		}
		return $userId;
	}
	
	
	// stores info in db required for activating new account
	function addNewRegistrationToComplete($inputList, $inputCleaned){
		
		if(!$inputCleaned){// clean strings
			$inputList = $this->_dbGeneral->mysqlHTML_cleanInputOutput($inputList, array());
			$inputCleaned = true;
		}
		
		// input vals
		$activationKey = $inputList['activationKey'];
		$usernameScramble = $inputList['usernameScramble'];
		$username = $inputList['username'];
		
		// open db
		$my_db = $this->_dbGeneral->mysql_i_OpenDB('user');
		
		if(!is_int($my_db)) {// if have server
			$result = $my_db->query("CALL UserAddRegistration('$username', '$usernameScramble', '$activationKey')");
			$this->_dbGeneral->mysql_i_CloseDB($my_db); // close connection
		}	
	}
	
	
	// removes info in db required for activating new account
	function deleteRegistrationToComplete($inputList, $inputCleaned){
		
		if(!$inputCleaned){// clean strings
			$inputList = $this->_dbGeneral->mysqlHTML_cleanInputOutput($inputList, array());
		}
		
		// input vals
		$username = $inputList['username'];
		$usernameScramble = $inputList['usernameScramble'];
		$activationKey = $inputList['activationKey'];
		
		
		// open db
		$my_db = $this->_dbGeneral->mysql_i_OpenDB('user');
		
		if(!is_int($my_db)) {// if have server
			$result = $my_db->query("CALL UserDeleteRegistration('$username', '$usernameScramble', '$activationKey')"); // call remove user function
			$this->_dbGeneral->mysql_i_CloseDB($my_db); // close connection
		}
	}
	
	
	// creates a unique user id used in file structires
	// requires unique input (here, username)
	function createUserFileID($username){
		
		// salt
		$st1="&'e3SP-";
		$st2="{evR73a";
		$saltStr = $st1 .$username .$st2;
		
		// encrypt
		$returnStr = sha1($saltStr);
		
		return $returnStr;
	}
	
	
	// removes ALL info in db required for activating new account associated with the given user name
	function deleteAllRegistrationToComplete($inputList, $inputCleaned){
		
		if(!$inputCleaned){// clean strings
			$inputList = $this->_dbGeneral->mysqlHTML_cleanInputOutput($inputList, array());
		}
		
		// input vals
		$username = $inputList['username'];
		
		// open db
		$my_db = $this->_dbGeneral->mysql_i_OpenDB('user');
		
		if(!is_int($my_db)) {// if have server
			$result = $my_db->query("CALL UserDeleteAllRegistration('$username')"); // call remove user function
			$this->_dbGeneral->mysql_i_CloseDB($my_db); // close connection
		}
	}
	
	
	// locks user account
	function lockAccount($inputList, $inputCleaned){
		
		if(!$inputCleaned){// clean strings
			$inputList = $this->_dbGeneral->mysqlHTML_cleanInputOutput($inputList, array());
		}
		
		// input vals
		$email = $inputList['email'];
		$ipAddress = $inputList['ipAddress'];
		
		// open db
		$my_db = $this->_dbGeneral->mysql_i_OpenDB('user');
		
		if(!is_int($my_db)) {// if have server
			$result = $my_db->query("CALL UserLockAccount('$ipAddress', '$email')"); 
			$this->_dbGeneral->mysql_i_CloseDB($my_db); // close connection
		}
		
		return $result;
	}
	
	// unlocks user account
	function unlockAccount($inputList, $inputCleaned){
		
		if(!$inputCleaned){// clean strings
			$inputList = $this->_dbGeneral->mysqlHTML_cleanInputOutput($inputList, array());
		}
		
		// input vals
		$email = $inputList['email'];
		$ipAddress = $inputList['ipAddress'];
		
		// open db
		$my_db = $this->_dbGeneral->mysql_i_OpenDB('user');
		
		if(!is_int($my_db)) {// if have server
			$result = $my_db->query("CALL UserUnlockAccount('$ipAddress', '$email')"); 
			$this->_dbGeneral->mysql_i_CloseDB($my_db); // close connection
		}
		
		return $result;
	}
	
	
	
	// clears failed login attempt history
	// stores info in db for activating new password
	function clearLoginAttemptHistory($inputList, $inputCleaned){
	
		if(!$inputCleaned){// clean strings
			$inputList = $this->_dbGeneral->mysqlHTML_cleanInputOutput($inputList, array());
		}
	
		// input vals		
		$email = $inputList['email'];
	
		// open db
		$my_db = $this->_dbGeneral->mysql_i_OpenDB('user');
	
		if(!is_int($my_db)) {// if have server
			$result = $my_db->query("CALL UserClearFailedLoginHistory('$email')");
			$this->_dbGeneral->mysql_i_CloseDB($my_db); // close connection
		}
	}
	
	
	
	
	// stores info in db for activating new password
	function addNewUserPwdResetToComplete($inputList, $inputCleaned){
		
		if(!$inputCleaned){// clean strings
			$inputList = $this->_dbGeneral->mysqlHTML_cleanInputOutput($inputList, array());
		}
		
		// input vals
		$activationKey = $inputList['activationKey'];
		$usernameScramble = $inputList['usernameScramble'];
		$username = $inputList['username'];
		$newPwd = $inputList['password'];
		
		// open db
		$my_db = $this->_dbGeneral->mysql_i_OpenDB('user');
		
		if(!is_int($my_db)) {// if have server
			$result = $my_db->query("CALL UserAddResetPassword('$username', '$usernameScramble', '$newPwd', '$activationKey')"); // call add to db
			$this->_dbGeneral->mysql_i_CloseDB($my_db); // close connection
		}
	}
	
	
	// removes info from db used for activating new password
	function deleteNewUserPwdResetToComplete($inputList, $inputCleaned){
	
		if(!$inputCleaned){// clean strings
			$inputList = $this->_dbGeneral->mysqlHTML_cleanInputOutput($inputList, array());
		}
	
		// input vals
		$username = $inputList['username'];
	
		// open db
		$my_db = $this->_dbGeneral->mysql_i_OpenDB('user');
	
		if(!is_int($my_db)) {// if have server
			$result = $my_db->query("CALL UserDeleteResetPassword('$username')"); // call add to db
			$this->_dbGeneral->mysql_i_CloseDB($my_db); // close connection
		}
	}
	
	
	
	
	// creates an random activation key
	function createNewActivationKey(){
		
		 $chars = "abcdefghijkmnopqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ023456789"; 
		 srand((double)microtime()*1000000); 
		 $i = 0; 
		 $password = ""; 
	
		 while ($i < ($this->_numCharsInRegKey)) { 
			$num = rand() % 33; 
			$tmp = substr($chars, $num, 1); 
			$password = $password . $tmp; 
			$i++; 
		 } 

    	return $password;
	}
		
	
	
	// returns number of chars in reg key
	function getNumCharsInRegKey(){
		return $this->_numCharsInRegKey;
	}

	
	
	
	// creates a new password
	function createNewUserPassword(){
		
		 $chars = "abcdefghijkmnopqrstuvwxyz[]*^ABCDEFGHJKMNPQRSTUVWXYZ023456789"; 
		 srand((double)microtime()*1000000); 
		 $i = 0; 
		 $password = ""; 
	
		 while ($i <= 6) { 
			$num = rand() % 33; 
			$tmp = substr($chars, $num, 1); 
			$password = $password . $tmp; 
			$i++; 
		 } 

    	return $password; 

	}
		
		
// 	function deleteUser($userName) {
		
// 		// open db
// 		$my_db = mysql_i_OpenDB();
		
// 		if(!is_int($my_db)) {// if have server
// 			$userName = mysqlHTML_cleanInputOutput($userName);
// 			$result = $my_db->query("CALL RemoveUser('$userName')"); // call remove user function
// 			mysql_i_CloseDB($my_db); // close connection
// 		}
// 	}
	
// 	function disableUser($userName) {
		
// 		$my_db = mysql_i_OpenDB(); // open db
		
// 		if(!is_int($my_db)){	// if have link to db
// 			$userName = mysqlHTML_cleanInputOutput($userName);
// 			$result = $my_db->query("CALL DisableUser('$userName')"); // call remove user function
// 			mysql_i_CloseDB($my_db); // close connection
// 		}
// 	}
	
// 	function enableUser($userName) {
		
// 		$my_db = mysql_i_OpenDB();
		
// 		if(!is_int($my_db)) {
// 			$userName = mysqlHTML_cleanInputOutput($userName);
// 			$result = $my_db->query("CALL EnableUser('$userName')");
// 			mysql_i_CloseDB($my_db);
// 		}
// 	}
	
	
	
	// updates given user details
	function updateUserDetails($type, $inputList, $inputCleaned){
	
		if(!$inputCleaned){// clean strings
			$inputList = $this->_dbGeneral->mysqlHTML_cleanInputOutput($inputList, array());
		}
		
		// return result
		$result = false;
		
		// db
		$my_db = $this->_dbGeneral->mysql_i_OpenDB('user');
		
		if(!is_int($my_db)) {
			
			switch($type){
				case 'core':// first name, last name, username, country
					$username = $inputList['username'];
					$firstName = $inputList['firstName_new'];
					$lastName = $inputList['lastName_new'];
					$country = $inputList['country_new'];
					
					if(key_exists('username_new', $inputList)){
						$username_new = $inputList['username_new'];
					} else {
						$username_new = $inputList['username'];
					}
					
					$encPassword = $inputList['encPassword'];
					
					$result = $my_db->query("CALL UserUpdateCoreDetails('$encPassword', '$username', '$firstName', '$lastName', '$country', '$username_new')");
					
					break;
						
				case 'email': // email only
					$username = $inputList['username'];
					$inputStr = $inputList['email_new'];
					$result = $my_db->query("CALL UserUpdateEmail('$username', '$inputStr')");
				break;
						
				case 'password': // password only
					$username = $inputList['username'];
					$inputStr = $inputList['encPassword_new'];
					$result = $my_db->query("CALL UserUpdatePassword('$username', '$inputStr')");
				break;
				
			}
			
		$this->_dbGeneral->mysql_i_CloseDB($my_db); // close connection
		}
	
		// return
		return $result;
	}
	
	// updates given user details
	// NOTE: uses a user ID, not user name (called by admin user)
	function updateUserDetailsById($inputList, $inputCleaned){
	
		if(!$inputCleaned){// clean strings
			$inputList = $this->_dbGeneral->mysqlHTML_cleanInputOutput($inputList, array());
		}
		
		$updateType = $inputList['updateType'];
		
		// return result
		$result= false;
		
		// db
		$my_db = $this->_dbGeneral->mysql_i_OpenDB('user');
		
		if(!is_int($my_db)) {
			
			switch($updateType){
				case 'core':// first name, last name, country, account type, space type, group, status
					$userId = $inputList['userId'];
					$firstName = $inputList['firstName'];
					$lastName = $inputList['lastName'];
					$country = $inputList['country'];
					$accountType = $inputList['accountType'];
					$spaceType = $inputList['spaceType'];
					$group = $inputList['group'];
					$status = $inputList['status'];
					
					$result = $my_db->query("CALL UserUpdateCoreDetailsById('$userId', '$firstName', '$lastName', '$country', '$accountType', '$spaceType', '$group', '$status')");
					
					
				break;
						
				case 'email': // email only
					$userId = $inputList['userId'];
					$email = $inputList['email'];
					$result = $my_db->query("CALL UserUpdateEmailById('$userId', '$email')");
				break;
						

			}
			
		$this->_dbGeneral->mysql_i_CloseDB($my_db); // close connection
		}
	
		// return
		return $result;
	}
	
	
	
	
	
	
	// updates account status of user
	function updateStatus($inputList, $inputCleaned){
		
		// default
		$result = false;
		
		if(!$inputCleaned){// clean strings
			$inputList = $this->_dbGeneral->mysqlHTML_cleanInputOutput($inputList, array());
		}
		
		// input vals
		$username = $inputList['username'];
		$status = $inputList['status'];
				
		$my_db = $this->_dbGeneral->mysql_i_OpenDB('user');
		
		if(!is_int($my_db)) {		
			$result = $my_db->query("CALL UserUpdateStatus('$username', '$status')");
			$this->_dbGeneral->mysql_i_CloseDB($my_db);
		}
		
		return $result;
		
	}
	
	
	// updates password.
	// Note: store encrypted password only
	function updatePassword($inputList, $inputCleaned){
		
		// default
		$result = false;
		
		if(!$inputCleaned){// clean strings
			$inputList = $this->_dbGeneral->mysqlHTML_cleanInputOutput($inputList, array());
		}
		
		// input vals
		$username = $inputList['username'];
		$password = $inputList['password'];
				
		$my_db = $this->_dbGeneral->mysql_i_OpenDB('user');
		
		if(!is_int($my_db)) {		
			$result = $my_db->query("CALL UserUpdatePassword('$username', '$password')");
			$this->_dbGeneral->mysql_i_CloseDB($my_db);
		}
		
		return $result;
		
	}
	
	
	
};






?>