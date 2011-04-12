<?php
/**
 * @package Newscoop
 * @subpackage Subscriptions
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 *
 *
 */
use Newscoop\Entity\Comments,
    Newscoop\Entity\CommentsRepository;

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

class Admin_CommentsController extends Zend_Controller_Action
{

    /**
     * @var Newscoop\Entity\Repository\CommentsRepository
     *
     */
    private $repository;

    /**
     *
     * @var Admin_Form_Comment
     */
    private $form;

    /**
     * Check permissions
     *
     *
     */
    public function preDispatch()
    {
        global $g_user;

        // permissions check
        if (!$g_user->hasPermission('CommentModerate')) {
            camp_html_display_error(getGS("You do not have the right to view logs."));

        }
    }

    public function init()
    {
        // get comments user repository
        $bootstrap = $this->getInvokeArg('bootstrap');
        // get comments repository
        $this->repository = $bootstrap->getResource('doctrine')
            ->getEntityManager()
            ->getRepository('Newscoop\Entity\Comments');

        $this->form = new Admin_Form_Comment;
        $this->form->setMethod('post');

        return $this;

    }

    public function indexAction()
    {
       $this->getHelper('contextSwitch')
            ->addActionContext('index', 'json')
            ->initContext();
        $table = $this->getHelper('datatable');

        $table->setDataSource($this->repository);

        $table->setCols(array(
            'time_created' => getGS('Date Posted'),
            'user' => getGS('Author'),
            'thread' => getGS('Article')
        ));

        $view = $this->view;
        $table->setHandle(function(Comments $comment) use ($view) {
            return array(
                $comment->getTimeCreated()->format('Y-i-d H:i:s'),
                $view->commentsUser($comment->getUser()),
                $comment->getSubject()
            );
        });

        $table->dispatch();
    }

    public function setAddAction() {
        $this->getHelper('contextSwitch')
            ->addActionContext('index', 'json')
            ->initContext();

        if (empty($params['format'])) { // render table
            return;
        }
        $comment = new Comment;
        $comment->setSubject($params['subject']);
        $comment->setMessage($params['message']);
        $comment->setTimeCreated(new DateTime());
        $comment->setStatus('approved');
        $comment->setUser();

        $this->view->code = '200';
        $this->view->message = "succesfull";
        $this->view->added = "true";
    }

    public function addExtAction() {

    }

    /**
     * Action for Adding a Comments User
     */
    public function addAction()
    {
        $comment = new Comments;

        $this->handleForm($this->form, $comment);

        $this->view->form = $this->form;
        $this->view->comment = $comment;
    }

    /**
     * Method for saving a comment user
     *
     * @param ZendForm $p_form
     * @param IComments $p_comment
     */
    private function handleForm(Zend_Form $p_form, Comments $p_comment)
    {
        if ($this->getRequest()->isPost() && $p_form->isValid($_POST)) {
            $values = $p_form->getValues();
            /*
            $values['language_id'] = 1;
            $values['thread_id'] = 64;
            $values['forum_id'] = 2;
            */
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