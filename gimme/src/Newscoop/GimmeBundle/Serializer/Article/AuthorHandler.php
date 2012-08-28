<?php
/**
 * @package Newscoop\Gimme
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\GimmeBundle\Serializer\Article;  

use Symfony\Component\Yaml\Inline;
use JMS\SerializerBundle\Serializer\YamlSerializationVisitor;
use JMS\SerializerBundle\Serializer\GenericSerializationVisitor;
use JMS\SerializerBundle\Serializer\JsonSerializationVisitor;
use JMS\SerializerBundle\Serializer\XmlSerializationVisitor;
use JMS\SerializerBundle\Serializer\VisitorInterface;
use JMS\SerializerBundle\Serializer\Handler\SerializationHandlerInterface;

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
            return true;
        }

        $articleAuthors = array();

        foreach ($data->getArticleAuthors() as $author) {
            $articleAuthors[] = array(
                'name' => $author->getFullName(),
                'link' => $this->router->generate('newscoop_gimme_authors_getarticle', array('id' => $author->getId()), true)
            );
        }

        $data->setArticleAuthors($articleAuthors);
    }
}