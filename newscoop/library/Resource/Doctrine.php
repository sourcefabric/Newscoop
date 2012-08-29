<?php
/**
 * @package Resource
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Doctrine\ORM\EntityManager;
use Doctrine\Common\EventManager;
use Doctrine\Common\ClassLoader;
use Doctrine\ORM\Mapping\Driver\DriverChain;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\Configuration;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\SimpleAnnotationReader;
use Doctrine\DBAL\Logging\EchoSQLLogger;
use Gedmo\Timestampable\TimestampableListener;
use Gedmo\Sluggable\SluggableListener;
use Gedmo\Tree\TreeListener;

/**
 * Doctrine Zend application resource
 */
class Resource_Doctrine extends \Zend_Application_Resource_ResourceAbstract
{
    /** @var Doctrine\ORM\EntityManager */
    private $em;

    /** @var Doctrine\Common\EventManager */
    private $evm;

    /** @var  */
    private $options;

    /**
     * Init doctrine
     */
    public function init()
    {
        Zend_Registry::set('doctrine', $this);
        return $this;
    }

    /**
     * Get Entity Manager
     *
     * @return Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        global $Campsite;

        if ($this->em !== NULL && $this->em->isOpen()) {
            return $this->em;
        }

        $this->evm = new EventManager();
        $config = new Configuration();
        $options = $this->getOptions();
        // standard doctrine annotations
        AnnotationRegistry::registerFile(
            APPLICATION_PATH . '/../library/Doctrine/ORM/Mapping/Driver/DoctrineAnnotations.php'
        );

        $this->registerAutoloadNamespaces();

        // standard annotation reader
        $annotationReader = new SimpleAnnotationReader();
        $annotationReader->addNamespace('Doctrine\ORM\Mapping');
        $annotationReader->addNamespace('Newscoop\Annotations');

        // register annotation driver
        $driverChain = new DriverChain();

        Gedmo\DoctrineExtensions::registerAbstractMappingIntoDriverChainORM(
            $driverChain, // our metadata driver chain, to hook into
            $annotationReader // our cached annotation reader
        );

        // drivers for diferent namespaces
        $driverChain->addDriver(new AnnotationDriver($annotationReader, array(
            $options['entity']['dir'],
        )), 'Newscoop\Entity');

        // register metadata driver
        $config->setMetadataDriverImpl($driverChain);

        //set cache
        $cache = new $options['cache'];
        $config->setMetadataCacheImpl($cache);
        $config->setQueryCacheImpl($cache);

        // timestampable
        if (!empty($options['gedmo']['timestampable'])) {
            $this->addTimestampable($annotationReader);
        }
        // sluggable
        if (!empty($options['gedmo']['sluggable'])) {
            $this->addSluggable($annotationReader);
        }
        // tree
        if (!empty($options['gedmo']['tree'])) {
            $this->addTree($annotationReader);
        }
        // profile logger
        if (!empty($this->options['gedmo']['profile'])) {
            $config->setSQLLogger(new EchoSQLLogger());
        }

        // set proxy
        $config->setProxyDir(realpath($options['proxy']['dir']));
        $config->setProxyNamespace($options['proxy']['namespace']);
        $config->setAutoGenerateProxyClasses($options['proxy']['autogenerate']);

        $config_file = APPLICATION_PATH . '/../conf/database_conf.php';
        if (empty($Campsite) && file_exists($config_file)) {
            require_once $config_file;
        }
    
        if (isset($options['database'])) {
            $database = $options['database'];
        } else {
            $database = $this->getDefaultDbConf($Campsite);
        }

        foreach ($options['functions'] as $function => $value)
            $config->addCustomNumericFunction(strtoupper($function), $value);

        $this->em = EntityManager::create(
            $database,
            $config,
            $this->evm
        );

        return $this->em;
    }

    /**
     * @param $Campsite
     * @return array
     */
    protected function getDefaultDbConf($Campsite)
    {
        // set database
        $database = array(
            'driver' => 'pdo_mysql',
            'host' => $Campsite['DATABASE_SERVER_ADDRESS'],
            'dbname' => $Campsite['DATABASE_NAME'],
            'user' => $Campsite['DATABASE_USER'],
            'password' => $Campsite['DATABASE_PASSWORD'],
            'driverOptions' => array(
                1002 => "SET NAMES 'UTF8'",
            ),
        );
        return $database;
    }

    /**
     * Register Autoload Namespaces
     *
     * @return void
     */
    protected function registerAutoloadNamespaces()
    {
        // gedmo annotation loader
        AnnotationRegistry::registerAutoloadNamespace(
            'Gedmo\Mapping\Annotation',
            realpath(APPLICATION_PATH . '/../library/Gedmo/doctrine-extensions/lib')
        );
    }

    /**
     * Add Timestampable listener
     *
     * @return void
     */
    protected function addTimestampable($annotationReader)
    {
        if (!empty($this->evm)) {
            $timestampable = new TimestampableListener();
            $timestampable->setAnnotationReader($annotationReader);
            $this->evm->addEventSubscriber($timestampable);
         }
    }

    /**
     * Add Sluggable listener
     *
     * @return void
     */
    protected function addSluggable($annotationReader)
    {
        if (!empty($this->evm)) {
            $sluggable = new SluggableListener();
            $sluggable->setAnnotationReader($annotationReader);
            $this->evm->addEventSubscriber($sluggable);
        }
    }
    
    /**
     * Add Tree listener
     *
     * @return void
     */
    protected function addTree($annotationReader)
    {
        if (!empty($this->evm)) {
            $tree = new TreeListener();
            $tree->setAnnotationReader($annotationReader);
            $this->evm->addEventSubscriber($tree);
        }
    }
}
