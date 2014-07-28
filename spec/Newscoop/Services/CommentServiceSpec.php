<?php

namespace spec\Newscoop\Services;

use PhpSpec\ObjectBehavior;

class CommentServiceSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Newscoop\Services\CommentService');
    }

    /**
     * @param \Doctrine\ORM\EntityManager           $em
     * @param \Doctrine\ORM\EntityRepository        $repository
     * @param \Doctrine\ORM\QueryBuilder            $queryBuilder
     * @param \Doctrine\ORM\Query\Expr              $expr
     * @param \Doctrine\ORM\AbstractQuery           $query
     * @param \Newscoop\Services\PublicationService $publicationService
     */
    public function let($em, $repository, $queryBuilder, $expr, $query)
    {
        $em
            ->getRepository('Newscoop\Entity\Comment\Acceptance')
            ->willReturn($repository);

        $repository
            ->createQueryBuilder('a')
            ->willReturn($queryBuilder);

        $queryBuilder->select('a.search')->willReturn($queryBuilder);
        $queryBuilder->where(null)->willReturn($queryBuilder);
        $queryBuilder->expr()->willReturn($expr);
        $queryBuilder->setParameters(array("name" => null, "email" => null, "ip" => null))->willReturn($queryBuilder);
        $queryBuilder->getQuery()->willReturn($query);

        $this->beConstructedWith($em, $publicationService);
    }

    /**
     * @param \Newscoop\Entity\Repository\CommentRepository $commentRepository
     */
    public function it_should_get_all_replies($commentRepository)
    {
        $commentRepository->getDirectReplies(33)->willReturn(array(20));
        $commentRepository->getDirectReplies(20)->willReturn(array());
        $this->getAllReplies(33, $commentRepository)->shouldReturn(array(33, 20));
    }

    /**
     * @param \Newscoop\Entity\Comment\Commenter $commenter
     */
    public function it_should_check_if_user_is_banned($commenter)
    {
        $this->isBanned($commenter)->shouldReturn(false);
    }
}
