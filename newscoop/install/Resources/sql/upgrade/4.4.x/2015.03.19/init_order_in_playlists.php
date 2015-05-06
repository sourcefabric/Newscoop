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
        'driver'    => 'pdo_mysql',
        'host'      => $Campsite['db']['host'],
        'dbname'    => $Campsite['db']['name'],
        'user'      => $Campsite['db']['user'],
        'password'  => $Campsite['db']['pass'],
        'port'      => $Campsite['db']['port'],
        'charset'   => 'utf8',
    ),
));

$app->register(new DoctrineOrmServiceProvider(), array(
    "orm.proxies_dir" => $newscoopDir."/library/Proxy",
    "orm.auto_generate_proxies" => true,
    "orm.proxies_namespace" => "Proxy",
    "orm.em.options" => array(
        "mappings" => array(
            array(
                "type" => "annotation",
                "namespace" => "Newscoop\Entity",
                "path" => $newscoopDir."/library/Newscoop/Entity",
                "use_simple_annotation_reader" => false,
            ),
            array(
                "type" => "annotation",
                "namespace" => "Newscoop\NewscoopBundle\Entity",
                "path" => $newscoopDir."/src/Newscoop/NewscoopBundle/Entity",
                "use_simple_annotation_reader" => false,
            ),
            array(
                "type" => "annotation",
                "namespace" => "Newscoop\Package",
                "path" => $newscoopDir."/library/Newscoop/Package",
                "use_simple_annotation_reader" => false,
            ),
            array(
                "type" => "annotation",
                "namespace" => "Newscoop\Image",
                "path" => $newscoopDir."/library/Newscoop/Image",
                "use_simple_annotation_reader" => false,
            ),
        ),
    ),
));

$logger = $app['monolog'];

try {
    $playlists = $app['orm.em']->getRepository('\Newscoop\Entity\Playlist')
        ->findAll();

    foreach ($playlists as $playlist) {
        $playlistArticles = $app['orm.em']->getRepository('Newscoop\Entity\Playlist')
            ->articles($playlist, null, true, null, null, false, true, 'id')
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
