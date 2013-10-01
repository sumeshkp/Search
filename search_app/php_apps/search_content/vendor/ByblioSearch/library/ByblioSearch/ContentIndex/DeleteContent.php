<?php
 
/**
 * Byblio
 * Search engine
 * Primary class for deleting content from the search index
 * @copyright 2013 Byblio.com
 * @author: Paul Oliver, Sumesh P
 *
 */

namespace ByblioSearch\ContentIndex;
use ByblioSearch\ContentIndex\Solarium\AddContent as SolariumAddContent;
use ByblioSearch\ContentIndex\Solarium\DeleteContent as SolariumDeleteContent;
use ByblioSearch\ContentIndex\Solarium\GetContent as SolariumGetContent;


class deleteContent{
	
	
	public function __construct(){
		
	}
	
	
	public function deleteContent($inputList){
		
		// return info
		$returnInfo = array(
				'deleteInfo' => array(),
				'deleteResults' => array()
				);
		
		if(is_array($inputList)){
			
			// enforce variable name formats
			$inputList = $this->_setVariableNameFormat($inputList);
			
			// check for sufficient info
			$inputCheckInfo = $this->_checkInputsForCompleteness($inputList);
			$errorInfo = $inputCheckInfo['error'];
			$warningInfo = $inputCheckInfo['warning'];
			
			// ok to delete?
			$haveErrors = $errorInfo['haveErrors'];
			if($haveErrors){
				// flag
				$okToDelete = false;
				// message
				$returnInfo['deleteInfo'] = $errorInfo['errorList'];
			} else {
				// flag
				$okToDelete = true;
			}
			
			// have warnings?
			$haveWarnings = $warningInfo['haveWarnings'];
			if($haveWarnings){
				// message
				$returnInfo['deleteInfo'] = array_merge($returnInfo['deleteInfo'], $warningInfo['warningList']);
			}
			
			if($okToDelete){
				
				// content id
				$contentId = $inputList['contentId'];
				
				// delete current document
				$solrDeleteContent = new SolariumDeleteContent();
				
				$deleteInfo = array();
				$deleteInfo['contentId'] = $inputList['contentId'];
				$deletedInfo = $solrDeleteContent->deleteFromIndex($deleteInfo);	
				$deleted = $deletedInfo['deleted'];
				
				if(!$deleted){
					// could not delete
					$returnInfo['deleteResults'][] = "<div class=\"error\">Fail: Could not delete current document version in search index</div>";
				} else {
					//  deleted previous version
					$returnInfo['deleteResults'][] = "<div class=\"success\">Success: Deleted previous document version from search index</div>";
				}
				
			}
			
		}
		
		
		// return
		return $returnInfo;
	}
	
	
	
	
	
	// checks key variable names to ensure correct casing
	private function _setVariableNameFormat($inputList){
		
		if(is_array($inputList)){
			
			// content id
			if(key_exists('contentid', $inputList)){
				// add with new variable name
				$inputList['contentId'] = $inputList['contentid'];
				
				// delete old variable name
				unset($inputList['contentid']);
			}
			
			// authorisation code, alpha
			if(key_exists('authalpha', $inputList)){
				// add with new variable name
				$inputList['authAlpha'] = $inputList['authalpha'];
				
				// delete old variable name
				unset($inputList['authalpha']);
			}
			
			// authorisation code, alpha
			if(key_exists('authbeta', $inputList)){
				// add with new variable name
				$inputList['authBeta'] = $inputList['authbeta'];
				
				// delete old variable name
				unset($inputList['authalpha']);
			}
		}
		
		return $inputList;
	}
	
	
	
	// checks the given input variables for sufficient info
	// returns error and warnings
	private function _checkInputsForCompleteness($inputList){
		
		// defaults
		$errorInfo = array('haveErrors'=> false, 'errorList'=>array());
		$warningInfo = array('haveWarning'=> false, 'warningList'=>array());
		
		if(is_array($inputList)){
		
			// check for required info
			$errorInfo = $this->_checkInputForErrors($inputList);
		}
		
		// return info
		$returnInfo = array('error'=>$errorInfo, 'warning'=>$warningInfo);
		
		// return
		return $returnInfo;
		
	}
	
	
	
	// checks the given input variables for required info
	private function _checkInputForErrors($inputList){
		
		// defaults
		$haveErrors = false;
		$errorList = array();
		
		// 1. content id 
		$contentIdOK = false;
		if(key_exists('contentId', $inputList)){
			$value = $inputList['contentId'];
			if(is_string($value) && $value != ""){
				$contentIdOK = true;
			}
		}
		
		// 2. auth code alpha
		$authAlphaOK = false;
		if(key_exists('authAlpha', $inputList)){
			$value = $inputList['authAlpha'];
			if(is_string($value) && $value != ""){
				$authAlphaOK = true;
			}
		}
		
		// 3. auth code beta
		$authBetaOK = false;
		if(key_exists('authBeta', $inputList)){
			$value = $inputList['authBeta'];
			if(is_string($value) && $value != ""){
				$authBetaOK = true;
			}
		}
		
		
		// if not required info
		if(!$contentIdOK){
			$errorList[] = "<div class=\"error\">Error: No content id given</div>";
			$haveErrors = true;
		}

// 		if(!$authAlphaOK){
// 			$errorList[] = "<div class=\"error\">Error: No authorisation alpha code</div>";
// 			$haveErrors = true;
// 		}
		
// 		if(!$authBetaOK){
// 			$errorList[] = "<div class=\"error\">Error: No authorisation beta code</div>";
// 			$haveErrors = true;
// 		}
				
		
		// return
		$returnInfo = array('haveErrors'=>$haveErrors, 'errorList'=>$errorList);
		return $returnInfo;
		
	}
	
	
	
}








