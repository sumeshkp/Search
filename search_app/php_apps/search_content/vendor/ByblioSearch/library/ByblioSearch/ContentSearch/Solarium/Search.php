<?php
 
/**
 * Byblio
 * Search engine
 * Primary SOLR content search class
 * @copyright 2013 Byblio.com
 * @author: Sumesh P
 *
 */

namespace ByblioSearch\ContentSearch\Solarium;
use ByblioSearch\Solarium\Client as SolariumClient;



class Search{
	
	private $_solariumClient;
	
	
	public function __construct(){
	
		// create a client instance
		$solariumClient = new SolariumClient();
		$this->_solariumClient = $solariumClient->getSolrClient();
	}
	
	
	public function search($inputList){
		
		// return info
		$returnInfo = array(
				'contentItems' => array(),
				'searchInfo' => array(),
				);
		try {
			// get a select query instance
			$query = $this->_solariumClient->createSelect();
		}
			catch (\Exception $e_solr) {
			/*TODO: Change file location to defined error logs*/
			error_log(time().":".$e_solr->getMessage()."\n", 3, "/Applications/AMPPS/www/byblio/search_app/php_apps/search_errors.log");
		}
		// set a query
		$search_string = $inputList['queryStr'];
		$search_in_text_filed = 'text:'.$search_string;
		$query->setQuery($search_in_text_filed);
		
		// set start and rows param (comparable to SQL limit) using fluent interface
		//$query->setStart(2)->setRows(20);
		
		// set fields to fetch (this overrides the default setting 'all fields')
		//$query->setFields(array('content_id'));
		
		try {
			// this executes the query and returns the result
			$resultset = $this->_solariumClient->select($query);
		}
		catch (\Exception $e_solr) {
			/*TODO: Change file location to defined error logs*/
			error_log(time().":".$e_solr->getMessage()."\n", 3, "/Applications/AMPPS/www/byblio/search_app/php_apps/search_errors.log");
		}
		
		$result = $resultset->getResponse()->getBody();
		
		/*TODO: XXX: Change response format, sitting with Paul*/
		/*TODO: Add search string, ipAddress, user_id to MongoDB */
		
		/*
		
		//******* TEMP ***********
		$contentItems = array(
				'3248efbde3960' => array(
						'id' => '3248efbde3960',
						'title'=>'Role of transformational and transactional leadership on job satisfaction and career satisfaction',
						'author'=>'Riaz, Adnan; Haider, Mubarak Hussein',
						'publisher'=> 'Business and Economic Horizons',
						'summary'=> 'The National Australia Bank (NAB), one of Australia\'s largest banks, announced losses in 2004 of AUD$360 million due to unauthorised foreign currency trading activities by four employees who incurred and deceptively concealed the losses. The NAB had in place risk limits and supervision to prevent trading desks ever reaching positions of this magnitude. However, the risk management policies and procedures proved ineffective. The purpose of this paper is to analyse the deceit, via a content analysis of official investigative reports and other published documents, to determine the extent to which the Bank\'s culture and leadership may have influenced the rogue traders\' behaviour. The findings suggest that cultural issues, and the role played by the Bank\'s leaders, were influential in creating a profit-driven culture that ultimately impacted the Bank\'s foreign exchange operating activities.',
						'yop'=> '2010',
						'mop'=> '00',
						'contentType' => 'PUBLN',
						'publicationType' =>'JOURNAL',
						'publicationDetails' => 'Volume 1, Issue 1, pp. 29-38',
						'ranking' => 0.99
						),
				
				'75078acbe9131' => array(
						'id' => '75078acbe9131',
						'title'=>'The Testing and Validation of a Model for Leadership Maturity Based on Jung\'s Concept of Individuation',
						'author'=>'du Tiot, Daniel; Veldsman, Theo; van Zyl, Deon',
						'publisher'=> 'University of Johannesburg, South Africa',
						'summary'=> 'This research acknowledges that knowledge management is a cross-disciplinary practice with strong links to organisational learning and complexity theory. Organisational learning is the process that enables an organisation to adapt to change and move forward by acquiring new knowledge. Complexity theory is the theory that argues that acquiring new knowledge evolves through the cognition in human organisations. Thus, to compete and be innovative in fast-moving environment organisations should enhance organisational learning by understanding the strength of cognition. Managing this cross-disciplinary practice requires a new form of leadership and therefore this research discovered crucial leadership behaviour and generated evidence of how leadership behaviour enhances organisational learning.',
						'yop'=> '2011',
						'mop'=> '00',
						'contentType' => 'PUBLN',
						'publicationType' =>'WEBSITE',
						'publicationDetails' => 'http://academic-conferences.org/pdfs/ecmlg2011_best_phd_paper.pdf',
						'ranking' => 0.78
						),
				);
		*/
		$returnInfo['contentItems'] = $result;
		
		// return
		return $returnInfo;
	}
	
	
	
	
}








