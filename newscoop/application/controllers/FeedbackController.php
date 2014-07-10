<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Feedback controller
 */

use Newscoop\Entity\Feedback;
use Newscoop\EventDispatcher\Events\GenericEvent;

require_once($GLOBALS['g_campsiteDir'].'/include/captcha/php-captcha.inc.php');
require_once($GLOBALS['g_campsiteDir']. '/classes/Plupload.php');

class FeedbackController extends Zend_Controller_Action
{
    public function init()
    {
        $this->getHelper('contextSwitch')->addActionContext('save', 'json')->initContext();
        $this->getHelper('contextSwitch')->addActionContext('upload', 'json')->initContext();
    }

    public function saveAction()
    {
        $translator = Zend_Registry::get('container')->getService('translator');
        $userService = Zend_Registry::get('container')->getService('user');
        $this->_helper->layout->disableLayout();
        $parameters = $this->getRequest()->getParams();

        $errors = array();

        $auth = Zend_Auth::getInstance();

        $publication = new Publication($parameters['f_publication']);

        if ($auth->getIdentity()) {
            $acceptanceRepository = $this->getHelper('entity')->getRepository('Newscoop\Entity\Comment\Acceptance');
            $user = new User($auth->getIdentity());

            $userIp = $userService->getUserIp();
            if ($acceptanceRepository->checkParamsBanned($user->m_data['Name'], $user->m_data['EMail'], $userIp, $parameters['f_publication'])) {
                $errors[] = $translator->trans('You have been banned from writing feedbacks.');
            }
        } else {
            $errors[] = $translator->trans('You are not logged in.');
        }

        if (!array_key_exists('f_feedback_content', $parameters) || empty($parameters['f_feedback_content'])) {
            $errors[] = $translator->trans('Feedback content was not filled in.');
        }

        if (empty($errors)) {
            $feedbackRepository = $this->getHelper('entity')->getRepository('Newscoop\Entity\Feedback');
            $feedback = new Feedback();

            $values = array(
                'user' => $auth->getIdentity(),
                'publication' => $parameters['f_publication'],
                'section' => $parameters['f_section'],
                'article' => $parameters['f_article'],
                'subject' => $parameters['f_feedback_subject'],
                'message' => $parameters['f_feedback_content'],
                'url' => $parameters['f_feedback_url'],
                'time_created' => new DateTime(),
                'language' => $parameters['f_language'],
                'status' => 'pending',
                'attachment_type' => 'none',
                'attachment_id' => 0
            );

            if (isset($parameters['image_id'])) {
                $values['attachment_type'] = 'image';
                $values['attachment_id'] = $parameters['image_id'];

                $feedbackRepository->save($feedback, $values);
                $feedbackRepository->flush();

                $current_user = $this->_helper->service('user')->getCurrentUser();            
                $this->_helper->service->getService('dispatcher')
                    ->dispatch('image.delivered', new GenericEvent($this, array(
                        'user' => $current_user,
                        'image_id' => $values['attachment_id']
                    )));

                $this->view->response = $translator->trans('File is uploaded and your message is sent.');
            }
            else if (isset($parameters['document_id'])) {
                $values['attachment_type'] = 'document';
                $values['attachment_id'] = $parameters['document_id'];

                $feedbackRepository->save($feedback, $values);
                $feedbackRepository->flush();

                $current_user = $this->_helper->service('user')->getCurrentUser();
                $this->_helper->service->getService('dispatcher')
                    ->dispatch('document.delivered', new GenericEvent($this, array(
                        'user' => $current_user,
                        'document_id' => $values['attachment_id']
                    )));

                $this->view->response = $translator->trans('File is uploaded and your message is sent.');
            }
            else {
                $feedbackRepository->save($feedback, $values);
                $feedbackRepository->flush();

                $this->view->response = $translator->trans('Your message is sent.');
            }
        }
        else {
            $errors = implode('<br>', $errors);
            $errors = $translator->trans('Following errors have been found:') . '<br>' . $errors;
            $this->view->response = $errors;
        }
    }

    public function uploadAction()
    {
        global $Campsite;

        $auth = Zend_Auth::getInstance();
        $userId = $auth->getIdentity();

        $_FILES['file']['name'] = preg_replace('/[^\w\._]+/', '', $_FILES['file']['name']);

        $mimeType = $_FILES['file']['type'];
        $type = explode('/', $mimeType);

        if ($type[0] == 'image') {
            $file = Plupload::OnMultiFileUploadCustom($Campsite['IMAGE_DIRECTORY']);
            $image = Image::ProcessFile($_FILES['file']['name'], $_FILES['file']['name'], $userId, array('Source' => 'feedback', 'Status' => 'Unapproved', 'Date' => date('Y-m-d')));
            $this->view->response = 'image_'.$image->getImageId();
        }
        else if ($type[1] == 'pdf') {
            $attachment = new Attachment();
            $attachment->makeDirectories();

            $file = Plupload::OnMultiFileUploadCustom($attachment->getStorageLocation());
            $document = Attachment::ProcessFile($_FILES['file']['name'], $_FILES['file']['name'], $userId, array('Source' => 'feedback', 'Status' => 'Unapproved'));
            $this->view->response = 'pdf_'.$document->getAttachmentId();
        }
    }

    public function indexAction()
    {
        $this->view->param = $this->_getParam('switch');
    }
}
