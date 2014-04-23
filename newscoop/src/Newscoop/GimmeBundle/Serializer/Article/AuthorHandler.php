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
class AuthorHandler
{
    protected $router;

    public function __construct($router)
    {
        $this->router = $router;
    }

    public function serializeToJson(JsonSerializationVisitor $visitor, $articleAuthors, $type)
    {
        if (count($articleAuthors) == 0) {
            return null;
        }

        $simpleArticleAuthors = array();
        foreach ($articleAuthors as $author) {
            $simpleArticleAuthors[] = array(
                'name' => $author->getFullName(),
                'link' => $this->router->generate('newscoop_gimme_authors_getauthor', array('id' => $author->getId()), true)
            );
        }

        return $simpleArticleAuthors;
    }
}
