<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Service;

class AuditTest extends \PHPUnit_Framework_TestCase
{
    /** @var Newscoop\Service\Audit */
    protected $service;

    /** @var Newscoop\Service\User */
    protected $userService;

    public function setUp()
    {
        $this->httpClient = $this->getMock('Zend_Http_Client');
        $this->userService = $this->getMockBuilder('Newscoop\Service\User')
            ->disableOriginalConstructor()
            ->getMock();

        $this->service = new Audit($this->httpClient, $this->userService);
    }

    public function testAudit()
    {
        $service = new Audit($this->httpClient, $this->userService);
        $this->assertInstanceOf('Newscoop\Service\Audit', $service);
    }

    public function testUpdate()
    {
        $event = new \sfEvent($this, 'event.test');
        $this->service->update($event);
    }
}
