<?php
/**
 * @package Newscoop
 * @subpackage Subscriptions
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 *
 *
 */
use Newscoop\Entity\Comment\User as CommentUser;

//function to get the ip address
function getIp()
{
    if(!empty($_SERVER['HTTP_CLIENT_IP']))
        return $_SERVER['HTTP_CLIENT_IP'];
    elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    else
        return $_SERVER['REMOTE_ADDR'];
}

class Admin_CommentUserController extends Zend_Controller_Action
{

    /**
     * @var ICommentUsers
     */
    private $repository;

    /**
     *
     * @var Admin_Form_Comment_User
     */
    private $form;

    /**
     * Check permissions
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
        // get comment user repository
        $bootstrap = $this->getInvokeArg('bootstrap');
        $this->repository = $bootstrap->getResource('doctrine')
            ->getEntityManager()
            ->getRepository('Newscoop\Entity\Comment\User');

        $this->getHelper('contextSwitch')
            ->addActionContext('index', 'json')
            ->initContext();
        // set the default form for comment user and set method to post
        $this->form = new Admin_Form_CommentUser;
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
            'time_created' => getGS('Date Created'),
            'name' => getGS('Name'),
            'user' => getGS('Username'),
            'email' => getGS('Email'),
            'url'   => getGS('Website'),
            'ip'   => getGS('Ip'),
            'edit' => getGS('Edit'),
            'delete' => getGS('Delete')
        ));

        $view = $this->view;
        $table->setHandle(function($commentUser) use ($view) {
            $urlParam = array('comment-user' => $commentUser->getId());
            return array(
                $commentUser->getTimeCreated()->format('Y-i-d H:i:s'),
                $commentUser->getName(),
                $commentUser->getUsername(),
                $commentUser->getEmail(),
                $commentUser->getUrl(),
                $commentUser->getIp(),
                $view->linkEdit($urlParam),
                $view->linkDelete($urlParam)
            );
        });

        $table->dispatch();
    }

    /**
     * Action for Adding a Comment User
     */
    public function addAction()
    {
        $commentUser = new CommentUser;

        $this->handleForm($this->form, $commentUser);

        $this->view->form = $this->form;
        $this->view->commentUser = $commentUser;
    }

    /**
     * Action for Editing a Comment User
     */
    public function editAction()
    {
        $params = $this->getRequest()->getParams();
        if (!isset($params['comment-user'])) {
            throw new InvalidArgumentException;
        }
        $commentUser = $this->repository->find($params['comment-user']);
        if($commentUser)
        {
            $this->form->setFromEntity($commentUser);
            $this->handleForm($this->form, $commentUser);
            $this->view->form = $this->form;
            $this->view->commentUser = $commentUser;
        }
    }

    /**
     * Action for Deleteing a Comment User
     */
    public function deleteAction()
    {
        $commentUser = new CommentUser;
        $this->repository->delete($commentUser);
        $this->repository->flush();
        $this->_helper->flashMessenger(getGS('Comment User "$1" deleted.',$p_user->getName()));
        $this->_helper->redirector->gotoSimple('index');
    }

    /**
     * Method for saving a comment user
     *
     * @param ZendForm $p_form
     * @param ICommentUser $p_user
     */
    private function handleForm(Zend_Form $p_form, $p_user)
    {
        if ($this->getRequest()->isPost() && $p_form->isValid($_POST)) {
            $values = $p_form->getValues();
            $values['ip'] = getIp();
            $values['time_created'] = new DateTime;
            $this->repository->save($p_user, $values);
            $this->repository->flush();
            $this->_helper->flashMessenger(getGS('Comment User "$1" saved.',$p_user->getName()));
            $this->_helper->redirector->gotoSimple('index');
        }
    }

}