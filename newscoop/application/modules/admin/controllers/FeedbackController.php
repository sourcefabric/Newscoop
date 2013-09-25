<?php
/**
 * @package Newscoop
 * @subpackage Feedback
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\Annotations\Acl;
use Newscoop\EventDispatcher\Events\GenericEvent;
use Newscoop\Entity\Feedback;

/**
 * @Acl(resource="feedback", action="manage")
 */
class Admin_FeedbackController extends Zend_Controller_Action
{

    /** @var Newscoop\Entity\Repository\FeedbackRepository */
    private $feedbackRepository;

    /** @var Admin_Form_Feedback */
    //private $form;

    /** @var Admin_Form_Feedback_EditForm */
    //private $editForm;

    public function init()
    {
        // get feedback repository
        $this->feedbackRepository = $this->_helper->entity->getRepository('Newscoop\Entity\Feedback');

        //$this->form = new Admin_Form_Comment;
        //$this->editForm = new Admin_Form_Comment_EditForm;

        return $this;
    }

    public function indexAction()
    {
        $this->_forward('table');
    }

    /**
     * Action to make the table
     */
    public function tableAction()
    {   
        $translator = \Zend_Registry::get('container')->getService('translator');
        $this->getHelper('contextSwitch')->addActionContext('table', 'json')->initContext();

        $view = $this->view;
        $table = $this->getHelper('datatable');
        /* @var $table Action_Helper_Datatable */
        $table->setDataSource($this->feedbackRepository);
        $table->setOption('oLanguage', array('oPaginate' => array(
                'sFirst' => $translator->trans('First', array(), 'comments'),
                'sLast' => $translator->trans('Last', array(), 'comments'),
                'sNext' => $translator->trans('Next'),
                'sPrevious' => $translator->trans('Previous'),
            ),
            'sZeroRecords' => $translator->trans('No records found.', array(), 'comments'),
            'sSearch' => $translator->trans('Search'),
            'sInfo' => $translator->trans('Showing _START_ to _END_ of _TOTAL_ entries', array(), 'comments'),
            'sEmpty' => $translator->trans('No entries to show', array(), 'comments'),
            'sInfoFiltered' => $translator->trans(' - filtering from _MAX_ records', array(), 'comments'),
            'sLengthMenu' => $translator->trans('Display _MENU_ records', array(), 'comments'),
            'sInfoEmpty' => '')
        );
        $table->setCols(
            array(
                'index' => $view->toggleCheckbox(), 'user' => $translator->trans('User', array(), 'comments'),
                'message' => $translator->trans('Date') . ' / ' . $translator->trans('Message', array(), 'comments'),
                'url' => $translator->trans('Coming from', array(), 'comments')
            ),
            array('index' => false)
        );

        $index = 1;
        $acceptanceRepository = $this->_helper->entity->getRepository('Newscoop\Entity\Comment\Acceptance');
        $table->setHandle(function($feedback) use ($view, &$index, $acceptanceRepository)
            {
                $user = $feedback->getUser();
                $url = $feedback->getUrl();
                $message = $feedback->getMessage();
                $publication = $feedback->getPublication();
                $section = $feedback->getSection();
                $article = $feedback->getArticle();
                
                if ($article) {
                    $article_name = $article->getName();
                    $article_url = $view->linkArticle($article);
                }
                else {
                    $article_name = $translator->trans('None', array(), 'comments');
                    $article_url = $view->baseUrl('admin/feedback');
                }
                
                if ($section) {
                    $section_name = $section->getName();
                }
                else {
                    $section_name = $translator->trans('None', array(), 'comments');
                }
                
                $attachment = array();

                $attachment['type'] = $feedback->getAttachmentType();
                $attachment['id'] = $feedback->getAttachmentId();

                if ($attachment['type'] == 'image') {
                    $image = new Image($attachment['id']);
                    $attachment['name'] = $image->getImageFileName();
                    $attachment['status'] = $image->getStatus();
                    $attachment['thumbnail'] = $image->getThumbnailUrl();
                    $attachment['approve_url'] = $view->url(array('action' => 'approve', 'type' => 'image', 'format' => 'json', 'id' => $attachment['id']));
                }
                if ($attachment['type'] == 'document') {
                    $document = new Attachment($attachment['id']);
                    $attachment['name'] = $document->getFileName();
                    $attachment['status'] = $document->getStatus();
                    $attachment['approve_url'] = $view->url(array('action' => 'approve', 'type' => 'document', 'format' => 'json', 'id' => $attachment['id']));
                }

                $banned = $acceptanceRepository->checkBanned(array('name' => $user->getName(), 'email' => '', 'ip' => ''), $publication);
                if ($banned['name'] == true) {
                    $banned = true;
                } else {
                    $banned = false;
                }

                return array(
                    'index' => $index++,
                    'user' => array(
                        'username' => $user->getUsername(),
                        'name' => $user->getFirstName(),
                        'email' => $user->getEmail(),
                        'avatar' => (string)$view->getAvatar($user->getEmail(), array('img_size' => 50, 'default_img' => 'wavatar')),
                        'banurl' => $view->url(array(
                            'controller' => 'user',
                            'action' => 'toggle-ban',
                            'user' => $user->getId(),
                            'publication' => $publication->getId()
                        )),
                        'is_banned' => $banned
                    ),
                    'message' => array(
                        'id' => $feedback->getId(),
                        'created' => array(
                            'date' => $feedback->getTimeCreated()->format('Y.m.d'),
                            'time' => $feedback->getTimeCreated()->format('H:i:s')
                        ),
                        'message' => $feedback->getMessage(),
                        'subject' => $feedback->getSubject(),
                        'status' => $feedback->getStatus(),
                        'attachmentType' => $feedback->getAttachmentType(),
                        'action' => array(
                            'reply' => $view->url(array(
                                'action' => 'reply',
                                'format' => 'json'
                            ))
                        ),
                        'url' => $url,
                        'publication' => $publication->getName(),
                        'section' => $section_name,
                        'article' => array(
                            'name' => $article_name,
                            'url' => $article_url
                        )
                    ),
                    'attachment' => $attachment
                );
            });

        $table->setOption('fnDrawCallback', 'datatableCallback.draw')
                ->setOption('fnRowCallback', 'datatableCallback.row')
                ->setOption('fnServerData', 'datatableCallback.addServerData')
                ->setOption('fnInitComplete', 'datatableCallback.init')
                ->setOption('sDom','<"top">lf<"#actionExtender">rit<"bottom"ip>')
                ->setStripClasses()
                ->toggleAutomaticWidth(false)
                ->setDataProp(array('index' => null, 'user' => null, 'message' => null, 'url' => null))
                ->setClasses(array('index' => 'commentId', 'user' => 'commentUser', 'message' => 'commentTimeCreated', 'url' => 'commentThread'));

        try {
            $table->dispatch();
        } catch (Exception $e) {
            var_dump($e);
            exit;
        }
        //$this->editForm->setSimpleDecorate()->setAction($this->_helper->url('update'));
        //$this->view->editForm = $this->editForm;
    }

    /**
     * Status action
     */
    public function setStatusAction()
    {   
        $translator = \Zend_Registry::get('container')->getService('translator');
        $this->getHelper('contextSwitch')->addActionContext('set-status', 'json')->initContext();
        if (!SecurityToken::isValid()) {
            $this->view->status = 401;
            $this->view->message = $translator->trans('Invalid security token!');
            return;
        }

        $status = $this->getRequest()->getParam('status');
        $feedbacks = $this->getRequest()->getParam('feedback');
        if (!is_array($feedbacks)) {
            $feedbacks = array($feedbacks);
        }

        try {
            foreach ($feedbacks as $id) {
                $feedback = $this->feedbackRepository->find($id);
            }
            $this->feedbackRepository->setStatus($feedbacks, $status);
            $this->feedbackRepository->flush();
        } catch (Exception $e) {
            $this->view->status = $e->getCode();
            $this->view->message = $e->getMessage();
            return;
        }
        $this->view->status = 200;
        $this->view->message = 'succcesful';
    }

    /**
     * Reply action
     */
    public function replyAction()
    {
        $this->getHelper('contextSwitch')->addActionContext('reply', 'json')->initContext();

        $auth = Zend_Auth::getInstance();
        $user = new User($auth->getIdentity());
        $fromEmail = $user->getEmail();

        $feedbackId = $this->getRequest()->getParam('parent');
        $subject = $this->getRequest()->getParam('subject');
        $message = $this->getRequest()->getParam('message');

        $feedback = $this->feedbackRepository->find($feedbackId);
        $user = $feedback->getUser();
        $toEmail = $user->getEmail();

        /*
        $configMail = array( 'auth' => 'login',
                             'username' => 'user@gmail.com',
                             'password' => 'password',
                             'ssl' => 'ssl',
                             'port' => 465
        );
        $mailTransport = new Zend_Mail_Transport_Smtp('smtp.gmail.com',$configMail);
        */
        $mail = new Zend_Mail('utf-8');
        $mail->setSubject($subject);
        $mail->setBodyText($message);
        $mail->setFrom($fromEmail);
        $mail->addTo($toEmail);
        try {
            $mail->send();
            $this->view->status = 200;
            $this->view->message = 'succcesful';
        }
        catch (Exception $e) {
            $this->view->status = 200;
            $this->view->message = 'succcesful?';
        }
    }

    /**
     * Approve action
     */
    public function approveAction()
    {
        $this->getHelper('contextSwitch')->addActionContext('approve', 'json')->initContext();

        $parameters = $this->getRequest()->getParams();

        if ($parameters['type'] == 'image') {
            $image = new Image($parameters['id']);
            $image->update(array('Status' => 'approved'));

            $user_id = $image->getUploadingUserId();
            $user = $this->_helper->service('user')->find($user_id);
            $this->_helper->service->getService('dispatcher')
                ->dispatch('image.approved', new GenericEvent($this, array(
                    'user' => $user,
                    'image_id' => $parameters['id']
                )));
        }
        if ($parameters['type'] == 'document') {
            $document = new Attachment($parameters['id']);
            $document->update(array('Status' => 'approved'));

            $user_id = $document->getUploadingUserId();
            $user = $this->_helper->service('user')->find($user_id);
            $this->_helper->service->getService('dispatcher')
                ->dispatch('document.approved', new GenericEvent($this, array(
                    'user' => $user,
                    'document_id' => $parameters['id']
                )));
        }
    }
}
