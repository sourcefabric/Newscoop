<?php 
/**
 * 
 * @todo will implement a decorateable type 
 * @author mihaibalaceanu
 *
 */
namespace Newscoop\Controller\Action\Helper\Datatable;

class Row // implements \ArrayAccess, \IteratorAggregate
{
    private $_data;
    
    public function __construct()
    {
        $this->_data = new \stdClass;
    }
    
    public function __set( $key, $value )
    {
        $this->_data->$key = $value;    
    }
    
    public function __get( $key )
    {
        return @$this->_data->$key;
    }
    
    public function toArray()
    {
        return (array) $this->_data;
    }
} 