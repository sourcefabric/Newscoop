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

    /**
     * For storing the keys index for cols array
     *
     * @var array
     */
    private $keyCols;

    /** @var Closure */
    private $handle;

    /**
     *
     * @var array where are keept the options
     */
    private $iOptions;

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

        $view = $this->getActionController()->initView();
        $this->iOptions = array(
            'sAjaxSource' => $view->url(array('action' => 'index', 'format' => 'json')),
            'bServerSide' => true,
            'bJQueryUI' => true,
            'bAutoWidth' => true,
            'bSaveState' => true,
            'iDisplayLength' => 25,
            'bLengthChange' => false,
            'sPaginationType' => 'full_numbers',
            'aoColumnDefs' => array()
        );
        return $this;
    }

    /**
     * Setter for options
     *
     * @param string $key
     * @param mixed $value
     */
    public function setOption($key, $value)
    {
        $this->iOptions[$key] = $value;
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
    public function setCols(array $cols, array $nonsorting = array(), array $sorting = array())
    {
        $this->cols = $cols;
        $this->keyCols = array_flip(array_keys($this->cols));
        $this->setSorting($sorting);
        $this->setNonSorting($nonsorting);
        return $this;
    }

    /**
     * Set non sorting columns,
     * in case you have edit, delete or other static columns
     *
     * @param array $nonsorting
     */
    public function setNonSorting(array $nonsorting = array())
    {
        $aTargets = array();
        foreach($nonsorting as $value)
            $aTargets[] = $this->keyCols[$value];
        if(count($aTargets))
            $this->iOptions['aoColumnDefs'][0] = array( 'bSortable'=> false, 'aTargets'=> $aTargets);
    }

    /**
     * Set sorting columns
     *
     * @param array $nonsorting
     */
    public function setSorting(array $sorting = array())
    {
        $aTargets = array();
        foreach($sorting as $value)
            $aTargets[] = $this->keyCols[$value];
        if(count($aTargets)) {
            $this->iOptions['aoColumnDefs'][1] = array( 'bSortable'=> true, 'aTargets'=> $aTargets);
        }
    }

    /**
     * Toggle automatic width
     *
     * @param bool $p_state null
     */
    public function toggleAutomaticWidth($p_state = null)
    {
        if(!is_null($p_state))
            $this->iOptions['bAutoWidth'] = $p_state;
    }

    /**
     * Set custom widths
     *
     * @param array|bool $p_widths
     */
    public function setHeaderWidths($p_widths = false)
    {
        $this->toggleAutomaticWidth(false);
        $this->setHeader('sWidth', $p_widths);
    }

    /**
     * Set header properties
     *
     * @param string $p_columnProperty
     * @param array $p_values
     */
    public function setHeader($p_columnProperty, array $p_values = array())
    {
        if(count($p_values))
        {
            foreach($p_values as $key => $value)
            {
                $this->iOptions['aoColumns'][$this->keyCols[$key]][$p_columnProperty] = $value;
            }
        }
    }

    /**
     * Set header properties
     *
     * @param string $p_columnProperty
     * @param array $p_values
     */
    public function setHeaderClasses(array $p_values = array())
    {
        $this->setHeader('sClass',$p_values);
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
            $view->iOptions = $this->iOptions;
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
