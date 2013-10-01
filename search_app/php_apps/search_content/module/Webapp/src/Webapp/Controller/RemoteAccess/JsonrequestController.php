<?php
/**
 * Byblio
 * webApp, responds to ajax request from the byblio web app
 * 
 * @copyright 2013 Byblio.com
 * @author: Paul A. Oliver
 *
 */


namespace Webapp\Controller\RemoteAccess;

use Zend\Mvc\Controller\AbstractActionController;
use ByblioSearch\ContentSearch\Search as ContentSearch;
use Webapp\Model\Test\ContentSearch as TestContentSearch;
use Hamel\Db\General as DbGeneral;



class JsonrequestController extends AbstractActionController{
	
	private $_dbGeneral;
	
	public function __construct(){
		
		// load up the database connection
		$this->_dbGeneral = new DbGeneral();
		
	}
	
	// request to get ui settings
	public function checkrequestAction(){
		
		// default response
		$requestResponse = array('success'=>false, 'message'=>'Not processed request');
		
		// get all post varaibles (cleaned)
		$postData = $this->_dbGeneral->mysqlHTML_cleanInputOutput($this->params()->fromPost(), array());
		
		// process request
		$processResponse = $this->_processRequest($postData);
		
    	// encode
    	if(is_array($processResponse)){
    		$rtnJsonStr = json_encode($processResponse);
    	} else {
    		$rtnJsonStr = json_encode($requestResponse);
    	}
    	
    	
    	// cancel view, set json return
    	$response = $this->getResponse();
    	$response->setStatusCode(200);
    	$response->setContent($rtnJsonStr);
    	
    	// return response
    	return $response;

	}
	
	
	// processes request
	private function _processRequest($infoList){
		
		// translator
		$translate = $this->getServiceLocator()->get('viewhelpermanager')->get('translate');
		
		// default
		$returnInfo = array();
		
		// request type
		$requestType = $infoList['requestType'];
		
		switch($requestType){
			case 'content-search-test':
				
				
				// load search class
				$contentSearch = new ContentSearch();
				
				// search
				$searchAllInfo = $contentSearch->search($infoList);
				$searchInfo = $searchAllInfo['searchInfo'];
				$searchResults = $searchAllInfo['searchResults'];
				
				
				// test info
				$testContentSearch = new TestContentSearch();
				
				// create received info for test page
				$receivedHTML = $testContentSearch->getReceivedInfoHTML($translate, $infoList);

				// create search info for test page
				$searchHTML = $testContentSearch->getSearchInfoHTML($translate, $searchInfo);
				
				// create results info for test page
				$resultsHTML = $testContentSearch->getResultsInfoHTML($translate, $searchResults);
				
				// test info
				$message = array(
						'received' => $receivedHTML,
						'search' => $searchHTML,
						'results' => $resultsHTML,
						);
				
				// add to return info
				$returnInfo['message'] = $message;
				$returnInfo['success'] = true;
				
			break;
			
			
			case 'content-search':
				
				// load search class
				$contentSearch = new ContentSearch();
				
				// search
				$searchAllInfo = $contentSearch->search($infoList);
				
				$searchInfo = $searchAllInfo['searchInfo'];
				$searchResults = $searchAllInfo['searchResults'];
				
				// add to return info
				$returnInfo['message'] = $searchResults;
				$returnInfo['success'] = true;
				
				
			break;
		}
		
		return $returnInfo;

	}
}



