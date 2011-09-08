<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once dirname(__FILE__).'/../lib/lime/lime.php';
require_once dirname(__FILE__).'/../../lib/sfServiceContainerAutoloader.php';
sfServiceContainerAutoloader::register();

$t = new lime_test(3);

// ->autoload()
$t->diag('->autoload()');

$t->ok(!class_exists('Foo'), '->autoload() does not try to load classes that does not begin with sfService');

$autoloader = new sfServiceContainerAutoloader();
$t->is($autoloader->autoload('sfServiceContainer'), true, '->autoload() returns true if it is able to load a class');
$t->is($autoloader->autoload('Foo'), false, '->autoload() returns false if it is not able to load a class');
