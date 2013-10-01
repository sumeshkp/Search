<?php
return array(
		'controllers' => array(
				'invokables' => array(
						'webApp-controller-ajaxrequest' => 'Webapp\Controller\RemoteAccess\JsonrequestController',
						'webApp-controller-test-search' => 'Webapp\Controller\Test\ContentsearchController',
				),
		),
		
		'router' => array(
				'routes' => array(
						'webApp-remote' => array( // catches ajax requests from the byblio web app
								'type'    => 'segment',
								'options' => array(
										'route'    => '/webapp/remote/[:requestType]',
										'constraints' => array(
												'requestType' => '[a-zA-Z][a-zA-Z_-]*',
										),
										'defaults' => array(
												'controller' => 'webApp-controller-ajaxrequest',
												'action'=> 'checkrequest'
										),
								),
						
						),
						
						'webApp-test-search' => array( // test page for web app interaction with the search engine
								'type'    => 'literal',
								'options' => array(
										'route'    => '/webapp/test/contentsearch',
										'defaults' => array(
												'controller' => 'webApp-controller-test-search',
												'action'     => 'home',											
										),
								),
						),
						
				),
				
				
				
				
		),
		
		'view_manager' => array(
				'template_path_stack' => array(
						'webapp' => __DIR__ . '/../view',
				),
				'template_map' => array( // partial view helpers
						
						),
		),
);
