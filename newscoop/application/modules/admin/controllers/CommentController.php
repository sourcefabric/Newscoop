<?php
/**
 * @package Newscoop
 * @subpackage Subscriptions
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 *
 *
 */
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
     *
     * @var Admin_Form_Comment
     */
    private $form;


    public function init()
    {
        // get comment repository
        $this->repository = $this->_helper->entity->getRepository('Newscoop\Entity\Comment');
        $this->form = new Admin_Form_Comment;
        $this->form->setMethod('post');

        return $this;

    }

    public function indexAction()
    {
        $view = $this->view;
        $this->getHelper('contextSwitch')
            ->addActionContext('index', 'json')
            ->initContext();
        $table = $this->getHelper('datatable');

        $table->setDataSource($this->repository);

        $table->setCols(array(
            'id' => $view->toggleCheckbox(),
            'user' => getGS('Author'),
            'time_created' => getGS('Date').' / '.getGS('Comment'),
            'thread' => getGS('Article')
        ));

        $table->setHandle(function($comment) use ($view) {
            return array(
                $view->commentIndex($comment),
                $view->commentCommenter($comment->getCommenter()),
                $comment->getTimeCreated()->format('Y-i-d H:i:s'),
                $comment->getSubject()
            );
        });

        $table->dispatch();
    }

    public function listAction()
    {
        $this->getHelper('contextSwitch')
            ->addActionContext('index', 'json')
            ->initContext();

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