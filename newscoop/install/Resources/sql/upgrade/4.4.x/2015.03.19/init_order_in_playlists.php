<?php

$newscoopDir = realpath(dirname(__FILE__).'/../../../../../../');

require $newscoopDir.'/conf/database_conf.php';
$loader = require $newscoopDir.'/vendor/autoload.php';
\Doctrine\Common\Annotations\AnnotationRegistry::registerLoader(array($loader, 'loadClass'));

use Monolog\Logger;
use Dflydev\Silex\Provider\DoctrineOrm\DoctrineOrmServiceProvider;

$upgradeErrors = array();
$app = new Silex\Application();
$app->register(new Silex\Provider\MonologServiceProvider(), array(
    'monolog.logfile' => $newscoopDir.'/log/upgrade.log',
    'monolog.level' => Logger::NOTICE,
    'monolog.name' => 'upgrade',
));

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

$app->register(new DoctrineOrmServiceProvider(), array(
    'orm.proxies_dir' => $newscoopDir.'/library/Proxy',
    'orm.auto_generate_proxies' => true,
    'orm.proxies_namespace' => 'Proxy',
    'orm.em.options' => array(
        'mappings' => array(
            array(
                'type' => 'annotation',
                'namespace' => "Newscoop\Entity",
                'path' => $newscoopDir.'/library/Newscoop/Entity',
                'use_simple_annotation_reader' => false,
            ),
            array(
                'type' => 'annotation',
                'namespace' => "Newscoop\NewscoopBundle\Entity",
                'path' => $newscoopDir.'/src/Newscoop/NewscoopBundle/Entity',
                'use_simple_annotation_reader' => false,
            ),
            array(
                'type' => 'annotation',
                'namespace' => "Newscoop\Package",
                'path' => $newscoopDir.'/library/Newscoop/Package',
                'use_simple_annotation_reader' => false,
            ),
            array(
                'type' => 'annotation',
                'namespace' => "Newscoop\Image",
                'path' => $newscoopDir.'/library/Newscoop/Image',
                'use_simple_annotation_reader' => false,
            ),
        ),
        'types' => array(
            'utcdatetime' => 'Newscoop\NewscoopBundle\ORM\UTCDateTimeType',
        ),
    ),
));

$logger = $app['monolog'];

try {
    $app['db']->query('ALTER TABLE playlist_article DROP INDEX id_playlist');
    $app['db']->query('ALTER TABLE `playlist_article` ADD KEY `IDX_BD05197C8759FDB8` (`id_playlist`), ADD KEY `IDX_BD05197CAA07C9D3813385DE` (`article_no`,`article_language`)');
    $app['db']->query('UPDATE `playlist_article` AS pa LEFT JOIN Articles AS a ON pa.`article_no` = a.`Number` SET pa.`article_language` = a.`IdLanguage`');
} catch (\Exception $e) {
    // ignore when already exist
}

try {
    $playlists = $app['orm.em']->getRepository('\Newscoop\Entity\Playlist')
        ->findAll();

    foreach ($playlists as $playlist) {
        $playlistArticles = $app['orm.em']->getRepository('Newscoop\Entity\Playlist')
            ->articles($playlist, array(), true, null, null, false, true, 'id')
            ->getResult();

        $index = 0;
        foreach ($playlistArticles as $article) {
            if ($article instanceof \Newscoop\Entity\PlaylistArticle) {
                $index++;
                $article->setOrder($index);
            }
        }
        $app['orm.em']->flush();
    }
} catch (\Exception $e) {
    $msg = $e->getMessage();
    $upgradeErrors[] = $msg;
    $logger->addError($msg);
    array_splice($upgradeErrors, 0, 0, array($msg));
}
