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
class AuthorHandler implements SerializationHandlerInterface
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

        if (count($data->getArticleAuthors()) == 0) {
            $data->setArticleAuthors(null);
            return null;
        }

        $articleAuthors = array();
        foreach ($data->getArticleAuthors() as $author) {
            $articleAuthors[] = array(
                'name' => $author->getFullName(),
                'link' => $this->router->generate('newscoop_gimme_authors_getarticle', array('id' => $author->getId()), true)
            );
        }

        $data->setArticleAuthors($articleAuthors);

        return true;
    }
}