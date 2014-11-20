<?php
use Newscoop\Entity\Repository\IDatatableSource;
use Newscoop\Entity\Repository\DatatableRepository;

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
    private $colsIndex;

    /**
     * @todo "Anonymous functions are currently implemented using the Closure class. This is an implementation detail and should not be relied upon." - php.net
     * @var Closure
     */
    private $handle;

    /**
     * @var array where are keept the options
     */
    private $iOptions;

    /**
     * @return Action_Helper_Datatable
     */
    public function init()
    {
        $request = $this->getRequest();
		$this->getActionController()->getHelper('contextSwitch')
            ->addActionContext($request->getParam('action', 'table'), 'json')
            ->initContext();

        $view = $this->getActionController()->initView();
        $this->iOptions = array(
            'sAjaxSource' => $view->url(array(
                'controller' => $request->getParam('controller'),
                'action' => $request->getParam('action', 'index'),
                'format' => 'json',
            )) . '?format=json',
            'bServerSide' => true,
            'bJQueryUI' => true,
            'bAutoWidth' => true,
            'bSaveState' => true,
            'iDisplayLength' => 25,
            'bLengthChange' => true,
            'sPaginationType' => 'full_numbers',
            'bPaginate' => true,
            'sDom' => '<"H"lfri>t<"F"ip>',
            'aoColumnDefs' => array()
        );

        return $this;
    }

    /**
     * Setter for options
     *
     * @param string $p_key
     * @param mixed  $p_value
     * @return Action_Helper_Datatable
     */
    public function setOption($p_key, $p_value)
    {
        $this->iOptions[$p_key] = $p_value;
        // return this for chaining mechanism
        return $this;
    }

    /**
     * Set Datasource
     *
     * @param Newscoop\Datatable\ISource $p_dataSource
     * @return Action_Helper_Datatable
     */
    public function setDataSource($p_dataSource)
    {
        $this->dataSource = $p_dataSource;
        // return this for chaining mechanism
        return $this;
    }

    /**
     * Set entity
     *
     * @param string $p_entity
     * @return Action_Helper_Datatable
     */
    public function setEntity($p_entity)
    {
        $em = $this->getActionController()
            ->getHelper('entity')
            ->getManager();

        $this->dataSource = new DatatableRepository($em, (string) $p_entity);
        // return this for chaining mechanism
        return $this;
    }

    private function buildColumnDefs()
    {
        foreach($this->colsIndex as $key => $value)
        {
            $this->iOptions['aoColumnDefs'][] = array( 'aTargets' => array($value));
        }
    }
    /**
     * Set table columns
     *
     * @param array $cols
     * @return Action_Helper_Datatable
     */
    public function setCols(array $cols, array $sorting = array())
    {
        $this->cols = $cols;
        $this->colsIndex = array_flip(array_keys($this->cols));
        $this->buildColumnDefs();
        $this->setSorting($sorting);
       
        // return this for chaining mechanism
        return $this;
    }

    /**
     * Set initial sorting columns (aaSorting)
     *
     * @param array $initial_sort
     * @return Action_Helper_Datatable
     */
    public function setInitialSorting(array $initial_sort = array())
    {
        $aa_sort = array();
        foreach ($initial_sort as $column_name => $direction) {
            $aa_sort[] = array($this->colsIndex[$column_name], $direction);
        }

        $this->iOptions['aaSorting'] = $aa_sort;
        // return this for chaining mechanism
        return $this;
    }
    
    /**
     * Set searchable columns (bSearchable_col#)
     *
     * @param array $searchable
     * @return Action_Helper_Datatable
     */
    public function setSearchable(array $searchable = array())
    {
        foreach ($searchable as $column_name => $boolean) {
            $id = $this->colsIndex[$column_name];
            
            $this->iOptions["bSearchable_{$id}"] = $boolean;
        }
    
        // return this for chaining mechanism
        return $this;
    }


    /**
     * Set sorting columns
     *
     * @param array $p_sorting
     * @return Action_Helper_Datatable
     */
    public function setSorting(array $p_sorting = array())
    {
        $this->setHeader('bSortable', $p_sorting);
        // return this for chaining mechanism
        return $this;
    }

    /**
     * Set strip classes to use when rendering the table
     * Usefull for odd and even class
     *
     * @param array $p_value
     * @return Action_Helper_Datatable
     */
    public function setStripClasses(array $p_value = array())
    {
       $this->iOptions['asStripClasses'] = $p_value;
        // return this for chaining mechanism
        return $this;
    }
    /**
     * Toggle automatic width
     *
     * @param bool $p_state null
     * @return Action_Helper_Datatable
     */
    public function toggleAutomaticWidth($p_state = null)
    {
        if(!is_null($p_state))
            $this->iOptions['bAutoWidth'] = $p_state;
        // return this for chaining mechanism
        return $this;
    }

    /**
     * Set custom widths
     *
     * @param array|bool $p_widths
     * @return Action_Helper_Datatable
     */
    public function setWidths($p_widths = false)
    {
        $this->toggleAutomaticWidth(false);
        $this->setHeader('sWidth', $p_widths);
        // return this for chaining mechanism
        return $this;
    }

    /**
     * Set body properties
     *
     * @param string $p_columnProperty
     * @param array $p_values
     * @return Action_Helper_Datatable
     */
    public function setBody($p_columnProperty, array $p_values = array())
    {
        if(count($p_values))
        {
            foreach($p_values as $key => $value)
            {
                if(is_string($key))
                    $key = $this->colsIndex[$key];
                $this->iOptions['aoColumns'][$key][$p_columnProperty] = $value;
            }
        }
        // return this for chaining mechanism
        return $this;
    }


    /**
     * Set visibility
     *
     * @param array|bool $p_values
     * @return Action_Helper_Datatable
     */
    public function setVisible(array $p_values = array())
    {
        $this->setBody('bVisible', $p_values);
        // return this for chaining mechanism
        return $this;
    }

    /**
     * Set custom widths
     *
     * @param array|bool $p_values
     * @return Action_Helper_Datatable
     */
    public function setDataProp(array $p_values = array())
    {
        $this->setBody('mData', $p_values);
        // return this for chaining mechanism
        return $this;
    }

    /**
     * Set header properties
     *
     * @param string $p_columnProperty
     * @param array $p_values
     * @return Action_Helper_Datatable
     */
    public function setHeader($p_columnProperty, array $p_values = array())
    {
        if(count($p_values))
        {
            foreach($p_values as $key => $value)
            {
                if(is_string($key))
                    $key = $this->colsIndex[$key];
                $this->iOptions['aoColumnDefs'][$key][$p_columnProperty] = $value;
            }
        }
        // return this for chaining mechanism
        return $this;
    }

    /**
     * Set header style classes
     *
     * @param array $p_values
     * @return Action_Helper_Datatable
     */
    public function setClasses(array $p_values = array())
    {
        $this->setHeader('sClass',$p_values);
        // return this for chaining mechanism
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
        // return this for chaining mechanism
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

        $data = $this->dataSource->getData($params, $this->cols);
        foreach ($data as $entity) {
            $rows[] = $handle($entity);
        }

        // set data
        $view->iTotalRecords = $this->dataSource->getCount();
        $view->iTotalDisplayRecords = $this->dataSource->getCount($params, $this->cols);
        $view->aaData = $rows;
        $view->sEcho = !empty($params['sEcho']) ? $params['sEcho'] : 0;
    }
}
