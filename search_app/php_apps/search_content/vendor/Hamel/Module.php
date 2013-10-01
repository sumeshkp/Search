<?php
// Hamel library

namespace Hamel;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;

class Module implements AutoloaderProviderInterface{
	
	public function getAutoloaderConfig(){
		return array(
				'Zend\Loader\ClassMapAutoloader' => array(
						__DIR__ . '/autoload_classmap.php',
				),
				'Zend\Loader\StandardAutoloader' => array(
						'namespaces' => array(
								__NAMESPACE__ => __DIR__ . '/library/' . __NAMESPACE__,
						),
				),
		);
	}
	
	// load config for this library
	public function getConfig()
	{
		return include __DIR__ . '/library/config/module.config.php';
	}
	
}