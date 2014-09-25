<?php

namespace spec\Newscoop\ArticlesBundle\Services;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Newscoop\Services\UserService;
use Newscoop\Entity\Article;

class EditorialCommentsServiceSpec extends ObjectBehavior
{
    public function let(
        $die,
        \Doctrine\ORM\EntityManager $em,
        UserService $userService,
        \Newscoop\Entity\User $user
    ){
        $em->persist(Argument::any())->willReturn(true);
        $em->flush(Argument::any())->willReturn(true);
        $em->remove(Argument::any())->willReturn(true);

        $userService->getCurrentUser()->willReturn($user);

        $this->beConstructedWith($em, $userService);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Newscoop\ArticlesBundle\Services\EditorialCommentsService');
    }

    public function it_should_create_comment(Article $article, $user)
    {
        $this->create('comment', $article, $user, false)
            ->shouldReturn(true);
    }

    public function it_should_edit_comment()
    {
        $this->edit('updated comment', $comment, $user)
            ->shouldReturn(true);
    }

    public function it_should_resolve_comment()
    {
        $this->resolve($comment, $user)
            ->shouldReturn(true);
    }

    public function it_should_remove_comment()
    {
        $this->remove($comment, $user)
            ->shouldReturn(true);
    }
}
