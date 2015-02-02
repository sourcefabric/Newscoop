<?php

namespace spec\Newscoop\Gimme;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Knp\Component\Pager\Paginator;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Newscoop\Gimme\Pagination;
use Newscoop\Gimme\PartialResponse;

class PaginatorServiceSpec extends ObjectBehavior
{
    function let(
        $die,
        Paginator $paginator,
        Router $router,
        \Knp\Component\Pager\Pagination\AbstractPagination $paginationView
    ){
        $paginator->paginate(array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20), null, null, array())
            ->willReturn($paginationView);
        $paginationView->getItems()->willReturn(array(6,7,8,9,10));

        $this->beConstructedWith($paginator, $router);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Newscoop\Gimme\PaginatorService');
    }

    function it_should_set_pagination(Pagination $pagination)
    {
        $this->setPagination($pagination)->shouldReturn($this);
        $this->getPagination()->shouldReturn($pagination);
    }

    function it_should_set_partial_response(PartialResponse $partialResponse)
    {
        $this->setPartialResponse($partialResponse)->shouldReturn($this);
        $this->getPartialResponse()->shouldReturn($partialResponse);

        $newPartialResponse = new PartialResponse();
        $newPartialResponse->setFields('id,name,subject');
        $this->setPartialResponse($newPartialResponse)->shouldReturn($this);
        $this->getPartialResponse()->shouldReturn($newPartialResponse);
    }

    function it_shoud_set_used_route_params()
    {
        $this->setUsedRouteParams(array('id' => 5, 'number' => 34))
            ->shouldReturn($this);
    }

    function it_should_paginate(Pagination $pagination)
    {
        $this->setPagination($pagination);
        $this->setPaginationData(array(
            'numItemsPerPage' => 5,
            'current' => 2,
            'totalCount' => 20,
            'lastPageInRange' => 3,
            'firstPageInRange' => 1,
        ));

        $this->paginate(array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20))->shouldReturn(array(
            'items' => array(6,7,8,9,10),
            'pagination' => array(
                'itemsPerPage' => 5,
                'currentPage' => 2,
                'itemsCount' => 20,
                'nextPageLink' => null,
                'previousPageLink' => null
            )
        ));
    }
}
