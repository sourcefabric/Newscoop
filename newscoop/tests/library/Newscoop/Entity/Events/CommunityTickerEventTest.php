<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity\Events;

use Newscoop\Entity\User;

/**
 */
class CommunityTickerEventTest extends \RepositoryTestCase
{
    /** @var Doctrine\ORM\EntityRepository */
    protected $repository;

    public function setUp()
    {
        parent::setUp('Newscoop\Entity\Events\CommunityTickerEvent', 'Newscoop\Entity\User', 'Newscoop\Entity\Acl\Role');
        $this->repository = $this->em->getRepository('Newscoop\Entity\Events\CommunityTickerEvent');
    }

    public function testCommunityTickerEvent()
    {
        $this->assertInstanceOf('Newscoop\Entity\Events\CommunityTickerEvent', new CommunityTickerEvent());
        $this->assertInstanceOf('Newscoop\Entity\Repository\Events\CommunityTickerEventRepository', $this->repository);
    }

    public function testSave()
    {
        $user = new User();
        $user->setUsername('testname');
        $user->setEmail('email');
        $this->em->persist($user);
        $this->em->flush();

        $event = new CommunityTickerEvent();
        $this->repository->save($event, array(
            'event' => 'test.event',
            'user' => 1,
            'params' => array(
                'param1' => 'value1',
            ),
        ));

        $this->em->flush();
        $this->em->clear();

        $this->assertEquals(1, $event->getId());
        $this->assertEquals('test.event', $event->getEvent());
        $this->assertEquals($user, $event->getUser());
        $this->assertEquals(array('param1' => 'value1'), $event->getParams());
    }
}
