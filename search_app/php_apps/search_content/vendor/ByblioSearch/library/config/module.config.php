<?php
/**
 * Byblio
 * Byblio vendor library configuration
 * @copyright 2013 Byblio.com
 * @author: Paul A. Oliver
 *
 */

return array(
		
	
 	
	'view_helpers' => array(
			'invokables' => array(
					
			),
	),
		
	'view_manager' => array(
			'template_map' => array( // partial view helpers
					'contentSearchForm' => __DIR__ . '/../ByblioSearch/Form/WebApp/View/Helper/Search.phtml',
					'contentAddForm' => __DIR__ . '/../ByblioSearch/Form/CMS/View/Helper/AddContent.phtml',
					'contentDeleteForm' => __DIR__ . '/../ByblioSearch/Form/CMS/View/Helper/DeleteContent.phtml',
			),
			
	),
	
);
