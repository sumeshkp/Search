<?php
 
/**
 * Byblio
 * Search engine
 * Primary Solarium class for adding content item to content search index
 * @copyright 2013 Byblio.com
 * @author: Sumesh P
 *
 */

namespace ByblioSearch\ContentIndex\Solarium;

use ByblioSearch\Solarium\Client as SolariumClient;



class AddContent{
	
	private $_solariumClient;
	
	
	public function __construct(){
		
		// create a client instance
		$solariumClient = new SolariumClient();
		$this->_solariumClient = $solariumClient->getSolrClient();
	}
	
	
	public function addToIndex($inputList){
		
		// return info
		$returnInfo = array(
				'addResults' => array(),
				'contentAdded' => false,
				);
		
		// get an update query instance
		try {
			$update = $this->_solariumClient->createUpdate();
		}
		catch (\Exception $e_solr) {
			/*TODO: Change file location to defined error logs*/
			error_log(time().":".$e_solr->getMessage()."\n", 3, "/Applications/AMPPS/www/byblio/search_app/php_apps/search_errors.log");
		}
		
		
		// create a new document for the data
		$doc = $update->createDocument();
		
		//unique id of the content. This is MongoDB's unique key
		$doc->contentId = $inputList['contentId'];    
		
		//title of the content
		$doc->title = $inputList['title'];

		//publisher of the content
		$doc->publisher = $inputList['publisher'];
		
		//author in case of book or sildeshare, photographer in case of image, speaker in case of audio or video
		$doc->author = $inputList['author'];
		
		//genre - FICTION or NON FICTION
		$doc->genre = $inputList['genre'];
		
		//Date of Publication is global const DATETIME_FORMAT and strictly UTC "Y-m-d\TH:i:s\Z" for Solr4.0
		$doc->dop = $inputList['dop'];
		
		//author's description of the content
		$doc->summary = $inputList['summary']; 

		//embedded type; for example has:video will return a sliedeware or document which has video embedded
		$doc->hasType = $inputList['hasType'];

		//type of the document; for example has:video will return a video content
		$doc->isType = $inputList['isType'];
		
		//the full body of the document - alternatively called content, text etc
		$doc->content = $inputList['contentText'];
		
		// add the documents and a commit command to the update query
		try {
			$update->addDocuments(array($doc));
		}
		catch (\Exception $e_solr) {
			/*TODO: Change file location to defined error logs*/
			error_log(time().":".$e_solr->getMessage()."\n", 3, "/Applications/AMPPS/www/byblio/search_app/php_apps/search_errors.log");
		}
		try {
			$update->addCommit();
		}
		catch (\Exception $e_solr) {
			/*TODO: Change file location to defined error logs*/
			error_log(time().":".$e_solr->getMessage()."\n", 3, "/Applications/AMPPS/www/byblio/search_app/php_apps/search_errors.log");
		}
		// this executes the query and returns the result
		try {	
			$result = $this->_solariumClient->update($update);
		}
		catch (\Exception $e_solr) {
			/*TODO: Change file location to defined error logs*/
			error_log(time().":".$e_solr->getMessage()."\n", 3, "/Applications/AMPPS/www/byblio/search_app/php_apps/search_errors.log");
		}
		
		//Adding document to Solr successful
		if (isset($result) && !isset($e_solr)) {
			$returnInfo['contentAdded'] = true;
			$returnInfo['addResults'] = $result;
		}
		return $returnInfo;
	}
}








