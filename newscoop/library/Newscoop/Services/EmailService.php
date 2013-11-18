<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

use Newscoop\Entity\User,
    Newscoop\Entity\Comment;

/**
 * Email service
 */
class EmailService
{
    const CHARSET = 'utf-8';
    const PLACEHOLDER_SUBJECT = 'subject';


    /** @var Zend_View_Abstract */
    private $view;

    /** @var Newscoop\Services\UserTokenService */
    private $tokenService;

    /**
     * @param Zend_View_Abstract $view
     * @param UserTokenService $tokenService
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
        $preferencesService = \Zend_Registry::get('container')->getService('system_preferences_service');
        $message = $this->view->action('confirm', 'email', 'default', array(
            'user' => $user->getId(),
            'token' => $this->tokenService->generateToken($user, 'email.confirm'),
            'format' => null,
        ));

        $this->send($this->view->placeholder(self::PLACEHOLDER_SUBJECT), $message, $user->getEmail(), $preferencesService->EmailFromAddress);
    }

    /**
     * Send password restore token
     *
     * @param Newscoop\Entity\User $user
     * @return void
     */
    public function sendPasswordRestoreToken(User $user)
    {   
        $preferencesService = \Zend_Registry::get('container')->getService('system_preferences_service');
        $message = $this->view->action('password-restore', 'email', 'default', array(
            'user' => $user->getId(),
            'token' => $this->tokenService->generateToken($user, 'password.restore'),
            'format' => null,
        ));

        $this->send($this->view->placeholder(self::PLACEHOLDER_SUBJECT), $message, $user->getEmail(), $preferencesService->EmailFromAddress);
    }

    /**
     * Send comment notification
     *
     * @param Newscoop\Entity\Comment $comment
     * @param Article $article
     * @param array $authors
     * @param Newscoop\Entity\User $user
     * @return void
     */
    public function sendCommentNotification(Comment $comment, \Article $article, array $authors, User $user = null)
    {
        $emails = array_unique(array_filter(array_map(function($author) { return $author->getEmail(); }, $authors)));
        if (empty($emails)) {
            return;
        }

        $preferencesService = \Zend_Registry::get('container')->getService('system_preferences_service');
        $this->view = $GLOBALS['controller']->view;
        $uri = \CampSite::GetURIInstance();

        $this->view->placeholder(self::PLACEHOLDER_SUBJECT)->set('New Comment');
        if ($user) {
            $this->view->username = $user->getUsername();
        }
        $this->view->comment = $comment;
        $this->view->article = $article;
        $this->view->publication = $uri->getBase();
        $this->view->articleLink = \ShortURL::GetURI($article->getPublicationId(), $article->getLanguageId(), $article->getIssueNumber(), $article->getSectionNumber(), $article->getArticleNumber());

        $message = $this->view->render('email_comment-notify.tpl');

        $mail = new \Zend_Mail(self::CHARSET);
        $mail->setSubject($this->view->placeholder(self::PLACEHOLDER_SUBJECT));
        $mail->setBodyHtml($message);
        $mail->setFrom($user ? $user->getEmail() : $preferencesService->EmailFromAddress);

        foreach ($emails as $email) {
            $mail->addTo($email);
        }

        $mail->send();
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
        $preferencesService = \Zend_Registry::get('container')->getService('system_preferences_service');
        $mail = new \Zend_Mail(self::CHARSET);
        $mail->setSubject($subject);
        $mail->setBodyText($message);
        $mail->setFrom(isset($from) ? $from : $preferencesService->EmailFromAddress);

        foreach ((array) $tos as $to) {
            $mail->addTo($to);
        }

        $mail->send();
    }
}
