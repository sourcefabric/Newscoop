#!/usr/bin/env php
<?php
/**
 * @package Newscoop
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @author Petr Jasek <petr.jasek@sourcefabric.org>
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

define('APPLICATION_ENV', 'cli');

require_once __DIR__ . '/../application.php';
require_once __DIR__ . '/../bin/newscoop_bootstrap.php';
require_once __DIR__ . '/../bin/cli_script_lib.php';
$application->bootstrap();

// Console
$cli = new \Symfony\Component\Console\Application(
    'Newscoop Command Line Interface',
    \Newscoop\Version::VERSION
);

// Bootstrapping Console HelperSet
$helperSet = array();

try {
    if (Zend_Registry::isRegistered('container')) {
        $container = $application->getBootstrap()->getResource('container');
        $helperSet['container'] = new \Newscoop\Tools\Console\Helper\ServiceContainerHelper($container);
        $helperSet['db'] = new \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper($container->getService('doctrine.connection'));
        $helperSet['em'] = new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper($container->getService('em'));
        $helperSet['dialog'] = new \Symfony\Component\Console\Helper\DialogHelper();
    }
} catch (\Exception $e) {
    $cli->renderException($e, new \Symfony\Component\Console\Output\ConsoleOutput());
}

$cli->setCatchExceptions(true);
$cli->setHelperSet(new \Symfony\Component\Console\Helper\HelperSet($helperSet));

// Register all Doctrine commands
\Doctrine\ORM\Tools\Console\ConsoleRunner::addCommands($cli);

$cli->addCommands(array(
    new \Newscoop\Tools\Console\Command\UpdateIngestCommand(),
    new \Newscoop\Tools\Console\Command\LogMaintenanceCommand(),
    new \Newscoop\Tools\Console\Command\SendStatsCommand(),
    new \Newscoop\Tools\Console\Command\UpdateImageStorageCommand(),
    new \Newscoop\Tools\Console\Command\CacheClearCommand(),
    new \Newscoop\Tools\Console\Command\VendorsUpdateCommand(),
    new \Newscoop\Tools\Console\Command\UpdateNewscoopCommand(),

    // Migrations Commands
    new \Doctrine\DBAL\Migrations\Tools\Console\Command\DiffCommand(),
    new \Doctrine\DBAL\Migrations\Tools\Console\Command\ExecuteCommand(),
    new \Doctrine\DBAL\Migrations\Tools\Console\Command\GenerateCommand(),
    new \Doctrine\DBAL\Migrations\Tools\Console\Command\MigrateCommand(),
    new \Doctrine\DBAL\Migrations\Tools\Console\Command\StatusCommand(),
    new \Doctrine\DBAL\Migrations\Tools\Console\Command\VersionCommand()
));

$cli->run();
