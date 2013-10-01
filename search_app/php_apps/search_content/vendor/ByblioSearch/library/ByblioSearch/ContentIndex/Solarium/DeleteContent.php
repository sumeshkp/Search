<?php
 
/**
 * Byblio
 * Search engine
 * Primary SOLR class for deleting content item from content search index
 * @copyright 2013 Byblio.com
 * @author: Sumesh P
 *
 */

namespace ByblioSearch\ContentIndex\Solarium;
use ByblioSearch\Solarium\Client as SolariumClient;


class DeleteContent{
	
	
private $_solariumClient;
	
	
	public function __construct(){
		
		// create a client instance
		$solariumClient = new SolariumClient();
		$this->_solariumClient = $solariumClient->getSolrClient();
	}
	
	
	public function deleteFromIndex($inputList){
		
		// return info
		$returnInfo = array(
				'deleted' => false,
				);
		
		try {
			$update = $this->_solariumClient->createUpdate();
		}
		catch (\Exception $e_solr) {
			/*TODO: Change file location to defined error logs*/
			error_log(time().":".$e_solr->getMessage()."\n", 3, "/Applications/AMPPS/www/byblio/search_app/php_apps/search_errors.log");
		}
		
		// add the delete id and a commit command to the update query
		try {	
			$update->addDeleteById($inputList['contentId']);
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

		//Deleting document from Solr successful
		if (isset($result) && !isset($e_solr)) {
			$returnInfo['deleted'] = true;
		}
		
		return $returnInfo;
	}
	
}