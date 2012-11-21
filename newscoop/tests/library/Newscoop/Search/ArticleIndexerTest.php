<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Search;

use Newscoop\Entity\Article;
use Newscoop\Entity\Language;

/**
 */
class ArticleIndexerTest extends \TestCase
{
    /** @var Newscoop\Search\ArticleIndexer */
    protected $indexer;

    public function setUp()
    {
        $this->em = $this->setUpOrm(
            'Newscoop\Entity\Article',
            'Newscoop\Entity\Language',
            'Newscoop\Entity\Author',
            'Newscoop\Entity\Topic'
        );

        $this->index = $this->getMock('Newscoop\Search\Index');
        $this->indexer = new ArticleIndexer($this->em, $this->index);

        $this->language = new Language();
        $this->em->persist($this->language);
        $this->em->flush();
    }

    public function testInstance()
    {
        $this->assertInstanceOf('Newscoop\Search\ArticleIndexer', $this->indexer);
    }

    public function testUpdateIndexEmpty()
    {
        $this->index->expects($this->never())
            ->method('add');

        $this->index->expects($this->never())
            ->method('delete');

        $this->index->expects($this->once())
            ->method('commit');

        $this->indexer->updateIndex();
    }

    public function testUpdateIndexNotPublished()
    {
        $article = new Article(1, $this->language);
        $this->em->persist($article);
        $this->em->flush();

        $this->index->expects($this->never())
            ->method('add');

        $this->index->expects($this->once())
            ->method('delete')
            ->with($this->isInstanceOf('Newscoop\View\ArticleView'));

        $this->indexer->updateIndex();
    }

    public function testUpdateIndexPublished()
    {
        $article = new Article(1, $this->language);
        $article->publish();
        $this->em->persist($article);
        $this->em->flush();

        $this->index->expects($this->once())
            ->method('add')
            ->with($this->isInstanceOf('Newscoop\View\ArticleView'));

        $this->indexer->updateIndex();
    }

    public function testUpdateIndexArticleDates()
    {
        $article = new Article(1, $this->language);
        $this->em->persist($article);
        $this->em->flush();

        $view = $article->getView();
        $this->assertNotNull($view->updated);
        $this->assertNull($view->indexed);

        $this->indexer->updateIndex();

        $this->em->clear();
        $article = $this->em->getRepository('Newscoop\Entity\Article')->findOneByNumber(1);

        $this->assertNotNull($article->getView()->indexed);
        $this->assertEquals($view->updated, $article->getView()->updated);
    }

    public function testReset()
    {
        $article = new Article(1, $this->language);
        $this->em->persist($article);
        $this->em->flush();

        $this->indexer->updateIndex();

        $articles = $this->em->getRepository('Newscoop\Entity\Article')->getIndexBatch();
        $this->assertEquals(0, count($articles));

        $this->indexer->reset();

        $articles = $this->em->getRepository('Newscoop\Entity\Article')->getIndexBatch();
        $this->assertEquals(1, count($articles));
    }
}
