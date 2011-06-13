<?php
use Newscoop\Entity\Repository\IDatatableSource,
    Newscoop\Entity\Repository\DatatableRepository;

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
            'iDisplayLength' => 10,
            'bLengthChange' => true,
            'sPaginationType' => 'full_numbers',
            'bPaginate' => true,
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

    /*
    public function setAdapter()
    {
        
    }
    */
    
    /**
     * Set entity
     *
     * @param string $entity
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
     * Set sorting columns
     *
     * @param array $nonsorting
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
     * @param array|bool $p_value
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
     * @param array|bool $p_value
     * @return Action_Helper_Datatable
     */
    public function setDataProp(array $p_values = array())
    {
        $this->setBody('mDataProp', $p_values);
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
