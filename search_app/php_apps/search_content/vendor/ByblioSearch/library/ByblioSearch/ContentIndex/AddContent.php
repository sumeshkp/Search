<?php
 
/**
 * Byblio
 * Search engine
 * Primary class for adding or updating content in the search index
 * @copyright 2013 Byblio.com
 * @author: Paul Oliver, Sumesh P
 *
 */

namespace ByblioSearch\ContentIndex;
use ByblioSearch\ContentIndex\Solarium\AddContent as SolariumAddContent;
use ByblioSearch\ContentIndex\Solarium\DeleteContent as SolariumDeleteContent;
use ByblioSearch\ContentIndex\Solarium\GetContent as SolariumGetContent;


class AddContent{
	
	
	public function __construct(){
	
	}
	
	
	public function addContent($inputList){
		
		// return info
		$returnInfo = array(
				'addInfo' => array(),
				'addResults' => array()
				);
		
		if(is_array($inputList)){
			
			// enforce variable formats
			$inputList = $this->_setVariableNameFormat($inputList);
			
			// check for sufficient info
			$inputCheckInfo = $this->_checkInputsForCompleteness($inputList);
			$errorInfo = $inputCheckInfo['error'];
			$warningInfo = $inputCheckInfo['warning'];
			
			// ok to add?
			$haveErrors = $errorInfo['haveErrors'];
			if($haveErrors){
				// flag
				$okToAdd = false;
				// message
				$returnInfo['addInfo'] = $errorInfo['errorList'];
			} else {
				// flag
				$okToAdd = true;
			}
			
			// have warnings?
			$haveWarnings = $warningInfo['haveWarnings'];
			if($haveWarnings){
				// message
				$returnInfo['addInfo'] = array_merge($returnInfo['addInfo'], $warningInfo['warningList']);
			}
			
			if($okToAdd){
				
				// content id
				$contentId = $inputList['contentId'];
				
				// add content to index
				$solrAddContent = new SolariumAddContent();
				$addAllInfo = $solrAddContent->addToIndex($inputList);
				
				// info
				$contentAdded = $addAllInfo['contentAdded'];
				$addResults = $addAllInfo['addResults'];
				
				// record results
				$returnInfo['addResults'] = $addResults; 
				
				// message
				if($contentAdded){
					$returnInfo['addInfo'][] = "<div class=\"success\">Success: Added document to search index</div>";
				} else {
					$returnInfo['addInfo'][] = "<div class=\"error\">Fail: Could not add document to search index</div>";
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
			
			// content text
			if(key_exists('contenttext', $inputList)){
				// add with new variable name
				$inputList['contentText'] = $inputList['contenttext'];
				
				// delete old variable name
				unset($inputList['contenttext']);
			}
			
			// is type
			if(key_exists('istype', $inputList)){
				// add with new variable name
				$inputList['isType'] = $inputList['istype'];
				
				// delete old variable name
				unset($inputList['istype']);
			}
			
			// has type
			if(key_exists('hastype', $inputList)){
				// add with new variable name
				$inputList['hasType'] = $inputList['hastype'];
				
				// delete old variable name
				unset($inputList['hastype']);
			}
			
			
			// date string to date
			if(key_exists('dop', $inputList)){
				
				$dateStr = $inputList['dop'];
				//TODO: Change the $dateStr to the appropriate string as Jeffery gives
				$date = gmdate(DATETIME_FORMAT, strtotime($dateStr));
				
			
				// $record
				$inputList['dop'] = $date;
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
		
			// check for warnings info
			$warningInfo = $this->_checkInputForWarnings($inputList);
		}
		
		// return info
		$returnInfo = array('error'=>$errorInfo, 'warning'=>$warningInfo);
		
		// return
		return $returnInfo;
		
	}
	
	
	// checks the given input variables for completeness of non-essential info
	private function _checkInputForWarnings($inputList){
		
		// defaults
		$haveWarnings = false;
		$warningList = array();
		
		
		// 1. publisher
		$publisherOK = false;
		if(key_exists('publisher', $inputList)){
			$value = $inputList['publisher'];
			if(is_string($value) && $value != ""){
				$publisherOK = true;
			}
		}
		
		// 2. summary
		$summaryOK = false;
		if(key_exists('summary', $inputList)){
			$value = $inputList['summary'];
			if(is_string($value) && $value != ""){
				$summaryOK = true;
			}
		}
		
		// 3. date of publication
		$dopOK = false;
		if(key_exists('dop', $inputList)){
			$value = $inputList['dop'];
			if(is_string($value) && $value != ""){
				$dopOK = true;
			}
		}
		
		
		
		
		
		if(!$publisherOK){
			$errorList[] = "<div class=\"warning\">Warning: No publisher specified</div>";
			$warningList = true;
		}
		
		if(!$summaryOK){
			$errorList[] = "<div class=\"warning\">Warning: Summary not given</div>";
			$warningList = true;
		}
		
		if(!$dopOK){
			$errorList[] = "<div class=\"warning\">Warning: Date of publication not specified</div>";
			$warningList = true;
		}
		
		
		// return
		$returnInfo = array('haveWarnings'=>$haveWarnings, 'warningList'=>$warningList);
		return $returnInfo;
		
	}
	
	
	
	// converts given date string into date object
	private function _convertDate($dateStr){
		
		if(is_string($dateStr)){
			// convert to date
			$date = date_create_from_format($this->_dateFormat, $dateStr);
		} else {
			$date = null;
		}
		
		return $date;
		
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
		
		// 2. title
		$titleOK = false;
		if(key_exists('title', $inputList)){
			$value = $inputList['title'];
			if(is_string($value) && $value != ""){
				$titleOK = true;
			}
		}
		
		// 3. content text
		$contentTextOK = false;
		if(key_exists('contentText', $inputList)){
			$value = $inputList['contentText'];
			if(is_string($value)){
				$contentTextOK = true;
			}
		}
		
		// 4. genre tag
		$genreOK = false;
		if(key_exists('genre', $inputList)){
			$value = $inputList['genre'];
			if(is_string($value) && $value != ""){
				$genreOK = true;
			}
		}
		
		// 4. author
		$authorOK = false;
		if(key_exists('author', $inputList)){
			$value = $inputList['author'];
			if(is_string($value) && $value != ""){
				$authorOK = true;
			}
		}
		
		
		// if not required info
		if(!$contentIdOK){
			$errorList[] = "<div class=\"error\">Error: No content id given</div>";
			$haveErrors = true;
		}

		if(!$titleOK){
			$errorList[] = "<div class=\"error\">Error: No title given</div>";
			$haveErrors = true;
		}
		
		if(!$contentTextOK){
			$errorList[] = "<div class=\"error\">Error: No content text given</div>";
			$haveErrors = true;
		}
		
				
		if(!$genreOK){
			$errorList[] = "<div class=\"error\">Error: Genre not specified</div>";
			$haveErrors = true;
		}
		if(!$authorOK){
			$errorList[] = "<div class=\"error\">Error: Author not specified</div>";
			$haveErrors = true;
		}
				
		
		
		// return
		$returnInfo = array('haveErrors'=>$haveErrors, 'errorList'=>$errorList);
		return $returnInfo;
		
	}
	
	
	
}








