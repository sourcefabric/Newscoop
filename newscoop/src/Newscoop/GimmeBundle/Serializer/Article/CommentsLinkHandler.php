<?php
/**
 * @package Newscoop\Gimme
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\GimmeBundle\Serializer\Article;

use JMS\Serializer\JsonSerializationVisitor;

/**
 * Create simple Author object from Newscoop\Entity\Author object.
 */
class CommentsLinkHandler
{
    protected $router;

    public function __construct($router)
    {
        $this->router = $router;
    }

    public function serializeToJson(JsonSerializationVisitor $visitor, $data, $type)
    {
        return $this->router->generate('newscoop_gimme_comments_getcommentsforarticle', array('number' => $data->number, 'language' => $data->language), true);
    }
}
