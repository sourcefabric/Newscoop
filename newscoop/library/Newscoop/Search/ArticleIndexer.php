<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Search;

use Doctrine\ORM\EntityManager;

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
     * @return void
     */
    public function updateIndex()
    {
        foreach ($this->getArticleRepository()->getIndexBatch() as $article) {
            $article->setIndexed();
            $articleView = $article->getView();
            if ($articleView->published !== null) {
                $this->index->add($articleView);
            } else {
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
        $query = $this->em->createQuery('UPDATE Newscoop\Entity\Article a SET a.indexed = null, a.updated = a.updated');
        $query->execute();
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
