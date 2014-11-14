<?php

namespace spec\Newscoop\GimmeBundle\Serializer\Article;

use PhpSpec\ObjectBehavior;
use JMS\Serializer\JsonSerializationVisitor;
use Newscoop\Entity\Publication;

class PublicationHandlerSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Newscoop\GimmeBundle\Serializer\Article\PublicationHandler');
    }

    public function it_should_return_a_simple_publication_array(JsonSerializationVisitor $visitor, Publication $publication)
    {
        $publication->getId()->willReturn(1);
        $publication->getName()->willReturn("The New Custodian");
        $type = array(
            'name' => "article_publication",
            'params' => array()
        );
        $this->serializeToJson($visitor, $publication, $type)->shouldReturn(array(
            'id' => 1,
            'name' => "The New Custodian"
        ));
    }

    public function it_should_return_null_when_no_publication(JsonSerializationVisitor $visitor, Publication $publication)
    {
        $type = array(
            'name' => "article_publication",
            'params' => array()
        );

        $this->serializeToJson($visitor, null, $type)->shouldReturn(null);
    }
}
