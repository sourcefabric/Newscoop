<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

use Newscoop\Entity\User;

/**
 * Email service
 */
class EmailService
{
    /** @var array */
    private $config = array();

    /** @var Zend_View_Abstract */
    private $view;

    /** @var Newscoop\Services\UserTokenService */
    private $tokenService;

    /**
     * @param array $config
     * @param Zend_View_Abstract $view
     * @param UserTokenService $tokenService
     */
    public function __construct(array $config, \Zend_View_Abstract $view, UserTokenService $tokenService)
    {
        $this->config = $config;
        $this->view = $view;
        $this->tokenService = $tokenService;
    }

    /**
     * Send to user email confirmation token
     *
     * @param Newscoop\Entity\User $user
     * @return void
     */
    public function sendConfirmationToken(User $user)
    {
        $message = $this->view->action('confirm', 'email', 'default', array(
            'user' => $user->getId(),
            'token' => $this->tokenService->generateToken($user, 'email.confirm'),
        ));

        // @todo use config
        $this->send("Registrierung bei tageswoche.ch", $message, $user->getEmail());
    }

    /**
     * Send user message from other user
     *
     * @param string $from
     * @param string $to
     * @param string $subject
     * @param string $message
     * @return void
     */
    public function sendUserEmail($from, $to, $subject, $message)
    {
        $this->send($subject, $message, $to, $from);
    }

    /**
     * Send email
     *
     * @param string $subject
     * @param string $message
     * @param mixed $tos
     * @return void
     */
    private function send($subject, $message, $tos, $from = null)
    {
        $mail = new \Zend_Mail('utf-8');
        $mail->setSubject($subject);
        $mail->setBodyText($message);
        $mail->setFrom(isset($from) ? $from : $this->config['from']);

        foreach ((array) $tos as $to) {
            $mail->addTo($to);
        }

        $mail->send();
    }
}
