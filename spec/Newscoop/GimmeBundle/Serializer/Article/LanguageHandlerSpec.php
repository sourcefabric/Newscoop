<?php

namespace spec\Newscoop\GimmeBundle\Serializer\Article;

use PhpSpec\ObjectBehavior;
use Newscoop\Entity\Language;
use JMS\Serializer\JsonSerializationVisitor;

class LanguageHandlerSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Newscoop\GimmeBundle\Serializer\Article\LanguageHandler');
    }

    public function it_should_return_a_simple_language_array(JsonSerializationVisitor $visitor, Language $language)
    {
        $language->getId()->willReturn(1);
        $language->getName()->willReturn("English");
        $language->getCode()->willReturn("en");
        $language->getRFC3066bis()->willReturn("en-US");
        $type = array(
            'name' => "article_language",
            'params' => array()
        );

        $this->serializeToJson($visitor, $language, $type)->shouldReturn(array(
            'id' => 1,
            'name' => "English",
            'code' => "en",
            'RFC3066bis' => "en-US",
        ));
    }

    public function it_should_return_null_when_no_language(JsonSerializationVisitor $visitor, Language $language)
    {
        $type = array(
            'name' => "article_language",
            'params' => array()
        );

        $this->serializeToJson($visitor, null, $type)->shouldReturn(null);
    }
}
