<?php
/**
 * Byblio
 * webApp, responds to ajax request from the byblio web app
 * 
 * @copyright 2013 Byblio.com
 * @author: Paul A. Oliver
 *
 */


namespace CMS\Controller\RemoteAccess;

use Zend\Mvc\Controller\AbstractActionController;
use ByblioSearch\ContentIndex\AddContent as IndexAddContent;
use ByblioSearch\ContentIndex\DeleteContent as IndexDeleteContent;
use CMS\Model\Test\ContentAddToIndex as ContentAddIndexTest;
use CMS\Model\Test\ContentDeleteFromIndex as ContentDeleteIndexTest;
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
			case 'content-add-test':
				
				// load add to search index class
				$indexAddContent = new IndexAddContent();
				
				// add or update index
				$addAllInfo = $indexAddContent->addContent($infoList);
				$addInfo = $addAllInfo['addInfo'];
				$addResults = $addAllInfo['addResults'];
				
				// test info
				$contentAddIndexTest = new ContentAddIndexTest();
				
				// create received info for test page
				$receivedHTML = $contentAddIndexTest->getReceivedInfoHTML($translate, $infoList);

				// create add info for test page
				$addHTML = $contentAddIndexTest->getAddInfoHTML($translate, $addInfo);
				
				// create results info for test page
				$resultsHTML = $contentAddIndexTest->getResultsInfoHTML($translate, $addResults);
				
				// test info
				$message = array(
						'received' => $receivedHTML,
						'add' => $addHTML,
						'results' => $resultsHTML,
						);
				
				// add to return info
				$returnInfo['message'] = $message;
				$returnInfo['success'] = true;
				
			break;
			
			
			case 'content-add':
				
				// load add to search index class
				$indexAddContent = new IndexAddContent();
				
				
				// add or update index
				$addAllInfo = $indexAddContent->addContent($infoList);
				$addInfo = $addAllInfo['addInfo'];
				$addResults = $addAllInfo['addResults'];
				
				// add to return info
				$returnInfo['message'] = $addResults;
				$returnInfo['success'] = true;
				
				
			break;
			
			case 'content-delete-test':
				
				// load delete from search index class
				$indexDeleteContent = new IndexDeleteContent();
				
				// add or update index
				$addDeleteInfo = $indexDeleteContent->deleteContent($infoList);
				$deleteInfo = $addDeleteInfo['deleteInfo'];
				$deleteResults = $addDeleteInfo['deleteResults'];
				
				// test info
				$contentDeleteIndexTest = new ContentDeleteIndexTest();
				
				// create received info for test page
				$receivedHTML = $contentDeleteIndexTest->getReceivedInfoHTML($translate, $infoList);

				// create add info for test page
				$deleteHTML = $contentDeleteIndexTest->getDeleteInfoHTML($translate, $deleteInfo);
				
				// create results info for test page
				$resultsHTML = $contentDeleteIndexTest->getResultsInfoHTML($translate, $deleteResults);
				
				// test info
				$message = array(
						'received' => $receivedHTML,
						'add' => $deleteHTML,
						'results' => $resultsHTML,
						);
				
				// add to return info
				$returnInfo['message'] = $message;
				$returnInfo['success'] = true;
				
			break;
			
			
			case 'content-delete':
				
				// load delete from search index class
				$indexDeleteContent = new IndexDeleteContent();
				
				// add or update index
				$addDeleteInfo = $indexDeleteContent->deleteContent($infoList);
				$deleteInfo = $addDeleteInfo['deleteInfo'];
				$deleteResults = $addDeleteInfo['deleteResults'];
				
				// add to return info
				$returnInfo['message'] = $deleteResults;
				$returnInfo['success'] = true;
				
				
			break;
		}
		
		return $returnInfo;

	}
	
	

	
}



