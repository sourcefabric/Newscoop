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

$t = new lime_test(25);

class ProjectLoader extends sfServiceContainerLoaderFileYaml
{
  public function getFilesAsArray(array $files)
  {
    return parent::getFilesAsArray($files);
  }
}

// ->getFilesAsArray()
$t->diag('->getFilesAsArray()');

$loader = new ProjectLoader(null, dirname(__FILE__).'/fixtures/ini');

try
{
  $loader->getFilesAsArray(array('foo.yml'));
  $t->fail('->load() throws an InvalidArgumentException if the loaded file does not exist');
}
catch (InvalidArgumentException $e)
{
  $t->pass('->load() throws an InvalidArgumentException if the loaded file does not exist');
}

try
{
  $loader->getFilesAsArray(array('parameters.ini'));
  $t->fail('->load() throws an InvalidArgumentException if the loaded file is not a valid YAML file');
}
catch (InvalidArgumentException $e)
{
  $t->pass('->load() throws an InvalidArgumentException if the loaded file is not a valid YAML file');
}

$loader = new ProjectLoader(null, dirname(__FILE__).'/fixtures/yaml');

foreach (array('nonvalid1', 'nonvalid2') as $fixture)
{
  try
  {
    $loader->getFilesAsArray(array($fixture.'.yml'));
    $t->fail('->load() throws an InvalidArgumentException if the loaded file does not validate');
  }
  catch (InvalidArgumentException $e)
  {
    $t->pass('->load() throws an InvalidArgumentException if the loaded file does not validate');
  }
}

$yamls = $loader->getFilesAsArray(array('services1.yml'));
$t->ok(is_array($yamls), '->getFilesAsArray() returns an array');
$t->is(key($yamls), realpath(dirname(__FILE__).'/fixtures/yaml/services1.yml'), '->getFilesAsArray() returns an array where the keys are absolutes paths to the original YAML file');

// ->load() # parameters
$t->diag('->load() # parameters');
$loader = new ProjectLoader(null, dirname(__FILE__).'/fixtures/yaml');
list($services, $parameters) = $loader->doLoad(array('services2.yml'));
$t->is($parameters, array('foo' => 'bar', 'values' => array(true, false, 0, 1000.3), 'bar' => 'foo', 'foo_bar' => new sfServiceReference('foo_bar')), '->load() converts YAML keys to lowercase');

$loader = new ProjectLoader(null, dirname(__FILE__).'/fixtures/yaml');
list($services, $parameters) = $loader->doLoad(array('services2.yml', 'services3.yml'));
$t->is($parameters, array('foo' => 'foo', 'values' => array(true, false), 'bar' => 'foo', 'foo_bar' => new sfServiceReference('foo_bar')), '->load() merges the first level of arguments when multiple files are loaded');

// ->load() # imports
$t->diag('->load() # imports');
list($services, $parameters) = $loader->doLoad(array('services4.yml'));
$t->is($parameters, array('foo' => 'bar', 'bar' => '%foo%', 'values' => array(true, false), 'foo_bar' => new sfServiceReference('foo_bar')), '->load() imports and merges imported files');

// ->load() # services
$t->diag('->load() # services');
list($services, $parameters) = $loader->doLoad(array('services6.yml'));
$t->ok(isset($services['foo']), '->load() parses service elements');
$t->is(get_class($services['foo']), 'sfServiceDefinition', '->load() converts service element to sfServiceDefinition instances');
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
$t->ok(isset($services['alias_for_foo']), '->load() parses aliases');
$t->is($services['alias_for_foo'], 'foo', '->load() parses aliases');

list($services, $parameters) = $loader->doLoad(array('services6.yml', 'services7.yml'));
$t->is($services['foo']->getClass(), 'BarClass', '->load() merges the services when multiple files are loaded');
