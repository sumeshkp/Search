<?php
 
/**
 * Byblio
 * Search engine
 * Primary content search class
 * @copyright 2013 Byblio.com
 * @author: Paul Oliver, Sumesh P
 *
 */

namespace ByblioSearch\ContentSearch;
use ByblioSearch\ContentSearch\Solarium\Search as SolariumContentSearch;


class Search{
	
	
	public function __construct(){
		
	}
	
	
	public function search($inputList){
		
		// return info
		$returnInfo = array(
				'searchInfo' => array(),
				'searchResults' => array()
				);
		
		if(is_array($inputList)){
			
			// clean user's query string
			$queryStr = $this->_cleanQueryString($inputList);
		
			// parse query for in-line content types
			$queryInfo = $this->_parseQueryForContentTypes($queryStr);
			$contentTypes = $queryInfo['contentTypes'];
			$queryStr = $queryInfo['query'];
			
			// parse request for in-line publication info (author, publisher, year and month)
			$publicationAllInfo = $this->_parseQueryForPublicationInfo($queryStr);
			$publicationInfo = $publicationAllInfo['publicationInfo'];
			$queryStr = $publicationAllInfo['query'];
			
			// get specified search facets
			$specifiedFacetInfo = $this->_getSearchFacets($inputList);
			
			// get user info
			$userInfo = $this->_getUserInfo($inputList);
			 
			// search instance
			$solrContentSearch = new SolariumContentSearch();
			
			// search
			$searchInfo = array(
					'queryStr' =>$queryStr,
					'contentTypes' =>$contentTypes,
					'publicationInfo' =>$publicationInfo,
					'specifiedFacetInfo' =>$specifiedFacetInfo,
					'userInfo' =>$userInfo,
					);
			$searchResults = $solrContentSearch->search($searchInfo);
			
			// record
			$returnInfo['searchInfo'] = $searchInfo;
			$returnInfo['searchResults'] = $searchResults;
		}
		
		
		// return
		return $returnInfo;
	}
	
	
	// gets user infor from input
	private function _getUserInfo($inputList){
		
		// ip address
		$ipAddress = 'unknown';
		if(key_exists('ipAddress', $inputList)){
			if(is_string($inputList['ipAddress'])){
				$ipAddress = $inputList['ipAddress'];
			}
		}
		
		//username
		$username = 'unknown';
		if(key_exists('username', $inputList)){
			if(is_string($inputList['username'])){
				$username = $inputList['username'];
			}
		}
		
		// return
		$returnInfo = array(
			'ipAddress' =>$ipAddress,
			'username' =>$username,	
		);
		
		return $returnInfo;
		
	}
	
	
	
	// sets query string
	// converts to lower case - UTF8
	// creates empty string if none given
	private function _cleanQueryString($inputList){
		
		// default
		$queryStr = "";
		
		if(key_exists('query', $inputList)){
			if(is_string($inputList['query'])){
				// read
				$query = $inputList['query'];
				
				// remove multiple white space
				$query1 = preg_replace('/\s+/', ' ', $query);
				
				// trim
				$query2 = trim($query1);
				
				// convert to lower case
				$queryStr = mb_convert_case($query2, MB_CASE_LOWER, "UTF-8");
			}
		}
		
		// return
		return $queryStr;
		
	}
	
	
	
	// looks for in-line  publication info
	// possible info sets are:
	// author: - author contains
	// publisher: - publisher's name contains
	// year: - year is exact match
	// month: - month is exact match
	private function _parseQueryForPublicationInfo($query){
		
		// defaults
		$publicationInfo = array(
				'author' => array(),
				'publisher' => array(),
				'year' => array(),
				'month' => array(),
				);
		
		// update
		$returnInfo = array('publicationInfo' => $publicationInfo, 'query'=>$query);
		
		// return
		return $returnInfo;
	}
	
	
	
	
	
	// looks for in-line facets entered in a query string
	// possible types are:
	// is:video Ð returns video content only.
	// is:document Ð returns written documents, slideshows and social media content items.
	// is:image Ð returns photographs only.
	// is:audio Ð returns audio tracks only.
	// is:socialMedia Ð returns social media compilations tracks only.
	// has:video Ð returns content items that contain video and content items that are videos.
	// has:image Ð returns content items that contain photographs and content items that are photographs.
	// has:audio Ð returns content items that contain audio and content items that are audio tracks.
	// author: Ð [of] returns content items accredited to the given author.
	// publisher: Ð [of] returns content items accredited to the given publisher.
	// year: - [of] returns content items associated with the given year of release/ publication.
	
	// assumes multiple white space is removed and string in trimmed
	private function _parseQueryForContentTypes($query){
		
		// defaults
		$contentTypes = array(
				'is'=>array('document'=>false, 'video'=>false, 'image'=>false, 'audio'=>false, 'socialMedia'=>false),
				'has'=>array('video'=>false, 'image'=>false, 'audio'=>false),
				);
		
		
		// remove white space from all is: content type declarations (if exist)
		$query = str_replace('is: video', 'is:video', $query);
		$query = str_replace('is: document', 'is:document', $query);
		$query = str_replace('is: audio', 'is:audio', $query);
		$query = str_replace('is: image', 'is:image', $query);
		$query = str_replace('is: socialMedia', 'is:socialMedia', $query);
		
		// remove white space from all has: content type declarations (if exist)
		$query = str_replace('has: video', 'has:video', $query);
		$query = str_replace('has: audio', 'has:audio', $query);
		$query = str_replace('has: image', 'has:image', $query);

	
		
		// look for is: content types & remove from query string
		
		// 1: is:video
		if(is_int(mb_stripos($query, 'is:video'))){
			// record
			$contentTypes['is']['video'] = true;
			
			// remove all intances from search query
			$query = str_replace('is:video', '', $query);
		}
		
		// 2: is:document
		if(is_int(mb_stripos($query, 'is:document'))){
			// record
			$contentTypes['is']['document'] = true;
			
			// remove all intances from search query
			$query = str_replace('is:document', '', $query);
		}
		
		// 3: is:audio
		if(is_int(mb_stripos($query, 'is:audio'))){
			// record
			$contentTypes['is']['audio'] = true;
			
			// remove all intances from search query
			$query = str_replace('is:audio', '', $query);
		}
		
		// 4: is:image
		if(is_int(mb_stripos($query, 'is:image'))){
			// record
			$contentTypes['is']['image'] = true;
			
			// remove all intances from search query
			$query = str_replace('is:image', '', $query);
		}
		
		// 5: is:socialMedia
		if(is_int(mb_stripos($query, 'is:socialMedia'))){
			// record
			$contentTypes['is']['socialMedia'] = true;
			
			// remove all intances from search query
			$query = str_replace('is:socialMedia', '', $query);
		}
		
		
		
		// look for has: content types  & remove from query string
		
		// 1: has:video
		if(is_int(mb_stripos($query, 'has:video'))){
			// record
			$contentTypes['has']['video'] = true;
				
			// remove all intances from search query
			$query = str_replace('has:video', '', $query);
		}
		
		// 2: has:document
		if(is_int(mb_stripos($query, 'has:image'))){
			// record
			$contentTypes['has']['image'] = true;
				
			// remove all intances from search query
			$query = str_replace('has:image', '', $query);
		}
		
		// 3: has:audio
		if(is_int(mb_stripos($query, 'has:audio'))){
			// record
			$contentTypes['has']['audio'] = true;
				
			// remove all intances from search query
			$query = str_replace('has:audio', '', $query);
		}
		
		
		// trim the query 
		$query = trim($query);
		
		// update
		$returnInfo = array('contentTypes' => $contentTypes, 'query'=>$query);
		
		// return
		return $returnInfo;
		
	}
	
	
	// retrive specified facets
	private function _getSearchFacets($infoList){
		
		// default
		$facetList = array(
				'author'=>false,
				'title'=>false,
				'publisher'=>false,
				'summary'=>false,
				'genre-all'=>true,
				'genre-fiction'=>false,
				'genre-nonFiction'=>false,
				'yop'=>false,
				'mop'=>false,
				);
		$useFacets = false;
		
		if(is_array($infoList)){
			
			// use facets?
			if(key_exists('facet-use', $infoList)){
				
				$facetInfo = $infoList['facet-use'];
				
				if(is_bool($facetInfo)){ // boolean
					$useFacets = $facetInfo;
				} else {
					if($facetInfo == 'true'){ // string representation of boolean
						$useFacets = true;
					} else {
						if($facetInfo == 'false'){
							$useFacets = false;
						}
					}
				}
			}
			
			// author
			if(key_exists('facet-author', $infoList)){
				
				// default
				$useFacet = false;
				$facetInfo = $infoList['facet-author'];
				
				if(is_bool($facetInfo)){ // boolean
					$useFacet = $facetInfo;
				} else {
					if($facetInfo == 'true'){ // string representation of boolean
						$useFacet = true;
					} else {
						if($facetInfo == 'false'){
							$useFacet = false;
						}
					}
				}
				
				$facetList['author'] = $useFacet;
				
			}
			
			// title
			if(key_exists('facet-title', $infoList)){
				
				// default
				$useFacet = false;
				$facetInfo = $infoList['facet-title'];
				
				if(is_bool($facetInfo)){ // boolean
					$useFacet = $facetInfo;
				} else {
					if($facetInfo == 'true'){ // string representation of boolean
						$useFacet = true;
					} else {
						if($facetInfo == 'false'){
							$useFacet = false;
						}
					}
				}
				
				$facetList['title'] = $useFacet;
				
			}
			
			// publisher
			if(key_exists('facet-publisher', $infoList)){
				
				// default
				$useFacet = false;
				$facetInfo = $infoList['facet-publisher'];
				
				if(is_bool($facetInfo)){ // boolean
					$useFacet = $facetInfo;
				} else {
					if($facetInfo == 'true'){ // string representation of boolean
						$useFacet = true;
					} else {
						if($facetInfo == 'false'){
							$useFacet = false;
						}
					}
				}
				
				$facetList['publisher'] = $useFacet;
				
			}
			
			// summary
			if(key_exists('facet-summary', $infoList)){
				
				// default
				$useFacet = false;
				$facetInfo = $infoList['facet-summary'];
				
				if(is_bool($facetInfo)){ // boolean
					$useFacet = $facetInfo;
				} else {
					if($facetInfo == 'true'){ // string representation of boolean
						$useFacet = true;
					} else {
						if($facetInfo == 'false'){
							$useFacet = false;
						}
					}
				}
				
				$facetList['summary'] = $useFacet;
			}
			
			// genre all
			if(key_exists('facet-genre-all', $infoList)){
				
				// default
				$useFacet = false;
				$facetInfo = $infoList['facet-genre-all'];
				
				if(is_bool($facetInfo)){ // boolean
					$useFacet = $facetInfo;
				} else {
					if($facetInfo == 'true'){ // string representation of boolean
						$useFacet = true;
					} else {
						if($facetInfo == 'false'){
							$useFacet = false;
						}
					}
				}
				
				$facetList['genre-all'] = $useFacet;
			}
			
			// genre fiction
			if(key_exists('facet-genre-fiction', $infoList)){
				
				// default
				$useFacet = false;
				$facetInfo = $infoList['facet-genre-fiction'];
				
				if(is_bool($facetInfo)){ // boolean
					$useFacet = $facetInfo;
				} else {
					if($facetInfo == 'true'){ // string representation of boolean
						$useFacet = true;
					} else {
						if($facetInfo == 'false'){
							$useFacet = false;
						}
					}
				}
				
				$facetList['genre-fiction'] = $useFacet;
				
			}
			
			// genre non fiction
			if(key_exists('facet-genre-nonFiction', $infoList)){
				
				// default
				$useFacet = false;
				$facetInfo = $infoList['facet-genre-nonFiction'];
				
				if(is_bool($facetInfo)){ // boolean
					$useFacet = $facetInfo;
				} else {
					if($facetInfo == 'true'){ // string representation of boolean
						$useFacet = true;
					} else {
						if($facetInfo == 'false'){
							$useFacet = false;
						}
					}
				}
				
				$facetList['genre-nonFiction'] = $useFacet;
			}
			
			// year of publication
			if(key_exists('facet-yop', $infoList)){
				if(is_string($infoList['facet-yop'])){
					$facetList['yop'] = $infoList['facet-yop'];
				}
			}
			
			// month of publication
			if(key_exists('facet-mop', $infoList)){
				if(is_string($infoList['facet-mop'])){
					$facetList['mop'] = $infoList['facet-mop'];
				}
			}
			
			
		}
		
		// update
		$returnInfo = array('facetList' => $facetList, 'useFacets'=>$useFacets);
		
		// return
		return $returnInfo;
		
	}
}








