<?php

/**
 * Byblio Library
 * General functions
 * @copyright 2013 Byblio.com
 * @author: Paul A. Oliver
 *
 */

namespace Hamel\General;

class General{
	
	
	// constructor
	public function __construct(){
		
	}

	// gets real path of relative path
	function getRealPath($path){
	
		 // whether $path is unix or not
	    $unipath=strlen($path)==0 || $path{0}!='/';
	    // attempts to detect if path is relative in which case, add cwd
	    if(strpos($path,':')===false && $unipath)
	        $path=getcwd().DIRECTORY_SEPARATOR.$path;
	    // resolve path parts (single dot, double dot and double delimiters)
	    $path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
	    $parts = array_filter(explode(DIRECTORY_SEPARATOR, $path), 'strlen');
	    $absolutes = array();
	    foreach ($parts as $part) {
	        if ('.'  == $part) continue;
	        if ('..' == $part) {
	            array_pop($absolutes);
	        } else {
	            $absolutes[] = $part;
	        }
	    }
	    $path=implode(DIRECTORY_SEPARATOR, $absolutes);
	    // resolve any symlinks
	    if(file_exists($path) && linkinfo($path)>0)$path=readlink($path);
	    // put initial separator that could have been lost
	    $path=!$unipath ? '/'.$path : $path;
	    return $path;
	
	}
	
	// formats a given string to be able to display in browser title
	function makeSafeForBrowserTitle($inputStr){
		
		// add to new html doc, forcing encoding
		$titleStr = "<title>" .$inputStr ."</title>";	
		$doc = new \DOMDocument();
		@$doc->loadHTML('<?xml encoding="UTF-8">' . $titleStr);
		
		// recover text
		$nodes = $doc->getElementsByTagName('title');
		$returnStr = $nodes->item(0)->nodeValue;
		
		// return
		return $returnStr;
		
	}
	
	// Unicode-proof htmlentities.
	// Returns 'normal' chars as chars and weirdos as numeric html entites.
	function superentities( $str ){
		// get rid of existing entities else double-escape
		$str = html_entity_decode(stripslashes($str),ENT_QUOTES,'UTF-8');
		$ar = preg_split('/(?<!^)(?!$)/u', $str );  // return array of every multi-byte character
		foreach ($ar as $c){
			$o = ord($c);
			if ( (strlen($c) > 1) || /* multi-byte [unicode] */
					($o <32 || $o > 126) || /* <- control / latin weirdos -> */
					($o >33 && $o < 40) ||/* quotes + ambersand */
					($o >59 && $o < 63) /* html */
			) {
				// convert to numeric entity
				$c = mb_encode_numericentity($c,array (0x0, 0xffff, 0, 0xffff), 'UTF-8');
			}
			$str2 .= $c;
		}
		return $str2;
	}
	
	// selectes a given number of whole words form a given string
	function selectNumWords($inputStr, $numWords){
	
		$words = explode(' ', $inputStr);
	
		if (count($words) > $numWords){
			$output = implode(' ', array_slice($words, 0, $numWords));
		} else {
			$output = $inputStr;
		}
		return trim($output); // cut off the last space.
	}
	
	
	// sorts a single level associative array, case insensitive, UTF-8
	function sortCaseInsensitive($arr, $direction) {
		$arr2 = $arr;
		foreach($arr2 as $key => $val) {
			$arr2[$key] = mb_strtolower($val,'UTF-8');
		}
	
		if($direction == 'DESC'){
			arsort($arr2);
		} else {
			asort($arr2);
		}
		
		foreach($arr2 as $key => $val) {
			$arr2[$key] = $arr[$key];
		}
	
		return $arr2;
	}


	// sorts an array of associative arrays by a given set of keys in the associative array
	// input: $unsortedArray = array(associative_array(), associative_array(), ...). This can also be an associative array.
	// input: $sortKey = the key in the associative_array to order on
	// input: $sortDir = ASC or DSC
	function sortArrayOfArraysOnKey($unsortedArray, $sortKey, $sortDir){
	    
	    
	    //  array of columns
	    $sortArray = array();
	    foreach($unsortedArray as $key => $row){
	        $sortArray[$key] = mb_strtolower($row[$sortKey],'UTF-8');   
	    }
	    
	    // direction
	    if($sortDir =='ASC'){
	        $sortDir = SORT_ASC; 
	    } else {
	        $sortDir = SORT_DESC;
	    }
	    // sort
	    array_multisort($sortArray, $sortDir, $unsortedArray);
	    
	    return $unsortedArray;
	}
	
	
	
	// converts a given string to title case
	// input parameters include a list of exceptions
	function stringConvertToTitleCase($inputStr){
	
		$delimiters = array(" ", "-", ".", "'", "O'", "Mc");
	
		// Exceptions in lower case are words you don't want converted
		// Exceptions all in upper case are any words you don't want converted to title case but should be converted to upper case, e.g.:
		// king henry viii or king henry Viii should be King Henry VIII
		$exceptions = array("út", "u", "s", "és", "utca", "tér", "krt", "körút", "sétány", "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII", "XIII", "XIV", "XV", "XVI", "XVII", "XVIII", "XIX", "XX", "XXI", "XXII", "XXIII", "XXIV", "XXV", "XXVI", "XXVII", "XXVIII", "XXIX", "XXX");
	
			
		$string = mb_convert_case($inputStr, MB_CASE_TITLE, "UTF-8");
	
		foreach ($delimiters as $dlnr => $delimiter){
			$words = explode($delimiter, $string);
			$newwords = array();
			foreach ($words as $wordnr => $word){
				 
				if (in_array(mb_strtoupper($word, "UTF-8"), $exceptions)){
					// check exceptions list for any words that should be in upper case
					$word = mb_strtoupper($word, "UTF-8");
				}
				elseif (in_array(mb_strtolower($word, "UTF-8"), $exceptions)){
					// check exceptions list for any words that should be in upper case
					$word = mb_strtolower($word, "UTF-8");
				}
					
				elseif (!in_array($word, $exceptions) ){
					// convert to uppercase (non-utf8 only)
						
					$word = ucfirst($word);
						
				}
				array_push($newwords, $word);
			}
			$string = join($delimiter, $newwords);
		}//foreach
		 
		return $string;
		 
	}
	
	
	function stringConvertToLowerCase($inputStr, $inputCleaned){
	    
	    // detect encoding
	    $encoding = mb_detect_encoding($inputStr);
	    
	    // convert to lower case
	    $returnStr = mb_strtolower($inputStr, $encoding);
	    
	    // return
	    return $returnStr;
	    
	}
	
	
	
	
	// converts to html entities in utf 8
	function html_encodeUTF8($string){
		return htmlentities($string, ENT_QUOTES, 'UTF-8') ;
	}
	

	// returns browser info
	function getBrowserInfo(){
	    
		$u_agent = $_SERVER['HTTP_USER_AGENT'];
		$bname = 'Unknown';
		$platform = 'Unknown';
		$version= "";
	
		//First get the platform?
		if (preg_match('/linux/i', $u_agent)) {
			$platform = 'linux';
		}
		elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
			$platform = 'mac';
		}
		elseif (preg_match('/windows|win32/i', $u_agent)) {
			$platform = 'windows';
		}
	
		// Next get the name of the useragent yes seperately and for good reason
		if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent))
		{
			$bname = 'Internet Explorer';
			$ub = "MSIE";
		}
		elseif(preg_match('/Firefox/i',$u_agent))
		{
			$bname = 'Mozilla Firefox';
			$ub = "Firefox";
		}
		elseif(preg_match('/Chrome/i',$u_agent))
		{
			$bname = 'Google Chrome';
			$ub = "Chrome";
		}
		elseif(preg_match('/Safari/i',$u_agent))
		{
			$bname = 'Apple Safari';
			$ub = "Safari";
		}
		elseif(preg_match('/Opera/i',$u_agent))
		{
			$bname = 'Opera';
			$ub = "Opera";
		}
		elseif(preg_match('/Netscape/i',$u_agent))
		{
			$bname = 'Netscape';
			$ub = "Netscape";
		}
	
		// finally get the correct version number
		$known = array('Version', $ub, 'other');
		$pattern = '#(?<browser>' . join('|', $known) .
		')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
		if (!preg_match_all($pattern, $u_agent, $matches)) {
			// we have no matching number just continue
		}
	
		// see how many we have
		$i = count($matches['browser']);
		if ($i != 1) {
			//we will have two since we are not using 'other' argument yet
			//see if version is before or after the name
			if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
				$version= $matches['version'][0];
			}
			else {
				$version= $matches['version'][1];
			}
		}
		else {
			$version= $matches['version'][0];
		}
	
		// check if we have a number
		if ($version==null || $version=="") {$version="?";}
	
		return array(
				'userAgent' => $u_agent,
				'name'      => $bname,
				'version'   => $version,
				'platform'  => $platform,
				'pattern'    => $pattern
		);
	}
	
	
	
}




?>