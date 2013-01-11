<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Search;

use Exception;
use Doctrine\ORM\EntityManager;
use Language;
use Newscoop\View\ArticleView;

/**
 * Article Indexer
 */
class ArticleIndexer
{
    /**
     * @var Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var Newscoop\Search\Index
     */
    private $index;

    /**
     * @param Doctrine\ORM\EntityManager $em
     * @param Newscoop\Search\Index $index
     */
    public function __construct(EntityManager $em, Index $index)
    {
        $this->em = $em;
        $this->index = $index;
    }

    /**
     * Update index to reflect article changes
     *
     * @param int $limit
     * @return void
     */
    public function updateIndex($limit = 50)
    {
        foreach ($this->getArticleRepository()->getIndexBatch($limit) as $article) {
            $article->setIndexed();
            $articleView = $article->getView();
            if ($articleView->published !== null) {
                $this->index->add($articleView);
            } elseif ($articleView->number) {
                $this->index->delete($articleView);
            }
        }

        $this->index->commit();
        $this->em->flush();
    }

    /**
     * Reset index
     *
     * @return void
     */
    public function reset()
    {
        $this->getArticleRepository()->resetIndex();
    }

    /**
     * Handle article.delete event
     *
     * @param Newscoop\Event
     * @return void
     */
    public function update($event)
    {
        $article = $event->getSubject();
        $language = new Language($article->getLanguageId());

        $this->index->delete(
            new ArticleView(
                array(
                    'number' => $article->getArticleNumber(),
                    'language' => $language->getCode(),
                )
            )
        );

        try {
            $this->index->commit();
        } catch (Exception $e) {
            // ignore
        }
    }

    /**
     * Get article repository
     *
     * @return Newscoop\Entity\Repository\ArticleRepository
     */
    private function getArticleRepository()
    {
        return $this->em->getRepository('Newscoop\Entity\Article');
    }
}
