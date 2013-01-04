<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\MailChimp;

use ArrayIterator;

/**
 */
class ListApiTest extends \TestCase
{
    const API_KEY = 'qwerty';
    const LIST_ID = '123456';
    const EMAIL = 'john@example.com';

    private $subscribedInfo = array(
        'success' => 1,
        'data' => array(
            array(
                'email' => self::EMAIL,
                'status' => 'subscribed',
                'merges' => array(
                    'GROUPINGS' => array(
                        array(
                            'name' => 'topics',
                            'groups' => 'sport, nature',
                        ),
                    ),
                ),
            ),
        ),
    );

    private $unsubscribedInfo = array(
        'success' => 1,
        'data' => array(
            array(
                'email' => self::EMAIL,
                'status' => 'unsubscribed',
            ),
        ),
    );

    public function setUp()
    {
        $this->api = $this->getMock('Rezzza\MailChimp\MCAPI', array(), array(self::API_KEY));
        $this->apiFactory = $this->getMock(
            'Newscoop\MailChimp\ApiFactory',
            array(),
            array(new ArrayIterator(array('mailchimp_apikey' => self::API_KEY)))
        );
        $this->apiFactory->expects($this->any())
            ->method('createApi')
            ->will($this->returnValue($this->api));
        $this->list = new ListApi($this->apiFactory, new ArrayIterator(array('mailchimp_listid' => self::LIST_ID)));
    }

    public function testInstance()
    {
        $this->assertInstanceOf('Newscoop\MailChimp\ListApi', $this->list);
    }

    public function testGetMemberView()
    {
        $this->api->expects($this->once())
            ->method('listMemberInfo')
            ->with(
                $this->equalTo(self::LIST_ID),
                $this->equalTo(array(self::EMAIL))
            )->will($this->returnValue($this->subscribedInfo));

        $view = $this->list->getMemberView(self::EMAIL);

        $this->assertTrue($view->subscriber);
        $this->assertEquals(array(
            'topics' => array('sport', 'nature'),
        ), $view->groups);
    }

    public function testGetMemberViewUnsubscribed()
    {
        $this->api->expects($this->once())
            ->method('listMemberInfo')
            ->will($this->returnValue($this->unsubscribedInfo));

        $view = $this->list->getMemberView(self::EMAIL);

        $this->assertFalse($view->subscriber);
        $this->assertEmpty($view->groups);
    }

    public function testGetListView()
    {
        $this->api->expects($this->once())
            ->method('listInterestGroupings')
            ->with(
                $this->equalTo(self::LIST_ID)
            )->will(
                $this->returnValue(array(
                    array(
                        'id' => 123,
                        'name' => 'topics',
                        'form_field' => 'checkbox',
                        'groups' => array(
                            array(
                                'name' => 'sport',
                            ),
                            array(
                                'name' => 'nature',
                            ),
                        ),
                    ),
                )));

        $view = $this->list->getListView();

        $this->assertEquals(self::LIST_ID, $view->id);
        $this->assertEquals(array(
            array(
                'id' => 123,
                'name' => 'topics',
                'form_field' => 'checkbox',
                'groups' => array('sport' => 'sport', 'nature' => 'nature'),
            ),
        ), $view->groups);
    }

    public function testSubscribe()
    {
        $this->api->expects($this->once())
            ->method('listSubscribe')
            ->with($this->equalTo(self::LIST_ID), $this->equalTo(self::EMAIL), $this->equalTo(array(
                'GROUPINGS' => array(
                    array('name' => 'topics', 'groups' => 'first,last'),
                ),
            )), $this->equalTo('html'), $this->equalTo(false), $this->equalTo(true), $this->equalTo(true), $this->equalTo(true))
            ->will($this->returnValue(true));

        $this->list->subscribe(self::EMAIL, array('subscriber' => 1, 'topics' => array('first', 'last')));
    }

    public function testUnsubscribe()
    {
        $this->api->expects($this->once())
            ->method('listUnsubscribe')
            ->with($this->equalTo(self::LIST_ID), $this->equalTo(self::EMAIL));

        $this->list->subscribe(self::EMAIL, array('subscriber' => null));
    }
}
