<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

use Newscoop\EventDispatcher\Events\GenericEvent;

/**
 */
class CommentNotificationService
{
    /** @var Newscoop\Services\EmailService */
    private $emailService;

    /** @var Newscoop\Services\CommentService */
    private $commentService;

    /** @var Newscoop\Services\UserService */
    private $userService;

    /**
     * @param Newscoop\Services\EmailService $emailService
     * @param Newscoop\Services\CommentService $commentService
     */
    public function __construct(EmailService $emailService, CommentService $commentService, UserService $userService)
    {
        $this->emailService = $emailService;
        $this->commentService = $commentService;
        $this->userService = $userService;
    }

    /**
     * Update
     *
     * @param GenericEvent $event
     * @return void
     */
    public function update(GenericEvent $event)
    {
        // $comment = $this->commentService->find($event['id']);
        // $article = new \Article($comment->getLanguage()->getId(), $comment->getThread()->getNumber());
        // $authors = \ArticleAuthor::GetAuthorsByArticle($comment->getThread()->getNumber(), $comment->getLanguage()->getId());
        // $this->emailService->sendCommentNotification($comment, $article, $authors, $this->userService->getCurrentUser());
    }
}
