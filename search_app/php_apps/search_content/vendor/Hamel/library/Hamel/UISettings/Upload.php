<?php

/**
 * Byblio UI Settings upload functions
 * @copyright 2013 Byblio.com
 * @author: Paul A. Oliver
 *
 */


namespace Hamel\UISettings;

use Hamel\Db\General as DbGeneral;


class Upload{
	
	private $_dbGeneral;
	
	
	function __construct(){
		// db class
		$this->_dbGeneral = new DbGeneral();
		
	}
    
    
    
    
    
    
	// updates the given ui settings
	function updateUISettings($inputList, $inputCleaned){
		
		// default
		$result = false;
		
		if(!$inputCleaned){// clean strings
			$inputList = $this->_dbGeneral->mysqlHTML_cleanInputOutput($inputList, array());
		}
		
		// input vals
		$username = $inputList['username'];
		$settingsRef = $inputList['settingsRef'];
		$settingsInfo = $inputList['settingsInfo'];
		
		// convert to json string
		if(!is_string($settingsInfo)){
			$settingsInfo = json_encode($settingsInfo);
		}
				
		$my_db = $this->_dbGeneral->mysql_i_OpenDB('uisettings');
		
		if(!is_int($my_db)) {		
			$result = $my_db->query("CALL UISettingsUpdateUserSettings('$username', '$settingsRef', '$settingsInfo')");
			$this->_dbGeneral->mysql_i_CloseDB($my_db);
		}
		
		return $result;
		
	}
    
    
    
   
	
	
};
	


?>