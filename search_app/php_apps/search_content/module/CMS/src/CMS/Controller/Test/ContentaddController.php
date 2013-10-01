<?php

/**
 * Byblio
 * Content add to search index, CMS
 * Test page CMS interaction with search engine
 * 
 * @copyright 2013 Byblio.com
 * @author: Paul A. Oliver
 *
 */

namespace CMS\Controller\Test;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use ByblioSearch\Form\CMS\AddContentForm;

class ContentaddController extends AbstractActionController{
	
	// landing page for CMS content add test
	public function homeAction(){
		
		
		// logo
		$headerLogo = 'pink';
		 
		// translator
		$translate = $this->getServiceLocator()->get('viewhelpermanager')->get('translate');
		 
		// browesr title
		$browserTitle = $translate('Add and update content to index - test');
		 
		// add/ update form
		$addFormName = 'form_add_cms';
		$inputs = array();
		$inputs['formName'] = $addFormName;
		$inputs['placeholder'] = array(
				'contentId'=>$translate('Content id'),
				'title'=>$translate('Title'),
				'summary'=>$translate('Summary'),
				'author'=>$translate('Author(s)'),
				'publisher'=>$translate('Publisher'),
				'dop'=>$translate('Date of publication'),
				'contentText'=>$translate('Text of content item'),
		);
		$inputs['buttonValues'] = array('submit'=>$translate('Add /update'));
		$inputs['classList'] = array(
				'submit'=>'submit',
		);
		
		$addForm  = new AddContentForm($translate, $inputs);
		 
		// key words
		$keyWords = $this->_getKeyWords($translate);
		
		// ajax info
		$ajaxInfo = $this->_getAjaxInfo($this);
		
		$rtn = new ViewModel(array(
				'headerLogo' => $headerLogo,
				'browserTitle' => $browserTitle,
				'addForm' => $addForm,
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
		$requestType = 'content-add-test';
		$ajaxUrl = $controller->url()->fromRoute($ajaxRoute) .$requestType;
		
		$searchInfo = array();
		$searchInfo['url'] = $ajaxUrl;
		$searchInfo['requestType'] = $requestType;
		$returnInfo['add'] = $searchInfo;
		
		return $returnInfo;
		
	}


	
}