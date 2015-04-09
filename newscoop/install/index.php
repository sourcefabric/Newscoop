<?php
/**
 * @package Newscoop
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

if (!file_exists(__DIR__ . '/../vendor') && !file_exists(__DIR__.'/../vendor/autoload.php')) {
    echo "Welcome to Newscoop Installer!<br/><br/>";
    echo "It doesn't look like you've installed vendors yet. Please install all dependencies using Composer.";
    echo "<pre>curl -s https://getcomposer.org/installer | php <br/>php composer.phar install --no-dev</pre>";
    echo "When it's done, please refresh this page. Thanks!";
    die;
}

require_once __DIR__.'/../vendor/autoload.php';
require_once dirname(__FILE__).'/SymfonyRequirements.php';

$symfonyRequirements = new SymfonyRequirements();
$requirements = $symfonyRequirements->getRequirements();

$missingReq = array();
foreach ($requirements as $req) {
    if (!$req->isFulfilled()) {
        $missingReq[] = $req->getTestMessage().'<br />      '.$req->getHelpText().'<br />';
    }
}

if (count($missingReq) > 0) {
    echo "Welcome to Newscoop Installer!<br/><br/>";
    echo "Before we will show You a real installer we need to fix some requirements first.<br />Please read all messages and try to fix them:<br />";
    echo "<pre>";
    foreach ($missingReq as $value) {
        echo $value.' <br />';
    }
    echo "</pre>";
    $phpFile = php_ini_loaded_file() ?: "File couldn't be found.";
    echo "Your php.ini config file path: <strong>" . $phpFile . "</strong><br/><br/>";
    echo "You can try to fix common problems by running our fixer.php script: <br/>";
    echo "<pre>sudo php ". realpath(__DIR__."/../scripts/fixer.php")."</pre>";

    echo "When it's done, please refresh this page. Thanks!";
    die;
}

use Silex\Provider\FormServiceProvider;
use Symfony\Component\HttpFoundation\Request;
use Newscoop\Installer\Services;
use Symfony\Component\Validator\Constraints as Assert;
use Dflydev\Silex\Provider\DoctrineOrm\DoctrineOrmServiceProvider;

$app = new Silex\Application();
$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new Silex\Provider\ValidatorServiceProvider());
$app->register(new FormServiceProvider());
$app->register(new Silex\Provider\MonologServiceProvider(), array(
    'monolog.logfile' => __DIR__.'/../log/install.log',
));
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/Resources/views',
));
$app->register(new Silex\Provider\TranslationServiceProvider(), array(
    'locale_fallbacks' => array('en'),
));

$app['debug'] = true;

$app['bootstrap_service'] = $app->share(function () use ($app) {
    return new Services\BootstrapService($app['monolog']);
});
$app['database_service'] = $app->share(function () use ($app) {
    return new Services\DatabaseService($app['monolog']);
});
$app['demosite_service'] = $app->share(function () use ($app) {
    return new Services\DemositeService($app['monolog']);
});
$app['finish_service'] = $app->share(function () use ($app) {
    return new Services\FinishService();
});

$app->before(function (Request $request) use ($app) {
    if ($request->request->has('db_config') || $app['session']->has('db_data')) {
        $requestDbConfig = $request->request->get('db_config');
        $sessionDbData = $app['session']->get('db_data');
        $app->register(new Silex\Provider\DoctrineServiceProvider(), array(
            'dbs.options' => array (
                'default' => array(
                    'driver'    => 'pdo_mysql',
                    'host'      => $requestDbConfig['server_name'] ? : $sessionDbData['server_name'],
                    'dbname'    => $requestDbConfig['database_name'] ? : $sessionDbData['database_name'],
                    'user'      => $requestDbConfig['user_name'] ? : $sessionDbData['user_name'],
                    'password'  => $requestDbConfig['user_password'] ? : $sessionDbData['user_password'],
                    'port'      => $requestDbConfig['server_port'] ? : $sessionDbData['server_port'],
                    'charset'   => 'utf8',
                )
            ),
        ));

        $app->register(new DoctrineOrmServiceProvider, array(
            "orm.proxies_dir" => __DIR__."/../library/Proxy",
            "orm.em.options" => array(
                "mappings" => array(
                    array(
                        "type" => "annotation",
                        "namespace" => "Newscoop\Entity",
                        "path" => __DIR__."/../library/Newscoop/Entity",
                        "use_simple_annotation_reader" => false
                    ),
                ),
            ),
        ));

        // call scheduler service only when database info is in session or request
        $app['scheduler_service'] = $app->share(function () use ($app) {
            return new Newscoop\Services\SchedulerService($app['orm.em']);
        });
    }
}, Silex\Application::EARLY_EVENT);

$app->get('/', function (Silex\Application $app) use ($symfonyRequirements, $requirements) {
    $app['bootstrap_service']->makeDirectoriesWritable();
    $directories = $app['bootstrap_service']->checkDirectories();
    if ($directories !== true) {
        return $app['twig']->render('botstrap_errors.twig', array('directories' => $directories));
    }

    $checkPassed = true;
    foreach ($requirements as $req) {
        if (!$req->isFulfilled()) {
            $checkPassed = false;
        }
    }

    $warning = false;
    foreach ($symfonyRequirements->getRecommendations() as $req) {
        if (!$req->isFulfilled()) {
            if ($req->isOptional()) {
                $warning = true;
            }
        }
    }

    return $app['twig']->render('index.twig', array(
        'requirements' => $requirements,
        'recommendations' => $symfonyRequirements->getRecommendations(),
        'checkPassed' => $checkPassed,
        'NewscoopVersion' => new \CampVersion(),
        'warning' => $warning
    ));
})
->assert('_method', 'GET')
->bind('index');

$app->get('/license', function (Request $request) use ($app) {
    $form = $app['form.factory']->createNamedBuilder('license', 'form', array())
        ->setAction($app['url_generator']->generate('license'))
        ->add('accept_terms', 'checkbox', array(
            'label'     => 'I accept the license terms',
            'required' => true,
            'constraints' => array(new Assert\NotBlank()),
            'error_bubbling' => true
        ))
        ->add('submit', 'submit', array(
            'label' => "Next",
        ))
        ->getForm();

    if ('POST' == $request->getMethod()) {
        $form->bind($request);
        if ($form->isValid()) {
            return $app->redirect($app['url_generator']->generate('prepare'));
        } else {
            foreach ($form->getErrors() as $error) {
                $app['session']->getFlashBag()->add('danger', $error->getMessage());
            }
        }
    }

    return $app['twig']->render('license.twig', array(
        'form' => $form->createView(),
        'NewscoopVersion' => new \CampVersion(),
    ));
})
->assert('_method', 'POST|GET')
->bind('license');

$app->get('/prepare', function (Request $request) use ($app) {
    $form = $app['form.factory']->createNamedBuilder('db_config', 'form', array(
            'server_name' => 'localhost',
            'database_name' => 'newscoop',
            'server_port' => '3306'
        ))
        ->add('server_name', null, array(
            'constraints' => array(new Assert\NotBlank(array('message' => 'Server name value should not be blank.'))),
            'required' => true,
            'label' => "Server name:",
            'error_bubbling' => true,
        ))
        ->add('server_port', null, array(
            'required' => false,
            'label' => "Server port:"
        ))
        ->add('user_name', null, array(
            'constraints' => array(new Assert\NotBlank(array('message' => 'User name value should not be blank.'))),
            'required' => true,
            'label' => "User name:",
            'error_bubbling' => true,
        ))
        ->add('user_password', 'password', array(
            'required' => false,
            'label' => "Password:",
            'error_bubbling' => true,
        ))
        ->add('database_name', null, array(
            'constraints' => array(new Assert\NotBlank(array('message' => 'Database name value should not be blank.'))),
            'required' => true,
            'label' => "Database name:",
            'error_bubbling' => true,
        ))
        ->add('override_database', 'checkbox', array(
            'label' => 'Overwrite existing database?',
            'required' => false,
        ))
        ->getForm();

    if ('POST' == $request->getMethod()) {
        $form->bind($request);

        if ($form->isValid()) {
            $data = $form->getData();

            try {
                $app['db']->connect();
            } catch (\Exception $e) {
                if ($e->getCode() == '1049') {
                    $app['database_service']->createNewscoopDatabase($app['db']);
                }

                if ($e->getCode() == '1045' || $e->getCode() == '1044') {
                    $app['session']->getFlashBag()->set('danger', 'Invalid database parameters. Could not connect to database server.');

                    return $app['twig']->render('prepare.twig', array(
                        'form' => $form->createView(),
                        'NewscoopVersion' => new \CampVersion(),
                    ));
                }
            }

            $tables = $app['db']->fetchAll('SHOW TABLES', array());
            if (count($tables) == 0 || $data['override_database']) {
                $app['database_service']->fillNewscoopDatabase($app['db']);
                $app['database_service']->loadGeoData($app['db']);
                $app['database_service']->saveDatabaseConfiguration($app['db']);

            } else {
                $app['session']->getFlashBag()->add('danger', 'Database <i>' . $app['db']->getDatabase() . '</i> already exists. Change name or overwrite it.');

                return $app['twig']->render('prepare.twig', array(
                    'form' => $form->createView(),
                    'NewscoopVersion' => new \CampVersion(),
                ));
            }

            // redirect somewhere
            $app['session']->set('db_data', $data);

            return $app->redirect($app['url_generator']->generate('process'));
        } else {
            foreach ($form->getErrors() as $error) {
                $app['session']->getFlashBag()->add('danger', $error->getMessage());
            }
        }
    }

    return $app['twig']->render('prepare.twig', array(
        'form' => $form->createView(),
        'NewscoopVersion' => new \CampVersion(),
    ));
})
->assert('_method', 'POST|GET')
->bind('prepare');

$app->get('/process', function (Request $request) use ($app) {
    $form = $app['form.factory']->createNamedBuilder('main_config', 'form', array())
        ->add('site_title', null, array(
            'constraints' => array(new Assert\NotBlank(array('message' => 'Site title value should not be blank.'))),
            'required' => true,
            'error_bubbling' => true
        ))
        ->add(
            'recheck_user_password',
            'repeated',
            array(
                'type' => 'password',
                'invalid_message' => 'The password fields must match.',
                'options' => array('attr' => array('class' => 'password-field')),
                'required' => true,
                'first_options'  => array('label' => 'Password'),
                'second_options' => array('label' => 'Repeat Password'),
                'constraints' => array(new Assert\NotBlank(array('message' => 'Password value should not be blank.'))),
                'required' => true,
                'error_bubbling' => true
            )
        )
        ->add('user_email', 'email', array(
            'constraints' => array(
                new Assert\Email(array('message' => 'Email value is not a valid email address.')),
                new Assert\NotBlank(array('message' => 'Email value should not be blank.'))),
            'required' => true,
            'error_bubbling' => true
        ))
        ->getForm();

    if ('POST' == $request->getMethod()) {
        $form->bind($request);

        if ($form->isValid()) {
            $data = $form->getData();
            $app['session']->set('main_config', $data);
            $app['database_service']->installDatabaseSchema($app['db'], $request->server->get('HTTP_HOST'), $data['site_title']);
            $app['demosite_service']->installEmptyTheme();

            return $app->redirect($app['url_generator']->generate('post-process'));
        } else {
            foreach ($form->getErrors() as $error) {
                $app['session']->getFlashBag()->add('danger', $error->getMessage());
            }
        }
    }

    return $app['twig']->render('process.twig', array(
        'form' => $form->createView(),
        'NewscoopVersion' => new \CampVersion(),
    ));
})
->assert('_method', 'POST|GET')
->bind('process');

$app->get('/post-process', function (Request $request) use ($app) {
    $app['finish_service']->saveCronjobs($app['scheduler_service']);
    $app['finish_service']->generateProxies();
    $app['finish_service']->installAssets();
    $app['finish_service']->saveInstanceConfig($app['session']->get('main_config'), $app['db']);
    $app['finish_service']->createDefaultOauthClient($request->server->get('HTTP_HOST'));

    return $app['twig']->render('post-process.twig', array('NewscoopVersion' => new \CampVersion(),));
})
->assert('_method', 'POST|GET')
->bind('post-process');

$app->run();
