<?php

namespace spec\Newscoop\GimmeBundle\Controller;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\Container;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\AbstractQuery;
use Newscoop\Entity\Repository\ArticleRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormView;
use Symfony\Component\Routing\Router;
use Newscoop\Services\UserService;
use Doctrine\ORM\EntityRepository;
use Newscoop\ArticlesBundle\Entity\EditorialComment;
use Newscoop\ArticlesBundle\Entity\Repository\EditorialCommentRepository;
use Newscoop\ArticlesBundle\Services\EditorialCommentsService;
use Newscoop\Entity\User;
use Newscoop\Entity\Article;

class EditorialCommentsApiControllerSpec extends ObjectBehavior
{
     function let(
        Container $container,
        Registry $doctrine,
        ArticleRepository $articleRepository,
        EntityManager $entityManager,
        Request $request,
        FormFactory $formFactory,
        FormBuilder $formBuilder,
        Form $form,
        FormView $formView,
        Router $router,
        EditorialCommentsService $editorialCommentService,
        EditorialComment $editorialComment,
        EditorialCommentRepository $editorialCommentRepository,
        User $user,
        UserService $userService,
        Article $article,
        AbstractQuery $query
    ){
        $container->get('em')->willReturn($entityManager);
        $container->get('form.factory')->willReturn($formFactory);
        $container->get('request')->willReturn($request);
        $container->get('router')->willReturn($router);
        $container->get('newscoop.editorial_comments')->willReturn($editorialCommentService);
        $container->get('user')->willReturn($userService);

        $formBuilder->getForm(Argument::cetera())->willReturn($form);
        $formFactory->create(Argument::cetera())->willReturn($form);
        $form->createView()->willReturn($formView);
        $form->handleRequest(Argument::cetera())->willReturn(true);
        $form->isValid()->willReturn(true);
        $userService->getCurrentUser()->willReturn($user);

        $entityManager->getRepository('Newscoop\ArticlesBundle\Entity\EditorialComment')->willReturn($editorialCommentRepository);
        $entityManager->getRepository('Newscoop\Entity\Article')->willReturn($articleRepository);

        $articleRepository->getArticle(Argument::cetera())->willReturn($query);
        $editorialCommentService->create(Argument::cetera())->willReturn($editorialComment);
        $editorialCommentService->edit(Argument::cetera())->willReturn($editorialComment);
        $editorialCommentService->resolve(
            Argument::type('\Newscoop\ArticlesBundle\Entity\EditorialComment'),
            Argument::type('\Newscoop\Entity\User'),
            Argument::type('bool')
        )->willReturn($editorialComment);

        $editorialCommentService->remove(
            Argument::type('\Newscoop\ArticlesBundle\Entity\EditorialComment'),
            Argument::type('\Newscoop\Entity\User')
        )->willReturn(true);

        $this->setContainer($container);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Newscoop\GimmeBundle\Controller\EditorialCommentsApiController');
        $this->shouldImplement('FOS\RestBundle\Controller\FOSRestController');
    }

    function it_should_create_new_editorial_comment(Request $request, $form, $query, $article)
    {
        $form->getData()->willReturn(array(
            'comment' => 'test editorial comment',
        ));

        $query->getOneOrNullResult()->willReturn($article);
        $response = $this->createCommentAction($request, 1, 1);

        $response->shouldBeAnInstanceOf('Symfony\Component\HttpFoundation\Response');
        $response->getStatusCode()->shouldReturn(201);
    }

    function it_should_edit_editorial_comment(Request $request, EditorialComment $comment, $form, $editorialCommentRepository, $query)
    {
        $form->getData()->willReturn(array(
            'comment' => 'edit editorial comment',
        ));
        $editorialCommentRepository->getOneByArticleAndCommentId(1,1,1)->willReturn($query);
        $query->getOneOrNullResult()->willReturn($comment);

        $response = $this->editCommentAction($request, 1, 1, 1);

        $response->shouldBeAnInstanceOf('Symfony\Component\HttpFoundation\Response');
        $response->getStatusCode()->shouldReturn(200);
    }

    function it_should_resolve_editorial_comment(Request $request, EditorialComment $comment, $form, $editorialCommentRepository, $query)
    {
        $form->getData()->willReturn(array(
            'resolved' => true,
        ));
        $editorialCommentRepository->getOneByArticleAndCommentId(1,1,1)->willReturn($query);
        $query->getOneOrNullResult()->willReturn($comment);

        $response = $this->editCommentAction($request, 1, 1, 1);

        $response->shouldBeAnInstanceOf('Symfony\Component\HttpFoundation\Response');
        $response->getStatusCode()->shouldReturn(200);
    }

    function it_should_remove_editorial_comment(Request $request, EditorialComment $comment, $editorialCommentRepository, $query)
    {
        $editorialCommentRepository->getOneByArticleAndCommentId(1,1,1)->willReturn($query);
        $query->getOneOrNullResult()->willReturn($comment);

        $response = $this->removeCommentAction($request, 1, 1, 1);

        $response->shouldBeAnInstanceOf('Symfony\Component\HttpFoundation\Response');
        $response->getStatusCode()->shouldReturn(204);
    }
}
