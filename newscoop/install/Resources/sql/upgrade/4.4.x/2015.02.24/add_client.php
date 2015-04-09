<?php

$newscoopDir = realpath(dirname(__FILE__).'/../../../../../../');

require_once $newscoopDir.'/vendor/autoload.php';
require $newscoopDir.'/conf/database_conf.php';

use Monolog\Logger;
use Newscoop\Installer\Services;
use Newscoop\NewscoopBundle\Services\SystemPreferencesService;
use Dflydev\Silex\Provider\DoctrineOrm\DoctrineOrmServiceProvider;
use Newscoop\GimmeBundle\Entity\Client;
use FOS\OAuthServerBundle\Util\Random;

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
                "namespace" => "Newscoop\GimmeBundle\Entity",
                "path" => $newscoopDir."/src/Newscoop/GimmeBundle/Entity",
                "use_simple_annotation_reader" => false,
                ),
            ),
        ),
    ));

$app['upgrade_service'] = $app->share(function () use ($app) {
    return new Services\UpgradeService($app['db'], $app['monolog']);
});

$app['preferences'] = $app->share(function () use ($app) {
    return new SystemPreferencesService($app['orm.em']);
});

$defaultClientName = 'newscoop_'.$app['preferences']->SiteSecretKey;
$client = $app['orm.em']->getRepository('Newscoop\GimmeBundle\Entity\Client')->findOneByName($defaultClientName);
if ($client) {
    return;
}

$logger = $app['monolog'];

try {
    $alias = $app['upgrade_service']->getDefaultAlias();
    if (!$alias) {
        $msg = "Could not find default alias! Aborting...";
        $upgradeErrors[] = $msg;
        $logger->addError($msg);
    } else {
        $publication = $app['orm.em']->getRepository('\Newscoop\Entity\Aliases')
            ->findOneByName($alias)
            ->getPublication();

        $conn = $app['orm.em']->getConnection();
        $stmt = $conn->prepare('INSERT INTO OAuthClient(random_id, redirect_uris, secret, allowed_grant_types, name, IdPublication, trusted)
        	VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->bindValue(1, Random::generateToken());
        $stmt->bindValue(2, serialize(array($alias)));
        $stmt->bindValue(3, Random::generateToken());
        $stmt->bindValue(4, serialize(array('token', 'authorization_code', 'client_credentials', 'password')));
        $stmt->bindValue(5, $defaultClientName);
        $stmt->bindValue(6, $publication->getId());
        $stmt->bindValue(7, true);
        $stmt->execute();
    }
} catch (\Exception $e) {
    $msg = $e->getMessage();
    $upgradeErrors[] = $msg;
    $logger->addError($msg);
    array_splice($upgradeErrors, 0, 0, array($msg));
}
