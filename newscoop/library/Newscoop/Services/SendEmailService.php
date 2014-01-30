<?php
/**
 * @package Newscoop
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2014 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

use Doctrine\ORM\EntityManager;
use Newscoop\Entity\User;
use Newscoop\NewscoopBundle\Services\SystemPreferencesService;

/**
 * Send email service
 */
class SendEmailService
{
    /** @var Doctrine\ORM\EntityManager */
    protected $em;

    /** @var Newscoop\Services\UserTokenService */
    protected $tokenService;

    /** @var Newscoop\NewscoopBundle\Services\SystemPreferencesService */
    protected $preferencesService;

    /** @var Zend_Controller_Router_Rewrite */
    protected $zendRouter;

    /** @var Swift_Mailer */
    protected $mailer;

    /** @var Symfony\Bundle\FrameworkBundle\Templating\DelegatingEngine */
    protected $templating;

    /**
     * @param Doctrine\ORM\EntityManager                                 $em
     * @param Newscoop\Services\UserTokenService                         $tokenService
     * @param Newscoop\NewscoopBundle\Services\SystemPreferencesService  $preferencesService
     * @param Zend_Controller_Router_Rewrite                             $zendRouter
     * @param Swift_Mailer                                               $mailer
     * @param Symfony\Bundle\FrameworkBundle\Templating\DelegatingEngine $templating
     */
    public function __construct(
        EntityManager $em,
        UserTokenService $tokenService,
        SystemPreferencesService $preferencesService,
        \Zend_Controller_Router_Rewrite $zendRouter,
        \Swift_Mailer $mailer,
        $templating
    )
    {
        $this->em = $em;
        $this->tokenService = $tokenService;
        $this->preferencesService = $preferencesService;
        $this->zendRouter = $zendRouter;
        $this->mailer = $mailer;
        $this->templating = $templating;
    }

    /**
     * Send to user email confirmation token
     *
     * @param Newscoop\Entity\User $user
     *
     * @return void
     */
    public function sendConfirmationToken(User $user, $hostname)
    {
        $params = $this->zendRouter->assemble(array(
            'user' => $user->getId(),
            'token' => $this->tokenService->generateToken($user, 'email.confirm')
        ), 'confirm-email', true, false);

        $link = $hostname.$params;
        $this->send($link, $user->getEmail(), $hostname, 0, $this->preferencesService->EmailFromAddress);
    }

    /**
     * Send password restore token
     *
     * @param Newscoop\Entity\User $user
     * @return void
     */
    public function sendPasswordRestoreToken(User $user, $hostname)
    {
        /*$params = $this->zendRouter->assemble(array(
            'user' => $user->getId(),
            'token' => $this->tokenService->generateToken($user, 'password.restore'),
        ), 'password-restore', true, false);*/


        //$link = $hostname.$params;
        $this->send($hostname, $user->getEmail(), $hostname, 1, $this->preferencesService->EmailFromAddress);
    }

    /**
     * Send email
     *
     * @param string $message
     * @param string $to
     * @param string $from
     *
     * @return void
     */
    private function send($link, $to, $hostname, $type, $from = null)
    {
        if (empty($from)) {
            $from = 'no-reply@' . $hostname;
        }

        $confirmationEmail = $this->templating->render(
            'NewscoopNewscoopBundle:Emails:confirmation.txt.twig',
            array(
                'link' => urldecode($link),
                'hostname' => $hostname
            )
        );

        try {
            $message = \Swift_Message::newInstance()
                ->setSubject('E-mail confirmation')
                ->setFrom($from)
                ->setTo($to)
                ->setBody($confirmationEmail);

            $this->mailer->send($message);
        } catch (\Exception $exception) {
            throw new \Exception("Error sending email.", 1);
        }
    }
}

