<?php

namespace spec\Newscoop\GimmeBundle\Controller;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\Container;
use Newscoop\Entity\Repository\ArticleRepository;
use Doctrine\ORM\EntityManager;
use Newscoop\Entity\User;
use Newscoop\Entity\Article;
use Doctrine\ORM\AbstractQuery;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class ArticlesControllerSpec extends ObjectBehavior
{
    public function let(
        Container $container,
        ArticleRepository $articleRepository,
        EntityManager $entityManager,
        Request $request,
        User $user,
        Article $article,
        AbstractQuery $query,
        SecurityContext $security,
        TokenInterface $token
    ) {
        $container->get('em')->willReturn($entityManager);
        $container->get('request')->willReturn($request);

        $security->getToken()->willReturn($token);
        $container->get('security.context')->willReturn($security);
        $container->has('security.context')->willReturn(true);

        $this->setContainer($container);

        $entityManager->getRepository('Newscoop\Entity\Article')->willReturn($articleRepository);
        $articleRepository->getArticle(Argument::cetera())->willReturn($query);
        $entityManager->flush(Argument::any())->willReturn(true);
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
        $query->getOneOrNullResult()->willReturn($article);
        $request->getMethod()->willReturn('POST');
        $article->isLocked()->willReturn(false);
        $user = new User('jhon.doe@example.com');
        $user->setUsername('doe');
        $security->getToken()->willReturn($token);
        $token->getUser()->willReturn($user);
        $article->setLockUser($user)->willReturn(null);
        $article->setLockTime(new \DateTime())->willReturn(null);
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
        $response->getStatusCode()->shouldReturn(200);
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
}
