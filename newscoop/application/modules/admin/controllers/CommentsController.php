<?php
/**
 * @package Newscoop
 * @subpackage Subscriptions
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 *
 *
 */
use Newscoop\Entity\Comments;

class Admin_CommentsController extends Zend_Controller_Action
{

    /**
     * @var Newscoop\Entity\Repository\CommentsUsersRepository
     *
     */
    private $commentsUsersRepository = null;

    private $commentsRepository = null;

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
        $this->commentsUsersRepository = $bootstrap->getResource('doctrine')
            ->getEntityManager()
            ->getRepository('Newscoop\Entity\CommentsUsers');

        // get comments repository
        $this->commentsRepository = $bootstrap->getResource('doctrine')
            ->getEntityManager()
            ->getRepository('Newscoop\Entity\Comments');

       $this->getHelper('contextSwitch')
            ->addActionContext('index', 'json')
            ->initContext();
        return $this;

    }

    public function indexAction()
    {
        $table = $this->getHelper('datatable');

        $table->setDataSource($this->commentsRepository);

        $table->setCols(array(
            'time_created' => getGS('Date Posted'),
            'subject' => getGS('Subject'),
            'forum_id' => getGS('Article'),
            getGS('Delete'),
        ));

        $view = $this->view;
        $table->setHandle(function(Comments $comment) use ($view) {
            $deleteLink = sprintf('<a href="%s" class="delete confirm" title="%s">%s</a>',
                $view->url(array(
                    'action' => 'delete',
                    'user' => $comment->getId(),
                )),
                getGS('Delete comment $1', $comment->getSubject()),
                getGS('Delete')
            );

            return array(
                $comment->getTimeCreated()->format('Y-i-d H:i:s'),
                $comment->getSubject(),
                $comment->getSubject(),
                $deleteLink,
            );
        });

        $table->dispatch();
    }

    public function listUsersAction()
    {
        $form = new Zend_Form;

        $form->addElement('text', 'name', array())
             ->addElement('submit', 'submit', array(
                'ignore' => true,
                'label' => getGS('Filter')))
             ->setMethod('get')
             ->setAction($this->view->url());

        // fetch logs
        $limit = 15;
        $offset = max(0, (int) $_GET['offset']);
        $this->view->users = $this->commentsUsersRepository->getList($offset, $limit, $name);

        $this->view->form = $form;
        // set pager
        $count = $this->commentsUsersRepository->getCount($name);
        $this->view->pager = new SimplePager($count, $limit, 'offset', isset($name) ? "?name={$name}&" : '?');
    }

    public function addAction()
    {

    }
}