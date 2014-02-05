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
 * Return recommended comments count for article
 */
class CommentsCountHandler
{
    protected $commentService;

    public function __construct($commentService)
    {
        $this->commentService = $commentService;
    }

    public function getRecomendedCommentsCount(JsonSerializationVisitor $visitor, $data, $type)
    {
        $commentsCount = $this->commentService->getCommentCounts(array($data->number), true);

        if (count($commentsCount)) {
            return $commentsCount[0][1];
        }

        return 0;
    }

    public function getCommentsCount(JsonSerializationVisitor $visitor, $data, $type)
    {
        $commentsCount = $this->commentService->getCommentCounts(array($data->number), false, true);

        if (count($commentsCount)) {
            return $commentsCount[0][1];
        }

        return 0;
    }
}
