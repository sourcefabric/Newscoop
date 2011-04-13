<?php
use Newscoop\Entity\Repository\IDatatableSource;
/**
 * Datatable helper
 */
class Action_Helper_Datatable extends Zend_Controller_Action_Helper_Abstract
{
    /** @var Newscoop\Entity\Repository\IDatatableSource */
    private $dataSource;

    /** @var array */
    private $cols;

    /** @var Closure */
    private $handle;

    /**
     * Init
     *
     * @return Action_Helper_Datatable
     */
    public function init()
    {
        $this->getActionController()->getHelper('contextSwitch')
            ->addActionContext('table', 'json')
            ->initContext();
        return $this;
    }

    /**
     * Set Datasource
     *
     * @param IDatatableSource $p_dataSource
     * @return Action_Helper_Datatable
     */
    public function setDataSource($p_dataSource)
    {
        $this->dataSource = $p_dataSource;
    }

    /**
     * Set entity
     *
     * @param string $entity
     * @return Action_Helper_Datatable
     */
    public function setEntity($entity)
    {
        $em = $this->getActionController()
            ->getHelper('entity')
            ->getManager();

        $this->dataSource = new DatatableRepository($em, (string) $entity);
        return $this;
    }

    /**
     * Set table columns
     *
     * @param array $cols
     * @return Action_Helper_Datatable
     */
    public function setCols(array $cols)
    {
        $this->cols = $cols;
        return $this;
    }

    /**
     * Set handle
     *
     * @param Closure $handle
     * @return Action_Helper_Datatable
     */
    public function setHandle(Closure $handle)
    {
        $this->handle = $handle;
        return $this;
    }

    /**
     * Dispatch table
     *
     * @return void
     */
    public function dispatch()
    {
        $view = $this->getActionController()->initView();
        $params = $this->getRequest()->getParams();
        if (empty($params['format'])) { // render table
            $view->cols = $this->cols;
            return;
        }

        // get data
        $rows = array();
        $handle = $this->handle;
        foreach ($this->dataSource->getData($params, $this->cols) as $entity) {
            $rows[] = $handle($entity);
        }
        // set data
        $view->iTotalRecords = $this->dataSource->getCount();
        $view->iTotalDisplayRecords = $this->dataSource->getCount($params, $this->cols);
        $view->aaData = $rows;
        $view->sEcho = $params['sEcho'];
    }
}
