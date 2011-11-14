<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

use Newscoop\Entity\User;

class EmailServiceTest extends \RepositoryTestCase
{
    const USER_EMAIL = 'foo@bar.com';

    /** @var Newscoop\Services\EmailService */
    protected $service;

    /** @var string */
    private $dir;

    /** @var Newscoop\Entity\User */
    private $user;

    public function setUp()
    {
        global $application;

        parent::setUp('Newscoop\Entity\User', 'Newscoop\Entity\Acl\Role', 'Newscoop\Entity\UserToken');

        $this->service = \Zend_Registry::get('container')
            ->getService('email');
        \Zend_Mail::setDefaultTransport(new \Zend_Mail_Transport_File(array(
            'path' => '/tmp',
            'callback' => function($transport) {
                return uniqid('mail', true);
            },
        )));

        try {
        $application->run();
        } catch (\Exception $e) { // ignore errors
        }

        $this->user = new User();
        $this->user->setEmail(self::USER_EMAIL);
        $this->em->persist($this->user);
        $this->em->flush();
    }

    public function testService()
    {
        $this->assertInstanceOf('Newscoop\Services\EmailService', $this->service);
    }

    public function testSendConfirmationToken()
    {
        $this->service->sendConfirmationToken($this->user);
        $this->assertRegExp('#' . $this->user->getId() . '/[a-z0-9]{40}#i', $this->getEmailBody());
        $this->assertArrayHasKey('Subject', $this->getEmailHeaders());
    }

    public function testSendPasswordRestoreToken()
    {
        $this->service->sendPasswordRestoreToken($this->user);
        $this->assertRegExp('#user/' . $this->user->getId() . '/token/[a-z0-9]{40}#i', $this->getEmailBody());
        $this->assertArrayHasKey('Subject', $this->getEmailHeaders());
    }

    /**
     * Get email body
     *
     * @return string
     */
    private function getEmailBody()
    {
        $transport = \Zend_Mail::getDefaultTransport();
        return quoted_printable_decode($transport->body);
    }

    /**
     * Get email headers
     *
     * @return array
     */
    private function getEmailHeaders()
    {
        $transport = \Zend_Mail::getDefaultTransport();
        $headers = array();
        foreach (explode("\n", $transport->header) as $line) {
            if (strpos($line, ':') !== false) {
                list($name, $value) = explode(':', $line);
                $headers[$name] = $value;
            }
        }

        return $headers;
    }
}
