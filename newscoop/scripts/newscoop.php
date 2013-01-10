<?php

use Newscoop\Tools\Console\Command;

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

$cli->setCatchExceptions(false);
$cli->setHelperSet(new \Symfony\Component\Console\Helper\HelperSet($helperSet));

$cli->addCommands(array(
    new \Newscoop\Tools\Console\Command\UpdateIngestCommand(),
    new \Newscoop\Tools\Console\Command\LogMaintenanceCommand(),
    new \Newscoop\Tools\Console\Command\SendStatsCommand(),
    new \Newscoop\Tools\Console\Command\UpdateImageStorageCommand(),
    new \Newscoop\Tools\Console\Command\UpdateAutoloadCommand(),
    new \Newscoop\Tools\Console\Command\UpdateIndexCommand(),
    new \Newscoop\Tools\Console\Command\ResetIndexCommand(),
    new \Newscoop\Tools\Console\Command\UpdateWeatherCommand(),
));

$cli->run();
