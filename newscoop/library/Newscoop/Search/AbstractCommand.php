<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Search;

use SimpleXmlElement;
use Newscoop\View\ArticleView;

/**
 * Abstract Command
 */
abstract class AbstractCommand
{
    /**
     * @var Newscoop\View\ArticleView
     */
    protected $article;

    /**
     * @param Newscoop\View\ArticleView $article
     */
    public function __construct(ArticleView $article)
    {
        $this->article = $article;
    }

    /**
     * Update xml
     *
     * @param SimpleXmlElement $xml
     * @return void
     */
    abstract public function update(SimpleXmlElement $xml);
}
