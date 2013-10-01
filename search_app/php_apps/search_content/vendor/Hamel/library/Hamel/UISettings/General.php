<?php
/**
 * Byblio UI general info
 * Includes cookies
 * @copyright 2013 Byblio.com
 * @author: Paul A. Oliver
 *
 */


namespace Hamel\UISettings;




class General{
	
	function __construct(){
		// set names
		$this->_setCookieNames();
		
	}
	
	
	// returns the current cookie names used for local storage and db storage
	public function getCookieName($pageRef){
		
		// default
		$cookieName = 'byblioDefault';
		
		if(key_exists($pageRef, $this->_cookieNames)){
			// read name
			$cookieName = $this->_cookieNames[$pageRef];	
		}
		
		return $cookieName;
		
	}
	
	// sets the  current cookie names
	private function _setCookieNames(){
		
		$this->_cookieNames = array(
				
				'useraccount-signup' =>'signupMgt',
				'library-librarymanagement' =>'libraryLibMgt',
				);
		
	}
	
};