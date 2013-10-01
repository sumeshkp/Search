<?php

/**
 * Byblio
 * Tabs content for useraccount pages
 * Referenced as factory in service mamager
 * 
 * @copyright 2013 Byblio.com
 * @author: Paul A. Oliver
 *
 */

namespace Useraccount\Model;
use Zend\ModuleManager\Feature\ServiceProviderInterface;

class TabsContent {
	
	public function __invoke(){
		
	}
	
/*  NOTE:
    tab id (e.g. pageMenutab-0) must end in '-x' (minus number) where x is the tab number
    tab num must start with 0 and go sequentially
    tab id only have a single'-'
*/
    		
	// returns core menu
	function getTabsInfo($page, $view){
		
		
		// default
		$menuList = array();
		$menuClass = "";
		$tabsType = "";
		
		switch($page){
			case 'signup-page': // page tabs for the sign up page, not logged in
				$menuList = array(
							array('text'=> $view->translate('Welcome & sign up'), 'href' => '#tab-0', 'id'=>'pageTab-0', 'linkType'=>'pagetab'),
							array('text'=> $view->translate('Account goodies & options '), 'href' => '#tab-1', 'id'=>'pageTab-1', 'linkType'=>'pagetab'),
				);
				
				// info
				$tabsType = 'page';
			    $menuClass = 'pageMenu_pageTabs';
				
			break;
			
			case 'signup-page-loggedin': // page tabs for the sign up page, user logged in
				$menuList = array(
							array('text'=> $view->translate('New account'), 'href' => '#tab-0', 'id'=>'pageTab-0', 'linkType'=>'pagetab'),
							array('text'=> $view->translate('Account goodies & options '), 'href' => '#tab-1', 'id'=>'pageTab-1', 'linkType'=>'pagetab'),
				);
				
				// info
				$tabsType = 'page';
			    $menuClass = 'pageMenu_pageTabs';
				
			break;
			
			
		}
		
		// return menu
		return array('menuList'=> $menuList, 'menuClass'=>$menuClass, 'tabsType'=>$tabsType);
	}
	
	
}