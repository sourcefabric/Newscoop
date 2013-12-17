<?php
/**
 * @package Newscoop
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

if (!file_exists(__DIR__ . '/vendor') && !file_exists(__DIR__.'/vendor/autoload.php')) {
    echo "Welcome in Newscoop Upgrade.<br/><br/>";
    echo "Looks like you forget about our vendors. Please install all dependencies with Composer.";
    echo "<pre>curl -s https://getcomposer.org/installer | php <br/>php composer.phar install --no-dev</pre>";
    echo "After that please refresh that page. Thanks!";
    die;
}

if (!file_exists(__DIR__.'/conf/database_conf.php')) {
    echo "Welcome in Newscoop Upgrade.<br/><br/>";
    echo "Looks like you want upgrade not installed yet Newscoop instance.<br/>";
    echo "Please install Newscoop first and upgrade it with next release!<br/>";
    die;
}

if (!is_writable(__DIR__.'/log')) {
    echo "Welcome in Newscoop Upgrade.<br/><br/>";
    echo "Looks like your log directory isn't writable - please fix it.<br/><br/>";
    echo "In linux systems it can be done with <pre>sudo chmod -R 777 ".__DIR__."/log</pre>";
    echo "After that please refresh that page. Thanks!";
    die;
}


require_once __DIR__.'/vendor/autoload.php';
require_once __DIR__.'/conf/database_conf.php';

use Newscoop\Installer\Services;
use Monolog\Logger;

$app = new Silex\Application();
$app->register(new Silex\Provider\MonologServiceProvider(), array(
    'monolog.logfile' => __DIR__.'/log/upgrade.log',
    'monolog.level' =>Logger::NOTICE,
    'monolog.name' => 'upgrade'
));

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/install/Resources/views',
));
$app->register(new Silex\Provider\TranslationServiceProvider(), array(
    'locale_fallbacks' => array('en'),
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
    )
));

$app['debug'] = true;

$app['upgrade_service'] = $app->share(function () use ($app) {return new Services\UpgradeService($app['db'], $app['monolog']);});

$app->get('/', function (Silex\Application $app) {
    $oldVersions = $app['upgrade_service']->getDBVersion();
    $response = $app['upgrade_service']->upgradeDatabase($oldVersions);
    $newVersions = $app['upgrade_service']->getDBVersion();

    if (is_array($response)) {
        return $app['twig']->render('upgrade/errors.twig', array(
            'errors' => $response, 
            'oldVersions' => $oldVersions,
            'newVersions' => $newVersions,
        ));
    }

    return $app['twig']->render('upgrade/success.twig', array(
        'newVersions' => $newVersions,
        'oldVersions' => $oldVersions,
    ));
    
});

$app->run();
