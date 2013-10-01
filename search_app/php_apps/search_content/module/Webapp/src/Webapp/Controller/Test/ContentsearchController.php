<?php

/**
 * Byblio
 * Content search, webApp
 * Test page web app interaction with search engine
 * 
 * @copyright 2013 Byblio.com
 * @author: Paul A. Oliver
 *
 */

namespace Webapp\Controller\Test;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use ByblioSearch\Form\WebApp\SearchForm;
use Hamel\Db\General as DbGeneral;

class ContentsearchController extends AbstractActionController{
	
	// landing page for web app content search
	public function homeAction(){
	
		// logo
		$headerLogo = 'brown';
		 
		// translator
		$translate = $this->getServiceLocator()->get('viewhelpermanager')->get('translate');
		 
		// browesr title
		$browserTitle = $translate('Content search - test');
		 
		// get ip address
		$dbGeneral = new DbGeneral();
		$ipAddress = $dbGeneral->getRealIpAddr();
		
		// search form
		$searchFormName = 'form_search_webapp';
		$inputs = array();
		$inputs['formName'] = $searchFormName;
		$inputs['placeholder'] = array(
				'query'=>$translate('Search...'),
				'yop'=>$translate('Year of publication'),
				'mop'=>$translate('Month of publication'),
		);
		$inputs['buttonValues'] = array('submit'=>$translate('Search'));
		$inputs['classList'] = array(
				'submit'=>'submit',
		);
		$inputs['elementList'] = array(
				'query'=>array('type'=>'query', 'name'=> 'webapp-search-query'),
				'facet-use'=>array('type'=>'facet', 'name'=> 'webapp-search-facet-use'),
				'facet-author'=>array('type'=>'facet', 'name'=> 'webapp-search-facet-author'),
				'facet-title'=>array('type'=>'facet', 'name'=> 'webapp-search-facet-title'),
				'facet-publisher'=>array('type'=>'facet', 'name'=> 'webapp-search-facet-publisher'),
				'facet-summary'=>array('type'=>'facet', 'name'=> 'webapp-search-facet-summary'),
				'facet-genre'=>array('type'=>'facet', 'name'=> 'webapp-search-facet-genre'),
				'facet-yop'=>array('type'=>'facet', 'name'=> 'webapp-search-facet-yop'),
				'facet-mop'=>array('type'=>'facet', 'name'=> 'webapp-search-facet-mop'),
				'submit'=>array('type'=>'submit', 'name'=> 'webapp-search-submit'),
				'ipAddress'=>array('type'=>'ipAddress', 'name'=> 'webapp-search-ipAddress', 'value'=>$ipAddress),
				'username'=>array('type'=>'username', 'name'=> 'webapp-search-username', 'value'=>'sumesh'),
		);
		$searchForm  = new SearchForm($inputs);
		 
		// key words
		$keyWords = $this->_getKeyWords($translate);
		
		// ajax info
		$ajaxInfo = $this->_getAjaxInfo($this);
		
		$rtn = new ViewModel(array(
				'headerLogo' => $headerLogo,
				'browserTitle' => $browserTitle,
				'searchForm' => $searchForm,
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
		$ajaxRoute = 'webApp-remote';
		$requestType = 'content-search-test';
		$ajaxUrl = $controller->url()->fromRoute($ajaxRoute) .$requestType;
		
		$searchInfo = array();
		$searchInfo['url'] = $ajaxUrl;
		$searchInfo['requestType'] = $requestType;
		$returnInfo['search'] = $searchInfo;
		
		return $returnInfo;
		
	}


	
}