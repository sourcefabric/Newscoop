<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Acl;

/**
 */
class StorageTest extends \TestCase
{
    /** @var Newscoop\Acl\Storage */
    protected $service;

    public function setUp()
    {
        $this->doctrine = $this->getMockBuilder('Newscoop\Doctrine\Registry')
            ->disableOriginalConstructor()
            ->getMock();

        $this->storage = new Storage($this->doctrine);
    }

    public function testInstance()
    {
        $this->assertInstanceOf('Newscoop\Acl\Storage', $this->storage);
    }

    /**
     * @ticket CS-4213
     */
    public function testGetResourcesWithObsoletePermissions()
    {
        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $repository = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $this->doctrine->expects($this->once())
            ->method('getEntityManager')
            ->will($this->returnValue($em));

        $em->expects($this->once())
            ->method('getRepository')
            ->will($this->returnValue($repository));

        $repository->expects($this->once())
            ->method('findAll')
            ->will($this->returnValue(array(
                'InitializeTemplateEngine',
                'AddArticle',
            )));

        $this->assertEquals(array(
            'article' => array('add'),
        ), $this->storage->getResources());
    }
}
