<?php
/**
 * @package Newscoop\Gimme
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\GimmeBundle\Tests\Newscoop\Gimme;

use Newscoop\GimmeBundle\Tests\ContainerAwareUnitTestCase;
use Newscoop\Gimme\PaginatorService;
use Newscoop\Gimme\Pagination;
use Newscoop\Gimme\PartialResponse;

class PaginatorServiceTest extends ContainerAwareUnitTestCase
{   
    protected $paginatorService;

    protected function setUp()
    {
        $this->paginatorService = $this->get('newscoop.paginator.paginator_service');
        $this->paginatorService->setPagination(new Pagination());
        $this->paginatorService->setPartialResponse(new PartialResponse());
    }

    public function testSetPagination()
    {
        $this->assertTrue(
            get_class(new Pagination()) == get_class($this->paginatorService->getPagination())
        );
    }

    public function testSetPartialResponse()
    {
        $this->assertTrue(
            get_class(new PartialResponse()) == get_class($this->paginatorService->getPartialResponse())
        );
    }
}