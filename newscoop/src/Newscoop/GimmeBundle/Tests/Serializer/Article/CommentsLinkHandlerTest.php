<?php
/**
 * @package Newscoop\Gimme
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\GimmeBundle\Tests\Serializer\Article;

use Newscoop\GimmeBundle\Tests\ContainerAwareUnitTestCase;
use Newscoop\GimmeBundle\Serializer\Article\CommentsLinkHandler;
use Newscoop\Entity\Article;
use JMS\SerializerBundle\Serializer\YamlSerializationVisitor;
use JMS\SerializerBundle\Serializer\Naming\SerializedNameAnnotationStrategy;
use JMS\SerializerBundle\Serializer\Naming\CamelCaseNamingStrategy;

class CommentsLinkHandlerTest extends ContainerAwareUnitTestCase
{   
    protected $article;
    protected $commentsHandler;
    protected $ymlSerializationVisitor;

    protected function setUp()
    {
        $this->article = new Article(10, new \Newscoop\Entity\Language());
        $this->commentsHandler = new CommentsLinkHandler($this->get('router'));
        $this->ymlSerializationVisitor = new YamlSerializationVisitor(
            new SerializedNameAnnotationStrategy(new CamelCaseNamingStrategy()),
            array()
        );
    }

    public function testSerialize ()
    {   
        $visited = true;
        $authorHandlerResult = $this->commentsHandler->serialize(
            $this->ymlSerializationVisitor, 
            $this->article,
            get_class($this->article),
            $visited
        );

        $this->assertTrue($authorHandlerResult);
    }

    public function testfailOnWrongClass ()
    {   
        $visited = true;
        $authorHandlerResult = $this->commentsHandler->serialize(
            $this->ymlSerializationVisitor, 
            new \stdClass(),
            get_class(new \stdClass()),
            $visited
        );

        $this->assertFalse($authorHandlerResult);
    }
}