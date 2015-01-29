<?php

namespace spec\Newscoop\Services;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Doctrine\ORM\EntityManager;
use Newscoop\Entity\RelatedArticles;
use Newscoop\Entity\RelatedArticle;
use Newscoop\Entity\Article;
use Newscoop\Entity\Repository\RelatedArticleRepository;
use Newscoop\Entity\Repository\RelatedArticlesRepository;
use Doctrine\ORM\AbstractQuery;

class RelatedArticlesServiceSpec extends ObjectBehavior
{
    
    public function let(
        EntityManager $em,
        Article $article,
        Article $articleToRemove,
        Article $articleToAdd,
        RelatedArticles $relatedArticles,
        RelatedArticle $relatedArticle,
        RelatedArticleRepository $relatedArticleRepository,
        RelatedArticlesRepository $relatedArticlesRepository,
        AbstractQuery $query,
        AbstractQuery $getRelatedArticleQuery,
        AbstractQuery $getAllArticlesQuery,
        \Doctrine\DBAL\Connection $connection

    ){
        $article->getNumber()->willReturn(1);
        $articleToRemove->getNumber()->willReturn(2);
        $articleToAdd->getNumber()->willReturn(2);
        $em->persist(Argument::any())->willReturn(true);
        $em->flush(Argument::any())->willReturn(true);
        $em->remove(Argument::any())->willReturn(true);
        $em->getConnection()->willReturn($connection);

        $em->getRepository('Newscoop\Entity\RelatedArticle')->willReturn($relatedArticleRepository);
        $em->getRepository('Newscoop\Entity\RelatedArticles')->willReturn($relatedArticlesRepository);

        $relatedArticlesRepository->getRelatedArticles(Argument::type('integer'))->willReturn($query);

        $relatedArticleRepository->getAllArticles(Argument::type('\Newscoop\Entity\RelatedArticles'))->willReturn($getAllArticlesQuery);
        $getAllArticlesQuery->getResult()->willReturn(array());
        $relatedArticleRepository->getRelatedArticle(Argument::type('\Newscoop\Entity\RelatedArticles'), Argument::type('integer'))->willReturn($query);
        $relatedArticleRepository->getRelatedArticle(Argument::type('integer'))->willReturn($getRelatedArticleQuery);
        $getRelatedArticleQuery->getOneOrNullResult()->willReturn($relatedArticle);

        $relatedArticle->getOrder()->willReturn(Argument::type('integer'));

        $this->beConstructedWith($em);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Newscoop\Services\RelatedArticlesService');
    }

    function it_should_get_related_articles($article)
    {
        $this->getRelatedArticles($article);
    }

    function it_should_remove_related_article($article, $articleToRemove)
    {
        $this->removeRelatedArticle($article, $articleToRemove)->shouldReturn(true);
    }

    function it_should_add_related_article($article, $articleToAdd)
    {
        $this->addArticle($article, $articleToAdd);
    }

    function it_should_reposition_related_article($article, $articleToAdd)
    {
        $this->addArticle($article, $articleToAdd, 2);
    }
}
