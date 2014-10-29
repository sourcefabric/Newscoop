<?php

namespace spec\Newscoop\ArticlesBundle\Controller;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\Container;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;

class EditorialCommentsControllerSpec extends ObjectBehavior
{
	function let(Container $container,
		Registry $doctrine,
		EntityRepository $repository,
		EntityManager $entityManager,
		Request $request
	) {
        $container->get('doctrine')->willReturn($doctrine);
        $container->get('request')->willReturn($request);
        $doctrine->getManager()->willReturn($entityManager);
        $entityManager->getRepository(Argument::any())->willReturn($repository);

        $this->setContainer($container);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Newscoop\ArticlesBundle\Controller\EditorialCommentsController');
    }

    function it_is_of_type_container_aware()
    {
        $this->shouldBeAnInstanceOf('Symfony\Component\DependencyInjection\ContainerAware');
    }

    /*function its_getAction_should_render_a_list_of_EditorialCommentArray(
    	$entityManager,
    	\Newscoop\ArticlesBundle\Entity\Repository\EditorialCommentRepository $repository,
    	Request $request
    	) {
        $entityManager->getRepository(Argument::exact('Newscoop\ArticlesBundle\Entity\EditorialComment'))->willReturn($repository);
        $repository->getAllByArticleNumber(1)->willReturn(Argument::type('array'));

        $this->getAction($request)->shouldReturn(Argument::type('array'));
    }*/
}
