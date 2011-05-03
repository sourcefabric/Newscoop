<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity\Repository;

use Newscoop\Entity;

use Newscoop\Entity\Notification,
    Newscoop\Entity\Comment;

class NotificationRepository
{

    /**
     * Get new instance of the notification
     */
    public function getPrototype()
    {
        return new Notification;
    }

    /**
     * Add to notification
     *
     * @return unknown_type
     */
    public function addComment(Comment $comment)
    {
        $notification = $this->getPrototype();
        // set content of the notification
        $content = array();
        $content[Notification::COMMENT_NAME]        = $comment->getCommenterName();
        $content[Notification::COMMENT_EMAIL]       = $comment->getCommenterEmail();
        $content[Notification::COMMENT_IP]          = $comment->getIp();
        $content[Notification::COMMENT_SUBJECT]     = $comment->getSubject();
        $content[Notification::COMMENT_MESSAGE]     = $comment->getMessage();
        $content[Notification::COMMENT_PUBLICATION] = $comment->getPublicationId();
        //serialize the content
        $notification->setContent(serialize($content));
        $notification->setType(Notification::TYPE_COMMENT);
        $notification->setStatus(Notification::STATUS_PENDING);
        $this->getEntityManager()->persist($notification);
        $this->flush();
    }

    /**
     * Flush method
     */
    public function flush()
    {
        $this->getEntityManager()->flush();
    }
}