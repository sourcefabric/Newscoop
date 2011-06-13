<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Doctrine\ORM\Mapping\ClassMetadataFactory,
    Doctrine\ORM\Tools\SchemaTool,
    Doctrine\Common\Cache\ArrayCache;

/**
 * Base entity repository test case
 */
abstract class RepositoryTestCase extends \PHPUnit_Framework_TestCase
{
    /** @var Resource_Doctrine */
    protected $doctrine;

    /** @var Doctrine\ORM\EntityManager */
    protected $em;

    /**
     * @param mixed $classes
     * @return void
     */
    public function setUp($classes = NULL)
    {
        $this->doctrine = Zend_Registry::get('doctrine');
        $this->em = $this->doctrine->getEntityManager();
        $this->em->clear();

        $tool = new SchemaTool($this->em);
        $tool->dropDatabase();

        if (!empty($classes)) {
            $metadataFactory = new ClassMetadataFactory();
            $metadataFactory->setEntityManager($this->em);
            $metadataFactory->setCacheDriver(new ArrayCache);

            $metadata = array();
            foreach ((array) $classes as $class) {
                $metadata[] = $metadataFactory->getMetadataFor($class);
            }

            $tool->createSchema($metadata);
        }
    }

    /**
     * @return void
     */
    public function tearDown()
    {
        $tool = new SchemaTool($this->em);
        $tool->dropDatabase();
    }
}
