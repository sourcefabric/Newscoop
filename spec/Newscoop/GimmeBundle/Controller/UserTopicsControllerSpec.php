<?php

namespace spec\Newscoop\GimmeBundle\Controller;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Newscoop\Services\UserTopicService;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityRepository;
use Newscoop\Entity\Repository\UserRepository;
use Symfony\Component\DependencyInjection\Container;
use Doctrine\ORM\EntityManager;
use Newscoop\Entity\User;
use Knp\Component\Pager\Paginator;
use Newscoop\Gimme\PaginatorService;
use Symfony\Component\HttpFoundation\ParameterBag;
use Newscoop\NewscoopBundle\Entity\Topic;

class UserTopicsControllerSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Newscoop\GimmeBundle\Controller\UserTopicsController');
        $this->shouldImplement('FOS\RestBundle\Controller\FOSRestController');
    }

    public function let(
        Container $container,
        EntityManager $entityManager,
        Request $request,
        AbstractQuery $query,
        UserTopicService $userTopicService,
        UserRepository $userRepository,
        User $user,
        Paginator $knpPaginator,
        PaginatorService $paginator,
        EntityRepository $repository
    ) {
        $container->get('em')->willReturn($entityManager);
        $container->get('user.topic')->willReturn($userTopicService);
        $container->get('newscoop.paginator.paginator_service')->willReturn($paginator);

        $entityManager->persist(Argument::any())->willReturn(true);
        $entityManager->flush(Argument::any())->willReturn(true);
        $entityManager->remove(Argument::any())->willReturn(true);
        $entityManager->getRepository('Newscoop\Entity\User')->willReturn($repository);
        $user->getId()->willReturn(1);
        $user->getUsername()->willReturn('admin');
        $user->getEmail()->willReturn('foo@bar.com');

        $this->setContainer($container);
    }

    public function its_getUserTopicsAction_should_return_list_of_topics_followed_by_user(
        $request,
        $repository,
        $user,
        $userTopicService,
        $paginator,
        ParameterBag $parameterBag,
        $knpPaginator
    ) {
        $userId = 1;
        $topic = new Topic();
        $topic->setId(10);
        $topic->setTitle('test');
        $topic->setParent(null);
        $repository->findOneBy(array('id' => $userId))->shouldBeCalled()->willReturn($user);
        $topics = array(
            $topic,
        );

        $userTopics = array(
            'items' => $topics,
        );

        $parameterBag->get("language", null)->willReturn(null);
        $request->query = $parameterBag;

        $userTopicService->getTopics($user, null)->shouldBeCalled()->willReturn($topics);
        $paginator->setUsedRouteParams(array("id" => $userId, "language" => null))->willReturn($knpPaginator);
        $paginator->paginate($topics, array(
            'distinct' => false,
        ))->willReturn($userTopics);

        $this->getUserTopicsAction($request, $userId)->shouldReturn($userTopics);
    }

    public function its_getUserTopicsAction_should_return_list_of_topics_by_language_followed_by_user(
        $request,
        $repository,
        $user,
        $userTopicService,
        $paginator,
        ParameterBag $parameterBag,
        $knpPaginator
    ) {
        $userId = 1;
        $topic = new Topic();
        $topic->setId(10);
        $topic->setTitle('test');
        $topic->setParent(null);
        $repository->findOneBy(array('id' => $userId))->shouldBeCalled()->willReturn($user);
        $topics = array(
            $topic,
        );

        $userTopics = array(
            'items' => $topics,
        );

        $parameterBag->get("language", null)->willReturn("de");
        $request->query = $parameterBag;
        $userTopicService->getTopics($user, "de")->shouldBeCalled()->willReturn($topics);
        $paginator->setUsedRouteParams(array("id" => $userId, "language" => "de"))->willReturn($knpPaginator);
        $paginator->paginate($topics, array(
            'distinct' => false,
        ))->willReturn($userTopics);

        $this->getUserTopicsAction($request, $userId)->shouldReturn($userTopics);
    }

    public function its_getUserTopicsAction_should_throw_exception_when_user_not_found(
        $request,
        $repository
    ) {
        $userId = 1;
        $repository->findOneBy(array('id' => $userId))->shouldBeCalled()->willReturn(null);

        $this
            ->shouldThrow('Symfony\Component\HttpKernel\Exception\NotFoundHttpException')
            ->during('getUserTopicsAction', array($request, $userId));
    }

    public function its_getUserTopicsAction_should_throw_exception_when_no_user_id_param_given(
        $request
    ) {
        $this
            ->shouldThrow('Symfony\Component\HttpKernel\Exception\NotFoundHttpException')
            ->during('getUserTopicsAction', array($request, null));
    }

    public function its_getUserTopicsAction_should_return_empty_array_when_no_results(
        $request,
        $repository,
        $user,
        $userTopicService,
        ParameterBag $parameterBag,
        $paginator,
        $knpPaginator
    ) {
        $userId = 1;
        $repository->findOneBy(array('id' => $userId))->shouldBeCalled()->willReturn($user);
        $parameterBag->get("language", null)->willReturn(null);
        $request->query = $parameterBag;
        $topics = array();
        $userTopicService->getTopics($user, null)->shouldBeCalled()->willReturn($topics);

        $userTopics = array(
            'items' => $topics,
        );

        $paginator->setUsedRouteParams(array("id" => $userId, "language" => null))->willReturn($knpPaginator);
        $paginator->paginate($topics, array(
            'distinct' => false,
        ))->willReturn($userTopics);

        $this->getUserTopicsAction($request, $userId)->shouldReturn($userTopics);
    }

    public function its_linkToUserAction_and_unlinkFromUserAction_should_throw_exception_when_user_not_found($request)
    {
        $this
            ->shouldThrow('Symfony\Component\HttpKernel\Exception\NotFoundHttpException')
            ->during('linkToUserAction', array($request, 1));

        $this
            ->shouldThrow('Symfony\Component\HttpKernel\Exception\NotFoundHttpException')
            ->during('unlinkFromUserAction', array($request, 1));
    }

    public function its_linkToUserAction_and_unlinkFromUserAction_should_throw_invalid_params_exception(
        $request,
        $repository,
        $user
    ) {
        $userId = 1;
        $parameterBag = new ParameterBag();
        $request->attributes = $parameterBag;
        $repository->findOneBy(array('id' => $userId))->shouldBeCalled()->willReturn($user);
        $this
            ->shouldThrow('Newscoop\Exception\InvalidParametersException')
            ->during('linkToUserAction', array($request, $userId));

        $this
            ->shouldThrow('Newscoop\Exception\InvalidParametersException')
            ->during('unlinkFromUserAction', array($request, $userId));
    }

    public function its_linkToUserAction_should_link_topic_to_user(
        $request,
        $repository,
        $user
    ) {
        $topic = new Topic();
        $topic->setId(10);
        $topic->setTitle('test');
        $topic->setParent(null);
        $userId = 1;
        $parameterBag = new ParameterBag();
        $parameterBag->set('links', array(array('object' => $topic)));
        $request->attributes = $parameterBag;
        $repository->findOneBy(array('id' => $userId))->shouldBeCalled()->willReturn($user);
        $this->linkToUserAction($request, $userId)->shouldReturn(null);
    }

    public function its_linkToUserAction_should_do_nothing_when_links_array_is_wrong(
        $request,
        $repository,
        $user
    ) {
        $topic = new Topic();
        $topic->setId(10);
        $topic->setTitle('test');
        $topic->setParent(null);
        $userId = 1;
        $parameterBag = new ParameterBag();
        $parameterBag->set('links', array('object' => $topic));
        $request->attributes = $parameterBag;
        $repository->findOneBy(array('id' => $userId))->shouldBeCalled()->willReturn($user);
        $this->linkToUserAction($request, $userId)->shouldReturn(null);
    }

    public function its_unlinkFromUserAction_should_do_nothing_when_links_array_is_wrong(
        $request,
        $repository,
        $user
    ) {
        $topic = new Topic();
        $topic->setId(10);
        $topic->setTitle('test');
        $topic->setParent(null);
        $userId = 1;
        $parameterBag = new ParameterBag();
        $parameterBag->set('links', array('object' => $topic));
        $request->attributes = $parameterBag;
        $repository->findOneBy(array('id' => $userId))->shouldBeCalled()->willReturn($user);
        $this->unlinkFromUserAction($request, $userId)->shouldReturn(null);
    }

    public function its_unlinkFromUserAction_should_unlink_topic_from_user(
        $request,
        $repository,
        $user
    ) {
        $topic = new Topic();
        $topic->setId(10);
        $topic->setTitle('test');
        $topic->setParent(null);
        $userId = 1;
        $parameterBag = new ParameterBag();
        $parameterBag->set('links', array(array('object' => $topic)));
        $request->attributes = $parameterBag;
        $repository->findOneBy(array('id' => $userId))->shouldBeCalled()->willReturn($user);
        $this->unlinkFromUserAction($request, $userId)->shouldReturn(null);
    }
}
