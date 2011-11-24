<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity;

/**
 */
class CommentTest extends \RepositoryTestCase
{
    /** @var Newscoop\Entity\Repository\CommentRepository */
    protected $repository;

    public function setUp()
    {
        parent::setUp('Newscoop\Entity\Comment', 'Newscoop\Entity\Comment\Commenter', 'Newscoop\Entity\Language', 'Newscoop\Entity\Article', 'Newscoop\Entity\Publication',
            'Newscoop\Entity\User', 'Newscoop\Entity\Acl\Role', 'Newscoop\Entity\UserPoints', 'Newscoop\Entity\UserAttribute'
        );
        $this->repository = $this->em->getRepository('Newscoop\Entity\Comment');
    }

    public function testRepository()
    {
        $this->assertInstanceOf('Newscoop\Entity\Repository\CommentRepository', $this->repository);
    }

    /**
     * @ticket CS-3872
     */
    public function testSaveWithoutParentOrder()
    {
        $language = new Language();
        $this->em->persist($language);
        $this->em->flush();

        $publication = new Publication();
        $this->em->persist($publication);
        $this->em->flush();

        $article = new Article(2, $language);
        $article->setPublication($publication);
        $this->em->persist($article);
        $this->em->flush();

        $user = new User();
        $user->setUsername('testname');
        $user->setEmail('testmail');
        $this->em->persist($user);
        $this->em->flush();

        $values = array(
            'user' => $user,
            'name' => 'testUser',
            'subject' => 'testSubject',
            'message' => 'testMessage',
            'language' => $language->getId(),
            'thread' => $article->getNumber(),
            'ip' => '127.0.0.1',
            'status' => 'approved',
            'time_created' => new \DateTime(),
        );

        $comment = $this->repository->save(new Comment(), $values);
        $this->em->flush();
        $this->assertEquals(1, $comment->getThreadOrder());

        $comment = $this->repository->save(new Comment(), $values);
        $this->assertEquals(2, $comment->getThreadOrder());
    }
}
