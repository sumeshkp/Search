<?php

/**
 * Byblio
 * Search engine landing page
 * index.php
 * @copyright 2013 Byblio.com
 * @author: Paul A. Oliver
 *
 */


/**
 * Define the path to the application
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */


// set dir to browser app
$appDir = realpath(dirname(__DIR__) . '/php_apps/search_content');

// go to app
chdir($appDir);



// Setup autoloading
require 'init_autoloader.php';

// Run the application!
Zend\Mvc\Application::init(require 'config/application.config.php')->run();
