<?php
/**
 * Byblio
 * useraccount, authentication test
 * ajax, remote call
 * @copyright 2013 Byblio.com
 * @author: Paul A. Oliver
 *
 */


namespace Useraccount\Controller\RemoteAccess;

use Zend\Mvc\Controller\AbstractActionController;
use Hamel\Usermanagement\Download as UsermanagementDownload;
use Hamel\Db\General as DbGeneral;


class AuthtestController extends AbstractActionController{
	
	private $_userInfo;
	
	// upload request for new content
	public function getUserInfoAction(){
		
		// db general
		$dbGeneral = new DbGeneral();
		
		// get values from uri
		$dbSessionId = $this->params()->fromPost('dbSessionId', 0);
    	
    	
    	// clean inputs
    	$inputList = array();
    	$inputList['dbSessionId'] = $dbSessionId;
    	$cleanInputList = $dbGeneral->mysqlHTML_cleanInputOutput($inputList, array());
    	
    	$ajax_dbSessionId = $cleanInputList['dbSessionId'];
    	
    	// session id
    	$machine_sessionId = session_id();
    	
    	// get ip address
    	$machine_ipAddress = $dbGeneral->getRealIpAddr();
    	
    	
    	// check if this session matches (i.e. is genuine user)
    	$usermanagementDownload = new UsermanagementDownload();
    	$inputList = array('dbSessionId'=>$ajax_dbSessionId, 'sessionId'=>$machine_sessionId, 'ipAddress'=>$machine_ipAddress);
    	$dbSession_username = $usermanagementDownload->checkDbSessionMatch($inputList, true);
    	
    	// logged in user info
    	$auth_userInfo = $this->checkForLogin->getUserInfo();
    	if(is_array($auth_userInfo)){
    		$auth_email = $auth_userInfo['email'];
    		$auth_sessionId = $auth_userInfo['sessionId'];
    		$auth_dbSessionId = $auth_userInfo['dbSessionId'];
    	} else {
    		$auth_email = "";
    		$auth_sessionId = "";
    	}
    	
    	// new auth check
    	$authCheckOK = $this->checkForLogin->authenticateRemoteRequest();
    	if($authCheckOK){
    		$authCheck = 'Success';
    	} else {
    		$authCheck = 'Fail';
    	}
    	
    	// resturn string
    	$rtnStr = "<table>"
    			."<thead>"
    				."<tr>"
    				."<td>Item</td><td>Value</td>"
    				."</tr>"
    			."</thead>"
    			."<tbody>"
    			."<tr>"
    				."<td>Ajax db session Id:</td><td>" .$ajax_dbSessionId ."</td>"
    			."</tr>"
    			."<tr>"
    				."<td>Machine session Id:</td><td>" .$machine_sessionId ."</td>"
    			."</tr>"
    			."<tr>"
    				."<td>Machine IP address:</td><td>" .$machine_ipAddress ."</td>"
    			."</tr>"
    			."<tr>"
    				."<td>Db session username:</td><td>" .$dbSession_username ."</td>"
    			."</tr>"
    			."<tr>"
    				."<td>Auth email address:</td><td>" .$auth_email ."</td>"
    			."</tr>"
    			."<tr>"
    				."<td>Auth session id:</td><td>" .$auth_sessionId ."</td>"
    			."</tr>"
    			."<tr>"
    				."<td>Auth db session id:</td><td>" .$auth_dbSessionId ."</td>"
    			."</tr>"
    			."<tr>"
    				."<td>Authentication test:</td><td>" .$authCheck ."</td>"
    			."</tr>"
    			."</tbody></table>";
    	
    	
    	// encode
    	$rtnInfo = array('action'=>'Auth test results', 'message'=>$rtnStr);
    	$rtnJsonStr = json_encode($rtnInfo);
    	
    	
    	// do not output a response (done by upload handler)
    	$response = $this->getResponse();
    	$response->setStatusCode(200);
    	$response->setContent($rtnJsonStr);
    	return $response;
		
		
    	
	}
	
	

	
}



