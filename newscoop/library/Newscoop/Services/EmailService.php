<?php
/**
 * @package Newscoop
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2014 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

use Newscoop\Entity\User;
use Newscoop\Entity\Comment;
use Newscoop\Entity\Article;
use Symfony\Component\DependencyInjection\Container;

/**
 * Email service
 */
class EmailService
{
    /** @var Container */
    protected $container;

    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
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
        $tokenService = $this->container->get('user.token');
        $publicationService = $this->container->get('newscoop_newscoop.publication_service');
        $templatesService = $this->container->get('newscoop.templates.service');
        $placeholdersService = $this->container->get('newscoop.placeholders.service');
        $preferencesService = $this->container->get('preferences');
        $smarty = $templatesService->getSmarty();
        $smarty->assign('user', new \MetaUser($user));
        $smarty->assign('token', $tokenService->generateToken($user, 'email.confirm'));
        $smarty->assign('site', $publicationService->getPublicationAlias()->getName());
        $message = $templatesService->fetchTemplate("email_confirm.tpl");
        $this->send($placeholdersService->get('subject'), $message, $user->getEmail(), $preferencesService->EmailFromAddress);
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
        $tokenService = $this->container->get('user.token');
        $publicationService = $this->container->get('newscoop_newscoop.publication_service');
        $templatesService = $this->container->get('newscoop.templates.service');
        $placeholdersService = $this->container->get('newscoop.placeholders.service');
        $preferencesService = $this->container->get('preferences');
        $smarty = $templatesService->getSmarty();
        $smarty->assign('user', new \MetaUser($user));
        $smarty->assign('token', $tokenService->generateToken($user, 'password.restore'));
        $smarty->assign('site', $publicationService->getPublicationAlias()->getName());
        $message = $templatesService->fetchTemplate("email_password-restore.tpl");
        $this->send($placeholdersService->get('subject'), $message, $user->getEmail(), $preferencesService->EmailFromAddress);
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
        $publicationService = $this->container->get('newscoop_newscoop.publication_service');
        $mailer = $this->container->get('mailer');
        if (empty($from)) {
            $from = 'no-reply@' . $publicationService->getPublicationAlias()->getName();
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

            $mailer->send($messageToSend);
        } catch (\Exception $exception) {
            throw new \Exception("Error sending email.", 1);
        }
    }

    /**
     * Send comment notification
     *
     * @param Comment $comment
     * @param Article $article
     * @param array                   $authors
     * @param Newscoop\Entity\User    $user
     *
     * @return void
     */
    public function sendCommentNotification(Comment $comment, Article $article, array $authors, User $user = null)
    {
        $publicationService = $this->container->get('newscoop_newscoop.publication_service');
        $templatesService = $this->container->get('newscoop.templates.service');
        $placeholdersService = $this->container->get('newscoop.placeholders.service');
        $preferencesService = $this->container->get('preferences');
        $emails = array_unique(array_filter(array_map(function ($author) {
            return $author->getEmail();
        }, $authors)));

        if (empty($emails)) {
            return;
        }

        $smarty = $templatesService->getSmarty();
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
        $moderatorFrom = $publicationService->getPublication()->getModeratorFrom();

        if ($publicationService->getPublication()->getCommentsPublicModerated()) {
            $moderatorTo = $publicationService->getPublication()->getModeratorTo();
            $moderatorTo ? $emails['moderator'] = $moderatorTo : null;
        }

        $message = $templatesService->fetchTemplate("email_comment-notify.tpl");
        $this->send($placeholdersService->get('subject'), $message, $emails, $moderatorFrom ?: $preferencesService->EmailFromAddress);
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
