<?php

namespace spec\Newscoop\GimmeBundle\Serializer\Topic;

use PhpSpec\ObjectBehavior;
use Newscoop\NewscoopBundle\Entity\Topic;
use JMS\Serializer\JsonSerializationVisitor;
use Doctrine\ORM\EntityManager;
use Newscoop\NewscoopBundle\Entity\Repository\TopicRepository;

class TopicPathHandlerSpec extends ObjectBehavior
{
    public function let($die, EntityManager $entityManager, TopicRepository $topicRepository)
    {
        $entityManager->getRepository('Newscoop\NewscoopBundle\Entity\Topic')->willReturn($topicRepository);

        $this->beConstructedWith($entityManager);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Newscoop\GimmeBundle\Serializer\Topic\TopicPathHandler');
    }

    public function it_should_return_a_path_of_the_topic(JsonSerializationVisitor $visitor, Topic $topic, $topicRepository)
    {
        $topic->getId()->willReturn(1);
        $topic->getTitle()->willReturn("Test topic");
        $topic->getParent()->willReturn("Parent topic");

        $topicRepository->getReadablePath($topic)->willReturn(" / Parent topic / Test topic");

        $type = array(
            'name' => "topic_path",
            'params' => array()
        );

        $this->serializeToJson($visitor, $topic, $type)->shouldReturn(" / Parent topic / Test topic");
    }

    public function it_should_return_null_when_no_language(JsonSerializationVisitor $visitor, Topic $topic)
    {
        $type = array(
            'name' => "topic_path",
            'params' => array()
        );

        $this->serializeToJson($visitor, null, $type)->shouldReturn(null);
    }
}
