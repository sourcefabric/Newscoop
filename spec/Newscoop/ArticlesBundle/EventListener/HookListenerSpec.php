<?php

namespace spec\Newscoop\ArticlesBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;

class HookListenerSpec extends ObjectBehavior
{
    public function let(
        $die,
        \Doctrine\ORM\EntityManager $em,
        \Symfony\Bundle\FrameworkBundle\Templating\DelegatingEngine $templating,
        EntityRepository $repository
    ) {
        $em->persist(Argument::any())->willReturn(true);
        $em->flush(Argument::any())->willReturn(true);
        $em->remove(Argument::any())->willReturn(true);

        $this->beConstructedWith($em, $templating);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Newscoop\ArticlesBundle\EventListener\HookListener');
    }

    public function it_will_display_form_and_list_of_editorial_comments(
        \Newscoop\EventDispatcher\Events\PluginHooksEvent $event,
        $em,
        \Newscoop\ArticlesBundle\Entity\Repository\EditorialCommentRepository $repository,
        $form,
        $formFactory
    )
    {
        $em->getRepository(Argument::exact('Newscoop\ArticlesBundle\Entity\EditorialComment'))->willReturn($repository);
        $repository->getAllByArticleNumber(1)->willReturn(Argument::type('array'));

        //$classBook = Argument::exact('Acme\DemoBundle\Entity\Book')->getValue();
        //$book = new $classBook;
        $classBookType = Argument::exact('Newscoop\ArticlesBundle\Form\EditorialCommentType')->getValue();
        $booktype = new $classBookType;

        $formFactory->create($booktype, $book)->willReturn($form);
        $form->bind($request)->willReturn($form);
        $form->isValid()->willReturn(true);

        $form = $this->container->get('form.factory')->create(new CommentButtonType(), array(
            'lists' => $listsArray
        ), array('em' => $em));

        $response = $this->container->get('templating')->renderResponse(
            'NewscoopCommentListsBundle:Hooks:listsButton.html.twig',
            array(
                'form' => $form->createView(),
                'lists' => $lists,
                'commentId' => $commentId
            )
        );

        $event->addHookResponse($response);

        //$this->getAction($request)->shouldReturn(Argument::type('array'));
    }

    public function its_createAction_should_save_the_BookObject_when_form_is_valid($request, $form, $formFactory, $entityManager)
    {
        $classBook = Argument::exact('Acme\DemoBundle\Entity\Book')->getValue();
        $book = new $classBook;
        $classBookType = Argument::exact('Acme\DemoBundle\Form\BookType')->getValue();
        $booktype = new $classBookType;

        $formFactory->create($booktype, $book)->willReturn($form);
        $form->bind($request)->willReturn($form);
        $form->isValid()->willReturn(true);

        $entityManager->persist($book)->shouldBeCalled();
        $entityManager->flush()->shouldBeCalled();

        $response = $this->createAction($request);
        $response->shouldBeAnInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse');
    }
}
