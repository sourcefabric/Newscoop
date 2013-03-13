<?php
/**
 * @package Newscoop\Gimme
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\GimmeBundle\Serializer\Article;  

use JMS\SerializerBundle\Serializer\VisitorInterface;
use JMS\SerializerBundle\Serializer\Handler\SerializationHandlerInterface;

/**
 * Create simple Author object from Newscoop\Entity\Author object.
 */
class CommentsLinkHandler implements SerializationHandlerInterface
{
    protected $router;

    public function __construct($router)
    {
        $this->router = $router;
    }

    public function serialize(VisitorInterface $visitor, $data, $type, &$visited)
    {   
        if ($type != 'Newscoop\\Entity\\Article') {
            return false;
        }

        $data->setCommentsLink($this->router->generate('newscoop_gimme_comments_getcommentsforarticle', array('number' => $data->getNumber(), 'language' => $data->getLanguage()->getCode()), true));

        return true;
    }
}