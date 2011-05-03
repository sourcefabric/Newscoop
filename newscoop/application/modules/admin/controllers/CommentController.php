<?php
use Newscoop\Entity\Comment;

class Admin_CommentController extends Zend_Controller_Action
{

    /**
     * @var ICommentRepository
     *
     */
    private $repository;

    /**
     * @var IArticleRepository
     *
     */
    private $articleRepository;

    /**
     * @var IAcceptanceRepository
     *
     */
    private $acceptanceRepository;

    /**
     *
     * @var Admin_Form_Comment
     */
    private $form;


    public function init()
    {
        // get comment repository
        $this->repository = $this->_helper->entity->getRepository('Newscoop\Entity\Comment');
        // get article repository
        $this->articleRepository = $this->_helper->entity->getRepository('Newscoop\Entity\Article');

        $this->form = new Admin_Form_Comment;
        $this->form->setMethod('post');

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
        $view = $this->view;
        $table = $this->getHelper('datatable');

        $table->setDataSource($this->repository);

        $table->setCols(array(
            'id' => $view->toggleCheckbox(),
            'user' => getGS('Author'),
            'action' => '',
            'time_created' => getGS('Date').' / '.getGS('Comment'),
            'thread' => getGS('Article')
        ),array('id' => false, 'action' => false));


        $table->setHandle(function($comment) use ($view) {
            return array(
                $view->commentIndex($comment),
                $view->commentCommenter($comment->getCommenter()),
                $view->commentAction($comment),
                $view->commentMessage($comment),
                $view->commentArticle($comment->getThread())
            );
        });

        $table->setOption('fnRowCallback','datatableCallback.row')
              ->setOption('fnServerData', 'datatableCallback.addServerData')
              ->setStripClasses()
              ->toggleAutomaticWidth(false)
              ->setClasses(array(
                'id'   => 'commentId',
                'user' => 'commentUser',
                'action' => 'commentAction',
                'time_created' => 'commentTimeCreated',
                'thread' => 'commentThread'));
        $table->dispatch();
    }

    /**
     * Action for setting a status
     */
    public function setStatusAction()
    {

        $this->getHelper('contextSwitch')
            ->addActionContext('set-status', 'json')
            ->initContext();
        if (!SecurityToken::isValid()) {
            $this->view->status = 401;
            $this->view->message = getGS('Invalid security token!');
            return;
        }
        try
        {
            $comment = $this->getRequest()->getParam('comment');
            $status = $this->getRequest()->getParam('status');
            if(!is_array($comment))
                $comment = array($comment);
            $this->repository->setStatus($comment,$status);
            $this->repository->flush();
        }
        catch(Exception $e)
        {
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
       $this->getHelper('contextSwitch')
            ->addActionContext('add-to-article', 'json')
            ->initContext();
        $comment = new Comment;
        $request = $this->getRequest();
        $values['user'] = Zend_Registry::get('user');
        $values['name'] = $request->getParam('name');
        $values['subject'] = $request->getParam('subject');
        $values['message'] = $request->getParam('message');
        $values['language'] = $request->getParam('language');
        $values['thread'] =  $request->getParam('article');
        $values['ip'] = $request->getClientIp();
        $values['status'] = 'approved';
        $values['time_created'] = new DateTime;

        if (!SecurityToken::isValid()) {
            $this->view->status = 401;
            $this->view->message = getGS('Invalid security token!');
            return;
        }
        try
        {
            $this->repository->save($comment, $values);
            $this->repository->flush();
        }
        catch(Exception $e)
        {
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
        $this->getHelper('contextSwitch')
            ->addActionContext('list', 'json')
            ->initContext();

        $cols = array('thread_order' => 'default');
        $article = $this->getRequest()->getParam('article');
        $language = $this->getRequest()->getParam('language');
        $comment = $this->getRequest()->getParam('comment');
        if($article)
            $filter = array(
                'thread'   => $article,
                'language' => $language,
            );
        elseif($comment)
            $filter = array(
                'id' => $comment,
            );
        $params = array(
            'sFilter'        => $filter
        );
        $comments = $this->repository->getData($params, $cols);
        $result = array();
        foreach($comments as $comment) {
            $commenter = $comment->getCommenter();
            $result[] = array(
                "name"    => $commenter->getName(),
                "email"   => $commenter->getEmail(),
                "ip"      => $commenter->getIp(),
                "id"      => $comment->getId(),
                "status"  => $comment->getStatus(),
                "subject" => $comment->getSubject(),
                "message" => $comment->getMessage(),
                "time_created" => $comment->getTimeCreated()->format('Y-i-d H:i:s'),
            );
        }
        $this->view->result = $result;
    }

    /**
     * Action for Updateing a Comment
     */
    public function updateAction()
    {
        $this->getHelper('contextSwitch')
            ->addActionContext('update', 'json')
            ->initContext();
        $values = $this->getRequest()->getParams();
        $comment = $this->repository->find($values['comment']);
        if ($this->getRequest()->isPost() && $comment) {
            $values['time_updated'] = new DateTime;
            try
            {
                $this->repository->update($comment, $values);
                $this->repository->flush();
            }
            catch(Exception $e)
            {
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

    /**
     * Action for Replying to a Comment
     */
    public function replyAction()
    {
        $this->getHelper('contextSwitch')
            ->addActionContext('reply', 'json')
            ->initContext();

        if (!SecurityToken::isValid()) {
            $this->view->status = 401;
            $this->view->message = getGS('Invalid security token!');
            return;
        }
        $values = $this->getRequest()->getParams();
        $comment = new Comment;
        if ($this->getRequest()->isPost()) {
            $values['user'] = Zend_Registry::get('user');
            $values['time_created'] = new DateTime;
            $values['ip'] = $request->getClientIp();
            $values['status'] = 'approved';
            try
            {
                $this->repository->save($comment, $values);
                $this->repository->flush();
            }
            catch(Exception $e)
            {
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

    /**
     * Action for Adding a Comment
     */
    public function addAction()
    {
        $comment = new Comment;

        $this->handleForm($this->form, $comment);

        $this->view->form = $this->form;
        $this->view->comment = $comment;
    }

    /**
     * Method for saving a comment
     *
     * @param ZendForm $p_form
     * @param IComment $p_comment
     */
    private function handleForm(Zend_Form $p_form, Comment $p_comment)
    {
        if ($this->getRequest()->isPost() && $p_form->isValid($_POST)) {
            $values = $p_form->getValues();
            $values['ip'] = $request->getClientIp();
            $values['status'] = 'hidden';
            $values['time_created'] = new DateTime;
            $this->repository->save($p_comment, $values);
            $this->repository->flush();
            $this->_helper->flashMessenger(getGS('Comment "$1" saved.',$p_comment->getSubject()));
            $this->_helper->redirector->gotoSimple('index');
        }
    }


}