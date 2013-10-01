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
					'loginForm' => __DIR__ . '/../Hamel/Form/User/View/Helper/LoginForm.phtml',
					'resetPasswordForm' => __DIR__ . '/../Hamel/Form/User/View/Helper/ResetpasswordForm.phtml',
					'tabsHtml' => __DIR__ . '/../Hamel/HTML/Tabs/Tabs.phtml',
			),
			
	),
	
);
