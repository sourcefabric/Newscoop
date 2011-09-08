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

        $this->send("Email confirmation", $message, $user->getEmail());
    }

    /**
     * Send email
     *
     * @param string $subject
     * @param string $message
     * @param mixed $tos
     * @return void
     */
    private function send($subject, $message, $tos)
    {
        $mail = new \Zend_Mail();
        $mail->setSubject($subject);
        $mail->setBodyText($message);
        $mail->setFrom($this->config['from']);

        foreach ((array) $tos as $to) {
            $mail->addTo($to);
        }

        $mail->send();
    }
}
