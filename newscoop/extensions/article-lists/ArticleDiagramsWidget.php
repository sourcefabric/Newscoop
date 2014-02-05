<?php
/**
 * @package Newscoop
 *
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.sourcefabric.org
 */

/**
 * @title Articles diagrams
 * @description Render diagrams for articles statistics
 */
class ArticleDiagramsWidget extends Widget
{
    /** @setting */
    protected $article_type = "news";

    /** @setting */
    protected $api_prefix = "api";


    public function render()
    {
        include_once dirname(__FILE__) . '/diagrams.phtml';
    }
}
