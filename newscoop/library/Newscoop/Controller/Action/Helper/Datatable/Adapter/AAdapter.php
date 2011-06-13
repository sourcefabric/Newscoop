<?php 

namespace Newscoop\Controller\Action\Helper\Datatable\Adapter;

use string;

abstract class AAdapter
{
    /**
     * Get data
     * @todo return decorateable row type object
     * @return array;
     */
    abstract public function getData( array $params, array $cols );
    
    /**
     * Search data
     * @return array;
     */
    abstract public function search( $query, array $cols = null );
    
    /**
     * Sort data
     * @return array;
     */
    abstract public function sort( array $p_cols );
    
    /**
     * Get total count
     * @param array $p_params
     * @param array $p_cols
     * @return int
     */
    abstract public function getCount( array $p_params = array(), array $p_cols = array() );
    
}