<?php

$newscoopDir = realpath(dirname(__FILE__).'/../../../../../../');

require_once $newscoopDir.'/vendor/autoload.php';
require $newscoopDir.'/conf/database_conf.php';

use Monolog\Logger;
use Newscoop\Installer\Services;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\PhpExecutableFinder;

$upgradeErrors = array();
$app = new Silex\Application();
$app->register(new Silex\Provider\MonologServiceProvider(), array(
    'monolog.logfile' => $newscoopDir.'/log/upgrade.log',
    'monolog.level' => Logger::NOTICE,
    'monolog.name' => 'upgrade',
));

$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver'    => 'pdo_mysql',
        'host'      => $Campsite['db']['host'],
        'dbname'    => $Campsite['db']['name'],
        'user'      => $Campsite['db']['user'],
        'password'  => $Campsite['db']['pass'],
        'port'      => $Campsite['db']['port'],
        'charset'   => 'utf8',
    ),
));

$app['upgrade_service'] = $app->share(function () use ($app) {
    return new Services\UpgradeService($app['db'], $app['monolog']);
});

$logger = $app['monolog'];

$newscoopConsole = escapeshellarg($newscoopDir.'/application/console');
$phpFinder = new PhpExecutableFinder();
$phpPath = $phpFinder->find();
if (!$phpPath) {
    throw new \RuntimeException('The php executable could not be found, add it to your PATH environment variable and try again');
}

try {
    $alias = $app['upgrade_service']->getDefaultAlias();
    if (!$alias) {
        $msg = "Could not find default alias! Aborting...";
        $upgradeErrors[] = $msg;
        $logger->addError($msg);
    } else {
        $php = escapeshellarg($phpPath);
        $process = new Process("$php $newscoopConsole oauth:create-client newscoop ".$alias." ".$alias." --default");
        $process->run();
        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }
    }
} catch (\Exception $e) {
    $msg = $e->getMessage();
    $upgradeErrors[] = $msg;
    $logger->addError($msg);
    array_splice($upgradeErrors, 0, 0, array($msg));
}
