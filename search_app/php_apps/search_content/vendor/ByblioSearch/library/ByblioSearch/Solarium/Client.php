<?php

/**
 * Byblio Solarium functions
 * Creates Solarium client
 * @copyright 2013 Byblio.com
 * @author: Paul A. Oliver
 *
 */

namespace ByblioSearch\Solarium;
use Solarium\Client as SolariumClient;


class Client{
	
	protected $_core;
	
	// constructor - sets connect variables
	public function __construct(){
		
		$autoloadPath = realpath(dirname(__DIR__) .'/../../../Solarium/vendor') .'/autoload.php';
		require $autoloadPath;
		
		
		if(substr_count($_SERVER['HTTP_HOST'], 'localhost')>0){
			
			// connection config to solr
			$this->_core = array(
					'sl_hostname'=> 'localhost',
					'sl_port'=> '8983',
					'sl_path'=> '/solr/',					
					
					);
			
		} else {
		    if(substr_count($_SERVER['HTTP_HOST'], 'bybliotest')>0){
		    	
		    	// ***** TODO Change to Tomcat server details ****/
		    	// connection config to solr
				$this->_core = array(
					'sl_hostname'=> 'localhost',
					'sl_port'=> '8983',
					'sl_path'=> '/solr/',					
					
					);
		    } 
		    
		}
		

	}

	
	// opens a solr connection
	public function getSolrClient(){
		
		// config array
		$config = array(
				'endpoint' => array(
						'localhost' =>$this->_core
						)
				);
		
		// create a client instance
		$client = new SolariumClient($config);
		
	
		return $client;
	}
	
	
	


}



?>