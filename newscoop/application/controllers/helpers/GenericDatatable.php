<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\Controller\Action\Helper\Datatable\ADatatable, Newscoop\Controller\Action\Helper\Datatable\Adapter; //,   Newscoop\Controller\Action\Helper\Datatable\Adapter\Doctrine as DefaultAdapter


/**
 * Datatable helper
 */
class Action_Helper_GenericDatatable extends ADatatable
{

    /**
     * Script used for rendering
     * @var string
     */
    public $viewPath = '_p/generic-datatable.phtml';

    /**
     * @var Zend_View
     */
    protected $_outputObject;

    /**
     * Inner flag for data mapping columns set/unset
     * @var bool
     */
    protected $_hasDataMap = false;
    
    /**
     * Init
     *
     * @return Action_Helper_GenericDatatable
     */
    public function init( Adapter\IAdapter $p_adapter = null )
    {
        parent::init();
        //$this->setAdapter( is_null( $p_adapter ) ? new DefaultAdapter() : $p_adapter )
        //    ->setOutputObject( $this->getActionController()->view );
        return $this;
    }

    public function dispatch( $p_params = null, $p_cols = null, $p_options = null )
    {
        $req = $this->getRequest();
        if( !$this->_hasDataMap )
            $this->setDataMap();
        $this->_isDispatched = true;
        if( $req->getParam( 'format' ) == 'json' ) {
            $this->dispatchData( $p_params, $p_cols );
            return false;
        }
        $this->dispatchMedatata( $p_params, $p_options );
        return $this->_outputObject->render( $this->viewPath );
    }

    public function setParams( $p_params )
    {
        if( @is_array( $p_params ) ) 
        {
            $sort = array();
            foreach( $p_params as $k => $v ) 
            {
                if( strpos( $k , 'iSortCol' ) !== false ) {
                    $sort[ $v ] = $p_params[ str_replace( 'iSortCol', 'sSortDir', $k ) ];
                }
                if( strpos( $k, 'sSearch' ) !== false ) {
                    $searchCol = ( $colIdxPosMark = strrpos( $k, '_' ) ) ? substr( $k, strrpos( $k, '_' )+1 ) : -1;
                    $search[ $searchCol ] = $v;                    
                }
            }
        }

        parent::setParams(array(
            'search' => isset($search) ? $search : NULL,
            'sort'	 => isset($sort) ? $sort : NULL,
        ));
    }
    
    /**
     * Set header properties
     * @param string $p_columnProperty
     * @param array $p_values
     * @return Action_Helper_GenericDatatable
     */
    public function setHeader( $p_columnProperty, array $p_values = array() )
    {
        if( count( $p_values ) )
        {
            foreach( $p_values as $key => $value )
            {
                if( is_string( $key ) ) {
                    $key = $this->_colsIndex[$key];
                }
                $this->_options['aoColumnDefs'][$key][$p_columnProperty] = $value;
            }
        }
        return $this;
    }
    
    /**
	 * Set body properties
	 *
	 * @param string $p_columnProperty
	 * @param array $p_values
	 * @return Action_Helper_GenericDatatable
	 */
    public function setBody( $p_columnProperty, array $p_values = array() )
    {
        if( count( $p_values ) )
        {
            foreach( $p_values as $key => $value )
            {
                if( is_string( $key ) )
                    $key = $this->_colsIndex[$key];
                $this->_options['aoColumns'][$key][$p_columnProperty] = $value;
            }
        }
        return $this;
    }

    /**
     * N-a sti nimeni
     */
    public function buildColumnDefs()
    {
        foreach( $this->_colsIndex as $key => $value )
        {
            $this->_options['aoColumnDefs'][] = array( 'aTargets' => array( $value ) );
        }
        return $this;
    }

    /**
     * Set header style classes
     *
     * @param array $p_values
     * @return Action_Helper_GenericDatatable
     */
    public function setClasses( array $p_values = array() )
    {
        $this->setHeader( 'sClass', $p_values );
        return $this;
    }

    /**
     * Set sorting columns
     *
     * @param array $nonsorting
     * @return Action_Helper_GenericDatatable
     */
    public function setSorting( array $p_sorting = array() )
    {
        $this->setHeader( 'bSortable', $p_sorting );
        return $this;
    }
    
	/**
	 * Set custom widths
	 *
	 * @param array|bool $p_widths
	 * @return Action_Helper_GenericDatatable
	 */
    public function setWidths( $p_widths = false )
    {
        //$this->toggleAutomaticWidth( false );
        $this->setHeader( 'sWidth', $p_widths );
        return $this;
    }
    
	/**
	 * Set data mapping
	 *
	 * @param array|bool $p_values
	 * @return Action_Helper_GenericDatatable
	 */
    public function setDataMap( array $p_values = array() )
    {
        if( !count( $p_values ) ) {
            foreach( $this->_colsIndex as $k => $v ) {
                $p_values[$k] = null;
            }
        }
        
        $this->setBody( 'mDataProp', $p_values );
        $this->_hasDataMap = true;
        
        return $this;
    }
    
    public function __toString()
    {
        return '';
    }
}
