<?php
 
/**
 * Byblio
 * Search engine
 * Creates return info for testing content search from web app
 * @copyright 2013 Byblio.com
 * @author: Paul A. Oliver
 *
 */

namespace Webapp\Model\Test;

use Hamel\General\General as HamelGeneral;



class ContentSearch{
	
	
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
	public function getSearchInfoHTML($translate, $inputList){
		
		$returnHTML = "";
		
		if(is_array($inputList)){
			
			// query string
			$query = $inputList['queryStr'];
			$queryStr = "<div class=\"heading\">" .$translate('Query string') ."</div><div class=\"value\">" .$query ."</div>";
			
			// content types, in-line
			$contentTypeStr = $this->_getSearchInfoContentTypeHTML($translate, $inputList);
					
			// publication info, in-line
			$publicationStr = $this->_getSearchInfoPublicationHTML($translate, $inputList);
			
			// specified search facets
			$specifiedFacetsStr = $this->_getSpecifiedFacetHTTML($translate, $inputList);
			
			
			// assemble
			$returnHTML = $queryStr .$contentTypeStr .$publicationStr .$specifiedFacetsStr;
				
		}
		
		// return
		return $returnHTML;
	}
	
	
	
	// creates html to display the search info interpretted from the post request
	// specified facets
	private function _getSpecifiedFacetHTTML($translate, $inputList){
		
		$facetsAllStr = "";
		$headerStr = "<div class=\"heading\">" .$translate('Search facets specified in post variables') ."</div>";
		$noneStr = "<div><span class=\"info\">" .$translate('none') ."</span></div>";
		
		if(is_array($inputList)){
			
			// facet info
			$allFacetInfo = $inputList['specifiedFacetInfo'];
			
			if(is_array($allFacetInfo)){
			
				// use facets
				$useFacets = $allFacetInfo['useFacets'];
				
				if(is_bool($useFacets)){
					if($useFacets){
						$valueStr = $translate('true');
					} else {
						$valueStr = $translate('false');
					}
				} else {
					$valueStr = $useFacets;
				}
				
				$facetsAllStr  = "<div><span class=\"name\">" .$translate('Use facets for search') .":</span> <span class=\"value\">" .$valueStr ."</span></div>";
				
				
				// facet list
				$facetList = $allFacetInfo['facetList'];
				
				if(is_array($facetList)){
					
					
					if(count($facetList) == 0){
						$facetsAllStr .= "<div>" .$translate('No facets specified') ."</div>";
					} else {
							
						$detectStr .= "<div><span class=\"name\">" .$translate('Facets specified') .": </span>";
						
						$facetNum = 0;
						foreach($facetList as $variableName => $value){
							
							$facetNum++;
							
							// value - boolean
							if(is_bool($value)){
								if($value){
									if($facetNum == 1){
										// row info
										$detectStr .=  "<span class=\"value\">" .$variableName ."</span>";
									} else {
										// row info
										$detectStr .=  ", <span class=\"value\">" .$variableName ."</span>";
									}
								}
							}
					
							// value - string
							if(is_string($value)){
								if($facetNum == 1){
									// row info
									$detectStr .=  "<span class=\"value\">" .$variableName ."</span> <span class=\"name\">(" .$value .")</span>";
								} else {
									// row info
									$detectStr .=  ", <span class=\"value\">" .$variableName ."</span> <span class=\"name\">(" .$value .")</span>";
								}
								
								
							}
							
						}
						// close
						$detectStr .= "</div>";

						// record
						
						$facetsAllStr .= $detectStr;
					}
					
				}
			}	
		}
		
		// assemble
		if($facetsAllStr == ""){
			$returnStr = $headerStr .$noneStr;
		} else {
			$returnStr = $headerStr .$facetsAllStr;
		}
		
		// return
		return $returnStr;
	}
	
	
	
	// creates html to display the search info interpretted from the post request
	// Publication info
	private function _getSearchInfoPublicationHTML($translate, $inputList){
		
		// default
		$publicationAllStr = "";
		$headerStr = "<div class=\"heading\">" .$translate('Publication info, specified in query') ."</div>";
		$noneStr = "<div><span class=\"info\">" .$translate('None found') ."</span></div>";
		
		if(is_array($inputList)){
			
			// publication info
			$publicationInfo = $inputList['publicationInfo'];
			
			if(is_array($publicationInfo)){
				
				foreach($publicationInfo as $key => $infoList){
					
					// value
					if(is_array($infoList)){
						
						if(count($infoList)>0){
							// value
							$valueStr = "";
							foreach($infoList as $index => $infoValue){
								// row
								$rowStr = "<div><span class=\"name\">" .$index .":</span> <span class=\"value\">" .$infoValue ."</span></div>";
								// add to value string
								$valueStr .= $rowStr;
							}
							
							// variable and value
							$rowStr = "<div><span class=\"name\">" .$key .":</span></div>" .$valueStr;
						}
					} else {
						if(is_string($infoList) && $infoList == ""){
							$value = $translate('empty string');
							$valueStr = "<span class=\"error\">" .$value .":</span>";
						} else {
							$valueStr = $infoList;
						}
						
						// variable and value
						$rowStr = "<div><span class=\"name\">" .$key .":</span> <span class=\"value\">" .$valueStr ."</span></div>";
						
					}
					
					// add to all list
					$publicationAllStr .= $rowStr;
					
					
				}
				
			}
		}
		
		// assemble
		if($publicationAllStr == ""){
			$returnStr = $headerStr .$noneStr;
		} else {
			$returnStr = $headerStr .$publicationAllStr;
		}

		// return
		return $returnStr;
		
	}
	
	
	
	// creates html to display the search info interpretted from the post request
	// Content type
	private function _getSearchInfoContentTypeHTML($translate, $inputList){
		
		// default
		$contentTypeStr = "";
		$contentTypeHeaderStr = "<div class=\"heading\">" .$translate('Content type, specified in query') ."</div>";
		$noneStr = "<div><span class=\"info\">" .$translate('None found') ."</span></div>";
		
		if(is_array($inputList)){	
			
			// content types
			$contentTypes = $inputList['contentTypes'];
			
			if(is_array($contentTypes)){
				$contentTypeIs = $contentTypes['is'];
				$contentTypeHas = $contentTypes['has'];
				
				// content type - is
				if(count($contentTypeIs) > 0){
					
					foreach($contentTypeIs as $variableName => $value){
						// value - boolean
						if(is_bool($value)){
							if($value){
								// row info
								$rowStr = "<div><span class=\"value\">is: " .$variableName ."</span></div>";
								
								// add
								$contentTypeStr .= $rowStr;
							}
						}	
					}
					
				}
				
				// content type - has
				if(count($contentTypeHas) > 0){
					
					foreach($contentTypeHas as $variableName => $value){
					// value - boolean
						if(is_bool($value)){
							if($value){
								// row info
								$rowStr = "<div><span class=\"value\">has: " .$variableName ."</span></div>";
								
								// add
								$contentTypeStr .= $rowStr;
							}
						}	
					}
					
				}
			}
			
		}
		
		// assemble
		if($contentTypeStr == ""){
			$returnStr = $contentTypeHeaderStr .$noneStr;
		} else {
			$returnStr = $contentTypeHeaderStr .$contentTypeStr;
		}
		

		// return
		return $returnStr;
	}
	
		
		
	// creates html to display the search results info to the post request
	public function getResultsInfoHTML($translate, $inputList){
		
		// default
		$returnHTML = "";
		$contentStr = "";
		$contentHeaderStr = "<div class=\"heading\">" .$translate('Content items') ."</div>";
		$noneStr = "<div class=\"info\">" .$translate('None found') ."</div>";
		
		// hamel general
		$hamelGeneral = new HamelGeneral();
		
		if(is_array($inputList)){
			
			// content items
			$contentItems = $inputList['contentItems'];
			
			if(count($contentItems) > 0){
				
				$allRowStr = "";

				foreach($contentItems as $contentId => $contentInfo){
					if(is_array($contentInfo)){
						
						
						// info
						$id = $contentInfo['id'];
						$title = $contentInfo['title'];
						$author = $contentInfo['author'];
						$publisher = $contentInfo['publisher'];
						$yop = $contentInfo['yop'];
						$mop = $contentInfo['mop'];
						$contentType = $contentInfo['contentType'];
						$publicationType = $contentInfo['publicationType'];
						$publicationDetails = $contentInfo['publicationDetails'];
						$ranking = $contentInfo['ranking'];
						
						$summaryFull = $contentInfo['summary'];
						$summary = $hamelGeneral->selectNumWords($summaryFull, 20) ."...";
						
						// info
						$rowStr ="<tr class=\"even\"><td class=\"name\">Title</td><td class=\"value\">" .$title ."</td></tr>"
								."<tr class=\"odd\"><td class=\"name\">Author</td><td class=\"value\">" .$author ."</td></tr>"
								."<tr class=\"even\"><td class=\"name\">Publisher</td><td class=\"value\">" .$publisher ."</td></tr>"
								."<tr class=\"odd\"><td class=\"name\">Year</td><td class=\"value\">" .$yop ."</td></tr>"
								."<tr class=\"even\"><td class=\"name\">Month</td><td class=\"value\">" .$mop ."</td></tr>"
								."<tr class=\"odd\"><td class=\"name\">Content type</td><td class=\"value\">" .$contentType ."</td></tr>"
								."<tr class=\"even\"><td class=\"name\">Publication type</td><td class=\"value\">" .$publicationType ."</td></tr>"
								."<tr class=\"odd\"><td class=\"name\">Publication details</td><td class=\"value\">" .$publicationDetails ."</td></tr>"
								."<tr class=\"even\"><td class=\"name\">Content id</td><td class=\"value\">" .$id ."</td></tr>"
								."<tr class=\"odd\"><td class=\"name\">Search ranking</td><td class=\"value\">" .$ranking ."</td></tr>"
								."<tr class=\"even\"><td class=\"name\">Summary</td><td class=\"value\">" .$summary ."</td></tr>"
								;
								
						// add
						$allRowStr .= $rowStr ."<tr class=\"break\"><td colspan=\"2\">................................</td></tr>";
						
					}
				}
				
				$contentStr = "<div class=\"results\"><table class=\"resultsTable\"><tbody>" .$allRowStr ."</tbody></table></div>";
				
			}
			
			
			if($contentStr == ""){
				$returnHTML = $contentHeaderStr .$noneStr; 
			} else {
				$returnHTML = $contentHeaderStr .$contentStr;
			}

			
			// search info
			$searchInfoAllStr = "";
			$searchInfoStr  = "";
			$headerStr = "<div class=\"heading\">" .$translate('Search info') ."</div>";
			$noneStr = "<div><span class=\"info\">" .$translate('None given') ."</span></div>";
			
				
			$searchInfo = $inputList['serchInfo'];
				
			if(is_array($searchInfo)){
		
				foreach($searchInfo as $key => $infoList){
						
					// value
					if(is_array($infoList)){
		
						if(count($infoList)>0){
							// value
							$valueStr = "";
							foreach($infoList as $index => $infoValue){
								// row
								$rowStr = "<div><span class=\"name\">" .$index .":</span> <span class=\"value\">" .$infoValue ."</span></div>";
								// add to value string
								$valueStr .= $rowStr;
							}
								
							// variable and value
							$rowStr = "<div><span class=\"name\">" .$key .":</span></div>" .$valueStr;
						}
					} else {
						if(is_string($infoList) && $infoList == ""){
							$value = $translate('empty string');
							$valueStr = "<span class=\"error\">" .$value .":</span>";
						} else {
							$valueStr = $infoList;
						}
		
						// variable and value
						$rowStr = "<div><span class=\"name\">" .$key .":</span> <span class=\"value\">" .$valueStr ."</span></div>";
		
					}
						
					// add to all list
					$searchInfoStr .= $rowStr;
						
						
				}
		
			}
		
			// assemble
			if($searchInfoStr == ""){
				$returnHTML .= $headerStr .$noneStr;
			} else {
				$returnHTML .= $headerStr .$searchInfoStr;
			}
			
			
		}
		
				
		
		
		// return
		return $returnHTML;
	}
	
	
	
	
}








