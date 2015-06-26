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
    $app['db']->query('ALTER TABLE playlist_article DROP INDEX id_playlist');
    $app['db']->query('ALTER TABLE `playlist_article` ADD `article_language` INT(11) NOT NULL');
    $app['db']->query('ALTER TABLE `playlist_article` ADD KEY `IDX_BD05197C8759FDB8` (`id_playlist`), ADD KEY `IDX_BD05197CAA07C9D3813385DE` (`article_no`,`article_language`)');
    $app['db']->query('UPDATE `playlist_article` AS pa LEFT JOIN Articles AS a ON pa.`article_no` = a.`Number` SET pa.`article_language` = a.`IdLanguage`');
} catch (\Exception $e) {
    // ignore when already exist
}
