<?php

/**
 * Byblio User download functions
 * @copyright 2013 Byblio.com
 * @author: Paul A. Oliver
 *
 */


namespace Hamel\Usermanagement;

use Hamel\Db\General as DbGeneral;
use Hamel\SystemAdmin\ErrorMessages as ErrorMessages;
require 'vendor/Hamel/library/Hamel/Password/password.php';

class Download{
	
	private $_dbGeneral;
	
	
	function __construct(){
		// db class
		$this->dbGeneral = new DbGeneral();
		
	}
    

	
	// checks if the given activation code (to reset a user password) is correct
	// returns boolean
	function checkUserPwdResetToComplete($inputList, $inputCleaned){
		
		// default
		$result= false;
		
		if(!$inputCleaned){// clean strings
			$inputList = $this->dbGeneral->mysqlHTML_cleanInputOutput($inputList, array());
		}
		
		// input vals
		$username = $inputList['username'];
		$oldEncPassword = $inputList['oldEncPassword'];
		$newEncPassword = $inputList['newEncPassword'];
		$activationKey = $inputList['activationKey'];
		
		// open db
		$my_db = $this->dbGeneral->mysql_i_OpenDB('user');
		
		if(!is_int($my_db)) {// if have server
			$stmt=$my_db->prepare("SELECT UserCheckResetPassword(?, ?, ?, ?)") or die($my_db->error);
			$stmt->bind_param('ssss', $username, $oldEncPassword, $newEncPassword, $activationKey) or die($stmt->error);
			$stmt->execute() or die($stmt->error);
			$stmt->bind_result($result);
			$stmt->fetch();
			$this->dbGeneral->mysql_i_CloseDB($my_db);
		}
		
		return $result;
	}
	
	
	
	
	// checks if the given account activation codes are correct. Returns username if codes are correct
	function getRegistrationDetails($inputList, $inputCleaned){
		
		// default
		$result = false;
		
		if(!$inputCleaned){// clean strings
			$inputList = $this->dbGeneral->mysqlHTML_cleanInputOutput($inputList, array());
		}
		
		// input vals
		$usernameScramble = $inputList['usernameScramble'];
		$activationKey = $inputList['activationKey'];
		
		
		// open db
		$my_db = $this->dbGeneral->mysql_i_OpenDB('user');
		
		if(!is_int($my_db)) {// if have server
			$result = $my_db->query("CALL UserGetRegistration('$usernameScramble', '$activationKey')");
		    $this->dbGeneral->mysql_i_CloseDB($my_db);
		     
		    if($result){ // have result
		    	// info
		    	$coreInfo = mysqli_fetch_array($result);
		    	
		    	if(is_array($coreInfo)){
			    	$result = array();
			    	$result['username'] = $coreInfo[0];
		    	}
		    }
		}
		return $result;	
	}
	
	
	// checks if the given password reset activation codes are correct. Returns username and new password if codes are correct
	function getPasswordResetDetails($inputList, $inputCleaned){
		
		// default
		$result = false;
		
		if(!$inputCleaned){// clean strings
			$inputList = $this->dbGeneral->mysqlHTML_cleanInputOutput($inputList, array());
		}
		
		// input vals
		$usernameScramble = $inputList['usernameScramble'];
		$activationKey = $inputList['activationKey'];
		
		
		// open db
		$my_db = $this->dbGeneral->mysql_i_OpenDB('user');
		
		if(!is_int($my_db)) {// if have server
			$result = $my_db->query("CALL UserGetPasswordReset('$usernameScramble', '$activationKey')");
		    $this->dbGeneral->mysql_i_CloseDB($my_db);
		     
		    if($result){ // have result
		    	// info
		    	$coreInfo = mysqli_fetch_array($result);
		    	
		    	if(is_array($coreInfo)){
			    	$result = array();
			    	$result['username'] = $coreInfo[0];
			    	$result['password'] = $coreInfo[1];
		    	}
		    }
		}
		return $result;	
	}
	
	

	
 

	// gets all emails for a given user
	function getUserEmailUsage($inputList, $inputCleaned){
	
		// default
		$returnArray = array();
		
		if(!$inputCleaned){// clean strings
			$inputList = $this->dbGeneral->mysqlHTML_cleanInputOutput($inputList, array());
			$inputCleaned = true;
		}
		
		// username
		$username = $inputList['username'];
		
	    // default
	    $returnArray = array();
	    
	    $my_db = $this->dbGeneral->mysql_i_OpenDB('user');
	    
	    if(!is_int($my_db)) {
	    	$result = $my_db->query("CALL UserGetUserEmailUsage('$username')");
	    	$this->dbGeneral->mysql_i_CloseDB($my_db);
	    
	    	if($result){ // have emails
				// num rows
				$numRows = mysqli_num_rows($result);
				
				for($j = 0; $j< $numRows; ++$j){
					// this content
					$rowData = mysqli_fetch_row($result);
					
					if(is_array($rowData)){
	
						// record
						$email = $rowData[0];
						$usage = $rowData[1];
						
						if(intval($rowData[2]) == 1){
							$verified = true;
						} else {
							$verified = false;
						}
						
						$returnArray[] = array(
								'email'=> $email, 
								'usage'=>$usage, 
								'verified'=>$verified
								);
					}
				}
	    	}
		}
	    
	    return $returnArray;
	 }
	    
	    
	
	 

	 
	// returns the additional info of a given user
	function getUserAdditionalDetails($inputList, $inputCleaned){
	
		// default
		$returnArray = array(
				'gender' => "",
				'yearOfBirth' => "",
				'profession' =>"",
				'organisation' =>""
				);
		
		if(!$inputCleaned){// clean strings
			$inputList = $this->dbGeneral->mysqlHTML_cleanInputOutput($inputList, array());
			$inputCleaned = true;
		}
		
		// username
		$username = $inputList['username'];
		
		$my_db = $this->dbGeneral->mysql_i_OpenDB('user');
		
		if(!is_int($my_db)){
			$result = $my_db->query("CALL UserGetUserAdditionalDetails('$username')");
			$coreInfo = mysqli_fetch_array($result);
			$this->dbGeneral->mysql_i_CloseDB($my_db);
		
			// process results
			if(is_array($coreInfo)){
				$returnArray['gender'] = $coreInfo[0];
				$returnArray['yearOfBirth'] = $coreInfo[1];
				$returnArray['profession'] = $coreInfo[2];
				$returnArray['organisation'] = $coreInfo[3];
			}
		
		}
		
		return $returnArray;
	}
	
	
	// returns core details of the given user
	function getUserCoreDetails($inputList, $inputCleaned){
	
		// default
		$returnArray = array();
		
		if(!$inputCleaned){// clean strings
			$inputList = $this->dbGeneral->mysqlHTML_cleanInputOutput($inputList, array());
			$inputCleaned = true;
		}
		
		// username
		$username = $inputList['username'];
		
		$my_db = $this->dbGeneral->mysql_i_OpenDB('user');
		
		if(!is_int($my_db)){
			$result = $my_db->query("CALL UserGetUserCoreDetails('$username')");
			$coreInfo = mysqli_fetch_array($result);
			$this->dbGeneral->mysql_i_CloseDB($my_db);
		
			// process results
			if(is_array($coreInfo)){
				$returnArray['firstName'] = $coreInfo[1];
				$returnArray['lastName'] = $coreInfo[2];
				$returnArray['country'] = $coreInfo[3];
				$returnArray['accountType'] = $coreInfo[4];
				$returnArray['accountStatus'] = $coreInfo[5];
				$returnArray['dateRegistered'] = $coreInfo[6];				
				$returnArray['dateAcTypeChanged'] = $coreInfo[7];
				$returnArray['dateStatusChanged'] = $coreInfo[8];
				$returnArray['dateAcceptedTandC'] = $coreInfo[9];
			}
		
		}
			
		return $returnArray;
	}
	
	
	
	// returns core home uri of the given user
	function getUserHomeURI($inputList, $inputCleaned){
	
		// default
		$returnArray = array('homeCore'=>"", 'homeAppendix'=>"", 'uri'=>"", "", 'dateLastSet'=>NULL);
		
		if(!$inputCleaned){// clean strings
			$inputList = $this->dbGeneral->mysqlHTML_cleanInputOutput($inputList, array());
			$inputCleaned = true;
		}
		
		// username
		$username = $inputList['username'];
		
		$my_db = $this->dbGeneral->mysql_i_OpenDB('user');
		
		if(!is_int($my_db)){
			$result = $my_db->query("CALL UserGetHomeURI('$username')");
			
			if($result){
				$coreInfo = mysqli_fetch_array($result);
				$this->dbGeneral->mysql_i_CloseDB($my_db);
			
				// process results
				if(is_array($coreInfo)){
					
					$homeCore = $coreInfo[0];
					$homeAppendix = $coreInfo[1];
					$homeURI = $homeCore .$homeAppendix;
					$dateLastSet = $coreInfo[2];
					
					$returnArray['homeCore'] = $homeCore;
					$returnArray['homeAppendix'] = $homeAppendix;
					$returnArray['uri'] = $homeURI;
					$returnArray['dateLastSet'] = $dateLastSet;
				}
			}
		}
			
		return $returnArray;
	}
	
	
	
	
	// returns public profile preferences of the given user
	function getUserPrefs_PublicProfile($userName, $inputCleaned){
	
	    
		if(!$inputCleaned){// clean strings
			$inputList = $this->dbGeneral->mysqlHTML_cleanInputOutput(array('username'=>$userName), array());
			$userName = $inputList[username];
		}
		
		$my_db = $this->dbGeneral->mysql_i_OpenDB('user');
		
		if(!is_int($my_db)) {
			$result = $my_db->query("CALL UserGetPrefPublicProfile('$userName')");
			
			if($result){
				$coreInfo = mysqli_fetch_array($result);
				$this->dbGeneral->mysql_i_CloseDB($my_db);
				
				if(!is_array($coreInfo)){
					$returnInfo = array();
				} else{
					$returnInfo['firstName']=(bool)$coreInfo[2];
					$returnInfo['lastName']=(bool)$coreInfo[3];
					$returnInfo['country']=(bool)$coreInfo[4];
				}
			}
		}
		
		return $returnInfo;
	}
	
	
	
	// returns public profile of user as a string
	function getUserPublicProfile($inputList, $inputCleaned){
		
		// load country info if not already initiated
		$classList = array('Countries');
		loadClass($classList);
		
		if(!$inputCleaned){// clean strings
			$inputList = $this->dbGeneral->mysqlHTML_cleanInputOutput($inputList, array());
		}

		// record values
		$firstName = $inputList['firstName'];
		$lastName = $inputList['lastName'];
		$username = $inputList['username'];
		
		// calc country name
		$countryNum = $inputList['country'];
		$countryName = $GLOBALS['Countries']->getCountryName($countryNum);
		
		// get the profile prefs for this user
		$prefs_pubProfile = $this->getUserPrefs_PublicProfile($username, true);
		
		// default return
		$pubProfileArray= array('text'=>"", 'settings'=>$prefs_pubProfile);
		$pubProfile = "";
		
		if(is_array($prefs_pubProfile)){
			// public profile string
		
			if(key_exists('firstName', $prefs_pubProfile)){
				// add first name to public profile name
				$pubProfile = $firstName;
				// add last name
				if(key_exists('lastName', $prefs_pubProfile)){
					$pubProfile .= " " .$lastName;
				}
			} else { //no first name
				// add last name
				if(key_exists('lastName', $prefs_pubProfile)){
					// add name
					$pubProfile = $lastName;
				}
			}
			// add country?
			if($prefs_pubProfile['country']){
				if($pubProfile !=""){
					$pubProfile .= ", " .$countryName;
				} else {
					$pubProfile = $countryName;
				}
			}
		}
		
		// update
		$pubProfileArray['text']= $pubProfile;
		
		// return
		return $pubProfileArray;
		
	}
	
	
	
	
	
	

	 
	
	


	

	
	
	
	
	// checks if password and email match
	// returns boolean
	function checkPasswordEmailMatch($inputList, $inputCleaned){
		
		// default
		$passwordEmailMatch = false;
		
		if(!$inputCleaned){// clean strings
			$inputList = $this->dbGeneral->mysqlHTML_cleanInputOutput($inputList, array());
			$inputCleaned = true;
		}
		
		// info
		$email = $inputList['email'];
		$password = $inputList['password'];
		
		// open db
		$my_db = $this->dbGeneral->mysql_i_OpenDB('user');
		
		if(!is_int($my_db)) {
			// get the encrypted password
			$hash = false;
			$stmt = $my_db->prepare("SELECT UserGetPasswordFromEmail(?)") or die($my_db->error);
			$stmt->bind_param('s', $email) or die($stmt->error);
			$stmt->execute() or die($stmt->error);
			$stmt->bind_result($hash);
			$stmt->fetch();
			
			// close connection
			$this->dbGeneral->mysql_i_CloseDB($my_db);
			
			// if have a result
			if($hash){
				// check password
				$passwordEmailMatch = password_verify($password, $hash);
			}
		}
		
		// return
		return $passwordEmailMatch;
		
	}	
	
	

	// Verifies given email address.
	// Returns: 
	// 1. Username if email is in db and verifired.
	// 2. 'not_verified' if email is in db but is not verified.
	// 3. 'not_found' if email is not in db.
	function verifyEmailAddress($email, $inputCleaned){
		
		// default
		$result = 'not_found';
	
		if(!$inputCleaned){// clean strings
			$emailList = $this->dbGeneral->mysqlHTML_cleanInputOutput(array($email), array());
			$email = $emailList[0];
		}
		

		$my_db = $this->dbGeneral->mysql_i_OpenDB('user');
		
		if(!is_int($my_db)) {
			$stmt=$my_db->prepare("SELECT UserVerifyEmailIsRegistered(?)") or die($my_db->error); 
			$stmt->bind_param('s', $email) or die($stmt->error);
			$stmt->execute() or die($stmt->error); 
			$stmt->bind_result($result);   
			$stmt->fetch(); 	 
			// close connection
			$this->dbGeneral->mysql_i_CloseDB($my_db);		
		}
		// if function fails
		if($result == NULL){
			
			// record error
			$errorMessages = new ErrorMessages();
			$routineInputList = array($email);
			$inputList = array('type'=>'function', 'routine'=>'UserVerifyEmailIsRegistered', 'error'=>'routine failed', 'routineInputList'=>$routineInputList);
			$errorMessages->recordNewMySQLError($inputList);
			
			// set default
			$result = 'not_found';
			
		}
		return $result;
	}
	
	
	// gets the history of failed log in attempts for a given email
	// Stores original IP address but does not check ip address subsequently.
	function getLoginAttemptHistory($inputList, $inputCleaned){
		
		// default
		$currentTime = strtotime('now');
		$history = array('firstAttemptTime'=>$currentTime, 'numAttempts' => 0);
		
		if(!$inputCleaned){// clean strings
			$inputList = $this->dbGeneral->mysqlHTML_cleanInputOutput($inputList, array());
			$inputCleaned = true;
		}
		
		// info
		$email = $inputList['email'];
		$ipAddress = $inputList['ipAddress'];
		
		// open db
		$my_db = $this->dbGeneral->mysql_i_OpenDB('user');
		
		if(!is_int($my_db)) {
				
			$result = $my_db->query("CALL UserGetFailedLoginHistory('$ipAddress', '$email')");
				
			if($result){
				$coreInfo = mysqli_fetch_array($result);
				// close connection
				$this->dbGeneral->mysql_i_CloseDB($my_db);
					
				// process results
				if(is_array($coreInfo)){
					// number of attempts
					$numAttempts = $coreInfo[0];
					
					// first attempt date
					$firstAttemptTime = $coreInfo[1];
					
						
					// record
					$history['firstAttemptTime'] = $firstAttemptTime;
					$history['numAttempts'] = $numAttempts;
						
				}
			}
		}
		
		// return
		return $history;
		
	}
	
	
	
	
};
	


?>