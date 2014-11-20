<?php
/**
 * @package Newscoop
 * @subpackage Subscriptions
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\Annotations\Acl;
use Newscoop\Entity\Comment\Acceptance;

/**
 * @Acl(resource="comment", action="moderate")
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
        $translator = \Zend_Registry::get('container')->getService('translator');

        $this->getHelper('contextSwitch')
            ->addActionContext('table', 'json')
            ->initContext();
        $table = $this->getHelper('datatable');

        $table->setDataSource($this->repository);
        $table->setOption('oLanguage', array('oPaginate' => array(
                'sFirst' => $translator->trans('First', array(), 'comments'),
                'sLast' => $translator->trans('Last', array(), 'comments'),
                'sNext' => $translator->trans('Next'),
                'sPrevious' => $translator->trans('Previous'),
            ),
            'sZeroRecords' => $translator->trans('No records found.', array(), 'comments'),
            'sSearch' => $translator->trans('Search'),
            'sInfo' => $translator->trans('Showing _START_ to _END_ of _TOTAL_ entries', array(), 'comments'),
            'sEmpty' => $translator->trans('No entries to show', array(), 'comments'),
            'sInfoFiltered' => $translator->trans(' - filtering from _MAX_ records', array(), 'comments'),
            'sLengthMenu' => $translator->trans('Display _MENU_ records', array(), 'comments'),
            'sInfoEmpty' => '')
        );
        $table->setCols(array(
            'id' => $translator->trans('ID', array(), 'comments'),
            'for_column' => $translator->trans('For Column', array(), 'comments'),
            'type' => $translator->trans('Type'),
            'search' => $translator->trans('Search'),
            'search_type'   => $translator->trans('Search Type', array(), 'comments'),
            'edit' => $translator->trans('Edit'),
            'delete' => $translator->trans('Delete')
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
        $translator = \Zend_Registry::get('container')->getService('translator');
        $params = $this->getRequest()->getParams();
        if (!isset($params['acceptance'])) {
            throw new InvalidArgumentException;
        }
        $acceptance = $this->repository->find($params['acceptance']);
        if($acceptance)
        {
            $this->repository->delete($acceptance);
            $this->repository->flush();

            $this->_helper->flashMessenger($translator->trans('Acceptance $1 deleted.', array('$1' => $acceptance->getSearch()), 'comments'));
            $this->_helper->redirector->gotoSimple('index');
        }
    }

    /**
     * Method for saving a Acceptance Criteria
     *
     * @param ZendForm $p_form
     * @param Acceptance $p_acceptance
     */
    private function handleForm(Zend_Form $p_form, Acceptance $p_acceptance)
    {   
        $translator = \Zend_Registry::get('container')->getService('translator');

        if ($this->getRequest()->isPost() && $p_form->isValid($_POST)) {
            $values = $p_form->getValues();
            $this->repository->save($p_acceptance, $values);
            $this->repository->flush();
            $this->_helper->flashMessenger($translator->trans('Acceptance $1 saved.', array('$1' => $p_acceptance->getSearch()), 'comments'));
            $this->_helper->redirector->gotoSimple('index');
        }
    }

}

