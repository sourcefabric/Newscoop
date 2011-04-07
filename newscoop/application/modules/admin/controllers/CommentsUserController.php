<?php
/**
 * @package Newscoop
 * @subpackage Subscriptions
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 *
 *
 */
use Newscoop\Entity\CommentsUser,
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

        return $this;

    }

    public function indexAction()
    {
       $this->getHelper('contextSwitch')
            ->addActionContext('index', 'json')
            ->initContext();
        $table = $this->getHelper('datatable');

        $table->setRepository($this->commentsRepository);

        $table->setCols(array(
            'time_created' => getGS('Date Posted'),
            'subject' => getGS('Subject'),
            'forum_id' => getGS('Article')
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


    public function addAction()
    {

    }
}