<?php

define('APPLICATION_ENV', 'cli');

require_once __DIR__ . '/../application.php';
$application->bootstrap();

// Retrieve Doctrine Container resource
$container = Zend_Registry::get('doctrine')->getEntityManager();

// Console
$cli = new \Symfony\Component\Console\Application(
    'Newscoop Command Line Interface',
    \Newscoop\Version::VERSION
);

try {
    // Bootstrapping Console HelperSet
    $helperSet = array();

    if (($em = $container) !== null) {
        $helperSet['container'] = new \Newscoop\Tools\Console\Helper\ServiceContainerHelper($application->getBootstrap()->getResource('container'));
    }
} catch (\Exception $e) {
    $cli->renderException($e, new \Symfony\Component\Console\Output\ConsoleOutput());
}

$cli->setCatchExceptions(true);
$cli->setHelperSet(new \Symfony\Component\Console\Helper\HelperSet($helperSet));

$cli->addCommands(array(
    new \Newscoop\Tools\Console\Command\UpdateFooCommand(),
));

$cli->run();
