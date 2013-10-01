<?php


return array(
    'router' => array(
        'routes' => array(
        		
	            'home' => array( // search engine home page
	                'type' => 'Zend\Mvc\Router\Http\Literal',
	                'options' => array(
	                    'route'    => '/',
	                    'defaults' => array(
	                        'controller' => 'Application\Controller\Index',
	                        'action'     => 'index',
	                    ),
	                ),
	            ),

        ),
    ),
    'service_manager' => array(
        'factories' => array(
            'translator' => 'Zend\I18n\Translator\TranslatorServiceFactory',
        ),
    	'invokables' => array(
    			'Zend\Authentication\AuthenticationService' => 'Zend\Authentication\AuthenticationService',
    	),
    ),
    'translator' => array(
        'locale' => 'en_GB',
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'application-controller-index' => 'Application\Controller\IndexController',
        ),
    ),
	'controller_plugins' => array(
			'invokables' => array(
					'loginPlugin' => 'Hamel\Form\User\Controller\Plugin\Login',
					'checkForLogin' => 'Hamel\Authenticate\Plugin\Checkforlogin',
			),
	),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array( // partial view helpers
            'layout/layout'           => __DIR__ . '/../view/layout/master.phtml',
        	'layoutMasterMainMenu' => __DIR__ . '/../src/Application/View/Helper/Layout/Layout_master_mainMenu.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    		
    ),
		
	'view_helpers' => array(
			'invokables' => array(
					
			),
	),
);
