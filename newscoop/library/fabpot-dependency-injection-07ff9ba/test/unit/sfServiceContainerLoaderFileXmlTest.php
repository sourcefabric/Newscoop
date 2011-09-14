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

$t = new lime_test(34);

class ProjectLoader extends sfServiceContainerLoaderFileXml
{
  public function getFilesAsXml(array $files)
  {
    return parent::getFilesAsXml($files);
  }
}

// ->getFilesAsXml()
$t->diag('->getFilesAsXml()');

$loader = new ProjectLoader(null, dirname(__FILE__).'/fixtures/ini');

try
{
  $loader->getFilesAsXml(array('foo.xml'));
  $t->fail('->load() throws an InvalidArgumentException if the loaded file does not exist');
}
catch (InvalidArgumentException $e)
{
  $t->pass('->load() throws an InvalidArgumentException if the loaded file does not exist');
}

try
{
  $loader->getFilesAsXml(array('parameters.ini'));
  $t->fail('->load() throws an InvalidArgumentException if the loaded file is not a valid XML file');
}
catch (InvalidArgumentException $e)
{
  $t->pass('->load() throws an InvalidArgumentException if the loaded file is not a valid XML file');
}

$loader = new ProjectLoader(null, dirname(__FILE__).'/fixtures/xml');

try
{
  $loader->getFilesAsXml(array('nonvalid.xml'));
  $t->fail('->load() throws an InvalidArgumentException if the loaded file does not validate the XSD');
}
catch (InvalidArgumentException $e)
{
  $t->pass('->load() throws an InvalidArgumentException if the loaded file does not validate the XSD');
}

$xmls = $loader->getFilesAsXml(array('services1.xml'));
$t->is(count($xmls), 1, '->getFilesAsXml() returns an array of simple xml objects');
$t->is(key($xmls), realpath(dirname(__FILE__).'/fixtures/xml/services1.xml'), '->getFilesAsXml() returns an array where the keys are absolutes paths to the original XML file');
$t->is(get_class(current($xmls)), 'sfServiceSimpleXMLElement', '->getFilesAsXml() returns an array where values are sfServiceSimpleXMLElement objects');

// ->load() # parameters
$t->diag('->load() # parameters');
$loader = new ProjectLoader(null, dirname(__FILE__).'/fixtures/xml');
list($services, $parameters) = $loader->doLoad(array('services2.xml'));
$t->is($parameters, array('a string', 'foo' => 'bar', 'values' => array(0, 'integer' => 4, 100 => null, 'true', true, false, true, false, 'float' => 1.3, 1000.3, 'a string', array('foo', 'bar')), 'foo_bar' => new sfServiceReference('foo_bar')), '->load() converts XML values to PHP ones');

$loader = new ProjectLoader(null, dirname(__FILE__).'/fixtures/xml');
list($services, $parameters) = $loader->doLoad(array('services2.xml', 'services3.xml'));
$t->is($parameters, array('a string', 'foo' => 'foo', 'values' => array(true, false), 'foo_bar' => new sfServiceReference('foo_bar')), '->load() merges the first level of arguments when multiple files are loaded');

// ->load() # imports
$t->diag('->load() # imports');
list($services, $parameters) = $loader->doLoad(array('services4.xml'));
$t->is($parameters, array('a string', 'foo' => 'bar', 'bar' => '%foo%', 'values' => array(true, false), 'foo_bar' => new sfServiceReference('foo_bar')), '->load() imports and merges imported files');

// ->load() # anonymous services
$t->diag('->load() # anonymous services');
list($services, $parameters) = $loader->doLoad(array('services5.xml'));
$t->is(count($services), 3, '->load() attributes unique ids to anonymous services');
$args = $services['foo']->getArguments();
$t->is(count($args), 1, '->load() references anonymous services as "normal" ones');
$t->is(get_class($args[0]), 'sfServiceReference', '->load() converts anonymous services to references to "normal" services');
$t->ok(isset($services[(string) $args[0]]), '->load() makes a reference to the created ones');
$inner = $services[(string) $args[0]];
$t->is($inner->getClass(), 'BarClass', '->load() uses the same configuration as for the anonymous ones');

$args = $inner->getArguments();
$t->is(count($args), 1, '->load() references anonymous services as "normal" ones');
$t->is(get_class($args[0]), 'sfServiceReference', '->load() converts anonymous services to references to "normal" services');
$t->ok(isset($services[(string) $args[0]]), '->load() makes a reference to the created ones');
$inner = $services[(string) $args[0]];
$t->is($inner->getClass(), 'BazClass', '->load() uses the same configuration as for the anonymous ones');

// ->load() # services
$t->diag('->load() # services');
list($services, $parameters) = $loader->doLoad(array('services6.xml'));
$t->ok(isset($services['foo']), '->load() parses <service> elements');
$t->is(get_class($services['foo']), 'sfServiceDefinition', '->load() converts <service> element to sfServiceDefinition instances');
$t->is($services['foo']->getClass(), 'FooClass', '->load() parses the class attribute');
$t->ok($services['shared']->isShared(), '->load() parses the shared attribute');
$t->ok(!$services['non_shared']->isShared(), '->load() parses the shared attribute');
$t->is($services['constructor']->getConstructor(), 'getInstance', '->load() parses the constructor attribute');
$t->is($services['file']->getFile(), '%path%/foo.php', '->load() parses the file tag');
$t->is($services['arguments']->getArguments(), array('foo', new sfServiceReference('foo'), array(true, false)), '->load() parses the argument tags');
$t->is($services['configurator1']->getConfigurator(), 'sc_configure', '->load() parses the configurator tag');
$t->is($services['configurator2']->getConfigurator(), array(new sfServiceReference('baz'), 'configure'), '->load() parses the configurator tag');
$t->is($services['configurator3']->getConfigurator(), array('BazClass', 'configureStatic'), '->load() parses the configurator tag');
$t->is($services['method_call1']->getMethodCalls(), array(array('setBar', array())), '->load() parses the method_call tag');
$t->is($services['method_call2']->getMethodCalls(), array(array('setBar', array('foo', new sfServiceReference('foo'), array(true, false)))), '->load() parses the method_call tag');
$t->ok(isset($services['alias_for_foo']), '->load() parses <service> elements');
$t->is($services['alias_for_foo'], 'foo', '->load() parses aliases');

list($services, $parameters) = $loader->doLoad(array('services6.xml', 'services7.xml'));
$t->is($services['foo']->getClass(), 'BarClass', '->load() merges the services when multiple files are loaded');
