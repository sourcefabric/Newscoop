<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\MailChimp;

use Newscoop\Entity\User;

/**
 */
class MailChimpFacadeTest extends \TestCase
{
    const API_KEY = 'qwerty';
    const LIST_ID = '123456';
    const USER_EMAIL = 'john@example.com';

    public function setUp()
    {
        $this->api = $this->getMock('MCAPI', array(), array(self::API_KEY));
        $this->object = new MailChimpFacade($this->api);
        $this->user = new User();
        $this->user->setEmail(self::USER_EMAIL);
    }

    public function testInstance()
    {
        $this->assertInstanceOf('Newscoop\MailChimp\MailChimpFacade', $this->object);
    }

    public function testIsSubscribed()
    {
        $this->api->expects($this->exactly(2))
            ->method('listsForEmail')
            ->with($this->equalTo(self::USER_EMAIL))
            ->will($this->onConsecutiveCalls(array(), array(self::LIST_ID)));

        $this->assertFalse($this->object->isSubscribed($this->user, self::LIST_ID));
        $this->assertTrue($this->object->isSubscribed($this->user, self::LIST_ID));
    }

    public function testSubscribe()
    {
        $this->api->expects($this->once())
            ->method('listSubscribe')
            ->with($this->equalTo(self::LIST_ID), $this->equalTo(self::USER_EMAIL), $this->equalTo(array(
                'GROUPINGS' => array(
                    array('id' => 1, 'groups' => 'first,last'),
                ),
            )), $this->equalTo('html'), $this->equalTo(false), $this->equalTo(true), $this->equalTo(true), $this->equalTo(true))
            ->will($this->returnValue(true));

        $this->object->subscribe($this->user, self::LIST_ID, array(1 => array('first', 'last')));
    }

    public function testUnsubscribe()
    {
        $this->api->expects($this->once())
            ->method('listUnsubscribe')
            ->with($this->equalTo(self::LIST_ID), $this->equalTo(self::USER_EMAIL))
            ->will($this->returnValue(true));

        $this->object->unsubscribe($this->user, self::LIST_ID);
    }

    public function testGetListGroups()
    {
        $this->api->expects($this->once())
            ->method('listInterestGroupings')
            ->with($this->equalTo(self::LIST_ID))
            ->will($this->returnValue(array()));

        $this->assertNotNull($this->object->getListGroups(self::LIST_ID));
    }

    public function testGetUserGroups()
    {
        $this->api->expects($this->once())
            ->method('listMemberInfo')
            ->with($this->equalTo(self::LIST_ID), $this->equalTo(self::USER_EMAIL))
            ->will($this->returnValue(array(
                'success' => 1,
                'data' => array(
                    array(
                        'merges' => array(
                            'GROUPINGS' => array(
                                array(
                                    'id' => 123,
                                    'groups' => 'g1,g2',
                                ),
                            ),
                        ),
                    ),
                ),
            )));

        $this->assertEquals(array(
            123 => array('g1', 'g2'),
        ), $this->object->getUserGroups($this->user, self::LIST_ID));
    }
}
