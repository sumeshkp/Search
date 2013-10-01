<?php
return array(
		'controllers' => array(
				'invokables' => array(
						'cms-controller-ajaxrequest' => 'CMS\Controller\RemoteAccess\JsonrequestController',
						'cms-controller-test-add' => 'CMS\Controller\Test\ContentaddController',
						'cms-controller-test-delete' => 'CMS\Controller\Test\ContentdeleteController',
				),
		),
		
		'router' => array(
				'routes' => array(
						'cms-remote' => array( // catches ajax requests from the byblio web app
								'type'    => 'segment',
								'options' => array(
										'route'    => '/cms/remote/[:requestType]',
										'constraints' => array(
												'requestType' => '[a-zA-Z][a-zA-Z_-]*',
										),
										'defaults' => array(
												'controller' => 'cms-controller-ajaxrequest',
												'action'=> 'checkrequest'
										),
								),
						
						),
						
						'cms-test-addcontent' => array( // test page for cms add & update content interaction with the search engine
								'type'    => 'literal',
								'options' => array(
										'route'    => '/cms/test/addcontent',
										'defaults' => array(
												'controller' => 'cms-controller-test-add',
												'action'     => 'home',											
										),
								),
						),
					
						'cms-test-deletecontent' => array( // test page for cms add content interaction with the search engine
								'type'    => 'literal',
								'options' => array(
										'route'    => '/cms/test/deletecontent',
										'defaults' => array(
												'controller' => 'cms-controller-test-delete',
												'action'     => 'home',											
										),
								),
						),
						
				),
				
				
				
				
		),
		
		'view_manager' => array(
				'template_path_stack' => array(
						'cms' => __DIR__ . '/../view',
				),
				'template_map' => array( // partial view helpers
						
						),
		),
);
