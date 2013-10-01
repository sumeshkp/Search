<?php

/**
 * Byblio Library
 * Custom number formatting, does not rely on additional libraries
 * @copyright 2013 Byblio.com
 * @author: Paul A. Oliver
 *
 */

namespace Hamel\General;

class Numberformat{
	
	private $_formatByLocale;
	private $_defaultFormat;
	private $_locale;
	
	
	// constructor
	public function __construct($locale = NULL){
		
		// set list of formats and locales
		$this->_setFormatList();
		
		// default
		$this->_defaultFormat = array('thousands_sep'=>',', 'dec_point'=>'.');
		
		// set locale
		$this->_locale = $locale;
	}
	
	// returns formatted number, acceptable for html output
	public function formatNumberHTML($number, $numDecimalPlaces, $locale = NULL){
		
		// set locale
		if(!$locale){
			$locale = $this->_locale;
		}
		
		// get/ set formatting info
		if(key_exists($locale, $this->_formatByLocale)){
			$formatInfo = $this->_formatByLocale[$locale];
		} else {
			$formatInfo = $this->_defaultFormat;
		}
		
		$thousands_sep = $formatInfo['thousands_sep'];
		$dec_point = $formatInfo['dec_point'];
		
		// format number
		$fNumber = number_format($number, $numDecimalPlaces, $dec_point, $thousands_sep);
		
		// convert space to html safe
		$htmlNumber = str_replace(" ", "&nbsp;", $fNumber);
		
		// return
		return $htmlNumber;
	}

	
	// returns formatting for current locale
	public function getCurrentFormattingInfo(){
		
		// current  locale
		$locale = $this->_locale;
		
		// get/ set formatting info
		if(key_exists($locale, $this->_formatByLocale)){
			$formatInfo = $this->_formatByLocale[$locale];
		} else {
			$formatInfo = $this->_defaultFormat;
		}
		
		// return
		return $formatInfo;
	}
	
	
	// returns formatting info for a given locale
	public function getFormattingInfo($locale){
		
		// get/ set formatting info
		if(key_exists($locale, $this->_formatByLocale)){
			$formatInfo = $this->_formatByLocale[$locale];
		} else {
			$formatInfo = $this->_defaultFormat;
		}
		
		// return
		return $formatInfo;
	}
	
	
	
	// gets real path of relative path
	private function _setFormatList(){
		
		$this->_formatByLocale = array(
				'ar-SA'=>array('thousands_sep'=>',', 'dec_point'=>'.'),
				'zh-TW'=>array('thousands_sep'=>',', 'dec_point'=>'.'),
				'en-US'=>array('thousands_sep'=>',', 'dec_point'=>'.'),
				'he-IL'=>array('thousands_sep'=>',', 'dec_point'=>'.'),
				'ja-JP'=>array('thousands_sep'=>',', 'dec_point'=>'.'),
				'ko-KR'=>array('thousands_sep'=>',', 'dec_point'=>'.'),
				'th-TH'=>array('thousands_sep'=>',', 'dec_point'=>'.'),
				'ur-PK'=>array('thousands_sep'=>',', 'dec_point'=>'.'),
				'hy-AM'=>array('thousands_sep'=>',', 'dec_point'=>'.'),
				'af-ZA'=>array('thousands_sep'=>',', 'dec_point'=>'.'),
				'hi-IN'=>array('thousands_sep'=>',', 'dec_point'=>'.'),
				'sw-KE'=>array('thousands_sep'=>',', 'dec_point'=>'.'),
				'pa-IN'=>array('thousands_sep'=>',', 'dec_point'=>'.'),
				'gu-IN'=>array('thousands_sep'=>',', 'dec_point'=>'.'),
				'ta-IN'=>array('thousands_sep'=>',', 'dec_point'=>'.'),
				'te-IN'=>array('thousands_sep'=>',', 'dec_point'=>'.'),
				'kn-IN'=>array('thousands_sep'=>',', 'dec_point'=>'.'),
				'mr-IN'=>array('thousands_sep'=>',', 'dec_point'=>'.'),
				'sa-IN'=>array('thousands_sep'=>',', 'dec_point'=>'.'),
				'kok-IN'=>array('thousands_sep'=>',', 'dec_point'=>'.'),
				'syr-SR'=>array('thousands_sep'=>',', 'dec_point'=>'.'),
				'dv-MV'=>array('thousands_sep'=>',', 'dec_point'=>'.'),
				'ar-IQ'=>array('thousands_sep'=>',', 'dec_point'=>'.'),
				'zh-CN'=>array('thousands_sep'=>',', 'dec_point'=>'.'),
				'en-GB'=>array('thousands_sep'=>',', 'dec_point'=>'.'),
				'es-MX'=>array('thousands_sep'=>',', 'dec_point'=>'.'),
				'ar-EG'=>array('thousands_sep'=>',', 'dec_point'=>'.'),
				'zh-HK'=>array('thousands_sep'=>',', 'dec_point'=>'.'),
				'en-AU'=>array('thousands_sep'=>',', 'dec_point'=>'.'),
				'ar-LY'=>array('thousands_sep'=>',', 'dec_point'=>'.'),
				'zh-SG'=>array('thousands_sep'=>',', 'dec_point'=>'.'),
				'en-CA'=>array('thousands_sep'=>',', 'dec_point'=>'.'),
				'es-GT'=>array('thousands_sep'=>',', 'dec_point'=>'.'),
				'ar-DZ'=>array('thousands_sep'=>',', 'dec_point'=>'.'),
				'zh-MO'=>array('thousands_sep'=>',', 'dec_point'=>'.'),
				'en-NZ'=>array('thousands_sep'=>',', 'dec_point'=>'.'),
				'ar-MA'=>array('thousands_sep'=>',', 'dec_point'=>'.'),
				'en-IE'=>array('thousands_sep'=>',', 'dec_point'=>'.'),
				'es-PA'=>array('thousands_sep'=>',', 'dec_point'=>'.'),
				'ar-TN'=>array('thousands_sep'=>',', 'dec_point'=>'.'),
				'en-ZA'=>array('thousands_sep'=>',', 'dec_point'=>'.'),
				'es-DO'=>array('thousands_sep'=>',', 'dec_point'=>'.'),
				'ar-OM'=>array('thousands_sep'=>',', 'dec_point'=>'.'),
				'en-JM'=>array('thousands_sep'=>',', 'dec_point'=>'.'),
				'ar-YE'=>array('thousands_sep'=>',', 'dec_point'=>'.'),
				'en-029'=>array('thousands_sep'=>',', 'dec_point'=>'.'),
				'ar-SY'=>array('thousands_sep'=>',', 'dec_point'=>'.'),
				'en-BZ'=>array('thousands_sep'=>',', 'dec_point'=>'.'),
				'es-PE'=>array('thousands_sep'=>',', 'dec_point'=>'.'),
				'ar-JO'=>array('thousands_sep'=>',', 'dec_point'=>'.'),
				'en-TT'=>array('thousands_sep'=>',', 'dec_point'=>'.'),
				'ar-LB'=>array('thousands_sep'=>',', 'dec_point'=>'.'),
				'en-ZW'=>array('thousands_sep'=>',', 'dec_point'=>'.'),
				'ar-KW'=>array('thousands_sep'=>',', 'dec_point'=>'.'),
				'en-PH'=>array('thousands_sep'=>',', 'dec_point'=>'.'),
				'ar-AE'=>array('thousands_sep'=>',', 'dec_point'=>'.'),
				'ar-BH'=>array('thousands_sep'=>',', 'dec_point'=>'.'),
				'ar-QA'=>array('thousands_sep'=>',', 'dec_point'=>'.'),
				'es-SV'=>array('thousands_sep'=>',', 'dec_point'=>'.'),
				'es-HN'=>array('thousands_sep'=>',', 'dec_point'=>'.'),
				'es-NI'=>array('thousands_sep'=>',', 'dec_point'=>'.'),
				'es-PR'=>array('thousands_sep'=>',', 'dec_point'=>'.'),
				'zu-ZA'=>array('thousands_sep'=>',', 'dec_point'=>'.'),
				'xh-ZA'=>array('thousands_sep'=>',', 'dec_point'=>'.'),
				'tn-ZA'=>array('thousands_sep'=>',', 'dec_point'=>'.'),
				'quz-PE'=>array('thousands_sep'=>',', 'dec_point'=>'.'),
				'cy-GB'=>array('thousands_sep'=>',', 'dec_point'=>'.'),
				'fil-PH'=>array('thousands_sep'=>',', 'dec_point'=>'.'),
				'iu-Latn-CA'=>array('thousands_sep'=>',', 'dec_point'=>'.'),
				'mi-NZ'=>array('thousands_sep'=>',', 'dec_point'=>'.'),
				'ga-IE'=>array('thousands_sep'=>',', 'dec_point'=>'.'),
				'moh-CA'=>array('thousands_sep'=>',', 'dec_point'=>'.'),
				'ns-ZA'=>array('thousands_sep'=>',', 'dec_point'=>'.'),
				'mt-MT'=>array('thousands_sep'=>',', 'dec_point'=>'.'),
				'ca-ES'=>array('thousands_sep'=>'.', 'dec_point'=>','),
				'da-DK'=>array('thousands_sep'=>'.', 'dec_point'=>','),
				'de-DE'=>array('thousands_sep'=>'.', 'dec_point'=>','),
				'el-GR'=>array('thousands_sep'=>'.', 'dec_point'=>','),
				'is-IS'=>array('thousands_sep'=>'.', 'dec_point'=>','),
				'it-IT'=>array('thousands_sep'=>'.', 'dec_point'=>','),
				'nl-NL'=>array('thousands_sep'=>'.', 'dec_point'=>','),
				'pt-BR'=>array('thousands_sep'=>'.', 'dec_point'=>','),
				'ro-RO'=>array('thousands_sep'=>'.', 'dec_point'=>','),
				'hr-HR'=>array('thousands_sep'=>'.', 'dec_point'=>','),
				'sq-AL'=>array('thousands_sep'=>'.', 'dec_point'=>','),
				'sv-SE'=>array('thousands_sep'=>'.', 'dec_point'=>','),
				'tr-TR'=>array('thousands_sep'=>'.', 'dec_point'=>','),
				'id-ID'=>array('thousands_sep'=>'.', 'dec_point'=>','),
				'sl-SI'=>array('thousands_sep'=>'.', 'dec_point'=>','),
				'lt-LT'=>array('thousands_sep'=>'.', 'dec_point'=>','),
				'vi-VN'=>array('thousands_sep'=>'.', 'dec_point'=>','),
				'eu-ES'=>array('thousands_sep'=>'.', 'dec_point'=>','),
				'mk-MK'=>array('thousands_sep'=>'.', 'dec_point'=>','),
				'fo-FO'=>array('thousands_sep'=>'.', 'dec_point'=>','),
				'ms-MY'=>array('thousands_sep'=>'.', 'dec_point'=>','),
				'gl-ES'=>array('thousands_sep'=>'.', 'dec_point'=>','),
				'fr-BE'=>array('thousands_sep'=>'.', 'dec_point'=>','),
				'nl-BE'=>array('thousands_sep'=>'.', 'dec_point'=>','),
				'pt-PT'=>array('thousands_sep'=>'.', 'dec_point'=>','),
				'sr-Latn_CS'=>array('thousands_sep'=>'.', 'dec_point'=>','),
				'ms-BN'=>array('thousands_sep'=>'.', 'dec_point'=>','),
				'de-AT'=>array('thousands_sep'=>'.', 'dec_point'=>','),
				'es-ES'=>array('thousands_sep'=>'.', 'dec_point'=>','),
				'sr-Cyrl-CS'=>array('thousands_sep'=>'.', 'dec_point'=>','),
				'de-LU'=>array('thousands_sep'=>'.', 'dec_point'=>','),
				'es-CR'=>array('thousands_sep'=>'.', 'dec_point'=>','),
				'es-VE'=>array('thousands_sep'=>'.', 'dec_point'=>','),
				'es-CO'=>array('thousands_sep'=>'.', 'dec_point'=>','),
				'es-AR'=>array('thousands_sep'=>'.', 'dec_point'=>','),
				'es-EC'=>array('thousands_sep'=>'.', 'dec_point'=>','),
				'es-CL'=>array('thousands_sep'=>'.', 'dec_point'=>','),
				'es-UY'=>array('thousands_sep'=>'.', 'dec_point'=>','),
				'es-PY'=>array('thousands_sep'=>'.', 'dec_point'=>','),
				'es-BO'=>array('thousands_sep'=>'.', 'dec_point'=>','),
				'sr-Cyrl-BA'=>array('thousands_sep'=>'.', 'dec_point'=>','),
				'fy-NL'=>array('thousands_sep'=>'.', 'dec_point'=>','),
				'se-SE'=>array('thousands_sep'=>'.', 'dec_point'=>','),
				'sma-SE'=>array('thousands_sep'=>'.', 'dec_point'=>','),
				'hr-BA'=>array('thousands_sep'=>'.', 'dec_point'=>','),
				'bs-La'=>array('thousands_sep'=>'.', 'dec_point'=>','),
				'bs-Cy'=>array('thousands_sep'=>'.', 'dec_point'=>','),
				'arn-CL'=>array('thousands_sep'=>'.', 'dec_point'=>','),
				'quz-EC'=>array('thousands_sep'=>'.', 'dec_point'=>','),
				'sr-La'=>array('thousands_sep'=>'.', 'dec_point'=>','),
				'smj-SE'=>array('thousands_sep'=>'.', 'dec_point'=>','),
				'quz-BO'=>array('thousands_sep'=>'.', 'dec_point'=>','),
				'de-CH'=>array('thousands_sep'=>"'", 'dec_point'=>'.'),
				'it-CH'=>array('thousands_sep'=>"'", 'dec_point'=>'.'),
				'fr-CH'=>array('thousands_sep'=>"'", 'dec_point'=>'.'),
				'de-LI'=>array('thousands_sep'=>"'", 'dec_point'=>'.'),
				'rm-CH'=>array('thousands_sep'=>"'", 'dec_point'=>'.'),
				'bg-BG'=>array('thousands_sep'=>' ', 'dec_point'=>','),
				'cs-CZ'=>array('thousands_sep'=>' ', 'dec_point'=>','),
				'fi-FI'=>array('thousands_sep'=>' ', 'dec_point'=>','),
				'fr-FR'=>array('thousands_sep'=>' ', 'dec_point'=>','),
				'hu-HU'=>array('thousands_sep'=>' ', 'dec_point'=>','),
				'nb-NO'=>array('thousands_sep'=>' ', 'dec_point'=>','),
				'pl-PL'=>array('thousands_sep'=>' ', 'dec_point'=>','),
				'ru-RU'=>array('thousands_sep'=>' ', 'dec_point'=>','),
				'sk-SK'=>array('thousands_sep'=>' ', 'dec_point'=>','),
				'uk-UA'=>array('thousands_sep'=>' ', 'dec_point'=>','),
				'be-BY'=>array('thousands_sep'=>' ', 'dec_point'=>','),
				'lv-LV'=>array('thousands_sep'=>' ', 'dec_point'=>','),
				'az-Latn-AZ'=>array('thousands_sep'=>' ', 'dec_point'=>','),
				'ka-GE'=>array('thousands_sep'=>' ', 'dec_point'=>','),
				'uz-Latn-UZ'=>array('thousands_sep'=>' ', 'dec_point'=>','),
				'tt-RU'=>array('thousands_sep'=>' ', 'dec_point'=>','),
				'mn-MN'=>array('thousands_sep'=>' ', 'dec_point'=>','),
				'nn-NO'=>array('thousands_sep'=>' ', 'dec_point'=>','),
				'sv-FI'=>array('thousands_sep'=>' ', 'dec_point'=>','),
				'az-Cyrl-AZ'=>array('thousands_sep'=>' ', 'dec_point'=>','),
				'uz-Cyrl-UZ'=>array('thousands_sep'=>' ', 'dec_point'=>','),
				'fr-CA'=>array('thousands_sep'=>' ', 'dec_point'=>','),
				'fr-LU'=>array('thousands_sep'=>' ', 'dec_point'=>','),
				'fr-MC'=>array('thousands_sep'=>' ', 'dec_point'=>','),
				'sma-NO'=>array('thousands_sep'=>' ', 'dec_point'=>','),
				'smn-FI'=>array('thousands_sep'=>' ', 'dec_point'=>','),
				'se-FI'=>array('thousands_sep'=>' ', 'dec_point'=>','),
				'sms-FI'=>array('thousands_sep'=>' ', 'dec_point'=>','),
				'smj-NO'=>array('thousands_sep'=>' ', 'dec_point'=>','),
				'lb-LU'=>array('thousands_sep'=>' ', 'dec_point'=>','),
				'se-NO'=>array('thousands_sep'=>' ', 'dec_point'=>','),
				'fa-IR'=>array('thousands_sep'=>',', 'dec_point'=>'/'),
				'kk-KZ'=>array('thousands_sep'=>' ', 'dec_point'=>'-'),
				'ky-KG'=>array('thousands_sep'=>' ', 'dec_point'=>'-'),
				'et-EE'=>array('thousands_sep'=>' ', 'dec_point'=>'.'),
				);
	}
	
	
}




?>