<?php
/**
 * @package Resource
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

/**
 * Doctrine Zend application resource.
 */
class Resource_Doctrine extends \Zend_Application_Resource_ResourceAbstract
{
    /** @var \Doctrine\ORM\EntityManager */
    private static $entityManager = NULL;

    /**
     * Init doctrine resource.
     */
    public function init()
    {
        \Zend_Registry::set('doctrine', $this);
        return $this;
    }

    /**
     * Get Doctrine Entity Manager instance.
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        global $Campsite;

        if (isset(self::$entityManager)) {
            return self::$entityManager;
        }

        $config = new \Doctrine\ORM\Configuration;
        $options = $this->getOptions();

        // set annotation driver
        $metadata = $config->newDefaultAnnotationDriver($options['entity']['dir']);
        $config->setMetadataDriverImpl($metadata);

        // set proxy
        $config->setProxyDir($options['proxy']['dir']);
        $config->setProxyNamespace($options['proxy']['namespace']);
        $config->setAutoGenerateProxyClasses($options['proxy']['autogenerate']);

        // set cache
        $cache = new \Doctrine\Common\Cache\ArrayCache;
        $config->setMetadataCacheImpl($cache);
        $config->setQueryCacheImpl($cache);

        // set database
        $database = array(
            'driver' => 'pdo_mysql',
            'host' => $Campsite['DATABASE_SERVER_ADDRESS'],
            'dbname' => $Campsite['DATABASE_NAME'],
            'user' => $Campsite['DATABASE_USER'],
            'password' => $Campsite['DATABASE_PASSWORD'],
        );

        return self::$entityManager = \Doctrine\ORM\EntityManager::create($database, $config);
    }
}
