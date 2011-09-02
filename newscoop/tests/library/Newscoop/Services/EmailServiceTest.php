<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

use Newscoop\Entity\User;

class EmailServiceTest extends \PHPUnit_Framework_TestCase
{
    /** @var Newscoop\Services\EmailService */
    protected $service;

    public function setUp()
    {
        $this->service = \Zend_Registry::get('container')->getService('email');
    }

    public function testService()
    {
        $this->assertInstanceOf('Newscoop\Services\EmailService', $this->service);
    }

    public function testSendConfirmationToken()
    {
    }
}
