<?php
/**
 * Byblio
 * useraccount, updates and retrives given ui settings into db
 * ajax, remote call
 * @copyright 2013 Byblio.com
 * @author: Paul A. Oliver
 *
 */


namespace Useraccount\Controller\RemoteAccess;

use Zend\Mvc\Controller\AbstractActionController;
use Hamel\UISettings\Upload as UISettingsUpload;
use Hamel\UISettings\Download as UISettingsDownload;
use Hamel\Db\General as DbGeneral;


class UisettingsController extends AbstractActionController{
	
	private $_userInfo;
	
	// request to get ui settings
	public function getAction(){
		
		// default response
		$response = array('action'=>'failed', 'message'=>'no auth');
		
		// new auth check
		$authCheckOK = $this->checkForLogin->authenticateRemoteRequest();
		
		if($authCheckOK){ // valid user
				
			// db general
			$dbGeneral = new DbGeneral();
			
			// get value from uri
			$settingsRef = $this->params()->fromPost('settingsRef', false);
	    	
	    	// clean input
	    	$inputList = array();
	    	$inputList['settingsRef'] = $settingsRef;
	    	$cleanInputList = $dbGeneral->mysqlHTML_cleanInputOutput($inputList, array());
	    	$settingsRef = $cleanInputList['settingsRef'];
	    	
	    	// user info
	    	$userInfo = $this->checkForLogin->getUserInfo();
	    	
	    	// username
	    	$username = $userInfo['username'];
	    	
	    	// get settings
	    	$inputList = array();
	    	$inputList['username'] = $username;
	    	$inputList['settingsRef'] = $settingsRef;
	    	$uISettingsDownload = new UISettingsDownload();
	    	$settingsInfo = $uISettingsDownload->getUserSettings($inputList, true);
    	
	    	// read
	    	$uiJsonStr = $settingsInfo['json'];
	    	
	    	if(is_string($uiJsonStr)){
	    		
	    		$uiStr = json_decode($uiJsonStr);
	    	
	    		// add to response
	    		$response['action'] = 'ui_settings_retrieve';
	    		$response['message'] = $uiStr;
	    	} else {
	    		$response['action'] = 'ui_setttings_retrieve';
	    		$response['message'] = 'failed to retrieve settings';
	    	}
	    	
		}
    	
    	// encode
    	$rtnJsonStr = json_encode($response);
    	
    	
    	// cancel view, set json return
    	$response = $this->getResponse();
    	$response->setStatusCode(200);
    	$response->setContent($rtnJsonStr);
    	return $response;

	}
	
	
	// request to update ui settings
	public function updateAction(){
		
		// default response
		$response = array('action'=>'failed', 'message'=>'no auth');
		
		// new auth check
		$authCheckOK = $this->checkForLogin->authenticateRemoteRequest();
		
		if($authCheckOK){ // valid user
				
			// db general
			$dbGeneral = new DbGeneral();
			
			// get value from uri
			$settingsInfo = $this->params()->fromPost('settingsInfo', false);
			$settingsRef = $this->params()->fromPost('settingsRef', false);
    	
	    	// clean input
	    	$inputList = array();
	    	$inputList['settingsInfo'] = $settingsInfo;
	    	$inputList['settingsRef'] = $settingsRef;
	    	$cleanInputList = $dbGeneral->mysqlHTML_cleanInputOutput($inputList, array());
	    	$settingsInfo = $cleanInputList['settingsInfo'];
	    	$settingsRef = $cleanInputList['settingsRef'];
	    	
	    	// user info
	    	$userInfo = $this->checkForLogin->getUserInfo();
	    	
	    	// username
	    	$username = $userInfo['username'];
	    	
	    	// update settings
	    	$inputList = array();
	    	$inputList['username'] = $username;
	    	$inputList['settingsRef'] = $settingsRef;
	    	$inputList['settingsInfo'] = $settingsInfo;
	    	$uISettingsUpload = new UISettingsUpload();
	    	$updated = $uISettingsUpload->updateUISettings($inputList, true);
    	
	    	if($updated){
	    		// response
		    	$response = array('action'=>'ui_setttings_update', 'message'=>'updated');
	    	} else {
	    		// response
	    		$response = array('action'=>'ui_setttings_update', 'message'=>'failed to update');
	    	}
		}
    	
    	// encode
    	$rtnJsonStr = json_encode($response);
    	
    	
    	// cancel view, set json return
    	$response = $this->getResponse();
    	$response->setStatusCode(200);
    	$response->setContent($rtnJsonStr);
    	return $response;

	}
	
	

	
}



