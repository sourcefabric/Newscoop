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

require_once dirname(__FILE__).'/../lib/yaml/sfYaml.php';

$t = new lime_test(4);

$dir = dirname(__FILE__).'/fixtures/yaml';


// ->dump()
$t->diag('->dump()');
$dumper = new sfServiceContainerDumperYaml($container = new sfServiceContainerBuilder());

$t->is($dumper->dump(), file_get_contents($dir.'/services1.yml'), '->dump() dumps an empty container as an empty YAML file');

$container = new sfServiceContainerBuilder();
$dumper = new sfServiceContainerDumperYaml($container);

// ->addParameters()
$t->diag('->addParameters()');
$container = include dirname(__FILE__).'/fixtures/containers/container8.php';
$dumper = new sfServiceContainerDumperYaml($container);
$t->is($dumper->dump(), file_get_contents($dir.'/services8.yml'), '->dump() dumps parameters');

// ->addService()
$t->diag('->addService()');
$container = include dirname(__FILE__).'/fixtures/containers/container9.php';
$dumper = new sfServiceContainerDumperYaml($container);
$t->is($dumper->dump(), str_replace('%path%', dirname(__FILE__).'/fixtures/includes', file_get_contents($dir.'/services9.yml')), '->dump() dumps services');

$dumper = new sfServiceContainerDumperYaml($container = new sfServiceContainerBuilder());
$container->register('foo', 'FooClass')->addArgument(new stdClass());
try
{
  $dumper->dump();
  $t->fail('->dump() throws a RuntimeException if the container to be dumped has reference to objects or resources');
}
catch (RuntimeException $e)
{
  $t->pass('->dump() throws a RuntimeException if the container to be dumped has reference to objects or resources');
}
