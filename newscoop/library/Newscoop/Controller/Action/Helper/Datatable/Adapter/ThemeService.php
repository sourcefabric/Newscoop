<?php 

namespace Newscoop\Controller\Action\Helper\Datatable\Adapter;

use Newscoop\Controller\Action\Helper\Datatable\Row,
    Newscoop\Service;

class ThemeService extends AAdapter
{
    public function getData( array $p_params, array $p_cols )
    {
        $p_params = (object) $p_params;
        if( isset( $p_params->search ) && trim( $p_params->search ) != "" ) {
            $this->search( $p_params->search );
        }
        if( isset( $p_params->sort ) ) {
            $this->sort( ( !is_array( $params->sort ) ? array( $params->sort ) : $params->sort ) );
        }
        
        $retArray = array
        ( 
            "images"      => array( "img", "img", "img" ), 
            "title"       => "The Journalist",
            "version"     => "version 1.1", 
            "designer"    => "Lucian E. Martin",
            "description" => "Immensely and deservedly populat template derived from Wordpress. Great for bloggers who have outgrown their platform. Huge content column, topics and archive dropdowns, and gorgeous pictures. Perfect for those tired of struggling with overworked commercial platforms",
            "subTitle"	  => "Newscoop 3.5 and higher"
        );
        return array_fill( 0, 3, $retArray );
    }
    
    public function search( string $query, array $cols )
    {
        
    }
    
    public function sort( array $p_params, array $p_cols )
    {
        
    }
    
    public function getCount( array $params = array(), array $cols = array() )
    {
        return 0;
    }
    
}