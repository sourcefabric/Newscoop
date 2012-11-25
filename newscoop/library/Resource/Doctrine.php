<?php
/**
 * @package Resource
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Doctrine\ORM\Configuration,
    Doctrine\ORM\EntityManager;

/**
 * Doctrine Zend application resource
 */
class Resource_Doctrine extends \Zend_Application_Resource_ResourceAbstract
{
    /** @var Doctrine\ORM\EntityManager */
    private $em;

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

        $config = new Configuration();
        $options = $this->getOptions();

        // set annotations reader
        $metadata = $config->newDefaultAnnotationDriver(realpath($options['entity']['dir']));
        $config->setMetadataDriverImpl($metadata);

        // set proxy
        $config->setProxyDir(realpath($options['proxy']['dir']));
        $config->setProxyNamespace($options['proxy']['namespace']);
        $config->setAutoGenerateProxyClasses($options['proxy']['autogenerate']);

        // set cache
        $cache = new $options['cache'];
        $config->setMetadataCacheImpl($cache);
        $config->setQueryCacheImpl($cache);

        $config_file = APPLICATION_PATH . '/../conf/database_conf.php';
        if (empty($Campsite) && is_readable($config_file)) {
            require_once $config_file;
        }

        // set database
        $database = array(
            'driver' => 'pdo_mysql',
            'driverOptions' => array(
                1002 => "SET NAMES 'UTF8'",
            ),
        );

        if (isset($options['database'])) {
            $database = $options['database'];
        } else {
            $database += array(
                'host' => $Campsite['DATABASE_SERVER_ADDRESS'],
                'dbname' => $Campsite['DATABASE_NAME'],
                'user' => $Campsite['DATABASE_USER'],
                'password' => $Campsite['DATABASE_PASSWORD'],
            );
        }

        foreach ($options['functions'] as $function => $value) {
            $config->addCustomNumericFunction(strtoupper($function), $value);
        }

        if (APPLICATION_ENV !== 'production') {
            //$config->setSQLLogger(new Doctrine\DBAL\Logging\EchoSQLLogger());
        }

        $this->em = EntityManager::create($database, $config);
        return $this->em;
    }
}
