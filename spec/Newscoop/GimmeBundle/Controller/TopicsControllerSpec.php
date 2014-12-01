<?php

namespace spec\Newscoop\GimmeBundle\Controller;

use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\Container;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\AbstractQuery;
use Newscoop\Gimme\PaginatorService;
use Newscoop\NewscoopBundle\Entity\Repository\TopicRepository;
use Newscoop\NewscoopBundle\Entity\Topic;
use Symfony\Component\HttpFoundation\ParameterBag;
use Doctrine\Common\Collections\ArrayCollection;

class TopicsControllerSpec extends ObjectBehavior
{
    public function let(
        Container $container,
        EntityManager $entityManager,
        Request $request,
        AbstractQuery $query,
        TopicRepository $topicRepository,
        PaginatorService $paginator,
        Topic $topic
    ) {
        $container->get('em')->willReturn($entityManager);
        $container->get('request')->willReturn($request);
        $container->get('newscoop.paginator.paginator_service')->willReturn($paginator);

        $this->setContainer($container);

        $entityManager->getRepository('Newscoop\NewscoopBundle\Entity\Topic')->willReturn($topicRepository);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Newscoop\GimmeBundle\Controller\TopicsController');
        $this->shouldImplement('FOS\RestBundle\Controller\FOSRestController');
    }

    public function its_getTopicsAction_should_return_list_of_all_topics($request, $topicRepository, $query, $topic, $paginator)
    {
        $topic->getId()->willReturn(1);
        $topic->getTitle()->willReturn('test topic');
        $topic->getRoot()->willReturn(1);
        $topic->getParent()->willReturn(null);
        $topics = array('items' => array(
            $topic
        ));

        $topicRepository->getTopics()->willReturn($query);

        $paginator->paginate($query, array(
            'distinct' => false
        ))->willReturn($topics);

        $this->getTopicsAction($request)->shouldReturn($topics);
    }

    public function its_getTopicsAction_should_throw_NotFoundHttpException_when_no_result($request, $topicRepository)
    {
        $topicRepository->getTopics()->willReturn(null);

        $this
            ->shouldThrow('Symfony\Component\HttpKernel\Exception\NotFoundHttpException')
            ->during('getTopicsAction', array($request));
    }

    public function its_getTopicByIdAction_should_return_single_topic($request, $topicRepository, $query, $topic)
    {
        $id = 1;
        $topic->getId()->willReturn(1);
        $topic->getTitle()->willReturn('test topic');
        $topic->getRoot()->willReturn(1);
        $topic->getParent()->willReturn(null);
        $topic->getLeft()->willReturn(2);
        $topic->getRight()->willReturn(4);
        $topicRepository->getSingleTopicQuery($id)->willReturn($query);
        $query->getOneOrNullResult()->willReturn($topic);
        $this->getTopicByIdAction($id)->shouldReturn($topic);
    }

    public function its_getTopicByIdAction_should_throw_NotFoundHttpException_when_no_result($request, $topicRepository, $query)
    {
        $topicRepository->getSingleTopicQuery(1)->willReturn($query);
        $query->getOneOrNullResult()->willReturn(null);
        $this
            ->shouldThrow('Symfony\Component\HttpKernel\Exception\NotFoundHttpException')
            ->during('getTopicByIdAction', array(1));
    }

    public function its_searchTopicsAction_should_return_array_of_topics_by_given_search_criteria(ParameterBag $parameterBag, $topic, $paginator, $query, $request, $topicRepository)
    {
        $searchPhrase = 'topic1';
        $parameterBag->get("query", "")->willReturn($searchPhrase);
        $request->query = $parameterBag;
        $topicRepository->searchTopicsQuery($searchPhrase)->willReturn($query);
        $topic->getId()->willReturn(1);
        $topic->getTitle()->willReturn('topic1');
        $topic->getRoot()->willReturn(1);
        $topic->getParent()->willReturn(null);
        $topic->getTranslations()->willReturn(new ArrayCollection());
        $topics = array(
            'id' => 1,
            'title' => 'topic1',
            'items' => array(
                $topic
            )
        );

        $paginator->paginate($query, array(
            'distinct' => false
        ))->willReturn($topics);

        $this->searchTopicsAction($request)->shouldReturn($topics);
    }
}
