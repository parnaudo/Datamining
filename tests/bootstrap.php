<?php

# Set the default date timezone to stop PHP nagging us!
date_default_timezone_set('Europe/London');

# Always display errors unless Zephyr tell me otherwise.
ini_set('display_errors', 'on');

# Define the application path
defined('LIBRARY_PATH') || define('LIBRARY_PATH', realpath(dirname(__FILE__) . '/library/'));
defined('TEST_PATH') || define('TEST_PATH', realpath(dirname(__FILE__) . '/tests/'));

# Setup the include path for autoloading.
set_include_path(implode(PATH_SEPARATOR, array(
		realpath(LIBRARY_PATH),
		get_include_path(),
)));

# Require the autoloader
require_once 'Zend/Loader/Autoloader.php';

# Initialise the autoloader and register appropriate namespaces 
Zend_Loader_Autoloader::getInstance()->pushAutoloader(function($file) { $file = str_replace('_', DIRECTORY_SEPARATOR, $file); require_once($file . '.php'); }, 'Zephyr');