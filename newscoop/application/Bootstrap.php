<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\DoctrineEventDispatcherProxy;

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initAutoloader()
    {
        $options = $this->getOptions();
        set_include_path(implode(PATH_SEPARATOR, array_map('realpath', $options['autoloader']['dirs'])) . PATH_SEPARATOR . get_include_path());
        $autoloader = Zend_Loader_Autoloader::getInstance();
        $autoloader->setFallbackAutoloader(TRUE);

        // autoload symfony service container
        $autoloader->pushAutoloader(function($class) {
            require_once APPLICATION_PATH . "/../library/fabpot-dependency-injection-07ff9ba/lib/{$class}.php";
        }, 'sfService');

        // autoload symfony event dispatcher
        $autoloader->pushAutoloader(function($class) {
            require_once APPLICATION_PATH . "/../library/fabpot-event-dispatcher-782a5ef/lib/{$class}.php";
        }, 'sfEvent');

        // fix adodb loading error
        $autoloader->pushAutoloader(function($class) {
            return;
        }, 'ADO');

        $autoloader->pushAutoloader(function($class) {
            if ($class === 'Smarty') {
                require_once APPLICATION_PATH . '/../library/smarty3/Smarty.class.php';
            } else {
                require_once APPLICATION_PATH . '/../library/smarty3/sysplugins/' . strtolower($class) . '.php';
            }
        }, 'Smarty');

        $autoloader->pushAutoloader(function($class) {
            require_once APPLICATION_PATH . '/../library/MailChimp/MCAPI.class.php';
        }, 'MCAPI');

        $GLOBALS['g_campsiteDir'] = realpath(APPLICATION_PATH . '/../');

        return $autoloader;
    }

    protected function _initSession()
    {
        $options = $this->getOptions();
        if (!empty($options['session'])) {
            Zend_Session::setOptions($options['session']);
        }
        Zend_Session::start();

        foreach ($_COOKIE as $name => $value) { // remove unused cookies
            if ($name[0] == 'w' && strrpos('_height', $name) !== FALSE) {
                setcookie($name, '', time() - 3600);
            }
        }
    }

    /**
     * TODO the name of this method is named confusing, container for what? a zend navigation container? obviously not..
     */
    protected function _initContainer()
    {
        $this->bootstrap('autoloader');
        $container = new sfServiceContainerBuilder($this->getOptions());
        $container['config'] = $this->getOptions();

        $this->bootstrap('doctrine');
        $doctrine = $this->getResource('doctrine');
        $container->setService('em', $doctrine->getEntityManager());

        $this->bootstrap('view');
        $container->setService('view', $this->getResource('view'));

        $container->register('dispatcher', 'Newscoop\Services\EventDispatcherService')
            ->addMethodCall('setListeners', array($container))
            ->addMethodCall('setSubscribers', array($container));

        $container->register('article', 'Newscoop\Services\ArticleService')
            ->addArgument(new sfServiceReference('em'));

        $container->register('section', 'Newscoop\Services\SectionService')
            ->addArgument(new sfServiceReference('em'));

        $container->register('publication', 'Newscoop\Services\PublicationService')
            ->addArgument(new sfServiceReference('em'));

        $container->register('image', 'Newscoop\Image\ImageService')
            ->addArgument('%image%')
            ->addArgument(new sfServiceReference('em'));

        $container->register('user', 'Newscoop\Services\UserService')
            ->addArgument('%config%')
            ->addArgument(new sfServiceReference('em'))
            ->addArgument(Zend_Auth::getInstance());

        $container->register('user.token', 'Newscoop\Services\UserTokenService')
            ->addArgument(new sfServiceReference('em'));

        $container->register('user_type', 'Newscoop\Services\UserTypeService')
            ->addArgument(new sfServiceReference('em'));

        $container->register('user_points', 'Newscoop\Services\UserPointsService')
            ->addArgument(new sfServiceReference('em'))
            ->addArgument(new sfServiceReference('user'));

        $container->register('user_attributes', 'Newscoop\Services\UserAttributeService')
            ->addArgument(new sfServiceReference('em'));

        $container->register('author', 'Newscoop\Services\AuthorService')
            ->addArgument(new sfServiceReference('em'));

        $container->register('audit', 'Newscoop\Services\AuditService')
            ->addArgument(new sfServiceReference('em'))
            ->addArgument(new sfServiceReference('user'));

        $container->register('comment', 'Newscoop\Services\CommentService')
            ->addArgument(new sfServiceReference('em'));

        $container->register('community_feed', 'Newscoop\Services\CommunityFeedService')
            ->addArgument(new sfServiceReference('em'));

        $container->register('article.popularity', 'Newscoop\Services\ArticlePopularityService')
            ->addArgument(new sfServiceReference('em'));

        $container->register('audit.maintenance', 'Newscoop\Services\AuditMaintenanceService')
            ->addArgument(new sfServiceReference('em'));

        $container->register('user.topic', 'Newscoop\Services\UserTopicService')
            ->addArgument(new sfServiceReference('em'))
            ->addArgument(new sfServiceReference('dispatcher'));


        $container->register('auth.adapter', 'Newscoop\Services\Auth\DoctrineAuthService')
            ->addArgument(new sfServiceReference('em'));

        $container->register('auth.adapter.social', 'Newscoop\Services\Auth\SocialAuthService')
            ->addArgument(new sfServiceReference('em'));

        $container->register('email', 'Newscoop\Services\EmailService')
            ->addArgument('%email%')
            ->addArgument(new sfServiceReference('view'))
            ->addArgument(new sfServiceReference('user.token'));

        $container->register('ingest.publisher', 'Newscoop\Services\Ingest\PublisherService')
            ->addArgument('%ingest_publisher%');

        $container->register('ingest', 'Newscoop\Services\IngestService')
            ->addArgument('%ingest%')
            ->addArgument(new sfServiceReference('em'))
            ->addArgument(new sfServiceReference('ingest.publisher'));

        $container->register('blog', 'Newscoop\Services\BlogService')
            ->addArgument('%blog%');

        $container->register('comment_notification', 'Newscoop\Services\CommentNotificationService')
            ->addArgument(new sfServiceReference('email'))
            ->addArgument(new sfServiceReference('comment'))
            ->addArgument(new sfServiceReference('user'));

        $container->register('user_subscription', 'Newscoop\Services\UserSubscriptionService')
            ->addArgument(new sfServiceReference('em'));
        
        $container->register('XMLExport', 'Newscoop\Services\XMLExportService')
            ->addArgument(new sfServiceReference('em'));

        $container->register('user.find', 'Newscoop\Services\UserFindService')
            ->addArgument(new sfServiceReference('em'));

        $container->register('tree', 'Newscoop\Tree\TreeService')
            ->addArgument(new sfServiceReference('em'));

        $container->register('view.helper.thumbnail', 'Newscoop\Image\ThumbnailViewHelper')
            ->addArgument(new sfServiceReference('image'));

        $container->register('view.helper.rendition', 'Newscoop\Image\RenditionViewHelper')
            ->addArgument(new sfServiceReference('image'));

        $container->register('image.rendition', 'Newscoop\Image\RenditionService')
            ->addArgument('%config%')
            ->addArgument(new sfServiceReference('em'))
            ->addArgument(new sfServiceReference('image'));

        $container->register('image.search', 'Newscoop\Image\ImageSearchService')
            ->addArgument(new sfServiceReference('em'));

        $container->register('package', 'Newscoop\Package\PackageService')
            ->addArgument(new sfServiceReference('em'))
            ->addArgument(new sfServiceReference('image'));

        $container->register('package.search', 'Newscoop\Package\PackageSearchService')
            ->addArgument(new sfServiceReference('em'));

        $container->register('solr.client.update', 'Zend_Http_Client')
            ->addArgument('http://localhost:8983/solr/update/json?commit=true');

        $container->register('solr.client.select', 'Zend_Http_Client')
            ->addArgument('http://localhost:8983/solr/select/');

        $container->register('search.index', 'Newscoop\Search\Index')
            ->addArgument(new sfServiceReference('solr.client.update'));

        $container->register('article.link', 'Newscoop\Article\LinkService')
            ->addArgument(new sfServiceReference('em'));

        $container->register('article.search', 'Newscoop\Article\SearchService')
            ->addArgument(new sfServiceReference('webcoder'))
            ->addArgument(new sfServiceReference('image.rendition'))
            ->addArgument(new sfServiceReference('article.link'))
            ->addArgument($container['search']['article'])
            ->addArgument(new sfServiceReference('em'));

        $container->register('comment.search', 'Newscoop\Comment\SearchService')
            ->addArgument(new sfServiceReference('article.link'));

        $container->register('user.search', 'Newscoop\User\SearchService')
            ->addArgument(new sfServiceReference('image'));

        $container->register('twitter.search', 'Newscoop\Twitter\SearchService')
            ->addArgument(new sfServiceReference('twitter.client'))
            ->addArgument(new sfServiceReference('solr.client.select'))
            ->addArgument('%twitter%');

        $container->register('twitter.client', 'Zend_Http_Client')
            ->addArgument('https://api.twitter.com/1/favorites.json');

        $container->register('search_indexer_article', 'Newscoop\Search\Indexer')
            ->addArgument(new sfServiceReference('search.index'))
            ->addArgument(new sfServiceReference('article.search'))
            ->addMethodCall('initRepository', array($container, 'Newscoop\Entity\Article'));

        $container->register('search_indexer_comment', 'Newscoop\Search\Indexer')
            ->addArgument(new sfServiceReference('search.index'))
            ->addArgument(new sfServiceReference('comment.search'))
            ->addMethodCall('initRepository', array($container, 'Newscoop\Entity\Comment'));

        $container->register('search_indexer_user', 'Newscoop\Search\Indexer')
            ->addArgument(new sfServiceReference('search.index'))
            ->addArgument(new sfServiceReference('user.search'))
            ->addMethodCall('initRepository', array($container, 'Newscoop\Entity\User'));

        $container->register('search_indexer_twitter', 'Newscoop\Search\Indexer')
            ->addArgument(new sfServiceReference('search.index'))
            ->addArgument(new sfServiceReference('twitter.search'))
            ->addArgument(new sfServiceReference('twitter.search'));
        
        $container->register('link', 'Newscoop\Article\LinkService')
            ->addArgument(new sfServiceReference('em'));

        $container->register('webcoder', 'Newscoop\Webcode\Mapper');

        $container->register('livedesk.client', 'Guzzle\Http\Client')
            ->addArgument('http://liveblog.lab.sourcefabric.org/');

        $container->register('livedesk.blog', 'Newscoop\Livedesk\BlogFacade')
            ->addArgument(new sfServiceReference('livedesk.client'));

        $container->register('agenda', 'Newscoop\Services\AgendaService');

        $container->register('http.client_factory', 'Newscoop\Http\ClientFactory');

        $container->register('mobile.purchase', 'Tageswoche\Mobile\PurchaseFacade')
            ->addArgument(new sfServiceReference('http.client_factory'))
            ->addArgument('%config%');

        $container->register('mobile.issue', 'Tageswoche\Mobile\IssueFacade')
            ->addArgument(new sfServiceReference('em'));

        $container->register('mobile.free_upgrade', 'Tageswoche\Mobile\FreeUpgradeFacade')
            ->addArgument(new sfServiceReference('em'));

        $container->register('mobile.issue.offline', 'Tageswoche\Mobile\OfflineIssueService')
            ->addArgument(new sfServiceReference('http.client_factory'))
            ->addArgument(new sfServiceReference('view'))
            ->addArgument('%offline%');

        $mailchimpOptions = $this->getOption('mailchimp');
        $container->register('mailchimp.api', 'MCAPI')
            ->addArgument($mailchimpOptions['api_key']);

        $container->register('mailchimp', 'Newscoop\MailChimp\MailChimpFacade')
            ->addArgument(new sfServiceReference('mailchimp.api'));

        $container->register('article.authors', 'Newscoop\Article\AuthorsFacade')
            ->addArgument(new sfServiceReference('em'))
            ->addArgument(new sfServiceReference('user'))
            ->addArgument(new sfServiceReference('blog'));

        Zend_Registry::set('container', $container);
        return $container;
    }

    /**
     * @todo pass container to allow lazy dispatcher loading
     */
    protected function _initEventDispatcher()
    {
        $this->bootstrap('container');
        $container = $this->getResource('container');

        DatabaseObject::setEventDispatcher($container->getService('dispatcher'));
        DatabaseObject::setResourceNames($container->getParameter('resourceNames'));

        $eventManager = $container->getService('em')->getEventManager();
        $eventManager->addEventSubscriber(new DoctrineEventDispatcherProxy($container->getService('dispatcher')));
        $eventManager->addEventSubscriber($container->getService('image'));
    }

    protected function _initPlugins()
    {
        $options = $this->getOptions();
        $front = Zend_Controller_Front::getInstance();
        $front->registerPlugin(new Application_Plugin_ContentType());
        $front->registerPlugin(new Application_Plugin_Upgrade());
        $front->registerPlugin(new Application_Plugin_CampPluginAutoload());
        $front->registerPlugin(new Application_Plugin_Auth($options['auth']));
        $front->registerPlugin(new Application_Plugin_Acl($options['acl']));
        $front->registerPlugin(new Application_Plugin_Locale());
    }

    protected function _initRouter()
    {
        $front = Zend_Controller_Front::getInstance();
        $router = $front->getRouter();
        $options = $this->getOptions();

        $router->addRoute(
            'content',
            new Zend_Controller_Router_Route(':language/:issue/:section/:articleNo/:articleUrl', array(
                'module' => 'default',
                'controller' => 'index',
                'action' => 'index',
                'articleUrl' => null,
                'articleNo' => null,
                'section' => null,
                'issue' => null,
                'language' => null,
            ), array(
                'language' => '[a-z]{2}',
            )));

         $router->addRoute(
            'webcode',
            new Zend_Controller_Router_Route(':webcode', array(
                'module' => 'default'
            ), array(
                'webcode' => '[\+\s@][a-z]{5,6}',
            )));

         $router->addRoute
         (
            'postfinance',
            new Zend_Controller_Router_Route_Static('postfinance', array( 'controller' => 'payment', 'action' => 'postfinance', 1 => null ))
         );

         $router->addRoute(
            'language/webcode',
            new Zend_Controller_Router_Route(':language/:webcode', array(
            ), array(
                'module' => 'default',
                'language' => '[a-z]{2}',
                'webcode' => '^[\+\s@][a-z]{5,6}',
            )));

        $router->addRoute(
            'confirm-email',
            new Zend_Controller_Router_Route('confirm-email/:user/:token', array(
                'module' => 'default',
                'controller' => 'register',
                'action' => 'confirm',
            )));

        $router->addRoute('user', new Zend_Controller_Router_Route('user/profile/:username/:action', array(
            'module' => 'default',
            'controller' => 'user',
            'action' => 'profile',
        )));

        $router->addRoute('topic', new Zend_Controller_Router_Route('themen/:topic', array(
            'module' => 'default',
            'controller' => 'topic',
            'action' => 'index',
            'topic' => null,
        )));

        $router->addRoute('my-topics', new Zend_Controller_Router_Route_Static('meine-themen', array(
            'module' => 'default',
            'controller' => 'my-topics',
            'action' => 'index',
        )));

        $router->addRoute('weather', new Zend_Controller_Router_Route_Static('wetter', array(
            'module' => 'default',
            'controller' => 'weather',
            'action' => 'index',
        )));

        $router->addRoute('image',
            new Zend_Controller_Router_Route_Regex($options['image']['cache_url'] . '/(.*)', array(
                'module' => 'default',
                'controller' => 'image',
                'action' => 'cache',
            ), array(
                1 => 'src',
            ), $options['image']['cache_url'] . '/%s'));

         $router->addRoute('rest',
             new Zend_Rest_Route($front, array(), array(
                 'admin' => array(
                     'slideshow-rest',
                 ),
             )));

        $router->addRoute('api', new Zend_Controller_Router_Route('api/:controller/:action/:id', array(
            'module' => 'api',
        )));
    }

    protected function _initActionHelpers()
    {
        require_once APPLICATION_PATH . '/controllers/helpers/Smarty.php';
        Zend_Controller_Action_HelperBroker::addHelper(new Action_Helper_Smarty());
    }

    protected function _initTranslate()
    {
        $options = $this->getOptions();

        $translate = new Zend_Translate(array(
            'adapter' => 'array',
            'disableNotices' => TRUE,
            'content' => $options['translation']['path'],
        ));

        Zend_Registry::set('Zend_Translate', $translate);
    }

    protected function _initAuthStorage()
    {
        $storage = new Zend_Auth_Storage_Session('Zend_Auth_Storage');
        Zend_Auth::getInstance()->setStorage($storage);
    }

    /**
     */
    protected function _initLog()
    {
        $writer = new Zend_Log_Writer_Syslog(array('application' => 'Newscoop'));
        $log = new Zend_Log($writer);
        \Zend_Registry::set('log', $log);
        return $log;
    }
}
