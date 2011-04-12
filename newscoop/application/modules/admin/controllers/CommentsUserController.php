<?php
/**
 * @package Newscoop
 * @subpackage Subscriptions
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 *
 *
 */
use Newscoop\Entity\CommentsUsers,
    Newscoop\Entity\CommentsUserRepository;

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

class Admin_CommentsUserController extends Zend_Controller_Action
{

    /**
     * @var Newscoop\Models\Comment\IUsers
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
        // get comments user repository
        $bootstrap = $this->getInvokeArg('bootstrap');
        $this->repository = $bootstrap->getResource('doctrine')
            ->getEntityManager()
            ->getRepository('Newscoop\Entity\CommentsUsers');

        $this->getHelper('contextSwitch')
            ->addActionContext('index', 'json')
            ->initContext();
        // set the default form for comments user and set method to post
        $this->form = new Admin_Form_CommentsUser;
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
        $table->setHandle(function($commentsUsers) use ($view) {
            $urlParam = array('comment-user' => $commentsUsers->getId());
            return array(
                $commentsUsers->getTimeCreated()->format('Y-i-d H:i:s'),
                $commentsUsers->getName(),
                $commentsUsers->getUsername(),
                $commentsUsers->getEmail(),
                $commentsUsers->getUrl(),
                $commentsUsers->getIp(),
                $view->linkEdit($urlParam),
                $view->linkDelete($urlParam)
            );
        });

        $table->dispatch();
    }

    /**
     * Action for Adding a Comments User
     */
    public function addAction()
    {
        $commentsUser = new CommentsUsers;

        $this->handleForm($this->form, $commentsUser);

        $this->view->form = $this->form;
        $this->view->commentsUser = $commentsUser;
    }

    /**
     * Action for Editing a Comments User
     */
    public function editAction()
    {
        $params = $this->getRequest()->getParams();
        if (!isset($params['comment-user'])) {
            throw new InvalidArgumentException;
        }
        $commentsUser = $this->repository->find($params['comment-user']);
        if($commentsUser)
        {
            $this->form->setFromEntity($commentsUser);
            $this->handleForm($this->form, $commentsUser);
            $this->view->form = $this->form;
            $this->view->commentsUser = $commentsUser;
        }
    }

    /**
     * Action for Deleteing a Comments User
     */
    public function deleteAction()
    {
        $commentsUser = new CommentsUsers;
        $this->repository->delete($commentsUser);
        $this->repository->flush();
        $this->_helper->flashMessenger(getGS('Comments User "$1" deleted.',$p_user->getName()));
        $this->_helper->redirector->gotoSimple('index');
    }

    /**
     * Method for saving a comment user
     *
     * @param ZendForm $p_form
     * @param ICommentsUsers $p_user
     */
    private function handleForm(Zend_Form $p_form, CommentsUsers $p_user)
    {
        if ($this->getRequest()->isPost() && $p_form->isValid($_POST)) {
            $values = $p_form->getValues();
            $values['ip'] = getIp();
            $values['time_created'] = new DateTime;
            $this->repository->save($p_user, $values);
            $this->repository->flush();
            $this->_helper->flashMessenger(getGS('Comments User "$1" saved.',$p_user->getName()));
            $this->_helper->redirector->gotoSimple('index');
        }
    }

}