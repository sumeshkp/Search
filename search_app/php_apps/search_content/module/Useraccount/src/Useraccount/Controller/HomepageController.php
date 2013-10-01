<?php

/**
 * Byblio
 * User's account home page
 * @copyright 2013 Byblio.com
 * @author: Paul A. Oliver
 *
 */

namespace Useraccount\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Hamel\URImanagement\Download as URIDownloadManagement;

class HomepageController extends AbstractActionController{
	
	// landing page for user (any type)
	public function homepageAction(){
		
		// get logged in
		$loggedIn = $this->checkForLogin->loggedIn;
			
		if($loggedIn){
			// get user info
			$auth = $this->checkForLogin->auth;
			$userLibraries = $auth->variableUserInfo['userLibraries'];
			$groupLibraries = $auth->variableUserInfo['groupLibraries'];
			$groupAcInfo = $auth->variableUserInfo['groupAcInfo'];
			$homeURIInfo = $auth->variableUserInfo['homeURIInfo'];
			$userInfo = $this->checkForLogin->userInfo;
		} else {
			$userLibraries = array();
			$groupLibraries = array();
			$groupAcInfo = array();
			$homeURIInfo = array();
			$userInfo = array();
		}

		// current URI
		$currentURI = $this->params()->fromRoute('userHomeURI', 0);
		
		// check if this is a valid user home page uri
		$inputList = array();
		$inputList['currentURI'] = $currentURI;
		$inputList['loggedIn'] = $loggedIn;
		$inputList['userLibraries'] = $userLibraries;
		$inputList['groupLibraries'] = $groupLibraries;
		$inputList['groupAcInfo'] = $groupAcInfo;
		$inputList['homeURIInfo'] = $homeURIInfo;
		$inputList['userInfo'] = $userInfo;
		$uriInfo = $this->_validateHomePageRequest($inputList);
		
		// is this valid
		$isHomePage = $uriInfo['isHomePage'];
		$okForThisUser = $uriInfo['okForThisUser'];
		$homeType = $uriInfo['homeType'];
		$browserTitle = $uriInfo['browserTitle'];
		
		
		// not valid home page
		if(!$isHomePage){
			
			// redirect to home page
			$this->redirect()->toRoute('home');
		
		} else {
			if(!$loggedIn){ // valid page but not logged in
				
				// set redirect flag to use route with segments
				$_SESSION['hamelLoginRedirectRoute'] = 'use_route_segments';
			
				// store the route segments to return to when the user has logged in (called if user is redirected to log in)
				$_SESSION['hamelLoginRedirectRouteSegments'] = array('route'=>'useraccount-homepage', 'segments'=>array('userHomeURI'=>$userHomeURI));
				
				// redirect to user login page (which will then come back to the requested library)
				$this->redirect()->toRoute('useraccount-login');
			
			} else { // is valid home page and the user is logged in
				
				if(!$okForThisUser){ // this user does not have persmission to view this page
					// redirect to public libraries
					$this->redirect()->toRoute('home');
				}
			}
		}
		
		// are here if valid home page for this (logged in) user:
		
		// get user info
		$inputList = array();
		$inputList['userLibraries'] = $userLibraries;
		$inputList['groupLibraries'] = $groupLibraries;
		$inputList['groupAcInfo'] = $groupAcInfo;
		$inputList['homeURIInfo'] = $homeURIInfo;
		$inputList['userInfo'] = $userInfo;
		$userAllInfo = $this->_getFullUserInfo($inputList);
		
		
		// get full info (user or group) required for page
		$pageInfo = $this->_getViewInfo($homeType);
		
		
		// get ui info (could be user or group)
		$UIInfo = array($homeType);
		
		// logo
		$headerLogo = 'blue';
			
		// enforce browser title
		if($browserTitle == ""){
			// translator
			$translate = $this->getServiceLocator()->get('viewhelpermanager')->get('translate');
			// title
			$browserTitle = $translate('Your home page');
		} 
		
		
		$rtn = new ViewModel(array(
				'browserTitle' => $browserTitle,
				'homeType' =>$homeType,
				'loggedIn' => $loggedIn,
				'userInfo' => $userAllInfo,
				'UIInfo' => $UIInfo,
				'pageInfo' =>$pageInfo,
				));
		
		return $rtn;
		
	}

	
	
	// returns info to build page and js
	private function _getViewInfo($homeType){
		
		if($homeType == 'group'){
			$returnInfo = $this->_getGroupViewInfo();
		} else {
			$returnInfo = $this->_getUserViewInfo();
		}
		
		return $returnInfo;
	}
	
	
	
	
	// returns info to build page and js for a user home page
	private function _getUserViewInfo(){
		
		return array();
	}
	
	
	// returns info to build page and js for a group home page
	private function _getGroupViewInfo(){
		
		return array();
	}
	
	
	
	
	private function _getFullUserInfo($inputList){
		
		// copy over core info
		$returnInfo = $inputList;
		
		// username
		$userInfo = $infoList['userInfo'];
		$username = $userInfo['username'];
		
		
		// add in updated user info
		// $returnInfo['userInfo'] =
		
		return $returnInfo;
	}
	
	
	// checks if this uri is ok for this user
	private function _validateHomePageRequest($infoList){
		
		// info
		$loggedIn = $infoList['loggedIn'];
		$userLibraries = $infoList['userLibraries'];
		$groupLibraries = $infoList['groupLibraries'];
		$groupAcInfo = $infoList['groupAcInfo'];
		$homeURIInfo = $infoList['homeURIInfo'];
		$userInfo = $infoList['userInfo'];
		$currentURI = $infoList['currentURI'];
		
		// defaults
		$okForThisUser = false;
		$isHomePage = false;
		$homeType = 'user';
		$browserTitle = "";
		
	
		
		// if logged in
		if($loggedIn){
		
			// user's home uri
			$userHomePageURI = null;
			if(is_string($homeURIInfo['uri'])){
				$userHomePageURI = $homeURIInfo['uri'];
			}
			
			
			// if this is the user's personal home page
			if($currentURI == $userHomePageURI){
				// set flags
				$okForThisUser = true;
				$isHomePage = true;
				
				// set browser title
				$firstName = $userInfo['firstName'];
				$lastName = $userInfo['lastName'];
				$browserTitle = $firstName ." " .$lastName;
				
			} else {
				// get info about the curent uri
				$inputList = array('URI'=>$currentURI);
				$URIDownloadManagement = new URIDownloadManagement();
				$uriInfo = $URIDownloadManagement->validateUserHomePageFromURI($inputList, false);
				
				// info
				$isHomePage = $uriInfo['isHomePage'];
				$groupId = $uriInfo['groupID'];
				$uriType = $uriInfo['uriType'];
				
				
				// if is group home page
				if($isHomePage && $uriType == 'gp_home'){
					
					// if belongs to group of which user is a member				
					if(key_exists($groupId, $groupAcInfo)){
						// set flag
						$okForThisUser = true;
						
						// set title for browser
						$groupInfo = $groupAcInfo[$groupId];
						if(is_array($groupInfo)){
							if(key_exists('menuName', $groupInfo)){
								$browserTitle = $groupInfo['menuName'];
							}
						}
						
					}
				}
				
				// type of home
				if($uriType == 'gp_home'){
					$homeType = 'group';
				}
			}
			
		} else { // not logged in
		
			// get info about the current uri
			$URIDownloadManagement = new URIDownloadManagement();
			$uriInfo = $URIDownloadManagement->validateUserHomePageFromURI($infoList, false);
		
			// info
			$isHomePage = $uriInfo['isHomePage'];
		}
		
		// return
		$returnInfo = array('isHomePage' =>$isHomePage, 'okForThisUser'=>$okForThisUser, 'homeType'=>$homeType, 'browserTitle'=>$browserTitle);
		return $returnInfo;
	}
	
}

