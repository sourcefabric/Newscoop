<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Subscription;

use Newscoop\Entity\User,
    Newscoop\Entity\Publication,
    Newscoop\Entity\Issue,
    Newscoop\Entity\Section;

/**
 */
class SubscriptionFacadeTest extends \TestCase
{
    public function setUp() {
        $this->orm = $this->setUpOrm('Newscoop\Subscription\Subscription', 'Newscoop\Entity\User', 'Newscoop\Entity\Acl\Role', 'Newscoop\Entity\Publication');
        $this->facade = new SubscriptionFacade($this->orm);

        $this->user = new User('test');
        $this->publication = new Publication();

        $this->orm->persist($this->user);
        $this->orm->persist($this->publication);
        $this->orm->flush();
    }

    public function tearDown()
    {
        $this->tearDownOrm($this->orm);
    }

    public function testInstance()
    {
        $this->assertInstanceOf('Newscoop\Subscription\SubscriptionFacade', $this->facade);
    }

    public function testSave()
    {
        $subscription = $this->facade->save(array(
            'user' => 1,
            'publication' => 1,
            'active' => true,
            'type' => Subscription::TYPE_PAID,
        ));

        $this->assertInstanceOf('Newscoop\Subscription\Subscription', $subscription);

        $this->assertNotNull($subscription->getId());
        $this->assertEquals($this->user, $subscription->getUser());
        $this->assertEquals($this->publication, $subscription->getPublication());
        $this->assertTrue($subscription->isActive());
        $this->assertEquals(Subscription::TYPE_PAID, $subscription->getType());
        $this->assertEquals(0.0, $subscription->getToPay());

        $this->facade->save(array(
            'active' => false,
        ), $subscription);

        $this->assertFalse($subscription->isActive());
    }

    public function testSaveAssignIdPublication()
    {
        $subscription = $this->facade->save(array(
            'user' => 1,
            'publication' => 1,
        ));

        $this->assertEquals(1, $subscription->getId());
        $this->assertEquals($this->publication->getId(), $subscription->getPublicationId());
        $this->assertEquals($this->publication->getName(), $subscription->getPublicationName());
    }

    public function testSaveUpdate()
    {
        $subscription = $this->facade->save(array(
            'user' => 1,
            'publication' => 1,
            'active' => false,
            'type' => Subscription::TYPE_PAID,
        ));

        $this->assertFalse($subscription->isActive());

        $this->facade->save(array(
            'active' => true,
            'toPay' => 50.0,
        ), $subscription);

        $this->assertTrue($subscription->isActive());
        $this->assertEquals(50.0, $subscription->getToPay());
    }

    public function testFindByUser()
    {
        $this->assertEmpty($this->facade->findByUser($this->user));

        $subscription = $this->facade->save(array(
            'user' => $this->user,
            'publication' => $this->publication,
        ));

        $this->assertEquals(array($subscription), $this->facade->findByUser($this->user));
        $this->assertEmpty($this->facade->findByUser(123));

        $this->assertEmpty($this->facade->findByUser(null));
    }

    public function testDelete()
    {
        $subscription = $this->facade->save(array(
            'user' => $this->user,
            'publication' => $this->publication,
        ));

        $this->facade->delete($subscription->getId());

        $this->assertEmpty($this->facade->findByUser($this->user));
    }

    public function testFind()
    {
        $this->assertNull($this->facade->find(1));
        $subscription = $this->facade->save(array(
            'user' => $this->user,
            'publication' => $this->publication,
        ));

        $this->assertEquals($subscription, $this->facade->find(1));
    }
}
