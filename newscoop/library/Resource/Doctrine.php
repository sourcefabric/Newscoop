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

        $config = new Doctrine\ORM\Configuration;
        $options = $this->getOptions();

        // set annotation driver
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

        // set database
        $database = array(
            'driver' => 'pdo_mysql',
            'host' => $Campsite['DATABASE_SERVER_ADDRESS'],
            'dbname' => $Campsite['DATABASE_NAME'],
            'user' => $Campsite['DATABASE_USER'],
            'password' => $Campsite['DATABASE_PASSWORD'],
        );


        $this->em = Doctrine\ORM\EntityManager::create($database, $config);
        return $this->em;
    }
}
