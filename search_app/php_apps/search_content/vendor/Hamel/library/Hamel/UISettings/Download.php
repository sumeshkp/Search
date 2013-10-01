<?php

/**
 * Byblio UI Settings download functions
 * @copyright 2013 Byblio.com
 * @author: Paul A. Oliver
 *
 */


namespace Hamel\UISettings;

use Hamel\Db\General as DbGeneral;


class Download{
	
	private $_dbGeneral;
	
	
	function __construct(){
		// db class
		$this->_dbGeneral = new DbGeneral();
		
	}
    
    
    
    
    
    
    // returns user settings
    function getUserSettings($inputList, $inputCleaned){
		
		// default
		$settingInfo = array('json'=>"", 'array'=>array());
		
		if(!$inputCleaned){// clean strings
			$inputList = $this->dbGeneral->mysqlHTML_cleanInputOutput($inputList, array());
		}
		
		// input vals
		$username = $inputList['username'];
		$settingsRef = $inputList['settingsRef'];
     
        // open db
        $my_db = $this->_dbGeneral->mysql_i_OpenDB('uisettings');
        
        if(!is_int($my_db)) {
        	// procedure
        	$result = $my_db->query("CALL UISettingsGetUserSettings('$username', '$settingsRef')");
        	// close db
        	$this->_dbGeneral->mysql_i_CloseDB($my_db);
        }
        
        if(!is_bool($result)){ // have result
		    	// info
	    	$coreInfo = mysqli_fetch_array($result);
	    	
	    	if(is_array($coreInfo)){
	    		
	    		// json String
	    		$settingsStr = $coreInfo[0];
	    		
	    		// convert to array
	    		$settingsArr = json_decode($settingsStr, true);
	    		
	    		// record
		    	$settingInfo['json'] = $settingsStr;
		    	$settingInfo['array'] = $settingsArr;
	    	}
        }
        	
        // return
        return $settingInfo;
   
    }
    
    
    
   
	
	
};
	


?>