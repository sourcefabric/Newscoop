<?php

$newscoopDir = dirname(__FILE__).'/../../../../../../';

require_once $newscoopDir.'vendor/autoload.php';
require $newscoopDir.'conf/database_conf.php';

global $Campsite;

use Monolog\Logger;

$app = new Silex\Application();

$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver' => 'pdo_mysql',
        'host' => $Campsite['db']['host'],
        'dbname' => $Campsite['db']['name'],
        'user' => $Campsite['db']['user'],
        'password' => $Campsite['db']['pass'],
        'port' => $Campsite['db']['port'],
        'charset' => 'utf8',
    ),
));

$app->register(new Silex\Provider\MonologServiceProvider(), array(
    'monolog.logfile' => $newscoopDir.'log/upgrade.log',
    'monolog.level' => Logger::NOTICE,
    'monolog.name' => 'upgrade',
));

$logger = $app['monolog'];

// Fetch all cronjobs
$cronjobs = $app['db']->fetchAll('SELECT id, command, name FROM cron_jobs');
$foundationCommand = $app['db']->fetchColumn('SELECT command FROM cron_jobs WHERE command LIKE "%bin/newscoop-autopublish" ORDER BY id ASC');
$altBasePath = rtrim(str_replace('bin/newscoop-autopublish', '', $foundationCommand), '/').'/';
$currBasePath = realpath($newscoopDir).'/';
$upgradeErrors = array();
$error = false;

foreach ($cronjobs as $job) {
    if (strpos($job['command'], $currBasePath) !== false) {
        $replacePath = $currBasePath;
    } elseif(!empty($altBasePath) && strpos($job['command'], $altBasePath) !== false ) {
        $replacePath = $altBasePath;
    } else {
        $msg = sprintf('Could not determine basepath for "%s".', $job['name']);
        $logger->addError($msg);
        $error = true;
        continue;
    }

    try {
        $app['db']->update('cron_jobs', array(
            'command' => substr_replace($job['command'], '', 0, mb_strlen($replacePath))
        ), array(
            'id' => $job['id']
        ));
    } catch(\Exception $e) {
        $msg = sprintf('There was an error while updating the cronjob "%s".', $job['name']);
        $logger->addError($msg);
        $error = true;
    }
}

if ($error) {
    $msg = "Some cronjobs could not be updated. Please change them manually in "
        ."the database. We've changed from absolute to relative paths with this "
        ."update, so if your newscoop installation is in: /var/www/newscoop, just "
        ."remove this path from the field command in the table cron_jobs for "
        ."each entry. Please make sure you use a leading slash on all values "
        ."in field command.";
    $logger->addError($msg);
    $upgradeErrors[] = $msg;
}
