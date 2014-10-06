<?php

$newscoopDir = realpath(dirname(__FILE__).'/../../../../../../').'/';

require_once $newscoopDir.'vendor/autoload.php';
require $newscoopDir.'conf/database_conf.php';

use Monolog\Logger;

$upgradeErrors = array();
$app = new Silex\Application();
$app->register(new Silex\Provider\MonologServiceProvider(), array(
    'monolog.logfile' => $newscoopDir.'log/upgrade.log',
    'monolog.level' => Logger::NOTICE,
    'monolog.name' => 'upgrade'
));

$logger = $app['monolog'];

$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver'    => 'pdo_mysql',
        'host'      => $Campsite['db']['host'],
        'dbname'    => $Campsite['db']['name'],
        'user'      => $Campsite['db']['user'],
        'password'  => $Campsite['db']['pass'],
        'port'      => $Campsite['db']['port'],
        'charset'   => 'utf8',
    )
));

try {
    $app['db']->query('ALTER TABLE `Plugins` DROP PRIMARY KEY');
} catch (\Exception $e) {
    if ($app['db']->errorCode() !== '42000') {
        $upgradeErrors[] = $e->getMessage();
        $logger->addError($e->getMessage());
    }
}

try {
    $app['db']->query('ALTER TABLE `Plugins` ADD `Id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST, ADD `Description` TEXT NOT NULL');
} catch (\Exception $e) {
    if ($app['db']->errorCode() !== '42S21' || $app['db']->errorCode() !== '42000') {
        $upgradeErrors[] = $e->getMessage();
        $logger->addError($e->getMessage());
    }
}
