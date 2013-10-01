<?php

/**
 * Byblio
 * @copyright 2013 Byblio.com
 * @author: Paul A. Oliver
 *
 */


return array(
		'controllers' => array(
				'invokables' => array(
						'useraccount-controller-signup' => 'Useraccount\Controller\SignupController',
						'useraccount-controller-homepage' => 'Useraccount\Controller\HomepageController',
						'useraccount-controller-login' => 'Useraccount\Controller\LoginController',
						'useraccount-controller-logout' => 'Useraccount\Controller\LogoutController',
						'useraccount-controller-forgotpwd' => 'Useraccount\Controller\ForgotpwdController',
						'useraccount-controller-activate' => 'Useraccount\Controller\ActivateController',
						'useraccount-controller-newActivateEmail' => 'Useraccount\Controller\NewactivationemailController',
						'useraccount-controller-accountSettings' => 'Useraccount\Controller\Settings',
						'useraccount-controller-newpassword-activate' => 'Useraccount\Controller\ActivatenewpasswordController',
						'useraccount-controller-authtest' => 'Useraccount\Controller\RemoteAccess\AuthtestController',
						'useraccount-controller-uISettings' => 'Useraccount\Controller\RemoteAccess\UisettingsController',
						'useraccount-controller-ajaxrequest' => 'Useraccount\Controller\RemoteAccess\AjaxrequestController',
				),
		),
		
		'router' => array(
				'routes' => array(
// 						'useraccount-authtest' => array(
// 								'type'    => 'literal',
// 								'options' => array(
// 										'route'    => '/useraccount/authtest',									
// 										'defaults' => array(
// 												'controller' => 'useraccount-controller-authtest',
// 												'action'     => 'getUserInfo',
// 										),
// 								),
// 						),
						'useraccount-remote' => array( // catches ajax requests for useraccount
								'type'    => 'segment',
								'options' => array(
										'route'    => '/useraccount/remote/[:requestType]',
										'constraints' => array(
												'requestType' => '[a-zA-Z][a-zA-Z_-]*',
										),
										'defaults' => array(
												'controller' => 'useraccount-controller-ajaxrequest',
												'action'=> 'checkrequest'
										),
								),
						
						),
						'useraccount-remote-checkVoucher' => array( // remote access
								'type'    => 'literal',
								'options' => array(
										'route'    => '/useraccount/remote/checkvoucher',									
										'defaults' => array(
												'controller' => 'useraccount-controller-checkvoucher',
												'action'     => 'check',
										),
								),
						),
						'useraccount-updateUISettings' => array( // remote access
								'type'    => 'literal',
								'options' => array(
										'route'    => '/useraccount/uisettings/update',									
										'defaults' => array(
												'controller' => 'useraccount-controller-uISettings',
												'action'     => 'update',
										),
								),
						),
						'useraccount-getUISettings' => array( // remote access
								'type'    => 'literal',
								'options' => array(
										'route'    => '/useraccount/uisettings/retrieve',									
										'defaults' => array(
												'controller' => 'useraccount-controller-uISettings',
												'action'     => 'get',
										),
								),
						),
						
						'signup' => array(
								'type'    => 'literal',
								'options' => array(
										'route'    => '/useraccount/signup',									
										'defaults' => array(
												'controller' => 'useraccount-controller-signup',
												'action'     => 'home',
										),
								),
						),
						
						'useraccount-login' => array(
								'type'    => 'literal',
								'options' => array(
										'route'    => '/useraccount/login',									
										'defaults' => array(
												'controller' => 'useraccount-controller-login',
												'action'     => 'home',
										),
								),
						
						),
						'useraccount-logout' => array(
								'type'    => 'literal',
								'options' => array(
										'route'    => '/useraccount/logout',									
										'defaults' => array(
												'controller' => 'useraccount-controller-logout',
												'action'     => 'logout',
										),
								),
						
						),
						
						'useraccount-forgotpassword' => array(
								'type'    => 'literal',
								'options' => array(
										'route'    => '/useraccount/forgotpassword',									
										'defaults' => array(
												'controller' => 'useraccount-controller-forgotpwd',
												'action'     => 'home',
										),
								),
						
						),
						'useraccount-accountSettings' => array(
								'type'    => 'literal',
								'options' => array(
										'route'    => '/useraccount/myaccount',									
										'defaults' => array(
												'controller' => 'useraccount-controller-accountSettings',
												'action'     => 'settings',
										),
								),
						
						),
						'useraccount-resendActivationEmail' => array(
								'type'    => 'literal',
								'options' => array(
										'route'    => '/useraccount/resendactivationemail',									
										'defaults' => array(
												'controller' => 'useraccount-controller-newActivateEmail',
												'action'     => 'home',
										),
								),
						
						),
						
						'useraccount-activate' => array(
								'type'    => 'segment',
								'options' => array(
										'route'    => '/useraccount/activate[/:id1][/:id2][/:id3][/:id4]',	
										'constraints' => array(
												'id1' => '[a-zA-Z0-9_-]+',
												'id2' => '[a-zA-Z0-9_-]+',
												'id3' => '[a-zA-Z0-9_-]+',
												'id4' => '[a-zA-Z0-9_-]+',												
										),
										'defaults' => array(
												'controller' => 'useraccount-controller-activate',
												'action'     => 'home',
										),
								),
						
						),
						'useraccount-newpassword-activate' => array(
								'type'    => 'segment',
								'options' => array(
										'route'    => '/useraccount/resetpassword[/:id1][/:id2][/:id3][/:id4]',	
										'constraints' => array(
												'id1' => '[a-zA-Z0-9_-]+',
												'id2' => '[a-zA-Z0-9_-]+',
												'id3' => '[a-zA-Z0-9_-]+',
												'id4' => '[a-zA-Z0-9_-]+',												
										),
										'defaults' => array(
												'controller' => 'useraccount-controller-newpassword-activate',
												'action'     => 'home',
										),
								),
						
						),
						
				),
		),
		
		'view_manager' => array(
				'template_path_stack' => array(
						'useraccount' => __DIR__ . '/../view',
				),
				'template_map' => array( // partial view helpers
						'dialogueSignupEmailInUse' => __DIR__ . '/../src/Useraccount/View/Helper/Dialogue/Signup/EmailInUse.phtml',
						'dialogueCreatedNewUser' => __DIR__ . '/../src/Useraccount/View/Helper/Dialogue/Signup/Success.phtml',
						'dialogueFailedToCreateNewUser' => __DIR__ . '/../src/Useraccount/View/Helper/Dialogue/Signup/Error.phtml',
						'dialogueAccountNotActive' => __DIR__ . '/../src/Useraccount/View/Helper/Dialogue/Login/AccountNotActive.phtml',
						'dialogueAccountLocked' => __DIR__ . '/../src/Useraccount/View/Helper/Dialogue/Login/AccountLocked.phtml',
						'dialogueNAAE_emailNotInUse' => __DIR__ . '/../src/Useraccount/View/Helper/Dialogue/ResendAccountActivationEmail/EmailNotInUse.phtml',
						'dialogueNAAE_emailSent' => __DIR__ . '/../src/Useraccount/View/Helper/Dialogue/ResendAccountActivationEmail/EmailSent.phtml',
						'dialogueNAAE_acAlreadyActive' => __DIR__ . '/../src/Useraccount/View/Helper/Dialogue/ResendAccountActivationEmail/AcAlreadyActive.phtml',
						'dialogueRP_emailNotInUse' => __DIR__ . '/../src/Useraccount/View/Helper/Dialogue/ResetPassword/EmailNotInUse.phtml',
						'dialogueRP_emailSent' => __DIR__ . '/../src/Useraccount/View/Helper/Dialogue/ResetPassword/EmailSent.phtml',
						
						'userHomePage' => __DIR__ . '/../src/Useraccount/View/Helper/Homepage/Userhomepage.phtml',
						'useraccount-accountPricing' => __DIR__ . '/../src/Useraccount/View/Helper/Signup/AccountPricing.phtml',
						'useraccount-accountOptions' => __DIR__ . '/../src/Useraccount/View/Helper/Signup/AccountOptions.phtml',

				
				),
		),
		
		'service_manager' => array(
				'invokables' => array(
						'tabsContentUserAccount' => 'Useraccount\Model\TabsContent',
				),
		),
		
		'view_helpers' => array(
				
		),
);
