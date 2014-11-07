<?php

namespace spec\Newscoop\NewscoopBundle\Controller;

use PhpSpec\ObjectBehavior;
use Symfony\Component\DependencyInjection\Container;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Prophecy\Argument;
use Newscoop\NewscoopBundle\Entity\Repository\TopicRepository;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormView;
use Newscoop\NewscoopBundle\Entity\Topic;

class TopicsControllerSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Newscoop\NewscoopBundle\Controller\TopicsController');
    }

    public function it_is_of_type_container_aware()
    {
        $this->shouldBeAnInstanceOf('Symfony\Component\DependencyInjection\ContainerAware');
    }

    public function let(
        Container $container,
        Translator $translator,
        Session $session,
        TopicRepository $topicRepository,
        EntityRepository $repository,
        EntityManager $entityManager,
        Request $request,
        FormFactory $formFactory,
        FormBuilder $formBuilder,
        Form $form,
        FormView $formView,
        Topic $topic
    )
    {
        $container->get('em')->willReturn($entityManager);
        $container->get('session')->willReturn($session);
        $container->get('request')->willReturn($request);
        $container->get('translator')->willReturn($translator);
        $container->get('form.factory')->willReturn($formFactory);

        $formBuilder->getForm(Argument::cetera())->willReturn($form);
        $formFactory->create(Argument::cetera())->willReturn($form);
        $form->createView()->willReturn($formView);
        $form->handleRequest(Argument::cetera())->willReturn(true);
        $form->isValid()->willReturn(true);

        $entityManager->persist(Argument::any())->willReturn(true);
        $entityManager->flush(Argument::any())->willReturn(true);
        $entityManager->remove(Argument::any())->willReturn(true);

        $entityManager->getRepository('Newscoop\NewscoopBundle\Entity\Topic')->willReturn($repository);
        $this->setContainer($container);
    }

    public function its_treeAction_should_render_the_tree_of_topics($topicRepository, $request, $entityManager)
    {
        $entityManager->getRepository('Newscoop\NewscoopBundle\Entity\Topic')->willReturn($topicRepository);
        $topicRepository->childrenHierarchy()->willReturn(Argument::type('array'));
        $response = $this->treeAction($request);
        $response->getStatusCode()->shouldReturn(200);
        $response->shouldBeAnInstanceOf('Symfony\Component\HttpFoundation\JsonResponse');
    }

    public function its_addAction_should_add_a_new_topic($request, $form, $repository, $topic)
    {
        $repository->findOneBy(array(
            'id' => 1,
        ))->willReturn($topic);

        $form->getData()->willReturn(array('title' => 'test topic', 'parent' => 1));
        $response = $this->addAction($request);
        $response->getStatusCode()->shouldReturn(200);
        $response->shouldBeAnInstanceOf('Symfony\Component\HttpFoundation\JsonResponse');
    }

    public function its_deleteAction_should_delete_single_topic($request, $topic, $repository)
    {
        $repository->findOneBy(array(
            'id' => 1,
        ))->willReturn($topic);
        $response = $this->deleteAction($request, 1);
        $response->getStatusCode()->shouldReturn(200);
        $response->shouldBeAnInstanceOf('Symfony\Component\HttpFoundation\JsonResponse');
    }

    public function its_deleteAction_should_return_404_when_topic_not_found($request, $repository)
    {
        $repository->findOneBy(array(
            'id' => 3,
        ))->willReturn(null);
        $response = $this->deleteAction($request, 3);
        $response->getStatusCode()->shouldReturn(404);
        $response->shouldBeAnInstanceOf('Symfony\Component\HttpFoundation\JsonResponse');
    }

    public function its_editAction_should_edit_topic($request, $repository, $topic)
    {
        $repository->findOneBy(array(
            'id' => 1,
        ))->willReturn($topic);
        $response = $this->editAction($request, 1);
        $response->getStatusCode()->shouldReturn(200);
        $response->shouldBeAnInstanceOf('Symfony\Component\HttpFoundation\JsonResponse');
    }

    public function its_editAction_should_return_404_when_topic_not_found($request, $repository)
    {
        $repository->findOneBy(array(
            'id' => 1,
        ))->willReturn(null);
        $response = $this->editAction($request, 1);
        $response->getStatusCode()->shouldReturn(404);
        $response->shouldBeAnInstanceOf('Symfony\Component\HttpFoundation\JsonResponse');
    }
}
