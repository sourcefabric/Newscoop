<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Search;

use Newscoop\View\ArticleView;

/**
 * Index Interface
 */
interface Index
{
    /**
     * Add given article to index
     *
     * @param Newscoop\View\ArticleView $article
     * @return void
     */
    public function add(ArticleView $article);

    /**
     * Delete given article from index
     *
     * @param Newscoop\View\ArticleView $article
     * @return void
     */
    public function delete(ArticleView $article);

    /**
     * Commit changes to index
     *
     * @return void
     */
    public function commit();

    /**
     * Find article numbers for given query
     *
     * @param Newscoop\Search\Query $query
     * @return object
     */
    public function find(Query $query);
}
