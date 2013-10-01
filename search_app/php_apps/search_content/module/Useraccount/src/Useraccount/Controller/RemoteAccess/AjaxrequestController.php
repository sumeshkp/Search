<?php
/**
 * Byblio
 * useraccount, responds to ajax request
 * ajax, remote call
 * @copyright 2013 Byblio.com
 * @author: Paul A. Oliver
 *
 */


namespace Useraccount\Controller\RemoteAccess;

use Zend\Mvc\Controller\AbstractActionController;
use Hamel\Vouchermanagement\ProcessVoucher as ProcessVoucher;
use Hamel\UISettings\Download as UISettingsDownload;
use Hamel\Db\General as DbGeneral;



class AjaxrequestController extends AbstractActionController{
	
	private $_userInfo;
	private $_dbGeneral;
	
	public function __construct(){
		
		// db general
		$this->_dbGeneral = new DbGeneral();
		
	}
	// request to get ui settings
	public function checkrequestAction(){
		
		// default response
		$verifyResponse = array('action'=>'failed', 'message'=>'no auth');
		
		// get all post varaibles (cleaned)
		$postData = $this->_dbGeneral->mysqlHTML_cleanInputOutput($this->params()->fromPost(), array());
		
		// get request type from uri (cleaned)
		$cleandedList = $this->_dbGeneral->mysqlHTML_cleanInputOutput(array('requestType'=>$this->params()->fromRoute('requestType', 0)), array());
		$requestType = $cleandedList['requestType'];
		
		// validate access request
		$routeMatch = $this->getEvent()->getRouteMatch()->getMatchedRouteName();
		$context = $routeMatch .$requestType;
		$authCheckOK = $this->checkForLogin->auth->validateRequest($context, $postData);
		
		// translator
		$translate = $this->getServiceLocator()->get('viewhelpermanager')->get('translate');
		
		// valid request
		if($authCheckOK){ 
			$inputList = $postData;
			$inputList['requestType'] = $requestType;
			$verifyResponse = $this->_processRequest($translate, $inputList);
		}
    	
    	// encode
    	$rtnJsonStr = json_encode($verifyResponse);
    	
    	
    	// cancel view, set json return
    	$response = $this->getResponse();
    	$response->setStatusCode(200);
    	$response->setContent($rtnJsonStr);
    	return $response;

	}
	
	
	// processes request
	private function _processRequest($translate, $infoList){
		
		// request type
		$requestType = $infoList['requestType'];
		
		switch($requestType){
			case 'signup-voucher':
				
				$voucherInfo = $infoList['voucherInfo'];
				
				if(is_array($voucherInfo)){
					$request_code = $voucherInfo['code'];
					$request_context = $voucherInfo['context'];
					
					// info
					$processList = array();
					$processList['voucherContext'] = 'signup';
					$processList['voucherInfo'] = $infoList['voucherInfo'];
				
					// initiate voucher class and get results
					$processVoucher = new ProcessVoucher();
					$returnInfo = $processVoucher->processForAjax($this, $translate, $processList);
				
					// return context and code from request
					$returnInfo['context'] = $request_context;
					$returnInfo['code'] = $request_code;
				} else {
					// default response
					$returnInfo = array('action'=>'failed', 'message'=>'no voucher info');
				}
				
			break;
		}
		
		return $returnInfo;

	}
	
	

	
}



