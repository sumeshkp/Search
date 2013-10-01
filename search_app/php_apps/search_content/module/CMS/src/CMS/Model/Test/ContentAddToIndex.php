<?php
 
/**
 * Byblio
 * Search engine
 * Creates return info for testing CMS add content to content search nidex
 * @copyright 2013 Byblio.com
 * @author: Paul A. Oliver
 *
 */

namespace CMS\Model\Test;



class ContentAddToIndex{
	
	
	public function __construct(){
		
	}
	
	
	// creates html to display the post request variables
	public function getReceivedInfoHTML($translate, $inputList){
		
		$returnHTML = "";
		
		if(is_array($inputList)){
			
			foreach($inputList as $variableName => $value){
				
				// value - boolean
				if(is_bool($value)){
					if($value){
						$valueStr = $translate('true');
					} else {
						$valueStr = $translate('false');
					}
				}
				
				// row info
				$rowStr = "<div><span class=\"name\">" .$variableName .":</span> <span class=\"value\">" .$value ."</span></div>";
				
				// add
				$returnHTML .= $rowStr;
			};
			
			
		}
		
		// return
		return $returnHTML;
	}
	
	
	// creates html to display the search info interpretted from the post request
	public function getAddInfoHTML($translate, $inputList){
		
		$returnHTML = "";
		
		if(is_array($inputList)){
			
			// heading string
			$allInfoStr = "<div class=\"heading\">" .$translate('Process information') ."</div>";
			
			foreach($inputList as $infoStr){
				// row
				$rowStr = "<div>" .	$infoStr ."</div>";
				
				// add
				$allInfoStr .= $rowStr;
			}

			$returnHTML = $allInfoStr;
		}
		
		// return
		return $returnHTML;
	}
	
	
	
	
	
	
	
	
	
		
		
	// creates html to display the search results info to the post request
	public function getResultsInfoHTML($translate, $inputList){
		
		// default
		$returnHTML = "";
		$contentStr = "";
		$noneStr = "<div class=\"info\">" .$translate('No info given') ."</div>";
		
		if(is_array($inputList)){
			
			if(count($inputList) > 0){
				
				$allRowStr = "";

				foreach($inputList as $variableName => $variableInfo){
					// row
					$rowStr = "<div><span class=\"name\">" .$variableName .": </span><span class=\"info\">" .$variableInfo ."</span></div>";
					
					// add
					$allRowStr .= $rowStr;
				}
			}
		}
			
		// assemble
		if($allRowStr == ""){
			$returnHTML = $noneStr; 
		} else {
			$returnHTML = $allRowStr;
		}

			
		// return
		return $returnHTML;
	}
	
	
	
	
}








