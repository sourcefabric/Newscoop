<?php

namespace spec\Newscoop\NewscoopBundle\Controller;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DependencyInjection\Container;
use Newscoop\Entity\Repository\PublicationRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Translation\Translator;
use Symfony\Bundle\FrameworkBundle\Templating\DelegatingEngine;
use Newscoop\Entity\User;
use Newscoop\Entity\Publication;
use Newscoop\Entity\Resource;
use Newscoop\Services\UserService;
use Newscoop\NewscoopBundle\Form\Type\PublicationType;
use Newscoop\NewscoopBundle\Form\Type\RemovePublicationType;
use Doctrine\ORM\AbstractQuery;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Newscoop\Service\Implementation\ThemeManagementServiceLocal;

class BackendPublicationsControllerSpec extends ObjectBehavior
{
    public function let(
        Container $container,
        EntityManager $entityManager,
        Request $request,
        User $user,
        UserService $userService,
        AbstractQuery $query,
        SecurityContext $security,
        TokenInterface $token,
        Translator $translator,
        PublicationRepository $publicationRepository,
        \Newscoop\Entity\Repository\IssueRepository $issueRepository,
        \Newscoop\Entity\Repository\SectionRepository $sectionRepository,
        \Newscoop\Entity\Repository\ArticleRepository $articleRepository,
        DelegatingEngine $templating,
        Publication $publication,
        ThemeManagementServiceLocal $themeManagementServiceLocal,
        \Symfony\Component\Form\FormFactory $formFactory,
        \Symfony\Component\Form\Form $form,
        \Symfony\Component\Form\FormView $formView,
        \Newscoop\Services\CacheService $cacheService,
        \Symfony\Component\HttpFoundation\Session\Session $session,
        \Symfony\Component\HttpFoundation\Session\Flash\FlashBag $flashBag,
        \Symfony\Bundle\FrameworkBundle\Routing\Router $router,
        \Doctrine\Common\Collections\ArrayCollection $arrayCollection
    ) {
        $container->get('em')->willReturn($entityManager);
        $container->get('request')->willReturn($request);

        $security->getToken()->willReturn($token);
        $container->get('security.context')->willReturn($security);
        $container->has('security.context')->willReturn(true);
        $container->get('user')->willReturn($userService);
        $userService->getCurrentUser()->willReturn($user);
        $container->get('translator')->willReturn($translator);
        $container->get('templating')->willReturn($templating);
        $container->get('form.factory')->willReturn($formFactory);
        $container->get('newscoop.cache')->willReturn($cacheService);
        $container->get('session')->willReturn($session);
        $container->get('router')->willReturn($router);

        $entityManager->getRepository('Newscoop\Entity\Publication')->willReturn($publicationRepository);
        $entityManager->getRepository('Newscoop\Entity\Issue')->willReturn($issueRepository);
        $entityManager->getRepository('Newscoop\Entity\Section')->willReturn($sectionRepository);
        $entityManager->getRepository('Newscoop\Entity\Article')->willReturn($articleRepository);
        $entityManager->flush(Argument::any())->willReturn(true);
        $entityManager->persist(Argument::any())->willReturn(true);
        $entityManager->remove(Argument::any())->willReturn(true);
        $entityManager->createQuery(Argument::any())->willReturn($query);
        $publicationRepository->getPublications()->willReturn($query);
        $issueRepository->getIssuesCountForPublication(Argument::type('int'))->willReturn($query);
        $sectionRepository->getSectionsCountForPublication(Argument::type('int'))->willReturn($query);
        $articleRepository->getArticlesCountForPublication(Argument::type('int'))->willReturn($query);
        $session->getFlashBag()->willReturn($flashBag);

        $publication->getId()->willReturn(1);
        $publication->getIssues()->willReturn($arrayCollection);
        $publication->getDefaultAlias()->willReturn('newscoop.dev');
        $publication->setDefaultAlias(Argument::any())->willReturn($publication);

        $themeManagementServiceLocal->getThemes(1)->willReturn(array());

        $this->setContainer($container);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Newscoop\NewscoopBundle\Controller\BackendPublicationsController');
    }

    function it_should_list_publications($request, $user, $templating)
    {
        $user->hasPermission('ManagePub')->willReturn(true);
        $response = new Response();

        $templating->renderResponse(
            'NewscoopNewscoopBundle:BackendPublications:index.html.twig',
            Argument::type('array'),
            null
        )
        ->willReturn($response);

        $response = $this->indexAction($request);
        $response->shouldBeAnInstanceOf('Symfony\Component\HttpFoundation\Response');
        $response->getStatusCode()->shouldReturn(200);
    }

    function it_should_edit_and_create_publication(
        $request,
        $publication,
        $user,
        $templating,
        $form,
        $formFactory,
        $formView,
        $router,
        Resource $resource
    ){
        $user->hasPermission('ManagePub')->willReturn(true);
        $response = new Response();

        $templating->renderResponse(
            'NewscoopNewscoopBundle:BackendPublications:edit.html.twig',
            Argument::type('array'),
            null
        )
        ->willReturn($response);

        $formFactory->create(
            Argument::type('\Newscoop\NewscoopBundle\Form\Type\PublicationType'),
            Argument::type('\Newscoop\Entity\Publication'),
            Argument::any()
        )->willReturn($form);
        $form->createView()->willReturn($formView);

        $response = $this->editAction($request, $publication);
        $response->shouldBeAnInstanceOf('Symfony\Component\HttpFoundation\Response');
        $response->getStatusCode()->shouldReturn(200);

        $form->handleRequest($request)->willReturn(true);
        $form->isValid()->willReturn(true);
        $router->generate('newscoop_newscoop_publications_index', Argument::cetera())->willReturn('http://newscoop.dev');
        $request->getMethod()->willReturn('POST');

        // Test POST edit request
        $response = $this->editAction($request, $publication);
        $response->shouldBeAnInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse');
        $response->getStatusCode()->shouldReturn(302);

        // Test GET create request
        $request->getMethod()->willReturn('GET');
        $response = $this->createAction($request);
        $response->shouldBeAnInstanceOf('Symfony\Component\HttpFoundation\Response');
        $response->getStatusCode()->shouldReturn(200);

        // Test POST create request
        $request->getMethod()->willReturn('POST');
        $form->getData()->willReturn($publication);
        $response = $this->createAction($request);
        $response->shouldBeAnInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse');
        $response->getStatusCode()->shouldReturn(302);
    }

    function it_should_remove_publication(
        $request,
        $publication,
        $user,
        $templating,
        $form,
        $formFactory,
        $formView,
        $router,
        $query
    )
    {
        $user->hasPermission('ManagePub')->willReturn(true);
        $response = new Response();

        $templating->renderResponse(
            'NewscoopNewscoopBundle:BackendPublications:remove.html.twig',
            Argument::type('array'),
            null
        )
        ->willReturn($response);

        $formFactory->create(
            Argument::type('\Newscoop\NewscoopBundle\Form\Type\RemovePublicationType'),
            Argument::type('\Newscoop\Entity\Publication'),
            Argument::any()
        )->willReturn($form);
        $form->createView()->willReturn($formView);

        $publication->getId()->willReturn(1);
        $query->setParameters(Argument::type('array'))->willReturn($query);
        $query->getSingleScalarResult()->willReturn(0);
        $query->getResult()->willReturn(true);

        $form->handleRequest($request)->willReturn(true);
        $form->isValid()->willReturn(true);
        $router->generate('newscoop_newscoop_publications_index', Argument::cetera())->willReturn('http://newscoop.dev');
        $request->getMethod()->willReturn('POST');

        // Test GET remove request
        $request->getMethod()->willReturn('GET');
        $response = $this->removeAction($request, $publication);
        $response->shouldBeAnInstanceOf('Symfony\Component\HttpFoundation\Response');
        $response->getStatusCode()->shouldReturn(200);

        // Test POST remove request
        $request->getMethod()->willReturn('POST');
        $form->getData()->willReturn($publication);
        $response = $this->removeAction($request, $publication);
        $response->shouldBeAnInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse');
        $response->getStatusCode()->shouldReturn(302);
    }
}
