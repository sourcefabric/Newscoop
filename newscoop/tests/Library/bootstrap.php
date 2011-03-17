<?php

require_once 'Zend/Loader/Autoloader.php';

// add library to include path
set_include_path(realpath(dirname(__FILE__) . '/../../library/') . PATH_SEPARATOR . get_include_path());

$autoloader = Zend_Loader_Autoloader::getInstance();

$autoloader->setFallbackAutoloader(TRUE);

