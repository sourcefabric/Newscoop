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
        $table->setDataSource($this->commentRepository);
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
        $table->setCols(array('index' => $view->toggleCheckbox(), 'commenter' => $translator->trans('Author'),
                             'comment' => $translator->trans('Date') . ' / ' . $translator->trans('Comment'), 'thread' => $translator->trans('Article'),
                             'threadorder' => '',), array('index' => false));

        $table->setInitialSorting(array('comment' => 'desc'));

        $index = 1;
        $acl = array();
        $acl['edit'] = $this->_helper->acl->isAllowed('comment', 'edit');
        $acl['enable'] = $this->_helper->acl->isAllowed('comment', 'enable');
        $acceptanceRepository = $this->_helper->entity->getRepository('Newscoop\Entity\Comment\Acceptance');
        $articleRepository = $this->_helper->entity->getRepository('Newscoop\Entity\Article');

        $table->setHandle(function($comment) use ($view, $acl, $acceptanceRepository, &$index) {
            /* var Newscoop\Entity\Comment\Commenter */
            $commenter = $comment->getCommenter();
            $thread = $comment->getThread();

            $articleNo = $comment->getArticleNumber();
            $commentLang = $comment->getLanguage()->getId();
            $article = new Article($commentLang, $articleNo);

            $forum = $comment->getForum();
            $section = $thread->getSection();
            return array(
                'index' => $index++, 
                'can' => array('enable' => $acl['enable'], 'edit' => $acl['edit']),
                'commenter' =>array(
                    'username' => $commenter->getUsername(), 'name' => $commenter->getName(), 'usernameEncoded' => urlencode($commenter->getUsername()),
                    'email' => $commenter->getEmail(),
                    'avatar' => (string)$view->getAvatar($commenter->getEmail(), array('img_size' => 50,
                                                                                     'default_img' => 'wavatar')),
                    /**
                     * @todo have this in the commenter entity as a flag isBanned witch is checked when a ban is done
                     *       for a faster result not having sql checks insides for statements
                     *       this needs entity changes can't be done in the bug release stage
                     */
                    'is' => array('banned' => $acceptanceRepository->isBanned($commenter, null)),
                    'trace' => array('ip' => 'http://www.ip-adress.com/ip_tracer/'.$commenter->getIp()),
                    'ip' => $commenter->getIp(), 'url' => $commenter->getUrl(),
                    'banurl' => $view->url(array(
                        'controller' => 'comment-commenter', 
                        'action' => 'toggle-ban',
                        'commenter' => $commenter->getId(), 
                        'thread' => $comment->getArticleNumber(), 
                        'language' => $comment->getLanguage()->getId()))),
                        'comment' => array(
                            'id' => $comment->getId(),
                            'created' => array(
                                'date' => $comment->getTimeCreated()->format('Y.m.d'),
                                'time' => $comment->getTimeCreated()->format('H:i:s')),
                                'subject' => $comment->getSubject(),
                                'message' => $comment->getMessage(), 
                                'likes' => '', 
                                'dislikes' => '',
                                'status' => $comment->getStatus(),
                                'recommended' => $comment->getRecommended(),
                                'action' => array('update' => $view->url(array(
                                    'action' => 'update', 'format' => 'json'
                                )),
                                'reply' => $view->url(array(
                                    'action' => 'reply', 
                                    'format' => 'json'
                                )
                            )
                        )
                    ),
                    'thread' => array(
                        'name' => $view->escape($article->getName()),
                        'link' => array(
                            'edit' => $view->baseUrl("admin/articles/edit.php?") . $view->linkArticleObj($article),
                            'get' => $view->baseUrl("admin/articles/get.php?") . $view->linkArticleObj($article)
                        ),
                        'forum' => array(
                            'name' => $view->escape($forum->getName())
                        ),
                        'section' => array(
                            'name' => ($section) ? $view->escape($section->getName()) : null
                        )
                    )
                );
        });

        $table->setOption('fnDrawCallback', 'datatableCallback.draw')
            ->setOption('fnRowCallback', 'datatableCallback.row')
            ->setOption('fnServerData', 'datatableCallback.addServerData')
            ->setOption('fnInitComplete', 'datatableCallback.init')
            ->setOption('sDom','<"top">lf<"#actionExtender">rit<"bottom"ip>')
            ->setStripClasses()
            ->toggleAutomaticWidth(false)
            ->setDataProp(array('index' => null, 'commenter' => null, 'comment' => null, 'thread' => null,
                'threadorder' => null))->setVisible(array('threadorder' => false))
            ->setClasses(array('index' => 'commentId', 'commenter' => 'commentUser', 'comment' => 'commentTimeCreated',
                'thread' => 'commentThread'));

        $table->dispatch();
        $this->editForm->setSimpleDecorate()->setAction($this->_helper->url('update'));
        $this->view->editForm = $this->editForm;
    }

    /**
    * Function to return all replies to a comment
    * Works by recursion
    * @param $p_comment_id (array or integer)
    * returns an array or comment ids
    */
    public function getAllReplies($p_comment_id)
    {
         if(!is_array($p_comment_id)) {
            $directReplies = $this->commentRepository->getDirectReplies($p_comment_id);
            if(count($directReplies)) {
                return array_merge( array($p_comment_id), $this->getAllReplies($directReplies) );
            } else {
                return array($p_comment_id);
            }
         } else {
            if(count($p_comment_id) > 1) {
                return array_merge(
                    $this->getAllReplies(array_pop($p_comment_id)),
                    $this->getAllReplies($p_comment_id)
                    );
            } else {
                return $this->getAllReplies(array_pop($p_comment_id));
            }
         }
    }

    /**
     * Action for setting a status
     * @Acl(action="moderate")
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
        $comments = $this->getRequest()->getParam('comment');
        if (!is_array($comments)) {
            $comments = array($comments);
        }

        if($status == "deleted") {
            $comments = array_unique(array_merge($comments, $this->getAllReplies($comments)));
        }

        try {
            foreach ($comments as $id) {
                $comment = $this->commentRepository->find($id);

                if ($status == "deleted") {
                    $msg = $translator->trans('Comment delete by $1 from the article $2 ($3)', array('$1' => Zend_Registry::get('user')->getName(),
                                '$2' => $comment->getThread()->getName(), '$3' => $comment->getLanguage()->getCode()), 'comments');

                    $this->_helper->flashMessenger($msg);
                } else {
                    $msg = $translator->trans('Comment $4 by $1 in the article $2 ($3)', array('$1' => Zend_Registry::get('user')->getName(),
                                '$2' => $comment->getThread()->getName(), '$3' => $comment->getLanguage()->getCode(), '$4' => $status), 'comments');
                    $this->_helper->flashMessenger($msg);
                }
            }
            $this->commentRepository->setStatus($comments, $status);
            $this->commentRepository->flush();
        } catch (Exception $e) {
            $this->view->status = $e->getCode();
            $this->view->message = $e->getMessage();
            return;
        }
        $this->view->status = 200;
        $this->view->message = "succcesful";
    }

    /**
     * Action for setting a status
     * @Acl(action="moderate")
     */
    public function setRecommendedAction()
    {   
        $translator = \Zend_Registry::get('container')->getService('translator');
        $this->getHelper('contextSwitch')->addActionContext('set-recommended', 'json')->initContext();
        if (!SecurityToken::isValid()) {
            $this->view->status = 401;
            $this->view->message = $translator->trans('Invalid security token!');
            return;
        }

        $comments = $this->getRequest()->getParam('comment');
        $recommended = $this->getRequest()->getParam('recommended');

        if (!is_array($comments)) {
            $comments = array($comments);
        }

        foreach ($comments as $commentId) {
            if (!$recommended) {
                continue;
            }

            $comment = $this->commentRepository->find($commentId);
            $this->_helper->service->getService('dispatcher')
                ->dispatch('comment.recommended', new GenericEvent($this, array(
                    'id' => $comment->getId(),
                    'subject' => $comment->getSubject(),
                    'article' => $comment->getThread()->getName(),
                    'commenter' => $comment->getCommenterName(),
                )));
        }

        try {
            $this->commentRepository->setRecommended($comments, $recommended);
            $this->commentRepository->flush();
        } catch (Exception $e) {
            $this->view->status = $e->getCode();
            $this->view->message = $e->getMessage();
            return;
        }

        $this->view->status = 200;
        $this->view->message = "succcesful";
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

    /*
     * Action for listing the comments per article
     */

    public function listAction()
    {
        $this->getHelper('contextSwitch')->addActionContext('list', 'json')->initContext();
        $cols = array('thread_order' => 'default');
        $article = $this->getRequest()->getParam('article');
        $language = $this->getRequest()->getParam('language');
        $comment = $this->getRequest()->getParam('comment');

        if ($article) {
            $filter = array('thread' => $article, 'language' => $language,);
        } elseif ($comment) {
            $filter = array('id' => $comment,);
        }

        $params = array(
            'sFilter' => $filter,
            'iDisplayStart' => $this->getRequest()->getParam('iDisplayStart') != null ? $this->getRequest()->getParam('iDisplayStart') : 0,
            'iDisplayLength' => $this->getRequest()->getParam('iDisplayLength'),
            'iSortCol_0' => 0,
            'sSortDir_0' => 'desc'
        );

        /* var Comment[] */
        $comments = $this->commentRepository->getData($params, $cols);
        $result = array();
        foreach ($comments as $comment) {
            /* @var $comment Newscoop\Entity\Comment */
            $commenter = $comment->getCommenter();
            $result[] = array(
                'name' => $commenter->getName(),
                'email' => $commenter->getEmail(),
                'ip' => $commenter->getIp(),
                'id' => $comment->getId(),
                'status' => $comment->getStatus(),
                'subject' => $comment->getSubject(),
                'message' => $comment->getMessage(),
                'time_created' => $comment->getTimeCreated()->format('Y-m-d H:i:s'),
                'recommended_toggle' => (int) !$comment->getRecommended(),
            );
        }

        $this->view->result = $result;
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