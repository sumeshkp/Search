<?php

/**
 * Byblio session management db functions - download
 * @copyright 2013 Byblio.com
 * @author: Paul A. Oliver
 * 
*/

namespace Hamel\Sessionmanagement;

use Hamel\Db\General as DbGeneral;


class Download{
	
	private $_numCharsInRegKey;
	private $_dbGeneral;
	
	
	public function __construct(){
		// create db instance
		$this->_dbGeneral = new DbGeneral();
	
	}
	
	
	
	
	// unknown user
	// checks the given session information against the current session info in the db
	// returns true if valid, false if not
	public function userUnknownVerifySession($infoList, $inputCleaned){
		
		// default
		$verified = false;
		
		if(!$inputCleaned){// clean strings
			$inputList = $this->_dbGeneral->mysqlHTML_cleanInputOutput($inputList, array());
			$inputCleaned = true;
		}
		
		$dbSessionId = $infoList['dbSessionId'];
		$phpSessionId = $infoList['phpSessionId'];
		$httpUserAgent = $infoList['httpUserAgent'];
		
		$currentSessionId = 0;
			
		$my_db = $this->_dbGeneral->mysql_i_OpenDB('session');
		
		if(!is_int($my_db)) {
			$stmt=$my_db->prepare("SELECT UserUnknownVerifySessionInfo(?, ?, ?)") or die($my_db->error);
			$stmt->bind_param('sss', $httpUserAgent, $dbSessionId, $phpSessionId) or die($stmt->error);
			$stmt->execute() or die($stmt->error);
			$stmt->bind_result($result);
			$stmt->fetch();
			$this->_dbGeneral->mysql_i_CloseDB($my_db);
		}
		
		if($result > 0){
			$verified = true;
		}
		
		return $verified;
		
	}
	
	
	
	// known user
	// checks the given session information against the current session info in the db
	// returns true if valid, false if not
	public function userVerifySession($infoList, $inputCleaned){
		
		// default
		$verified = false;
		
		if(!$inputCleaned){// clean strings
			$inputList = $this->_dbGeneral->mysqlHTML_cleanInputOutput($inputList, array());
			$inputCleaned = true;
		}
		
		$dbSessionId = $infoList['dbSessionId'];
		$phpSessionId = $infoList['phpSessionId'];
		$username = $infoList['username'];
		
		$currentSessionId = 0;
			
		$my_db = $this->_dbGeneral->mysql_i_OpenDB('session');
		
		if(!is_int($my_db)) {
			$stmt=$my_db->prepare("SELECT UserVerifySessionInfo(?, ?)") or die($my_db->error);
			$stmt->bind_param('ss', $username, $phpSessionId) or die($stmt->error);
			$stmt->execute() or die($stmt->error);
			$stmt->bind_result($currentSessionId);
			$stmt->fetch();
			$this->_dbGeneral->mysql_i_CloseDB($my_db);
		}
		
		//  check results
		if($currentSessionId == $dbSessionId){
			$verified = true;
		}
		
		return $verified;
		
	}
	
	
	
	
	
	

};






?>