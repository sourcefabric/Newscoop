<?php
/**
 * @package Newscoop\Gimme
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\GimmeBundle\Tests\Newscoop\Gimme;

use Newscoop\GimmeBundle\Tests\ContainerAwareUnitTestCase;
use Newscoop\Gimme\Pagination;

class PaginationTest extends ContainerAwareUnitTestCase
{
    private $pagination;

    protected function setUp()
    {
        $this->pagination = new Pagination();
    }

    public function testSetPage()
    {
        $page = 1;
        $this->pagination->setPage($page);

        $this->assertTrue($this->pagination->getPage() == $page);
    }

    public function testSetSort()
    {
        $sort = array('nmuber' => 'desc');
        $this->pagination->setSort($sort);

        $this->assertTrue($this->pagination->getSort() == $sort);
    }

    public function testSetItemsPerPage()
    {
        $itemsPerPage = 10;
        $this->pagination->setItemsPerPage($itemsPerPage);

        $this->assertTrue($this->pagination->getItemsPerPage() == $itemsPerPage);
    }
}
