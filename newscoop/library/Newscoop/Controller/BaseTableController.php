<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Controller;

use Closure,
    Zend_Controller_Action,
    Newscoop\Entity\Repository\DatatableRepository;

abstract class BaseTableController extends Zend_Controller_Action
{
    /** @var Newscoop\Entity\Repository\DatatableRepository */
    protected $repository;

    /** @var array */
    protected $cols;

    /** @var Closure */
    protected $handle;

    public function init()
    {
        $this->_helper->contextSwitch()
            ->addActionContext('data', 'json')
            ->initContext();
    }

    /**
     * Set entity
     *
     * @param string $entity
     * @return void
     */
    public function setEntity($entity)
    {
        $em = $this->_helper->em->getEntityManager();
        $this->repository = new DatatableRepository($em, (string) $entity);
    }

    /**
     * Set table columns
     *
     * @param array $cols
     * @return void
     */
    public function setCols(array $cols)
    {
        $this->cols = $cols;
    }

    /**
     * Set handle
     *
     * @param Closure $handle
     * @return void
     */
    public function setHandle(Closure $handle)
    {
        $this->handle = $handle;
    }

    /**
     * Render table
     */
    public function tableAction()
    {
        $this->view->cols = $this->cols;
    }

    /**
     * Return json data
     */
    final public function dataAction()
    {
        $params = $this->getRequest()->getParams();

        $rows = array();
        $handle = $this->handle;
        foreach ($this->repository->getData($params, $this->cols) as $entity) {
            $rows[] = $handle($entity);
        }

        $this->view->iTotalRecords = $this->repository->getCount(); 
        $this->view->iTotalDisplayRecords = $this->repository->getFilteredCount($params, $this->cols);
        $this->view->aaData = $rows;
        $this->view->sEcho = $params['sEcho'];
    }
}
