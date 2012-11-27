<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\Common\Cache\ArrayCache;

/**
 */
abstract class TestCase extends PHPUnit_Framework_TestCase
{
    const PICTURE_LANDSCAPE = 'tests/fixtures/picture_landscape.jpg';
    const PICTURE_PORTRAIT = 'tests/fixtures/picture_portrait.jpg';

    /**
     * @var Doctrine\ORM\Mapping\ClassMetadataFactory;
     */
    private $metadataFactory;

    /**
     * Set up document manager
     *
     * @return Doctrine\ODM\MongoDB\DocumentManager
     */
    protected function setUpOdm()
    {
        global $application;

        $odm = $application->getBootstrap()->getResource('odm');

        if ($odm === null) {
            $this->markTestSkipped('Mongo extension not available.');
        }

        $odm->getConfiguration()->setDefaultDB('phpunit');
        $this->tearDownOdm($odm);

        return $odm;
    }

    /**
     * Tear down document manager
     *
     * @param Doctrine\ODM\MongoDB\DocumentManager $odm
     * @return void
     */
    protected function tearDownOdm(\Doctrine\ODM\MongoDB\DocumentManager $odm)
    {
        $odm->getSchemaManager()->dropDatabases();
        $odm->clear();
    }

    /**
     * Set up entity manager
     *
     * @return Doctrine\ORM\EntityManager
     */
    protected function setUpOrm()
    {
        global $application;

        $doctrine = $application->getBootstrap()->getResource('container')->getService('doctrine');
        $orm = $doctrine->getEntityManager();
        $orm->clear();

        $tool = new SchemaTool($orm);
        $tool->dropDatabase();

        $classes = func_get_args();
        if (!empty($classes)) {
            $metadataFactory = $this->getMetadataFactory($orm);
            $metadata = array();
            foreach ((array) $classes as $class) {
                $metadata[] = $metadataFactory->getMetadataFor($class);
            }

            $tool->createSchema($metadata);
        }

        return $orm;
    }

    /**
     * Get metadata factory
     *
     * @param Doctrine\ORM\EntityManager $em
     * @return Doctrine\ORM\Mapping\ClassMetadataFactory
     */
    private function getMetadataFactory(EntityManager $em)
    {
        if (!isset($this->metadataFactory)) {
            $this->metadataFactory = new ClassMetadataFactory();
            $this->metadataFactory->setEntityManager($em);
            $this->metadataFactory->setCacheDriver(new ArrayCache());
        }

        return $this->metadataFactory;
    }

    /**
     * Tear down entity manager
     *
     * @param Doctrine\ORM\EntityManager $orm
     * @return void
     */
    protected function tearDownOrm(\Doctrine\ORM\EntityManager $orm)
    {
        $tool = new SchemaTool($orm);
        $tool->dropDatabase();
    }
}
