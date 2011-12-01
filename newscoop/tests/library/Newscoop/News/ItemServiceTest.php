<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\News;

/**
 */
class ItemServiceTest extends \PHPUnit_Framework_TestCase
{
    /** @var Newscoop\News\ItemService */
    protected $service;

    /** @var Doctrine\Common\Persistance\Objectmanager */
    protected $odm;

    public function setUp()
    {
        $this->odm = $this->getMockBuilder('Doctrine\ODM\MongoDB\DocumentManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->repository = $this->getMockBuilder('Doctrine\ODM\MongoDB\DocumentRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $this->odm->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo('Newscoop\News\Item'))
            ->will($this->returnValue($this->repository));

        $this->service = new ItemService($this->odm);
    }

    public function testInstance()
    {
        $this->assertInstanceOf('Newscoop\News\ItemService', $this->service);
    }

    public function testFindBy()
    {
        $this->repository->expects($this->once())
            ->method('findBy')
            ->with($this->equalTo(array('foo' => 'bar')), $this->equalTo(array('id' => 'desc')), $this->equalTo(1), $this->equalTo(2))
            ->will($this->returnValue('res'));

        $this->assertEquals('res', $this->service->findBy(array('foo' => 'bar'), array('id' => 'desc'), 1, 2));
    }
}
