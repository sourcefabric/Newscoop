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
    /** @var Zend_View_Abstract */
    private $view;

    /** @var Newscoop\Services\UserTokenService */
    private $tokenService;

    /**
     * @param Zend_View_Abstract
     */
    public function __construct(\Zend_View_Abstract $view, UserTokenService $tokenService)
    {
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
        $text = $this->view->action('confirm', 'email', 'default', array(
            'user' => $user->getId(),
            'token' => $this->tokenService->generateToken($user, 'email.confirm'),
        ));

        // @todo send to user email from some valid email
        $mail = new \Zend_Mail();
        $mail->setBodyText($text);
        $mail->setFrom('no-reply@localhost');
        $mail->addTo('petr@localhost');
        $mail->setSubject('Confirm e-mail');
        $mail->send();
    }
}
