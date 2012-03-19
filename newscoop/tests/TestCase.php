<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Doctrine\ORM\Mapping\ClassMetadataFactory,
    Doctrine\ORM\Tools\SchemaTool,
    Doctrine\Common\Cache\ArrayCache as Cache;

/**
 */
abstract class TestCase extends PHPUnit_Framework_TestCase
{
    const PICTURE_LANDSCAPE = 'tests/fixtures/picture_landscape.jpg';
    const PICTURE_PORTRAIT = 'tests/fixtures/picture_portrait.jpg';

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

        $orm = $application->getBootstrap()->getResource('doctrine')->getEntityManager();
        $orm->clear();

        $tool = new SchemaTool($orm);
        $tool->dropDatabase();

        $classes = func_get_args();
        if (!empty($classes)) {
            $metadataFactory = new ClassMetadataFactory();
            $metadataFactory->setEntityManager($orm);
            $metadataFactory->setCacheDriver(new Cache());

            $metadata = array();
            foreach ((array) $classes as $class) {
                $metadata[] = $metadataFactory->getMetadataFor($class);
            }

            $tool->createSchema($metadata);
        }

        return $orm;
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
