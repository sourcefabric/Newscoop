<?php
/**
 * @package Newscoop
 */

use Doctrine\ORM\Mapping\ClassMetadataFactory,
    Doctrine\ORM\Mapping\ClassMetadata,
    Doctrine\ORM\Tools\SchemaTool,
    Doctrine\Common\Cache\ApcCache;


/**
 * Base entity repository test case
 */
abstract class RepositoryTestCase extends \PHPUnit_Framework_TestCase
{
    protected $doctrine;
    protected $em;

    private $metadata;

    public function setUp()
    {
        $this->doctrine = Zend_Registry::get('doctrine');
        $this->em = $this->doctrine->getEntityManager();
        $this->em->clear();

        if ($this->metadata === NULL) {
            $metadataFactory = new ClassMetadataFactory();
            $metadataFactory->setEntityManager($this->em);
            $metadataFactory->setCacheDriver(new ApcCache);
            $this->metadata = $metadataFactory->getAllMetadata();
        }

        $tool = new SchemaTool($this->em);
        $tool->dropDatabase();
        $tool->createSchema($this->metadata);
    }

    public function tearDown()
    {
        $tool = new SchemaTool($this->em);
        $tool->dropDatabase();
    }
}
