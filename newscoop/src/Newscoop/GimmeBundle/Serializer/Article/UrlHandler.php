<?php
/**
 * @package Newscoop\Gimme
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2014 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\GimmeBundle\Serializer\Article;

use JMS\Serializer\JsonSerializationVisitor;

/**
 * Return url for article
 */
class UrlHandler
{
    protected $linkService;

    public function __construct($linkService)
    {
        $this->linkService = $linkService;
    }

    public function getArticleUrl(JsonSerializationVisitor $visitor, $data, $type)
    {
        $articleUrl = $this->linkService->getLink($data);

        return $articleUrl;
    }
}
