<?php
/**
 * @package Newscoop
 * @subpackage Subscriptions
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 *
 *
 */
//use Newscoop\Entity\Comment\Acceptance;

class Admin_CommentAcceptanceController extends Zend_Controller_Action
{

    /**
     * @var ICommentAcceptance
     */
    private $repository;

    /**
     *
     * @var Admin_Form_Comment_Acceptance
     */
    private $form;


    public function init()
    {
        /*
        // get comment acceptance repository
        $this->repository = $this->_helper->entity->getRepository('Newscoop\Entity\Comment\Acceptance');

        $this->getHelper('contextSwitch')
            ->addActionContext('index', 'json')
            ->initContext();
        // set the default form for comment commenter and set method to post
        $this->form = new Admin_Form_CommentAcceptance;
        $this->form->setMethod('post');
        return $this;
        */

    }

    public function indexAction()
    {
       $this->getHelper('contextSwitch')
            ->addActionContext('index', 'json')
            ->initContext();
        $table = $this->getHelper('datatable');

        $table->setDataSource($this->repository);

        $table->setCols(array(
            'id' => getGS('Identifier'),
            'forum' => getGS('Forum'),
            'for_column' => getGS('For column'),
            'type' => getGS('Type'),
            'search_type'   => getGS('Search Type'),
            'search'   => getGS('Search'),
            'edit' => getGS('Edit'),
            'delete' => getGS('Delete')
        ));

        $view = $this->view;
        $table->setHandle(function($acceptance) use ($view) {
            $urlParam = array('acceptance' => $acceptace->getId());
            return array(
                $commenter->getTimeCreated()->format('Y-i-d H:i:s'),
                $commenter->getName(),
                $commenter->getUsername(),
                $commenter->getEmail(),
                $commenter->getUrl(),
                $commenter->getIp(),
                $view->linkEdit($urlParam),
                $view->linkDelete($urlParam)
            );
        });

        $table->dispatch();
    }

    /**
     * Action for Adding a Comment acceptance criteria
     */
    public function addAction()
    {
        //$acceptance = new Acceptance;

        $this->handleForm($this->form, $acceptance);

        $this->view->form = $this->form;
        $this->view->acceptance = $acceptance;
    }

    /**
     * Action for Editing a acceptance criteria
     */
    public function editAction()
    {
        $params = $this->getRequest()->getParams();
        if (!isset($params['acceptance'])) {
            throw new InvalidArgumentException;
        }
        $commenter = $this->repository->find($params['acceptance']);
        if($commenter)
        {
            $this->form->setFromEntity($commenter);
            $this->handleForm($this->form, $commenter);
            $this->view->form = $this->form;
            $this->view->commenter = $commenter;
        }
    }

    /**
     * Action for Deleteing a Commenter
     */
    public function deleteAction()
    {
        $commenter = new Commenter;
        $this->repository->delete($commenter);
        $this->repository->flush();
        $this->_helper->flashMessenger(getGS('Commenter "$1" deleted.',$commenter->getName()));
        $this->_helper->redirector->gotoSimple('index');
    }

    /**
     * Method for saving a commenter
     *
     * @param ZendForm $p_form
     * @param ICommenter $p_commenter
     */
    private function handleForm(Zend_Form $p_form, $p_commenter)
    {
        if ($this->getRequest()->isPost() && $p_form->isValid($_POST)) {
            $values = $p_form->getValues();
            $values['ip'] = getIp();
            $values['time_created'] = new DateTime;
            $this->repository->save($p_commenter, $values);
           $this->repository->flush();
            $this->_helper->flashMessenger(getGS('Commenter "$1" saved.',$p_commenter->getName()));
            $this->_helper->redirector->gotoSimple('index');
        }
    }

}