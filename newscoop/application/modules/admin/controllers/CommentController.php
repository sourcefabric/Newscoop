<?php
/**
 * @package Newscoop
 * @subpackage Subscriptions
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\Annotations\Acl;
use Newscoop\Entity\Comment;
use Newscoop\EventDispatcher\Events\GenericEvent;

/**
 * @Acl(resource="comment", action="enable")
 */
class Admin_CommentController extends Zend_Controller_Action
{

    /** @var Newscoop\Entity\Repository\CommentRepository */
    private $commentRepository;

    /** @var Newscoop\Entity\Repository\ArticleRepository */
    private $articleRepository;

    /** @var Newscoop\Entity\Repository\LanguageRepository */
    private $languageRepository;

    /** @var Newscoop\Entity\Repository\Comment\AcceptanceRepository */
    private $acceptanceRepository;

    /** @var Admin_Form_Comment */
    private $form;

    /** @var Admin_Form_Comment_EditForm */
    private $editForm;

    public function init()
    {

        // get comment repository
        $this->commentRepository = $this->_helper->entity->getRepository('Newscoop\Entity\Comment');

        // get article repository
        $this->articleRepository = $this->_helper->entity->getRepository('Newscoop\Entity\Article');

        // get language repository
        $this->languageRepository = $this->_helper->entity->getRepository('Newscoop\Entity\Language');

        $this->form = new Admin_Form_Comment;

        $this->editForm = new Admin_Form_Comment_EditForm;

        return $this;
    }

    /**
     *
     */
    public function addToArticleAction()
    {   
        $translator = \Zend_Registry::get('container')->getService('translator');
        $this->getHelper('contextSwitch')->addActionContext('add-to-article', 'json')->initContext();
        $comment = new Comment;
        $request = $this->getRequest();

        $values['user']         = Zend_Registry::get('user');
        $values['name']         = $request->getParam('name');
        $values['subject']      = $request->getParam('subject');
        $values['message']      = $request->getParam('message');
        $values['language']     = $request->getParam('language');
        $values['thread']       = $request->getParam('article');
        $values['ip']           = $request->getClientIp();
        $values['status']       = 'approved';
        $values['time_created'] = new DateTime;

        if (!SecurityToken::isValid()) {
            $this->view->status = 401;
            $this->view->message = $translator->trans('Invalid security token!');
            return;
        }

        try {
            $comment = $this->commentRepository->save($comment, $values);
            $this->commentRepository->flush();
        } catch (Exception $e) {
            $this->view->status = $e->getCode();
            $this->view->message = $e->getMessage();
            return;
        }

        $this->view->status = 200;
        $this->view->message = "succcesful";
        $this->view->comment = $comment->getId();
    }

    /**
     * @Acl(action="edit")
     */
    public function updateContentsAction()
    {
        $translator = \Zend_Registry::get('container')->getService('translator');
        $this->getHelper('contextSwitch')->addActionContext('update-contents', 'json')->initContext();
        if (!SecurityToken::isValid()) {
            $this->view->status = 401;
            $this->view->message = $translator->trans('Invalid security token!');
            return;
        }

        $subject = $this->getRequest()->getParam('subject');
        $body = $this->getRequest()->getParam('body');
        $id = $this->getRequest()->getParam('id');

        $values = array(
            'subject' => $subject,
            'message' => $body
        );

        try {
            $comment = $this->commentRepository->find($id);
            $comment = $this->commentRepository->update($comment, $values);
            $this->commentRepository->flush();
        }
        catch (Exception $e) {
            $this->view->status = $e->getCode();
            $this->view->message = $e->getMessage();
            return;
        }
        $this->view->status = 200;
        $this->view->message = "succesful";
    }

    /**
     * @Acl(action="edit")
     */
    public function editAction()
    {
        $this->editForm->setAction($this->_helper->url('update'));
        $this->view->form = $this->editForm;
    }

    /**
     * @Acl(action="edit")
     */
    public function updateAction()
    {
        if (!$this->editForm->isValid($this->_getAllParams())) {
            $return = array('status' => 101, 'message' => 'invalid', 'data' => $this->editForm->getMessages());
            $this->_helper->json($return);
        }

        try {
            $values = $this->editForm->getValues();
            $comment = $this->commentRepository->find($values['id']);
            $comment = $this->commentRepository->update($comment, $values);
            $this->commentRepository->flush();
        } catch (Exception $e) {
            $return = array('status' => $e->getCode(), 'message' => $e->getMessage(), 'data' => array());
            $this->_helper->json($return);
        }

        $return = array('status' => 100, 'message' => 'succesful', 'data' => array('comment' => $comment->getId()));
        $this->_helper->json($return);
    }

    public function replyAction()
    {   
        $translator = \Zend_Registry::get('container')->getService('translator');
        $this->getHelper('contextSwitch')->addActionContext('reply', 'json')->initContext();

        if (!SecurityToken::isValid()) {
            $this->view->status = 401;
            $this->view->message = $translator->trans('Invalid security token!');
            return;
        }
        $values = $this->getRequest()->getParams();
        $comment = new Comment;
        if ($this->getRequest()->isPost()) {
            $values['user'] = Zend_Registry::get('user');
            $values['time_created'] = new DateTime;
            $values['ip'] = $this->getRequest()->getClientIp();
            $values['status'] = 'approved';
            try {
                $comment = $this->commentRepository->save($comment, $values);
                $this->commentRepository->flush();
            } catch (Exception $e) {
                $this->view->status = $e->getCode();
                $this->view->message = $e->getMessage();
                return;
            }

            $this->view->status = 200;
            $this->view->message = "succcesful";
            $this->view->comment = $comment->getId();
        }
        $this->view->comment = $comment;
    }

    public function addAction()
    {
        $comment = new Comment;

        $this->handleForm($this->form, $comment);

        $this->view->form = $this->form;
        $this->view->comment = $comment;
    }

    /**
     * @Acl(action="moderate")
     */
    public function deleteArticleAction()
    {
        $article = $this->getRequest()->getParam('article');
        $this->commentRepository->deleteArticle($article);
        $this->commentRepository->flush();
        $this->getHelper('viewRenderer')->setNoRender();
    }

    /**
     * @Acl(action="moderate")
     */
    public function statusArticleAction()
    {
        $article = $this->getRequest()->getParam('article');
        $language = $this->getRequest()->getParam('language');
        $this->commentRepository->setArticleStatus($article, $language, "hidden");
        $this->commentRepository->flush();
        $this->getHelper('viewRenderer')->setNoRender();
    }

    private function handleForm(Zend_Form $p_form, Comment $p_comment)
    {   
        $translator = \Zend_Registry::get('container')->getService('translator');
        if ($this->getRequest()->isPost() && $p_form->isValid($_POST)) {
            $values = $p_form->getValues();
            $values['ip'] = $this->getRequest()->getClientIp();
            $values['status'] = 'hidden';
            $values['time_created'] = new DateTime;
            $this->commentRepository->save($p_comment, $values);
            $this->commentRepository->flush();
            $this->_helper->flashMessenger($translator->trans('Comment $1 saved.', array('$1' => $p_comment->getSubject()), 'comments'));
            $this->_helper->redirector->gotoSimple('index');
        }
    }



}