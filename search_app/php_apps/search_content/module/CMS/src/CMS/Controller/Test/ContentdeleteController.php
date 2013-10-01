<?php

/**
 * Byblio
 * Content delete from search index, CMS
 * Test page CMS interaction with search engine
 * 
 * @copyright 2013 Byblio.com
 * @author: Paul A. Oliver
 *
 */

namespace CMS\Controller\Test;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use ByblioSearch\Form\CMS\DeleteContentForm;

class ContentdeleteController extends AbstractActionController{
	
	// landing page for CMS content delete test
	public function homeAction(){
		
		
		// logo
		$headerLogo = 'green';
		 
		// translator
		$translate = $this->getServiceLocator()->get('viewhelpermanager')->get('translate');
		 
		// browesr title
		$browserTitle = $translate('Delete content from index - test');
		 
		// add/ update form
		$deleteFormName = 'form_delete_cms';
		$inputs = array();
		$inputs['formName'] = $deleteFormName;
		$inputs['placeholder'] = array(
				'contentId'=>$translate('Content id'),
				'authAlpha'=>$translate('Authorisation code - alpha'),
				'authBeta'=>$translate('Authorisation code - beta'),
		);
		$inputs['buttonValues'] = array('submit'=>$translate('Delete content item'));
		$inputs['classList'] = array(
				'submit'=>'submit',
		);
		
		$deleteForm  = new DeleteContentForm($translate, $inputs);
		 
		// key words
		$keyWords = $this->_getKeyWords($translate);
		
		// ajax info
		$ajaxInfo = $this->_getAjaxInfo($this);
		
		$rtn = new ViewModel(array(
				'headerLogo' => $headerLogo,
				'browserTitle' => $browserTitle,
				'deleteForm' => $deleteForm,
				'keyWords' => $keyWords,
				'ajaxInfo' => $ajaxInfo,
				
		));
		 
		return $rtn;
		
	}
	
	
	
	private function _getKeyWords($translate){
		
		// key words
		$returnInfo = array();
		$returnInfo['trueStr']= $translate('true');
		$returnInfo['falseStr']= $translate('false');
		$returnInfo['emptyString']= $translate('empty string');
		$returnInfo['requestFailed']= $translate('Connected to server but failed in processing request');
		$returnInfo['ajaxFailed']= $translate('Could not connect to server (Ajax - jSon request failed');
		
		return $returnInfo;
		
	}
	
	private function _getAjaxInfo($controller){
		
		// return info
		$returnInfo = array();
		
		// search:
		$ajaxRoute = 'cms-remote';
		$requestType = 'content-delete-test';
		$ajaxUrl = $controller->url()->fromRoute($ajaxRoute) .$requestType;
		
		$searchInfo = array();
		$searchInfo['url'] = $ajaxUrl;
		$searchInfo['requestType'] = $requestType;
		$returnInfo['add'] = $searchInfo;
		
		return $returnInfo;
		
	}


	
}