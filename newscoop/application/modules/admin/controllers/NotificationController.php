<?php
/**
 * @package Newscoop
 * @subpackage Subscriptions
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 *
 *
 */
use Newscoop\Entity\Comment,
    Newscoop\Entity\Publication,
    Newscoop\Entity\Notification;

/**
 */
class Admin_NotificationController extends Zend_Controller_Action
{
    /**
     * @var Newscoop\Entity\Comment
     */
    private $commentRepository = NULL;

    /**
     * @var Newscoop\Entity\Notification
     */
    private $notificationRepository = NULL;

    public function init()
    {
        $this->commentRepository = $this->_helper->entity->getRepository('Newscoop\Entity\Comment');
        $this->notificationRepository = $this->_helper->entity->getRepository('Newscoop\Entity\Notification');
    }

    /**
     * @Acl(action="moderate-comment")
     */
    public function moderateCommentAction()
    {
        /**
         * @todo these two lines can be processed at script setup time (e.g., config.inc or similar)
         */
        try {
            /*
            $configMail = array( 'auth' => 'login',
                                 'username' => 'user@gmail.com',
                                 'password' => 'password',
                                 'ssl' => 'ssl',
                                 'port' => 465
            );
            $mailTransport = new Zend_Mail_Transport_Smtp('smtp.gmail.com',$configMail);
            $comment = $this->commentRepository->find($this->getRequest()->getParam('comment'));
            $moderatorTo = $comment->getForum()->getModeratorTo();
            $moderatorFrom = $comment->getForum()->getModeratorFrom();

            $message = $this->view->moderateBodyComment($comment);
            $subject = $this->view->moderateSubjectComment($comment);
            $mail = new Zend_Mail();
            $mail->setBodyText($message);
            $mail->setFrom($moderatorFrom);
            $mail->addTo($moderatorTo);
            $mail->setSubject($subject);
            //$mail->send();
            */
            $headers = 'From: ' . $moderatorFrom. "\r\n" .
                'Reply-To: ' . $moderatorFrom . "\r\n";
            mail($moderatorTo, $subject, $message, $headers);

        } catch (Zend_Exception $e) {
            echo $e->getMessage();
        }
        $this->getHelper('viewRenderer')->setNoRender();
    }
}
