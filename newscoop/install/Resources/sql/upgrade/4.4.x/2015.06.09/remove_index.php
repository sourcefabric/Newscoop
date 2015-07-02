<?php

$newscoopDir = realpath(dirname(__FILE__).'/../../../../../../').'/';

require_once $newscoopDir.'vendor/autoload.php';
require $newscoopDir.'conf/database_conf.php';

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

try {
    $app['db']->query('ALTER TABLE playlist_article DROP INDEX playlist_article');
} catch (\Exception $e) {
    // ignore when index doesn't exist
}
