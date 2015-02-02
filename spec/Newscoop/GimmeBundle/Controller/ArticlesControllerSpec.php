<?php

namespace spec\Newscoop\GimmeBundle\Controller;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\Container;
use Newscoop\Entity\Repository\ArticleRepository;
use Newscoop\Entity\Repository\LanguageRepository;
use Newscoop\Entity\Repository\ArticleTypeRepository;
use Newscoop\Entity\Repository\AuthorTypeRepository;
use Newscoop\Entity\Repository\PublicationRepository;
use Newscoop\Entity\Repository\IssueRepository;
use Newscoop\Entity\Repository\SectionRepository;
use Newscoop\Services\ArticleService;
use Newscoop\Services\AuthorService;
use Newscoop\Services\CacheService;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormView;
use Symfony\Component\Routing\Router;
use Newscoop\Entity\User;
use Newscoop\Entity\Article;
use Newscoop\Entity\ArticleType;
use Newscoop\Entity\Publication;
use Newscoop\Entity\Issue;
use Newscoop\Entity\Section;
use Newscoop\Entity\Language;
use Newscoop\Entity\Author;
use Newscoop\Entity\AuthorType;
use Doctrine\ORM\AbstractQuery;
use Newscoop\Services\UserService;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class ArticlesControllerSpec extends ObjectBehavior
{
    public function let(
        ArticleService $articleService,
        AuthorService $authorService,
        CacheService $cacheService,
        Container $container,
        ArticleRepository $articleRepository,
        LanguageRepository $languageRepository,
        ArticleTypeRepository $articleTypeRepository,
        PublicationRepository $publicationRepository,
        IssueRepository $issueRepository,
        SectionRepository $sectionRepository,
        AuthorTypeRepository $authorTypeRepository,
        EntityManager $entityManager,
        Request $request,
        FormFactory $formFactory,
        FormBuilder $formBuilder,
        Form $form,
        FormView $formView,
        User $user,
        UserService $userService,
        Article $article,
        Publication $publication,
        ArticleType $articleType,
        Issue $issue,
        Section $section,
        Language $language,
        Author $author,
        AuthorType $authorType,
        AbstractQuery $query,
        SecurityContext $security,
        TokenInterface $token,
        Router $router
    ) {
        $container->get('em')->willReturn($entityManager);
        $container->get('request')->willReturn($request);
        $container->get('user')->willReturn($userService);
        $container->get('form.factory')->willReturn($formFactory);
        $container->get('newscoop_newscoop.article_service')->willReturn($articleService);
        $container->get('author')->willReturn($authorService);
        $container->get('newscoop.cache')->willReturn($cacheService);
        $container->get('router')->willReturn($router);

        $formBuilder->getForm(Argument::cetera())->willReturn($form);
        $formFactory->create(Argument::cetera())->willReturn($form);
        $form->createView()->willReturn($formView);
        $form->handleRequest(Argument::cetera())->willReturn(true);
        $form->isValid()->willReturn(true);

        $security->getToken()->willReturn($token);
        $container->get('security.context')->willReturn($security);
        $container->has('security.context')->willReturn(true);

        $this->setContainer($container);

        $entityManager->getRepository('Newscoop\Entity\Article')->willReturn($articleRepository);
        $entityManager->getRepository('Newscoop\Entity\Language')->willReturn($languageRepository);
        $entityManager->getRepository('Newscoop\Entity\ArticleType')->willReturn($articleTypeRepository);
        $entityManager->getRepository('Newscoop\Entity\Publication')->willReturn($publicationRepository);
        $entityManager->getRepository('Newscoop\Entity\Issue')->willReturn($issueRepository);
        $entityManager->getRepository('Newscoop\Entity\Section')->willReturn($sectionRepository);
        $entityManager->getRepository('Newscoop\Entity\AuthorType')->willReturn($authorTypeRepository);
        $articleRepository->getArticle(Argument::cetera())->willReturn($query);
        $entityManager->flush(Argument::any())->willReturn(true);
        $userService->getCurrentUser()->willReturn($user);
        $number = 64;
        $language = "en";

    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Newscoop\GimmeBundle\Controller\ArticlesController');
        $this->shouldImplement('FOS\RestBundle\Controller\FOSRestController');
    }

    public function its_lockUnlockArticle_should_lock_article($request, $article, $query, $number, $language, $user, $token, $security)
    {
        $now = new \DateTime();
        $query->getOneOrNullResult()->willReturn($article);
        $request->getMethod()->willReturn('POST');
        $article->isLocked()->willReturn(false);
        $user = new User('jhon.doe@example.com');
        $user->setUsername('doe');
        $security->getToken()->willReturn($token);
        $token->getUser()->willReturn($user);
        $article->setLockUser($user)->willReturn(null);
        $article->setLockTime($now)->willReturn(null);
        $response = $this->lockUnlockArticle($request, $number, $language);
        $response->shouldBeAnInstanceOf('Symfony\Component\HttpFoundation\Response');
        $response->getStatusCode()->shouldReturn(200);
    }

    public function its_lockUnlockArticle_should_unlock_article($request, $article, $query, $number, $language)
    {
        $article->isLocked()->willReturn(true);
        $request->getMethod()->willReturn('DELETE');
        $article->setLockUser()->willReturn(null);
        $article->setLockTime()->willReturn(null);
        $query->getOneOrNullResult()->willReturn($article);
        $response = $this->lockUnlockArticle($request, $number, $language);
        $response->shouldBeAnInstanceOf('Symfony\Component\HttpFoundation\Response');
        $response->getStatusCode()->shouldReturn(204);
    }

    public function its_lockUnlockArticle_should_return_status_code_403_when_setting_the_same_status_while_locking($request, $article, $query, $number, $language)
    {
        $article->isLocked()->willReturn(true);
        $request->getMethod()->willReturn('POST');
        $query->getOneOrNullResult()->willReturn($article);
        $response = $this->lockUnlockArticle($request, $number, $language);
        $response->shouldBeAnInstanceOf('Symfony\Component\HttpFoundation\Response');
        $response->getStatusCode()->shouldReturn(403);
    }

    public function its_deleteUnlockArticle_should_return_status_code_403_when_setting_the_same_status_while_unlocking($request, $article, $query, $number, $language)
    {
        $article->isLocked()->willReturn(false);
        $request->getMethod()->willReturn('DELETE');
        $query->getOneOrNullResult()->willReturn($article);
        $response = $this->lockUnlockArticle($request, $number, $language);
        $response->shouldBeAnInstanceOf('Symfony\Component\HttpFoundation\Response');
        $response->getStatusCode()->shouldReturn(403);
    }

    public function it_should_create_new_article(
        $request,
        $user,
        $form,
        $languageRepository,
        $language,
        $articleTypeRepository,
        $articleType,
        $publicationRepository,
        $publication,
        $issueRepository,
        $issue,
        $sectionRepository,
        $section,
        $author,
        $authorTypeRepository,
        $authorType,
        $articleService,
        $article
    ) {
        $user->hasPermission('AddArticle')->willReturn(true);
        $user->getAuthor()->willReturn($author);
        $form->getData()->willReturn(array(
            'language' => 1,
            'type' => 'news',
            'publication' => 1,
            'issue' => null,
            'section' => null,
        ));
        $languageRepository->findOneBy(Argument::any())->willReturn($language);
        $articleTypeRepository->findOneBy(Argument::any())->willReturn($articleType);
        $publicationRepository->findOneBy(Argument::any())->willReturn($publication);
        $issueRepository->findOneBy(Argument::any())->willReturn($issue);
        $sectionRepository->findOneBy(Argument::any())->willReturn($section);
        $authorTypeRepository->findOneBy(Argument::any())->willReturn($authorType);

        $articleService->createArticle(Argument::cetera())->willReturn($article);

        $this->createArticleAction($request);
    }

    public function it_should_update_article($request, $user, $query, $article, $form, $articleService)
    {
        $user->hasPermission('AddArticle')->willReturn(true);
        $query->getOneOrNullResult()->willReturn($article);

        $form->getData()->willReturn(array(
            'language' => 1,
            'type' => 'news',
            'publication' => 1,
            'issue' => null,
            'section' => null,
            'fields' => array(
                'lead' => 'sample lead',
                'content' => 'sample content'
            )
        ));

        $articleService->updateArticle(Argument::cetera())->willReturn($article);

        $this->patchArticleAction($request, 1, 'en');
    }
}
