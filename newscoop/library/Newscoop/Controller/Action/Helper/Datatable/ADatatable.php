<?php

namespace Newscoop\Controller\Action\Helper\Datatable;

use Newscoop\Controller\Action\Helper\Datatable\Adapter\AAdapter,
    Zend_Controller_Action_Helper_Abstract, Adapter;

abstract class ADatatable extends Zend_Controller_Action_Helper_Abstract
{

    /**
     * The data adapter
     * @var AAdapter
     */
    protected $_adapter;

    /**
     * Output object for processed data
     * @var object
     */
    protected $_outputObject;

    /**
     * Row handler, called for each row in datatable
     * @var function
     */
    protected $_rowHandler;

    /**
     * Columns
     * @var array
     */
    protected $_cols = array();
    
    /**
     * Columns' index
     * @var array
     */
    protected $_colsIndex = array();
    
    /**
     * Paramenters for the data fetching
     * Get them from the request object for example
     * @var array (search,sort)
     */
    protected $_params = array();
    
    /**
     * Options
     * @var array
     */
    protected $_options = array();
    
    /**
     * @param bool
     */
    protected $_isDispatched = false;
    
    public function init()
    {
        $this->_outputObject = new \stdClass;
    }
    
    /**
     * Set the data adapter
     * @param Adapter $p_adapter
     * @return Newscoop\Controller\Action\Helper\Datatable\ADatatable
     */
    public function setAdapter( AAdapter $p_adapter )
    {
        $this->_adapter = $p_adapter;
        return $this;
    }
    
    /**
     * Get the data adapter
     * @return Adapter $p_adapter
     */
    public function getAdapter( )
    {
        return $this->_adapter;
    }

    /**
     * Set output object, for example the view object.
     * @param object $p_object
     * @return Newscoop\Controller\Action\Helper\Datatable\ADatatable
     */
    public function setOutputObject( &$p_object )
    {
        $this->_outputObject = $p_object;
        return $this;
    }
    
	/**
     * Get output object
     * @return object $p_object
     */
    public function getOutputObject( )
    {
        return $this->_outputObject;
    }

    /**
     * Set row handler
     * @param function $p_func 
     * @return Newscoop\Controller\Action\Helper\Datatable\ADatatable 
     */
    public function setRowHandler( $p_func )
    {
        $this->_rowHandler = $p_func;
        return $this;
    }

    /**
     * Set columns
     * @param array
     * @return Newscoop\Controller\Action\Helper\Datatable\ADatatable
     */
    public function setCols( $p_cols )
    {
        $this->_cols      = $p_cols;
        $this->_colsIndex = array_flip( array_keys( $this->_cols ) );
        return $this;  
    }
    
	/**
     * Set params
     * @param array
     * @return Newscoop\Controller\Action\Helper\Datatable\ADatatable
     */
    public function setParams( $p_params )
    {
        $this->_params = $p_params;
        return $this;  
    }
    
 	/**
     * Set option
     * @param string $p_key
     * @param mixed $p_val
     * @return Newscoop\Controller\Action\Helper\Datatable\ADatatable
     */
    public function setOption( $p_key, $p_value )
    {
        $this->_options[$p_key] = $p_value;
        return $this;
    }
    
	/**
     * Set multiple options
     * @param array $p_options
     * @return Newscoop\Controller\Action\Helper\Datatable\ADatatable
     */
    public function setOptions( $p_options )
    {
        foreach( $p_options as $key => $opt ) {
            $this->setOption( $key, $opt );
        }
        return $this;
    }
    
    /**
     * Get options
     * @return array
     */
    public function getOptions( )
    {
        return $this->_options;
    }
    
    /**
     * Get data and set the out object
     * @param array $p_params
     * @param array $p_cols
     */
    public function dispatchData( $p_params = null, $p_cols = null )
    {
        if( is_null( $this->_adapter ) )
            throw new \Exception( 'No adapter' );

        $rows       = array();
        $rowHandler = $this->_rowHandler;
        foreach
        ( 
            $theData = $this->_adapter->getData
            ( 
                ( is_null( $p_params ) ? $this->_params : ( $this->setParams( $p_params )->_params ) ),  // reset params here
                ( is_null( $p_cols ) ? $this->_cols : ( $this->setParams( $p_cols )->_cols ) ) // and cols, if given as param
            ) 
            as $index => $entity 
        ) 
        {
            $rows[] = !is_null($rowHandler) ? $rowHandler( $entity, $index ) : $entity;
        }
        $this->_outputObject->iTotalRecords        = $this->_adapter->getCount();
        $this->_outputObject->iTotalDisplayRecords = $this->_adapter->getCount( $this->_params, $this->_cols );
        $this->_outputObject->aaData               = $rows;
        return $this;
    }
    
    /**
     * set output metadata
     * @param array $p_cols
     * @param array $p_options
     */
    public function dispatchMedatata( $p_cols = null, $p_options = null )
    {
        $this->_outputObject->columns = ( is_null( $p_cols ) ? $this->_cols : ( $this->setParams( $p_cols )->_cols ) );
        $this->_outputObject->options = ( is_null( $p_options ) ? $this->_options : ( $this->setParams( $p_options )->_options ) );
        return $this;
    }
    
    /**
     * should implement a switching method for data and metadata fetching
     */
    abstract public function dispatch( $p_params = null, $p_cols = null, $p_options = null );
}