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

class Admin_CommentsUserController extends Zend_Controller_Action
{

    /**
     * @var Newscoop\Entity\Repository\CommentsUsersRepository
     *
     */
    private $commentsUsersRepository = null;

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
        $this->commentsUsersRepository = $bootstrap->getResource('doctrine')
            ->getEntityManager()
            ->getRepository('Newscoop\Entity\CommentsUsers');
        $this->getHelper('contextSwitch')
            ->addActionContext('index', 'json')
            ->initContext();

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

        $table->setDataSource($this->commentsUsersRepository);

        $table->setCols(array(
            'time_created' => getGS('Date Created'),
            'name' => getGS('Name'),
            'fk_user_id' => getGS('Username'),
            'email' => getGS('Email'),
            'url'   => getGS('Website'),
            'ip'   => getGS('Ip'),
            'edit' => getGS('Edit'),
            'delete' => getGS('Delete')
        ));

        $view = $this->view;
        $table->setHandle(function(CommentsUsers $commentsUsers) use ($view) {
            $urlParam = array('user' => $commentsUsers->getId());
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

    public function addAction()
    {
        $subscriber = new Subscriber;

        $this->handleForm($this->form, $subscriber);

        $this->view->form = $this->form;
        $this->view->user = $subscriber;
    }

    private function handleForm(Zend_Form $form, Subscriber $subscriber)
    {
        if ($this->getRequest()->isPost() && $form->isValid($_POST)) {
            $this->repository->save($subscriber, $form->getValues());
            $this->_helper->entity->getManager()->flush();

            $this->_helper->flashMessenger(getGS('Subscriber saved.'));
            $this->_helper->redirector->gotoSimple('index');
        }
    }

}