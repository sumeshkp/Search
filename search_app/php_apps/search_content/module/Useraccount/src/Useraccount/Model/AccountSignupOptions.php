<?php
/**
 * Byblio new user account functions
 * @copyright 2013 Byblio.com
 * @author: Paul A. Oliver
 *
 */

namespace Useraccount\Model;



use Hamel\Billingmanagement\Useraccount as BillingUseraccount;
use Hamel\Billingmanagement\General as BillingGeneral;
use Hamel\AccountManagement\Options as AccountOptions;

class AccountSignupOptions {
	

	
    public function __construct(){
    	
    }
    
    // returns an array of information about the account options for sign up
   public function getAccountSignupOptions($translate, $countryInfo, $showAdminAccount){
        
    	// return info
   		$returnInfo = array();
   		$accountOptions = array();
   	
   		// browser country and region
   		$browserCountry = $countryInfo['country'];
   		$browserRegion = $countryInfo['region'];
   		
    	// get options for each account type
    	$billingUseraccount = new BillingUseraccount();
    	$acBillingOptions = $billingUseraccount->getAccountOptions();
    	
    	// get curency info
    	$billingGeneral = new BillingGeneral($translate);
    	$allCurrencyInfo = $billingGeneral->getAllCurrencyBySymbol();
    	
    	
    	
    	// for each account type
    	foreach($acBillingOptions as $accountType => $billingInfo){
    		
    		if(!$showAdminAccount && $accountType != 'hamel_admin'){
    			
    		
	    		// for each billing option
	    		foreach($billingInfo as $billingOption){
		    		// info
		    		$option = $billingOption['option'];
		    		$pricing = $billingOption['pricing'];
		    		$countryOption = $billingOption['country'];
		    		$currency = $billingOption['currency'];
		    		
		    		// if the country/ region of this option is valid for the current browser country or region
		    		if($countryOption == 'ALL' || $countryOption = $browserRegion || $countryOption = $browserCountry){
		    			
		    			// ensure ac type in return info
		    			if(!key_exists($accountType, $accountOptions)){
		    				$accountOptions[$accountType] = array();
		    			}
		
		    			// get currency info
		    			if(key_exists($currency, $allCurrencyInfo)){
		    				$currencyInfo =  $allCurrencyInfo[$currency];
		    			} else {
		    				$currencyInfo = array();
		    			}
		    			
		    			// record option
		    			$accountOptions[$accountType][$option] = array('pricing'=>$pricing, 'currencyInfo'=>$currencyInfo);
		    			
		    		}
	    		}
    		}
		
    	}
    	// record account options
    	$returnInfo['acOptions'] = $accountOptions;
    	
 
    	// return info
    	return $returnInfo;
    	
    
    	
   }
    
}





