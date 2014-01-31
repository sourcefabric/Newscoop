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
use Symfony\Component\HttpFoundation\Response;

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

    /** @var Swift_Mailer */
    protected $mailer;

    /** @var Newscoop\Services\TemplatesService */
    protected $templatesService;

    /** @var Newscoop\Services\PlaceholdersService */
    protected $placeholdersService;

    /**
     * @param Doctrine\ORM\EntityManager                                $em
     * @param Newscoop\Services\UserTokenService                        $tokenService
     * @param Newscoop\NewscoopBundle\Services\SystemPreferencesService $preferencesService
     * @param Swift_Mailer                                              $mailer
     * @param Newscoop\Services\TemplatesService                        $templatesService
     * @param Newscoop\Services\PlaceholdersService                     $placeholdersService
     */
    public function __construct(
        EntityManager $em,
        UserTokenService $tokenService,
        SystemPreferencesService $preferencesService,
        \Swift_Mailer $mailer,
        $templatesService,
        $placeholdersService
    )
    {
        $this->em = $em;
        $this->tokenService = $tokenService;
        $this->preferencesService = $preferencesService;
        $this->mailer = $mailer;
        $this->templatesService = $templatesService;
        $this->placeholdersService = $placeholdersService;
    }

    /**
     * Send to user email confirmation token
     *
     * @param Newscoop\Entity\User $user
     * @param string               $hostname
     *
     * @return void|Exception
     */
    public function sendConfirmationToken(User $user, $hostname)
    {
        $smarty = $this->templatesService->getSmarty();
        $smarty->assign('user', $user->getId());
        $smarty->assign('token', $this->tokenService->generateToken($user, 'email.confirm'));
        $smarty->assign('publication', $hostname);
        $smarty->assign('site', $hostname);
        $message = $this->templatesService->fetchTemplate("email_confirm.tpl");
        $this->send($this->placeholdersService->get('subject'), $message, $user->getEmail(), $hostname, $this->preferencesService->EmailFromAddress);
    }

    /**
     * Send password restore token
     *
     * @param Newscoop\Entity\User $user
     * @param string               $hostname
     *
     * @return void|Exception
     */
    public function sendPasswordRestoreToken(User $user, $hostname)
    {
        $smarty = $this->templatesService->getSmarty();
        $smarty->assign('user', $user->getId());
        $smarty->assign('token', $this->tokenService->generateToken($user, 'password.restore'));
        $smarty->assign('publication', $hostname);
        $smarty->assign('site', $hostname);
        $message = $this->templatesService->fetchTemplate("email_password-restore.tpl");
        $this->send($this->placeholdersService->get('subject'), $message, $user->getEmail(), $hostname, $this->preferencesService->EmailFromAddress);
    }

    /**
     * Send email
     *
     * @param string $placeholder
     * @param string $message
     * @param string $to
     * @param string $hostname
     * @param string $from
     *
     * @return void
     */
    private function send($placeholder, $message, $to, $hostname, $from = null)
    {
        if (empty($from)) {
            $from = 'no-reply@' . $hostname;
        }

        try {
            $messageToSend = \Swift_Message::newInstance()
                ->setSubject($placeholder)
                ->setFrom($from)
                ->setTo($to)
                ->setBody($message);

            $this->mailer->send($messageToSend);
        } catch (\Exception $exception) {
            throw new \Exception("Error sending email.", 1);
        }
    }
}

