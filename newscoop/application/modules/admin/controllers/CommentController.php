<?php
use Newscoop\Entity\Comment;

 // function to get the ip address
function getIp()
{
    if(!empty($_SERVER['HTTP_CLIENT_IP']))
        return $_SERVER['HTTP_CLIENT_IP'];
    elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    else
        return $_SERVER['REMOTE_ADDR'];
}

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
            'thread_order' => getGS('Thread Order'),
            'time_created' => getGS('Date').' / '.getGS('Comment'),
            'thread' => getGS('Article')
        ),array('id' => false));


        $table->setHandle(function($comment) use ($view) {
            return array(
                $view->commentIndex($comment),
                $view->commentCommenter($comment->getCommenter()),
                $view->commentAction($comment),
                $view->commentMessage($comment),
                $view->commentArticle($comment)
            );
        });

        $table->setOption('fnRowCallback','datatableCallback.row');
        $table->setOption('fnServerData', 'datatableCallback.addServerData');

        $table->toggleAutomaticWidth(false);
        $table->setClasses(array(
            'id'   => 'commentId',
            'user' => 'commentUser',
            'thread_order' => 'commentThreadOrder',
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
        try
        {
            $comment = (int)$this->getRequest()->getParam('comment');
            $status = $this->getRequest()->getParam('status');
            $this->repository->setStatus(array($comment),$status);
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
        $user = Zend_Registry::get('user');
        //$values['user'] = $user;
        $values['name'] = $request->getParam('name');
        $values['subject'] = $request->getParam('subject');
        $values['message'] = $request->getParam('message');
        $values['language_id'] = $request->getParam('language');
        $values['thread_id'] =  $request->getParam('article');
        $values['thread_id'] = 64;
        $values['ip'] = getIp();
        $values['status'] = 'pending';
        $values['time_created'] = new DateTime;
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
        $article = $this->getRequest()->getParam('article');
        $language = $this->getRequest()->getParam('language');
        $article = 64;
        $comments = $this->repository->getArticleComments($article, $language, array());
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
            $values['language_id'] = 1;
            $values['thread_id'] = 64;
            $values['forum_id'] = 2;
            $values['ip'] = getIp();
            $values['status'] = 'hidden';
            $values['time_created'] = new DateTime;
            $this->repository->save($p_comment, $values);
            $this->repository->flush();
            $this->_helper->flashMessenger(getGS('Comment "$1" saved.',$p_comment->getSubject()));
            $this->_helper->redirector->gotoSimple('index');
        }
    }


}