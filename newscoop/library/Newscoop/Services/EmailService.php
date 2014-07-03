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
use Newscoop\Entity\Comment;
use Newscoop\Entity\Article;
use Newscoop\NewscoopBundle\Services\SystemPreferencesService;

/**
 * Email service
 */
class EmailService
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

    /** @var Newscoop\Services\PublicationService */
    protected $publicationService;

    /**
     * @param Doctrine\ORM\EntityManager                                $em
     * @param Newscoop\Services\UserTokenService                        $tokenService
     * @param Newscoop\NewscoopBundle\Services\SystemPreferencesService $preferencesService
     * @param Swift_Mailer                                              $mailer
     * @param Newscoop\Services\TemplatesService                        $templatesService
     * @param Newscoop\Services\PlaceholdersService                     $placeholdersService
     * @param Newscoop\Services\PublicationService                      $publicationService
     */
    public function __construct(
        EntityManager $em,
        UserTokenService $tokenService,
        SystemPreferencesService $preferencesService,
        \Swift_Mailer $mailer,
        $templatesService,
        $placeholdersService,
        $publicationService
    )
    {
        $this->em = $em;
        $this->tokenService = $tokenService;
        $this->preferencesService = $preferencesService;
        $this->mailer = $mailer;
        $this->templatesService = $templatesService;
        $this->placeholdersService = $placeholdersService;
        $this->publicationService = $publicationService;
    }

    /**
     * Send to user email confirmation token
     *
     * @param Newscoop\Entity\User $user
     *
     * @return void|Exception
     */
    public function sendConfirmationToken(User $user)
    {
        $smarty = $this->templatesService->getSmarty();
        $smarty->assign('user', new \MetaUser($user));
        $smarty->assign('token', $this->tokenService->generateToken($user, 'email.confirm'));
        $smarty->assign('site', $this->publicationService->getPublicationAlias()->getName());
        $message = $this->templatesService->fetchTemplate("email_confirm.tpl");
        $this->send($this->placeholdersService->get('subject'), $message, $user->getEmail(), $this->preferencesService->EmailFromAddress);
    }

    /**
     * Send password restore token
     *
     * @param Newscoop\Entity\User $user
     *
     * @return void|Exception
     */
    public function sendPasswordRestoreToken(User $user)
    {
        $smarty = $this->templatesService->getSmarty();
        $smarty->assign('user', new \MetaUser($user));
        $smarty->assign('token', $this->tokenService->generateToken($user, 'password.restore'));
        $smarty->assign('site', $this->publicationService->getPublicationAlias()->getName());
        $message = $this->templatesService->fetchTemplate("email_password-restore.tpl");
        $this->send($this->placeholdersService->get('subject'), $message, $user->getEmail(), $this->preferencesService->EmailFromAddress);
    }

    /**
     * Send email
     *
     * @param string      $placeholder
     * @param string      $message
     * @param string      $to
     * @param string|null $from
     * @param string|null $attachmentDir
     *
     * @return void
     * @throws Exception when error sending email
     */
    public function send($placeholder, $message, $to, $from = null, $attachmentDir = null)
    {
        if (empty($from)) {
            $from = 'no-reply@' . $this->publicationService->getPublicationAlias()->getName();
        }

        try {

            $messageToSend = \Swift_Message::newInstance();

            if (is_array($to)) {
                if (array_key_exists('moderator', $to)) {
                    $messageToSend->addBcc($to['moderator']);
                    unset($to['moderator']);
                }
            }

            $messageToSend->setSubject($placeholder)
                ->setFrom($from)
                ->setTo($to)
                ->setBody($message, 'text/html');

            if ($attachmentDir) {
                $messageToSend->attach(\Swift_Attachment::fromPath($attachmentDir));
            }

            $this->mailer->send($messageToSend);
        } catch (\Exception $exception) {
            throw new \Exception("Error sending email.", 1);
        }
    }

    /**
     * Send comment notification
     *
     * @param Newscoop\Entity\Comment $comment
     * @param Newscoop\Entity\Article $article
     * @param array                   $authors
     * @param Newscoop\Entity\User    $user
     *
     * @return void
     */
    public function sendCommentNotification(Comment $comment, Article $article, array $authors, User $user = null)
    {
        $emails = array_unique(array_filter(array_map(function ($author) {
            return $author->getEmail();
        }, $authors)));

        if (empty($emails)) {
            return;
        }

        $smarty = $this->templatesService->getSmarty();
        $uri = \CampSite::GetURIInstance();
        if ($user) {
            $smarty->assign('username', $user->getUsername());
        } else {
            $smarty->assign('username', 'Unbekannt');
        }

        $smarty->assign('comment', $comment);
        $smarty->assign('article', new \MetaArticle($article->getLanguageId(), $article->getNumber()));
        $smarty->assign('publication', $uri->getBase());
        $smarty->assign('articleLink', \ShortURL::GetURI($article->getPublicationId(), $article->getLanguageId(), $article->getIssueId(), $article->getSectionId(), $article->getNumber()));
        $moderatorFrom = $this->publicationService->getPublication()->getModeratorFrom();

        if ($this->publicationService->getPublication()->getCommentsPublicModerated()) {
            $moderatorTo = $this->publicationService->getPublication()->getModeratorTo();
            $moderatorTo ? $emails['moderator'] = $moderatorTo : null;
        }

        $message = $this->templatesService->fetchTemplate("email_comment-notify.tpl");
        $this->send($this->placeholdersService->get('subject'), $message, $emails, $moderatorFrom ?: $this->preferencesService->EmailFromAddress);
    }

    /**
     * Send user message from other user
     *
     * @param string $from
     * @param string $to
     * @param string $subject
     * @param string $message
     *
     * @return void
     */
    public function sendUserEmail($from, $to, $subject, $message)
    {
        $this->send($subject, $message, $to, $from);
    }
}
