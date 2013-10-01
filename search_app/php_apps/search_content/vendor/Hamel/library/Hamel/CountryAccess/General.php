<?php

/**
 * Byblio country codes
 * @copyright 2013 Byblio.com
 * @author: Paul A. Oliver
 *
 */

namespace Hamel\CountryAccess;

use Hamel\Db\General as DbGeneral;
use Hamel\General\General as HamelGeneral;


class General{
	
	protected $_allCountries;
	protected $_countryByRegion;
	protected $_regionNames;
	protected $_loggedIn;
	protected $_accountType;
	
	
	public function __construct($translate, $loggedIn = false, $accountType = 'user_free'){
		
		// record info
		$this->_loggedIn = $loggedIn;
		$this->_accountType = $accountType;
		
		if($translate){
			// set country list
			$this->_setCountryList($translate);
			
			// set region list
			$this->_setRegionList($translate);
		}
	}
	

	// returns list of all countries
	public function getAllCountries(){
		
		return $this->_allCountries;
	}
	
	// returns list of all regions
	public function getAllRegions(){
		
		return $this->_regionNames;
	}
	
	
	// returns info for the given country code
	// includes full name, region code and region name
	public function getCountryInfo($countryCode){
		
		// default
		$returnInfo = array('countryName'=>"", 'regionName' =>"", 'regionCode'=>"", 'countryCode'=>"");
		
		if(key_exists($countryCode, $this->_allCountries)){
				
			// country info
			$countryInfo = $this->_allCountries[$countryCode];
			
			// name
			$countryName = $countryInfo['name'];
			
			// region code
			$regionCode = $countryInfo['region'];
			
			if(key_exists($regionCode, $this->_regionNames)){
				// region name
				$regionName = $this->_regionNames[$regionCode];
				// record
				$returnInfo['regionName'] = $regionName;
			}
			
			// record
			$returnInfo['regionName'] = $regionName;
			$returnInfo['countryName'] = $countryName;
			$returnInfo['regionCode'] = $regionCode;
			$returnInfo['countryCode'] = $countryCode;
		}
		
		return $returnInfo;
		
	}
	
	
	
	// checks if current browser location is valid for the given country accesss restriction
	public function verifyBrowserAccess($countryRestrictions, $useAdminCountrySetting){
		
		// default:
		$countryOK = false;
		
		// permitted countries
		$permittedCountries = array();
		if(key_exists('countries', $countryRestrictions)){
			if(is_array($countryRestrictions['countries'])){
				// permitted countries
				$permittedCountries = $countryRestrictions['countries'];
			}
		}
		
		// permitted regions
		$permittedRegions = array();
		if(key_exists('regions', $countryRestrictions)){
			if(is_array($countryRestrictions['regions'])){
				$permittedRegions = $countryRestrictions['regions'];
			}
		}
			
		// check for the 'ALL' region flag which means any country is permitted
		if(in_array('ALL', $permittedRegions)){
			// all countries permitted
			$countryOK = true;
		}
		
		
		if(!$countryOK){ // no 'All" region flag in permitted countries, so check for specific county
			
			// current country or region of the browser
			$bcInfo = $this->getBrowserAccessCountry($useAdminCountrySetting);
			$currentBrowserCountry = $bcInfo['country'];
			
			// if the current browser country is set as ALL regions (by an admin user)
			if($currentBrowserCountry == 'ALL'){
				// set flag
				$countryOK = true;

			} else{
		
				// if the current browser country is set as a specifc region (by an admin user)
				if(key_exists($currentBrowserCountry, $this->_countryByRegion)){
					
					// list of countries acceptable for this region
					$browserCountryList = $this->_countryByRegion[$currentBrowserCountry];
					
					if(is_array($browserCountryList)){
						// check each permitted county
						foreach($permittedCountries as $countryCode){
							if(key_exists($countryCode, $browserCountryList)){
								// set flag
								$countryOK = true;
								// exit loop
								break;
							}
						}
					}
					
				} else { // the browser country is a single country

					// permitted countries
					if(key_exists('countries', $countryRestrictions)){
						// permitted countries
						$permittedCountries = $countryRestrictions['countries'];
							
						if(is_array($permittedCountries)){
							
							if(in_array($currentBrowserCountry, $permittedCountries)){
								// current country is in list of permitted countries
								$countryOK = true;
							}
							
						}
					}
				}
			}
		}	
			
		// return
		$returnInfo = array('countryOK'=> $countryOK, 'currentCountryCode'=>$currentBrowserCountry);
		
		return $returnInfo;
		
	}
	
	
	// gets current geographical region based on ip address
	public function getBrowserAccessRegion($useAdminCountrySetting){
		
		// default
		$browserRegion ='ALL';
		
		// country
		$browserCountry = $this->getBrowserAccessCountry($useAdminCountrySetting);
		
		if(key_exists($browserCountry, $this->_allCountries)){
			// country in
			$countryInfo = $this->_allCountries[$browserCountry];
			// region
			$browserRegion = $countryInfo['region'];
		}
			
		// return 
		return $browserRegion;
		
	}
	
	
	// gets current country based on ip address
	public function getBrowserAccessCountry($useAdminCountrySetting){

		// flag
		$userIPAddress = true;
		
		// if to look for the admin user's country setting (rather than the ip address)
		if($useAdminCountrySetting){
			
			// if logged in
			if($this->_loggedIn){
				
				// if this user is an admin
				if(mb_substr($this->_accountType, 0, 6) =='admin_'){
					
					// check to see if they have set the country of access
					$userCA = $_SESSION['hamelCA'];
					
					// if have code
					if(is_string($userCA)){
						// this is a valid country code
						if(in_array($userCA, $this->_allCountries)){
							// set flag
							$userIPAddress = false;
						} else {
							// if valid region code
							if(in_array($userCA, $this->_regionNames)){
								// set flag
								$userIPAddress = false;
							}
						}
					}
				}
			}
		}
		
		 
		if($userIPAddress){
			// get ip address
			$dbGeneral = new DbGeneral();
			$ipAddress = $dbGeneral->getRealIpAddr();
			
			// Tonbridge, UK
			//$ipAddress = '89.243.54.197';
			
			// current country
			switch($ipAddress){
				case "localhost":
					// set flag to the UK
					$browserCountry = "GB"; // God Bless you , sonny! :-)
				break;
				
				default:
					// geo ip
					require_once ("geoip/geoip.inc");
					// dbPath
					$dbPath = realpath(__DIR__ ."/geoip/GeoIP.dat");
					$gi = geoip_open($dbPath, GEOIP_STANDARD);
					
					// get country code
					$browserCountry = geoip_country_code_by_addr($gi, $ipAddress);
					
					// close geo ip
					geoip_close($gi);
				break;
			}
		} else {
			// use user-set country
			$browserCountry = $userCA;
		}
			
		
		// get region
		if(key_exists($browserCountry, $this->_allCountries)){
			// country info
			$countryInfo = $this->_allCountries[$browserCountry];
			// region
			$browserRegion = $countryInfo['region'];
			// region name
			$browserRegionName = $this->_regionNames[$browserRegion];
			// country name
			$browserCountryName = $countryInfo['name'];
		}
		
		// return 
		$returnInfo = array('country'=> $browserCountry, 'countryName'=> $browserCountryName, 'region'=>$browserRegion, 'regionName'=>$browserRegionName);
		
		return $returnInfo;
		
	}
	
	
	// sets list of countries by region
	private function _setRegionList($translate){
		
		// all countries by region
		$regionList = array();
		
		// region and names
		$this->_regionNames = array(
				'ALL' => $translate('All countries'),
				'AFR' => $translate('Africa'),
				'NAM' => $translate('North America'),
				'SAM' => $translate('South America'),
				'CAM' => $translate('Central America'),
				'CAR' => $translate('Carribbean'),
				'FAR' => $translate('Far East Asia'),
				'MEA' => $translate('Middle East'),
				'ASI' => $translate('Asia, excluding Middle East and Far East'),
				'EEU' => $translate('European Union'),
				'ENE' => $translate('Europe, not European Union'),
				'CIS' => $translate('Commonwealth of Independent States'),
				'OCE' => $translate('Australasia, Pacific islands'),
				'OTH' => $translate('Other'),
				);
		
		// create list of countries by region
		foreach($this->_allCountries as $countryCode => $countryInfo){
			
			$regionCode = $countryInfo['region'];
			
			// region list in list of all regions
			if(!key_exists($regionCode, $regionList)){
				$regionList[$regionCode] = array();
			}
			
			// name of region
			$regionName = "";
			if(key_exists($regionCode, $this->_regionNames)){
				$regionName = $this->_regionNames[$regionCode];
			}
			
			// record
			$countryInfo['regionName'] = $regionName;
			$regionList[$region][$countryCode] = $countryInfo;
			
		}
		
		// record
		$this->_countryByRegion = $regionList;
		
	}
	
	// sets list of countries, their codes and region ids
	// user current language to set names
	// based on ISO 3166
	private function _setCountryList($translate){
		
		
		$this->_allCountries = array(
				'AF' => array('code'=>'AF', 'region'=>'ASI', 'name' => $translate('Afghanistan')),
				'AX' => array('code'=>'AX', 'region'=>'EEU', 'name' => $translate('​&Aring;land Islands')),
				'AL' => array('code'=>'AL', 'region'=>'ENE', 'name' => $translate('Albania')),
				'DZ' => array('code'=>'DZ', 'region'=>'AFR', 'name' => $translate('Algeria')),
				'AS' => array('code'=>'AS', 'region'=>'OCE', 'name' => $translate('American Samoa')),
				'AD' => array('code'=>'AD', 'region'=>'ENE', 'name' => $translate('Andorra')),
				'AO' => array('code'=>'AO', 'region'=>'AFR', 'name' => $translate('Angola')),
				'AI' => array('code'=>'AI', 'region'=>'CAR', 'name' => $translate('Anguilla')),
				'AQ' => array('code'=>'AQ', 'region'=>'OTH', 'name' => $translate('Antarctica')),
				'AG' => array('code'=>'AG', 'region'=>'CAR', 'name' => $translate('Antigua and Barbuda')),
				'AR' => array('code'=>'AR', 'region'=>'SAM', 'name' => $translate('Argentina')),
				'AM' => array('code'=>'AM', 'region'=>'CIS', 'name' => $translate('Armenia')),
				'AW' => array('code'=>'AW', 'region'=>'CAR', 'name' => $translate('Aruba')),
				'AU' => array('code'=>'AU', 'region'=>'OCE', 'name' => $translate('Australia')),
				'AT' => array('code'=>'AT', 'region'=>'EEU', 'name' => $translate('Austria')),
				'AZ' => array('code'=>'AZ', 'region'=>'CIS', 'name' => $translate('Azerbaijan')),
				'BS' => array('code'=>'BS', 'region'=>'CAR', 'name' => $translate('Bahamas')),
				'BH' => array('code'=>'BH', 'region'=>'MEA', 'name' => $translate('Bahrain')),
				'BD' => array('code'=>'BD', 'region'=>'ASI', 'name' => $translate('Bangladesh')),
				'BB' => array('code'=>'BB', 'region'=>'CAR', 'name' => $translate('Barbados')),
				'BY' => array('code'=>'BY', 'region'=>'CIS', 'name' => $translate('Belarus')),
				'BE' => array('code'=>'BE', 'region'=>'EEU', 'name' => $translate('Belgium')),
				'BZ' => array('code'=>'BZ', 'region'=>'CAM', 'name' => $translate('Belize')),
				'BJ' => array('code'=>'BJ', 'region'=>'AFR', 'name' => $translate('Benin')),
				'BM' => array('code'=>'BM', 'region'=>'CAR', 'name' => $translate('Bermuda')),
				'BT' => array('code'=>'BT', 'region'=>'ASI', 'name' => $translate('Bhutan')),
				'BO' => array('code'=>'BO', 'region'=>'SAM', 'name' => $translate('Bolivia')),
				'BQ' => array('code'=>'BQ', 'region'=>'CAR', 'name' => $translate('Bonaire, Sint Eustatius and Saba')),
				'BA' => array('code'=>'BA', 'region'=>'ENE', 'name' => $translate('Bosnia and Herzegovina')),
				'BW' => array('code'=>'BW', 'region'=>'AFR', 'name' => $translate('Botswana')),
				'BR' => array('code'=>'BR', 'region'=>'SAM', 'name' => $translate('Brazil')),
				'IO' => array('code'=>'IO', 'region'=>'ASI', 'name' => $translate('British Indian Ocean Territory')),
				'BN' => array('code'=>'BN', 'region'=>'FAR', 'name' => $translate('Brunei Darussalam')),
				'BG' => array('code'=>'BG', 'region'=>'EEU', 'name' => $translate('Bulgaria')),
				'BF' => array('code'=>'BF', 'region'=>'AFR', 'name' => $translate('Burkina Faso')),
				'BI' => array('code'=>'BI', 'region'=>'AFR', 'name' => $translate('Burundi')),
				'KH' => array('code'=>'KH', 'region'=>'FAR', 'name' => $translate('Cambodia')),
				'CM' => array('code'=>'CM', 'region'=>'AFR', 'name' => $translate('Cameroon')),
				'CA' => array('code'=>'CA', 'region'=>'NAM', 'name' => $translate('Canada')),
				'CV' => array('code'=>'CV', 'region'=>'AFR', 'name' => $translate('Cape Verde')),
				'KY' => array('code'=>'KY', 'region'=>'CAR', 'name' => $translate('Cayman Islands')),
				'CF' => array('code'=>'CF', 'region'=>'AFR', 'name' => $translate('Central African Republic')),
				'TD' => array('code'=>'TD', 'region'=>'AFR', 'name' => $translate('Chad')),
				'CL' => array('code'=>'CL', 'region'=>'SAM', 'name' => $translate('Chile')),
				'CN' => array('code'=>'CN', 'region'=>'FAR', 'name' => $translate('China')),
				'CX' => array('code'=>'CX', 'region'=>'OCE', 'name' => $translate('Christmas Island')),
				'CC' => array('code'=>'CC', 'region'=>'OCE', 'name' => $translate('Cocos (Keeling) Islands')),
				'CO' => array('code'=>'CO', 'region'=>'SAM', 'name' => $translate('Colombia')),
				'KM' => array('code'=>'KM', 'region'=>'AFR', 'name' => $translate('Comoros')),
				'CG' => array('code'=>'CG', 'region'=>'AFR', 'name' => $translate('Congo')),
				'CD' => array('code'=>'CD', 'region'=>'AFR', 'name' => $translate('Congo, the Democratic Republic of the')),
				'CK' => array('code'=>'CK', 'region'=>'OCE', 'name' => $translate('Cook Islands')),
				'CR' => array('code'=>'CR', 'region'=>'CAM', 'name' => $translate('Costa Rica')),
				'CI' => array('code'=>'CI', 'region'=>'AFR', 'name' => $translate('C&ocirc;te d\'Ivoire')),
				'HR' => array('code'=>'HR', 'region'=>'EEU', 'name' => $translate('Croatia')),
				'CU' => array('code'=>'CU', 'region'=>'CAR', 'name' => $translate('Cuba')),
				'CW' => array('code'=>'CW', 'region'=>'CAR', 'name' => $translate('Curaçao')),
				'CY' => array('code'=>'CY', 'region'=>'EEU', 'name' => $translate('Cyprus')),
				'CZ' => array('code'=>'CZ', 'region'=>'EEU', 'name' => $translate('Czech Republic')),
				'DK' => array('code'=>'DK', 'region'=>'EEU', 'name' => $translate('Denmark')),
				'DJ' => array('code'=>'DJ', 'region'=>'AFR', 'name' => $translate('Djibouti')),
				'DM' => array('code'=>'DM', 'region'=>'CAR', 'name' => $translate('Dominica')),
				'DO' => array('code'=>'DO', 'region'=>'CAR', 'name' => $translate('Dominican Republic')),
				'EC' => array('code'=>'EC', 'region'=>'SAM', 'name' => $translate('Ecuador')),
				'EG' => array('code'=>'EG', 'region'=>'AFR', 'name' => $translate('Egypt')),
				'SV' => array('code'=>'SV', 'region'=>'CAM', 'name' => $translate('El Salvador')),
				'GQ' => array('code'=>'GQ', 'region'=>'AFR', 'name' => $translate('Equatorial Guinea')),
				'ER' => array('code'=>'ER', 'region'=>'AFR', 'name' => $translate('Eritrea')),
				'EE' => array('code'=>'EE', 'region'=>'EEU', 'name' => $translate('Estonia')),
				'ET' => array('code'=>'ET', 'region'=>'AFR', 'name' => $translate('Ethiopia')),
				'FK' => array('code'=>'FK', 'region'=>'SAM', 'name' => $translate('Falkland Islands (Malvinas)')),
				'FO' => array('code'=>'FO', 'region'=>'ENE', 'name' => $translate('Faroe Islands')),
				'FJ' => array('code'=>'FJ', 'region'=>'OCE', 'name' => $translate('Fiji')),
				'FI' => array('code'=>'FI', 'region'=>'EEU', 'name' => $translate('Finland')),
				'FR' => array('code'=>'FR', 'region'=>'EEU', 'name' => $translate('France')),
				'GF' => array('code'=>'GF', 'region'=>'SAM', 'name' => $translate('French Guiana')),
				'PF' => array('code'=>'PF', 'region'=>'OCE', 'name' => $translate('French Polynesia')),
				'TF' => array('code'=>'TF', 'region'=>'OTH', 'name' => $translate('French Southern Territories')),
				'GA' => array('code'=>'GA', 'region'=>'AFR', 'name' => $translate('Gabon')),
				'GM' => array('code'=>'GM', 'region'=>'AFR', 'name' => $translate('Gambia')),
				'GE' => array('code'=>'GE', 'region'=>'ENE', 'name' => $translate('Georgia')),
				'DE' => array('code'=>'DE', 'region'=>'EEU', 'name' => $translate('Germany')),
				'GH' => array('code'=>'GH', 'region'=>'AFR', 'name' => $translate('Ghana')),
				'GI' => array('code'=>'GI', 'region'=>'ENE', 'name' => $translate('Gibraltar')),
				'GR' => array('code'=>'GR', 'region'=>'EEU', 'name' => $translate('Greece')),
				'GL' => array('code'=>'GL', 'region'=>'ENE', 'name' => $translate('Greenland')),
				'GD' => array('code'=>'GD', 'region'=>'CAR', 'name' => $translate('Grenada')),
				'GP' => array('code'=>'GP', 'region'=>'CAR', 'name' => $translate('Guadeloupe')),
				'GU' => array('code'=>'GU', 'region'=>'OCE', 'name' => $translate('Guam')),
				'GT' => array('code'=>'GT', 'region'=>'CAM', 'name' => $translate('Guatemala')),
				'GG' => array('code'=>'GG', 'region'=>'ENE', 'name' => $translate('Guernsey')),
				'GN' => array('code'=>'GN', 'region'=>'AFR', 'name' => $translate('Guinea')),
				'GW' => array('code'=>'GW', 'region'=>'AFR', 'name' => $translate('Guinea-Bissau')),
				'GY' => array('code'=>'GY', 'region'=>'SAM', 'name' => $translate('Guyana')),
				'HT' => array('code'=>'HT', 'region'=>'CAR', 'name' => $translate('Haiti')),
				'VA' => array('code'=>'VA', 'region'=>'ENE', 'name' => $translate('Holy See (Vatican City State)')),
				'HN' => array('code'=>'HN', 'region'=>'CAM', 'name' => $translate('Honduras')),
				'HK' => array('code'=>'HK', 'region'=>'FAR', 'name' => $translate('Hong Kong')),
				'HU' => array('code'=>'HU', 'region'=>'EEU', 'name' => $translate('Hungary')),
				'IS' => array('code'=>'IS', 'region'=>'ENE', 'name' => $translate('Iceland')),
				'IN' => array('code'=>'IN', 'region'=>'ASI', 'name' => $translate('India')),
				'ID' => array('code'=>'ID', 'region'=>'FAR', 'name' => $translate('Indonesia')),
				'IR' => array('code'=>'IR', 'region'=>'MEA', 'name' => $translate('Iran, Islamic Republic of')),
				'IQ' => array('code'=>'IQ', 'region'=>'MEA', 'name' => $translate('Iraq')),
				'IE' => array('code'=>'IE', 'region'=>'EEU', 'name' => $translate('Ireland')),
				'IM' => array('code'=>'IM', 'region'=>'ENE', 'name' => $translate('Isle of Man')),
				'IL' => array('code'=>'IL', 'region'=>'MEA', 'name' => $translate('Israel')),
				'IT' => array('code'=>'IT', 'region'=>'EEU', 'name' => $translate('Italy')),
				'JM' => array('code'=>'JM', 'region'=>'CAR', 'name' => $translate('Jamaica')),
				'JP' => array('code'=>'JP', 'region'=>'FAR', 'name' => $translate('Japan')),
				'JE' => array('code'=>'JE', 'region'=>'ENE', 'name' => $translate('Jersey')),
				'JO' => array('code'=>'JO', 'region'=>'MEA', 'name' => $translate('Jordan')),
				'KZ' => array('code'=>'KZ', 'region'=>'CIS', 'name' => $translate('Kazakhstan')),
				'KE' => array('code'=>'KE', 'region'=>'AFR', 'name' => $translate('Kenya')),
				'KI' => array('code'=>'KI', 'region'=>'OCE', 'name' => $translate('Kiribati')),
				'KP' => array('code'=>'KP', 'region'=>'FAR', 'name' => $translate('Korea (North)')),
				'KR' => array('code'=>'KR', 'region'=>'FAR', 'name' => $translate('Korea, Republic of (South)')),
				'KW' => array('code'=>'KW', 'region'=>'MEA', 'name' => $translate('Kuwait')),
				'KG' => array('code'=>'KG', 'region'=>'CIS', 'name' => $translate('Kyrgyzstan')),
				'LA' => array('code'=>'LA', 'region'=>'FAR', 'name' => $translate('Laos (Lao People\'s Democratic Republic)')),
				'LV' => array('code'=>'LV', 'region'=>'EEU', 'name' => $translate('Latvia')),
				'LB' => array('code'=>'LB', 'region'=>'MEA', 'name' => $translate('Lebanon')),
				'LS' => array('code'=>'LS', 'region'=>'AFR', 'name' => $translate('Lesotho')),
				'LR' => array('code'=>'LR', 'region'=>'AFR', 'name' => $translate('Liberia')),
				'LY' => array('code'=>'LY', 'region'=>'AFR', 'name' => $translate('Libya')),
				'LI' => array('code'=>'LI', 'region'=>'ENE', 'name' => $translate('Liechtenstein')),
				'LT' => array('code'=>'LT', 'region'=>'EEU', 'name' => $translate('Lithuania')),
				'LU' => array('code'=>'LU', 'region'=>'EEU', 'name' => $translate('Luxembourg')),
				'MO' => array('code'=>'MO', 'region'=>'FAR', 'name' => $translate('Macao')),
				'MK' => array('code'=>'MK', 'region'=>'ENE', 'name' => $translate('Macedonia (FYR)')),
				'MG' => array('code'=>'MG', 'region'=>'AFR', 'name' => $translate('Madagascar')),
				'MW' => array('code'=>'MW', 'region'=>'AFR', 'name' => $translate('Malawi')),
				'MY' => array('code'=>'MY', 'region'=>'FAR', 'name' => $translate('Malaysia')),
				'MV' => array('code'=>'MV', 'region'=>'ASI', 'name' => $translate('Maldives')),
				'ML' => array('code'=>'ML', 'region'=>'AFR', 'name' => $translate('Mali')),
				'MT' => array('code'=>'MT', 'region'=>'EEU', 'name' => $translate('Malta')),
				'MH' => array('code'=>'MH', 'region'=>'OCE', 'name' => $translate('Marshall Islands')),
				'MQ' => array('code'=>'MQ', 'region'=>'CAR', 'name' => $translate('Martinique')),
				'MR' => array('code'=>'MR', 'region'=>'AFR', 'name' => $translate('Mauritania')),
				'MU' => array('code'=>'MU', 'region'=>'AFR', 'name' => $translate('Mauritius')),
				'YT' => array('code'=>'YT', 'region'=>'AFR', 'name' => $translate('Mayotte')),
				'MX' => array('code'=>'MX', 'region'=>'NAM', 'name' => $translate('Mexico')),
				'FM' => array('code'=>'FM', 'region'=>'OCE', 'name' => $translate('Micronesia, Federated States of')),
				'MD' => array('code'=>'MD', 'region'=>'CIS', 'name' => $translate('Moldova, Republic of')),
				'MC' => array('code'=>'MC', 'region'=>'ENE', 'name' => $translate('Monaco')),
				'MN' => array('code'=>'MN', 'region'=>'FAR', 'name' => $translate('Mongolia')),
				'ME' => array('code'=>'ME', 'region'=>'ENE', 'name' => $translate('Montenegro')),
				'MS' => array('code'=>'MS', 'region'=>'CAR', 'name' => $translate('Montserrat')),
				'MA' => array('code'=>'MA', 'region'=>'AFR', 'name' => $translate('Morocco')),
				'MZ' => array('code'=>'MZ', 'region'=>'AFR', 'name' => $translate('Mozambique')),
				'MM' => array('code'=>'MM', 'region'=>'FAR', 'name' => $translate('Myanmar')),
				'NA' => array('code'=>'NA', 'region'=>'AFR', 'name' => $translate('Namibia')),
				'NR' => array('code'=>'NR', 'region'=>'OCE', 'name' => $translate('Nauru')),
				'NP' => array('code'=>'NP', 'region'=>'ASI', 'name' => $translate('Nepal')),
				'NL' => array('code'=>'NL', 'region'=>'EEU', 'name' => $translate('Netherlands')),
				'NC' => array('code'=>'NC', 'region'=>'OCE', 'name' => $translate('New Caledonia')),
				'NZ' => array('code'=>'NZ', 'region'=>'OCE', 'name' => $translate('New Zealand')),
				'NI' => array('code'=>'NI', 'region'=>'CAM', 'name' => $translate('Nicaragua')),
				'NE' => array('code'=>'NE', 'region'=>'AFR', 'name' => $translate('Niger')),
				'NG' => array('code'=>'NG', 'region'=>'AFR', 'name' => $translate('Nigeria')),
				'NU' => array('code'=>'NU', 'region'=>'OCE', 'name' => $translate('Niue')),
				'NF' => array('code'=>'NF', 'region'=>'OCE', 'name' => $translate('Norfolk Island')),
				'MP' => array('code'=>'MP', 'region'=>'OCE', 'name' => $translate('Northern Mariana Islands')),
				'NO' => array('code'=>'NO', 'region'=>'ENE', 'name' => $translate('Norway')),
				'OM' => array('code'=>'OM', 'region'=>'MEA', 'name' => $translate('Oman')),
				'PK' => array('code'=>'PK', 'region'=>'ASI', 'name' => $translate('Pakistan')),
				'PW' => array('code'=>'PW', 'region'=>'OCE', 'name' => $translate('Palau')),
				'PS' => array('code'=>'PS', 'region'=>'MEA', 'name' => $translate('Palestine, State of')),
				'PA' => array('code'=>'PA', 'region'=>'CAM', 'name' => $translate('Panama')),
				'PG' => array('code'=>'PG', 'region'=>'OCE', 'name' => $translate('Papua New Guinea')),
				'PY' => array('code'=>'PY', 'region'=>'SAM', 'name' => $translate('Paraguay')),
				'PE' => array('code'=>'PE', 'region'=>'SAM', 'name' => $translate('Peru')),
				'PH' => array('code'=>'PH', 'region'=>'FAR', 'name' => $translate('Philippines')),
				'PN' => array('code'=>'PN', 'region'=>'OCE', 'name' => $translate('Pitcairn')),
				'PL' => array('code'=>'PL', 'region'=>'EEU', 'name' => $translate('Poland')),
				'PT' => array('code'=>'PT', 'region'=>'EEU', 'name' => $translate('Portugal')),
				'PR' => array('code'=>'PR', 'region'=>'CAR', 'name' => $translate('Puerto Rico')),
				'QA' => array('code'=>'QA', 'region'=>'MEA', 'name' => $translate('Qatar')),
				'RE' => array('code'=>'RE', 'region'=>'ASF', 'name' => $translate('Réunion')),
				'RO' => array('code'=>'RO', 'region'=>'EEU', 'name' => $translate('Romania')),
				'RU' => array('code'=>'RU', 'region'=>'CIS', 'name' => $translate('Russian Federation')),
				'RW' => array('code'=>'RW', 'region'=>'AFR', 'name' => $translate('Rwanda')),
				'BL' => array('code'=>'BL', 'region'=>'CAR', 'name' => $translate('Saint Barthélemy')),
				'SH' => array('code'=>'SH', 'region'=>'OTH', 'name' => $translate('Saint Helena, Ascension and Tristan da Cunha')),
				'KN' => array('code'=>'KN', 'region'=>'CAR', 'name' => $translate('Saint Kitts and Nevis')),
				'LC' => array('code'=>'LC', 'region'=>'CAR', 'name' => $translate('Saint Lucia')),
				'MF' => array('code'=>'MF', 'region'=>'CAR', 'name' => $translate('Saint Martin (French part)')),
				'PM' => array('code'=>'PM', 'region'=>'NAM', 'name' => $translate('Saint Pierre and Miquelon')),
				'VC' => array('code'=>'VC', 'region'=>'CAR', 'name' => $translate('Saint Vincent and the Grenadines')),
				'WS' => array('code'=>'WS', 'region'=>'OCE', 'name' => $translate('Samoa')),
				'SM' => array('code'=>'SM', 'region'=>'ENE', 'name' => $translate('San Marino')),
				'ST' => array('code'=>'ST', 'region'=>'AFR', 'name' => $translate('São Tomé and Príncipe')),
				'SA' => array('code'=>'SA', 'region'=>'MEA', 'name' => $translate('Saudi Arabia')),
				'SN' => array('code'=>'SN', 'region'=>'AFR', 'name' => $translate('Senegal')),
				'RS' => array('code'=>'RS', 'region'=>'ENE', 'name' => $translate('Serbia')),
				'SC' => array('code'=>'SC', 'region'=>'AFR', 'name' => $translate('Seychelles')),
				'SL' => array('code'=>'SL', 'region'=>'AFR', 'name' => $translate('Sierra Leone')),
				'SG' => array('code'=>'SG', 'region'=>'FAR', 'name' => $translate('Singapore')),
				'SX' => array('code'=>'SX', 'region'=>'CAR', 'name' => $translate('Sint Maarten (Dutch part)')),
				'SK' => array('code'=>'SK', 'region'=>'EEU', 'name' => $translate('Slovakia')),
				'SI' => array('code'=>'SI', 'region'=>'EEU', 'name' => $translate('Slovenia')),
				'SB' => array('code'=>'SB', 'region'=>'OCE', 'name' => $translate('Solomon Islands')),
				'SO' => array('code'=>'SO', 'region'=>'AFR', 'name' => $translate('Somalia')),
				'ZA' => array('code'=>'ZA', 'region'=>'AFR', 'name' => $translate('South Africa')),
				'GS' => array('code'=>'GS', 'region'=>'SAM', 'name' => $translate('South Georgia and the South Sandwich Islands')),
				'SS' => array('code'=>'SS', 'region'=>'AFR', 'name' => $translate('South Sudan')),
				'ES' => array('code'=>'ES', 'region'=>'EEU', 'name' => $translate('Spain')),
				'LK' => array('code'=>'LK', 'region'=>'ASI', 'name' => $translate('Sri Lanka')),
				'SD' => array('code'=>'SD', 'region'=>'AFR', 'name' => $translate('Sudan')),
				'SR' => array('code'=>'SR', 'region'=>'SAM', 'name' => $translate('Suriname')),
				'SJ' => array('code'=>'SJ', 'region'=>'ENE', 'name' => $translate('Svalbard and Jan Mayen')),
				'SZ' => array('code'=>'SZ', 'region'=>'AFR', 'name' => $translate('Swaziland')),
				'SE' => array('code'=>'SE', 'region'=>'EEU', 'name' => $translate('Sweden')),
				'CH' => array('code'=>'CH', 'region'=>'ENE', 'name' => $translate('Switzerland')),
				'SY' => array('code'=>'SY', 'region'=>'MEA', 'name' => $translate('Syrian Arab Republic')),
				'TW' => array('code'=>'TW', 'region'=>'FAR', 'name' => $translate('Taiwan')),
				'TJ' => array('code'=>'TJ', 'region'=>'CIS', 'name' => $translate('Tajikistan')),
				'TZ' => array('code'=>'TZ', 'region'=>'AFR', 'name' => $translate('Tanzania')),
				'TH' => array('code'=>'TH', 'region'=>'FAR', 'name' => $translate('Thailand')),
				'TL' => array('code'=>'TL', 'region'=>'OCE', 'name' => $translate('Timor-Leste')),
				'TG' => array('code'=>'TG', 'region'=>'AFR', 'name' => $translate('Togo')),
				'TK' => array('code'=>'TK', 'region'=>'OCE', 'name' => $translate('Tokelau')),
				'TO' => array('code'=>'TO', 'region'=>'OCE', 'name' => $translate('Tonga')),
				'TT' => array('code'=>'TT', 'region'=>'CAR', 'name' => $translate('Trinidad and Tobago')),
				'TN' => array('code'=>'TN', 'region'=>'AFR', 'name' => $translate('Tunisia')),
				'TR' => array('code'=>'TR', 'region'=>'ASI', 'name' => $translate('Turkey')),
				'TM' => array('code'=>'TM', 'region'=>'CIS', 'name' => $translate('Turkmenistan')),
				'TC' => array('code'=>'TC', 'region'=>'CAR', 'name' => $translate('Turks and Caicos Islands')),
				'TV' => array('code'=>'TV', 'region'=>'OCE', 'name' => $translate('Tuvalu')),
				'UG' => array('code'=>'UG', 'region'=>'AFR', 'name' => $translate('Uganda')),
				'UA' => array('code'=>'UA', 'region'=>'CIS', 'name' => $translate('Ukraine')),
				'AE' => array('code'=>'AE', 'region'=>'MEA', 'name' => $translate('United Arab Emirates')),
				'GB' => array('code'=>'GB', 'region'=>'EEU', 'name' => $translate('United Kingdom')),
				'US' => array('code'=>'US', 'region'=>'NAM', 'name' => $translate('United States')),
				'UM' => array('code'=>'UM', 'region'=>'OCE', 'name' => $translate('United States Minor Outlying Islands')),
				'UY' => array('code'=>'UY', 'region'=>'SAM', 'name' => $translate('Uruguay')),
				'UZ' => array('code'=>'UZ', 'region'=>'CIS', 'name' => $translate('Uzbekistan')),
				'VU' => array('code'=>'VU', 'region'=>'OCE', 'name' => $translate('Vanuatu')),
				'VE' => array('code'=>'VE', 'region'=>'SAM', 'name' => $translate('Venezuela')),
				'VN' => array('code'=>'VN', 'region'=>'FAR', 'name' => $translate('Vietnam')),
				'VG' => array('code'=>'VG', 'region'=>'CAR', 'name' => $translate('Virgin Islands, British')),
				'VI' => array('code'=>'VI', 'region'=>'CAR', 'name' => $translate('Virgin Islands, U.S.')),
				'WF' => array('code'=>'WF', 'region'=>'OCE', 'name' => $translate('Wallis and Futuna')),
				'EH' => array('code'=>'EH', 'region'=>'AFR', 'name' => $translate('Western Sahara')),
				'YE' => array('code'=>'YE', 'region'=>'MEA', 'name' => $translate('Yemen')),
				'ZM' => array('code'=>'ZM', 'region'=>'AFR', 'name' => $translate('Zambia')),
				'ZW' => array('code'=>'ZW', 'region'=>'AFR', 'name' => $translate('Zimbabwe')),
				);
		
	}
	
	
	
}








?>