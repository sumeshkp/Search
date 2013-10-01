<?php

/**
 * Byblio session management db functions - upload
 * @copyright 2013 Byblio.com
 * @author: Paul A. Oliver
 * 
*/

namespace Hamel\Sessionmanagement;

use Hamel\Db\General as DbGeneral;


class Upload{
	
	
	private $_dbGeneral;
	
	
	public function __construct(){
		// create db instance
		$this->_dbGeneral = new DbGeneral();
		
	}
	

	
	
	// unknown user
	// adds info to db for verificaion of remote requests and non-logged in users
	// returns historic session info if exist
	public function userUnknownStartSession($infoList, $inputCleaned){
		
		if(!$inputCleaned){// clean strings
			$inputList = $this->_dbGeneral->mysqlHTML_cleanInputOutput($inputList, array());
			$inputCleaned = true;
		}
		
		$phpSessionId = $infoList['phpSessionId'];
		$ipAddress = $infoList['ipAddress'];
		$httpUserAgent = $infoList['httpUserAgent'];
		
		$hamelSessionId = 0; // default (user not logged in in the db)
			
		$my_db = $this->_dbGeneral->mysql_i_OpenDB('session');
		
		if(!is_int($my_db)) {
			$result = $my_db->query("CALL UserUnknownStartCurrentSession('$httpUserAgent', '$ipAddress', '$phpSessionId')");
			$this->_dbGeneral->mysql_i_CloseDB($my_db); // close connection
			
			if($result){ // have result
				// info
				$coreInfo = mysqli_fetch_array($result);
				 
				if(is_array($coreInfo)){
					// get core info
					$hamelSessionId = $coreInfo[0];
					$ipAddress = $coreInfo[1];
					$httpUserAgent = $coreInfo[2];
					$dateStarted = $coreInfo[3];
					
					// historic session info
					$historicInfo['ipAddress'] = $ipAddress;
					$historicInfo['httpUserAgent'] = $httpUserAgent;
					$historicInfo['dateStarted'] = $dateStarted;
					
					// ******* Record historic session info in mongo db
					
					
				}
			}
		}
		
		return $hamelSessionId;
		
	}
	
	// unknown user
	// ends current sesion
	// returns historic session info if exist
	public function userUnknownEndSession($infoList, $inputCleaned){
		
		if(!$inputCleaned){// clean strings
			$inputList = $this->_dbGeneral->mysqlHTML_cleanInputOutput($inputList, array());
			$inputCleaned = true;
		}
		
		$dbSessionId = $infoList['dbSessionId'];
			
		$my_db = $this->_dbGeneral->mysql_i_OpenDB('session');
		
		if(!is_int($my_db)) {
			$result = $my_db->query("CALL UserUnknownEndCurrentSession('$dbSessionId')");
			$this->_dbGeneral->mysql_i_CloseDB($my_db); // close connection
			
			if($result){ // have result
				// info
				$coreInfo = mysqli_fetch_array($result);
				 
				if(is_array($coreInfo)){
					// get core info
					$ipAddress = $coreInfo[1];
					$httpUserAgent = $coreInfo[2];
					$dateStarted = $coreInfo[3];
					
					// historic session info
					$historicInfo['ipAddress'] = $ipAddress;
					$historicInfo['httpUserAgent'] = $httpUserAgent;
					$historicInfo['dateStarted'] = $dateStarted;
					
					// ******* Record historic session info in mongo db
					
					
				}
			}
		}
		
	}
	
	
	
	
	
	// known user
	// if successful, reutrns the db index and historic session info (if exist)
	function userStartSession($inputList, $inputCleaned){
		
		if(!$inputCleaned){// clean strings
			$inputList = $this->_dbGeneral->mysqlHTML_cleanInputOutput($inputList, array());
			$inputCleaned = true;
		}
		$username = $inputList['username'];
		$phpSessionId = $inputList['phpSessionId'];
		$ipAddress = $inputList['ipAddress'];
		$httpUserAgent = $inputList['httpUserAgent'];
		
		$hamelSessionId = 0; // default (user not logged in in the db)
			
		$my_db = $this->_dbGeneral->mysql_i_OpenDB('session');
		
		if(!is_int($my_db)) {
			$result = $my_db->query("CALL UserStartCurrentSession('$username', '$httpUserAgent', '$ipAddress', '$phpSessionId')");
			$this->_dbGeneral->mysql_i_CloseDB($my_db); // close connection
			
			if($result){ // have result
				// info
				$coreInfo = mysqli_fetch_array($result);
				 
				if(is_array($coreInfo)){
					// get core info
					$userID = $coreInfo[0];
					$hamelSessionId = $coreInfo[1];
					$ipAddress = $coreInfo[2];
					$httpUserAgent = $coreInfo[3];
					$dateStarted = $coreInfo[4];
					
					// if have historic session info
					if($userID >0){
						$historicInfo['username'] = $username;
						$historicInfo['ipAddress'] = $ipAddress;
						$historicInfo['httpUserAgent'] = $httpUserAgent;
						$historicInfo['dateStarted'] = $dateStarted;
						
						// ******* Record historic session info in mongo db
					}
					
				}
			}
		}
		
		return $hamelSessionId;
	}
	
	
	
	// known user
	// logs user out of the db, returns the ip address used for the session just ended
	// returns historic session info if exist
	function userEndSession($inputList, $inputCleaned){
		
		$sessionIPAddress = 0; // default (user not logged in in the db)
		
		if(!$inputCleaned){// clean strings
			$inputList = $this->_dbGeneral->mysqlHTML_cleanInputOutput($inputList, array());
		}
		$username = $inputList['username'];
		
		$my_db = $this->_dbGeneral->mysql_i_OpenDB('session');
		
		
		if(!is_int($my_db)) {
			$result = $my_db->query("CALL UserEndCurrentSession('$username')");
			$this->_dbGeneral->mysql_i_CloseDB($my_db); // close connection
			
			if($result){ // have result
				// info
				$coreInfo = mysqli_fetch_array($result);
				 
				if(is_array($coreInfo)){
					// get core info
					$userID = $coreInfo[0];
					$hamelSessionId = $coreInfo[1];
					$ipAddress = $coreInfo[2];
					$httpUserAgent = $coreInfo[3];
					$dateStarted = $coreInfo[4];
					
					// if have historic session info
					if($userID >0){
						$historicInfo['username'] = $username;
						$historicInfo['ipAddress'] = $ipAddress;
						$historicInfo['httpUserAgent'] = $httpUserAgent;
						$historicInfo['dateStarted'] = $dateStarted;
						
						// ******* Record historic session info in mongo db
					}
					
				}
			}
		}
	
	}
	

};






?>