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

class Admin_CommentsController extends Zend_Controller_Action
{

    /**
     * @var Newscoop\Entity\Repository\CommentsRepository
     *
     */
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
        // get comments repository
        $this->commentsRepository = $bootstrap->getResource('doctrine')
            ->getEntityManager()
            ->getRepository('Newscoop\Entity\Comments');

        return $this;

    }

    public function indexAction()
    {
       $this->getHelper('contextSwitch')
            ->addActionContext('index', 'json')
            ->initContext();
        $table = $this->getHelper('datatable');

        $table->setDataSource($this->commentsRepository);

        $table->setCols(array(
            'time_created' => getGS('Date Posted'),
            'fk_comments_user_id' => getGS('Author'),
            'fk_thread_id' => getGS('Article')
        ));

        $view = $this->view;
        $table->setHandle(function(Comments $comment) use ($view) {
            return array(
                $comment->getSubject(),
                $comment->getTimeCreated()->format('Y-i-d H:i:s'),
                $comment->getSubject()
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

}