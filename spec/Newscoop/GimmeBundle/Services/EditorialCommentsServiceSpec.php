<?php

namespace spec\Newscoop\ArticlesBundle\Services;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Newscoop\Services\UserService;
use Newscoop\Entity\Article;
use Newscoop\Entity\User;
use Newscoop\ArticlesBundle\Entity\EditorialComment;

class EditorialCommentsServiceSpec extends ObjectBehavior
{
    public function let(
        $die,
        \Doctrine\ORM\EntityManager $em,
        UserService $userService,
        \Newscoop\Entity\User $user
    ) {
        $em->persist(Argument::any())->willReturn(true);
        $em->flush(Argument::any())->willReturn(true);
        $em->remove(Argument::any())->willReturn(true);

        $user->getId()->willReturn(1);

        $this->beConstructedWith($em);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Newscoop\ArticlesBundle\Services\EditorialCommentsService');
    }

    public function it_should_create_comment(Article $article, User $user)
    {
        $this->create('comment', $article, $user, false)
            ->shouldReturn(true);
    }

    /**
     * @param \Newscoop\ArticlesBundle\Entity\EditorialComment $comment
     * @param \Newscoop\Entity\User                            $user
     */
    public function it_should_edit_comment(EditorialComment $comment, User $user)
    {
        $comment->getUser()->willReturn($user);
        $comment->setComment(Argument::type('string'))->willReturn(true);

        $this->edit('updated comment', $comment, $user)
            ->shouldReturn(true);
    }

    public function it_shouldnt_edit_comment(EditorialComment $comment, User $user, User $newUser)
    {
        $comment->getUser()->willReturn($user);
        $comment->setComment(Argument::type('string'))->willReturn(true);

        $newUser->getId()->willReturn(5);
        $newUser->isAdmin()->willReturn(false);

        $this
            ->shouldThrow('Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException')
            ->during('edit', array('updated comment', $comment, $newUser));
    }

    public function it_should_resolve_comment(EditorialComment $comment, User $user)
    {
        $comment->getUser()->willReturn($user);
        $comment->setResolved(Argument::type('bool'))->willReturn(true);

        $this->resolve($comment, $user)
            ->shouldReturn(true);
    }

    public function it_should_remove_comment(EditorialComment $comment, User $user)
    {
        $comment->getUser()->willReturn($user);
        $comment->setIsActive(Argument::type('bool'))->willReturn(true);

        $this->remove($comment, $user)
            ->shouldReturn(true);
    }
}
