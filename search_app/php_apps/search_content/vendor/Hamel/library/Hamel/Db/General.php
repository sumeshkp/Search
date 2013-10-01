<?php

/**
 * Byblio Database functions
 * @copyright 2013 Byblio.com
 * @author: Paul A. Oliver
 *
 */

namespace Hamel\Db;

class General{
	
	protected $_core;
	protected $_session;

	
	// constructor - sets connect variables
	public function __construct(){
		
		if(substr_count($_SERVER['HTTP_HOST'], 'localhost')>0){
			
			// core data base
			$this->_core = array(
					'db_hostname'=> 'localhost',
					'db_database'=> 'bb_core_v1',
					'db_username'=> 'bybliosearch',					
					'db_password'=> 'FTuWMB3y',
// 					'db_username'=> 'root',
// 					'db_password'=> 'root'
					);
			
			// session data base
			$this->_session = array(
					'db_hostname'=> 'localhost',
					'db_database'=> 'bb_session_v1',
					'db_username'=> 'bybliosearch',
					'db_password'=> 'FTuWMB3y'
// 					'db_username'=> 'root',
// 					'db_password'=> 'root'
					);
			
		} else {
		    if(substr_count($_SERVER['HTTP_HOST'], 'bybliotest')>0){
		    	
		    	// core data base
		    	$this->_core = array(
		    			'db_hostname'=> 'localhost',
		    			'db_database'=> 'bb_core_v1',
		    			'db_username'=> 'byblioSearchApp',
		    			'db_password'=> 'FTuWMB3y'
		    	);
		    		
		    	// session data base
		    	$this->_session = array(
		    			'db_hostname'=> 'localhost',
		    			'db_database'=> 'bb_session_v1',
		    			'db_username'=> 'byblioSearchApp',
		    			'db_password'=> 'FTuWMB3y'
		    	);
		    	
		    } 
		    
		}
		

	}

	
	// opens a database with  mysqli
	function mysql_i_OpenDB($dbType){
		
		// set db type
		switch($dbType){
			case 'user':
			case 'publications':				
			case 'videos':
			case 'authors':
			case 'search':
			case 'scrapbook':
			case 'hc':
			case 'library':
			case 'uri':
			case 'uisettings':
			case 'voucher':
				$db = $this->_core;
			break;
			
			case 'session':
				$db = $this->_session;
			break;
		}
		
		// get connection variables
		$db_hostname = $db['db_hostname'];
		$db_database = $db['db_database'];
		$db_username = $db['db_username'];
		$db_password = $db['db_password'];
		
		
		// open connection
	$my_db = new \mysqli($db_hostname, $db_username, $db_password, $db_database);
// 		$my_db = new \mysqli($db_hostname, $db_username, "", $db_database);
		
		// set charset to utf 8
		$my_db->set_charset("utf8");
		
		
		if(mysqli_connect_errno()) { // error connecting to database
			return mysqli_connect_errno(); // return error code
		}
	
		return $my_db;
	}
	
	
	// closes given database link with mysqli
	function mysql_i_CloseDB($my_db) {
		$my_db->close();
	}
	
	
		
	// salts and encrypts the given string
	function scrambleString($inputStr){
		$st1="w>6&s*y0";
		$st2="@fd!kp[5";
		$returnStr = sha1("$st1$inputStr$st2");
		return $returnStr;
	}
	
	
	// cleans a given list of strings to avoid msql injection.
	// opens a mysqli connection
	function mysqlHTML_cleanInputOutput($inputList, $notCleanFieldList){
		
		// default return
		$returnList = array();
		
		if(is_array($inputList)){
			// mysql connection
			$mySQLi = $this->mysql_i_OpenDB('user');
			
			if($mySQLi){ // if have connection
			
				// for each string to clean
				foreach($inputList as $key=>$value){
					
					// default action
					$clean = true;
					
					// if not to clean
					if(is_array($notCleanFieldList)){
						if(in_array($key, $notCleanFieldList)){
							$clean = false; // set flag
						}
					}
					
					if($clean){
						if(is_string($value)){

							// encode to utf 8
							$value3 = $this->_html_encodeUTF8($value);
							
							// escape for mysql
							$value4 = $mySQLi->real_escape_string($value3);
							
							// add to return list
							$returnList[$key] = $value4;
							
						} else { // not string
							// add to return list unedited
							$returnList[$key] = $value;
						}
					} else {
						// add to return list unedited
						$returnList[$key] = $value;
					}
				}
			// close connection
			$this->mysql_i_CloseDB($mySQLi);
			}
		}
		
		return $returnList; 
	}
	
	
	// converts to html entities in utf 8
	private function _html_encodeUTF8($string){
	    return htmlentities($string, ENT_QUOTES, 'UTF-8');
	}
	
	
	
	// gets the real ip address of the client machine
	// returns 'localhost' if on local host
	
	function getRealIpAddr(){
		
		if(substr_count($_SERVER['HTTP_HOST'], 'localhost')>0){
			$ip = 'localhost';
		} else {
			if (!empty($_SERVER['HTTP_CLIENT_IP'])){   //check ip from share internet
				$ip=$_SERVER['HTTP_CLIENT_IP'];
			} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){   //to check ip is pass from proxy
				$ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
			} else {
				$ip=$_SERVER['REMOTE_ADDR'];
			}
		}
		// return 
		return $ip;
	}
	

}








?>