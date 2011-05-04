<?php
/**
 * @package Newscoop
 * @subpackage Subscriptions
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 *
 *
 */

use Newscoop\Entity\Comment\Acceptance;
/**
 */
class Admin_CommentAcceptanceController extends Zend_Controller_Action
{
    /**
     * @var ICommentAcceptanceRepository
     *
     */
    private $repository;

    /**
     *
     * @var Admin_Form_Comment_Acceptance
     */
    private $form;


    public function init()
    {
        // get comment repository
        $this->repository = $this->_helper->entity->getRepository('Newscoop\Entity\Comment\Acceptance');
        $this->form = new Admin_Form_CommentAcceptance;
        $this->form->setMethod('post');

        return $this;
    }

    public function indexAction()
    {
        $this->_forward('table');
    }

    public function tableAction()
    {
       $this->getHelper('contextSwitch')
            ->addActionContext('table', 'json')
            ->initContext();
        $table = $this->getHelper('datatable');

        $table->setDataSource($this->repository);

        $table->setCols(array(
            'id' => getGS('ID'),
            'for_column' => getGS('For Column'),
            'type' => getGS('Type'),
            'search' => getGS('Search'),
            'search_type'   => getGS('Search Type'),
            'edit' => getGS('Edit'),
            'delete' => getGS('Delete')
        ));

        $view = $this->view;
        $table->setHandle(function($acceptance) use ($view) {
            $urlParam = array('acceptance' => $acceptance->getId());
            return array(
                $acceptance->getId(),
                $acceptance->getForColumn(),
                $acceptance->getType(),
                $acceptance->getSearch(),
                $acceptance->getSearchType(),
                $view->linkEdit($urlParam),
                $view->linkDelete($urlParam)
            );
        });

        $table->dispatch();
    }

    /**
     * Action for Adding a Acceptance Criteria
     */
    public function addAction()
    {
        $acceptance = new Acceptance;
        $this->handleForm($this->form, $acceptance);

        $this->view->form = $this->form;
        $this->view->acceptance = $acceptance;
    }

    /**
     * Action for Editing a Acceptance Criteria
     */
    public function editAction()
    {
        $params = $this->getRequest()->getParams();
        if (!isset($params['acceptance'])) {
            throw new InvalidArgumentException;
        }
        $acceptance = $this->repository->find($params['acceptance']);
        if($acceptance)
        {
            $this->form->setFromEntity($acceptance);
            $this->handleForm($this->form, $acceptance);
            $this->view->form = $this->form;
            $this->view->acceptance = $acceptance;
        }
    }

    /**
     * Action for Deleteing a Acceptance Criteria
     */
    public function deleteAction()
    {
        $params = $this->getRequest()->getParams();
        if (!isset($params['acceptance'])) {
            throw new InvalidArgumentException;
        }
        $acceptance = $this->repository->find($params['acceptance']);
        if($acceptance)
        {
            $this->repository->delete($acceptance);
            $this->repository->flush();

            $this->_helper->flashMessenger(getGS('Acceptance "$1" deleted.',$acceptance->getSearch()));
            $this->_helper->redirector->gotoSimple('index');
        }
    }

    /**
     * Method for saving a Acceptance Criteria
     *
     * @param ZendForm $p_form
     * @param IComment $p_acceptance
     */
    private function handleForm(Zend_Form $p_form, Acceptance $p_acceptance)
    {
        if ($this->getRequest()->isPost() && $p_form->isValid($_POST)) {
            $values = $p_form->getValues();
            $this->repository->save($p_acceptance, $values);
            $this->repository->flush();
            $this->_helper->flashMessenger(getGS('Acceptance "$1" saved.',$p_acceptance->getSearch()));
            $this->_helper->redirector->gotoSimple('index');
        }
    }

}

