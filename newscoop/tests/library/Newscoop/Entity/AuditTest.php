<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity;

/**
 */
class AuditTest extends \RepositoryTestCase
{
    /** @var Newscoop\Entity\Repository\AuditRepository */
    protected $repository;

    public function setUp()
    {
        parent::setUp('Newscoop\Entity\AuditEvent', 'Newscoop\Entity\User', 'Newscoop\Entity\Acl\Role');
        $this->repository = $this->em->getRepository('Newscoop\Entity\AuditEvent');
    }

    public function testAuditRepository()
    {
        $this->assertInstanceOf('Newscoop\Entity\AuditEvent', new AuditEvent());
        $this->assertInstanceOf('Newscoop\Entity\Repository\AuditRepository', $this->repository);
    }

    public function testFindAllEmpty()
    {
        $this->assertEmpty($this->repository->findAll());
    }

    public function testSaveEntity()
    {
        $user = new User('email');
        $this->em->persist($user);

        $audit = new AuditEvent();
        $this->repository->save($audit, array(
            'resource_type' => 'test-type',
            'resource_id' => array('id' => 123),
            'resource_title' => 'test-title',
            'resource_diff' => array('name' => array('tic', 'toc')),
            'action' => 'test-action',
            'user' => $user,
        ));

        $this->em->flush();

        $audits = $this->repository->findAll();
        $this->assertEquals(1, sizeof($audits));
        $this->assertEquals($audit, $audits[0]);
        $this->assertEquals(1, $audit->getId());
        $this->assertEquals('test-type', $audit->getResourceType());
        $this->assertEquals(array('id' => 123), $audit->getResourceId());
        $this->assertEquals('test-title', $audit->getResourceTitle());
        $this->assertEquals(array('name' => array('tic', 'toc')), $audit->getResourceDiff());
        $this->assertEquals('test-action', $audit->getAction());
        $this->assertEquals($user, $audit->getUser());

        $now = new \DateTime();
        $this->assertLessThanOrEqual(1, $now->diff($audit->getCreated())->s);
    }
}
