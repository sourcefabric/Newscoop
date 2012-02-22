<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Image;

/**
 */
class ImageSearchServiceTest extends \TestCase
{
    /** @var Newscoop\Image\ImageSearchService */
    protected $service;

    /** @var Doctrine\ORM\EntityManager */
    protected $orm;

    public function setUp()
    {
        $this->orm = $this->setUpOrm('Newscoop\Image\LocalImage');
        $this->service = new ImageSearchService($this->orm);
    }

    public function tearDown()
    {
        $this->tearDownOrm($this->orm);
    }

    public function testInstance()
    {
        $this->assertInstanceOf('Newscoop\Image\ImageSearchService', $this->service);
    }

    public function testFind()
    {
        foreach (array('tic', 'tic tac', 'tac', 'tac toc', 'toc') as $description) {
            $image = new LocalImage($description);
            $image->setDescription($description);
            $this->orm->persist($image);
        }

        $this->orm->flush();

        $this->assertEquals(2, count($this->service->find('tic')));
    }
}
