<?php
/**
 * Byblio
 * Search engine home page contoller
 * 
 * @copyright 2013 Byblio.com
 * @author: Paul A. Oliver
 *
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;


class IndexController extends AbstractActionController
{
	// set application-wide variables
    public function indexAction(){
    	
    	// logo
    	$headerLogo = 'yellow';
    	
    	// translator
    	$translate = $this->getServiceLocator()->get('viewhelpermanager')->get('translate');
    	
    	// browser title
    	$browserTitle = $translate('Byblio search engine');
    	
    	
    	$rtn = new ViewModel(array(
    			'headerLogo' => $headerLogo,
    			'browserTitle' => $browserTitle,
    	));
    	
    	return $rtn;
    	
    }
    
}
