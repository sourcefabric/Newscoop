<?php
/**
 *
 * @package Newscoop
 * @author mihaibalaceanu
 * @uses Zend_Controller_Plugin_Abstract
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

class Admin_View_Helper_JQueryUtils
{

    /**
     * Placeholder agregated object
     * @var Admin_View_Helper_JQueryReady
     */
    protected $_placeholder = null;

    /**
     * @var array
     */
    private $_repo = array();

    /**
     * @return Admin_View_Helper_JQueryUtils
     */
    public function jQueryUtils()
    {
        return $this;
    }

    /**
     * @param Admin_View_Helper_JQueryReady $placeholder
     * @return Admin_View_Helper_JQueryUtils
     */
    public function setPlaceholder( $placeholder )
    {
        $this->_placeholder = $placeholder;
    }

    /**
     * uses $.registry to store variables on namespace
     * @param string $var switch to val if $var null
     * @param string $val
     */
    public function registerVar( $var, $val=null, $placement=null )
    {
        !$var ? ( $this->_repo[] = $val ) : ( $this->_repo[ $var ] = $val );
        switch( $placement )
        {
            case Zend_View_Helper_Placeholder_Container::PREPEND :
                $placeMe = 'prepend';
                break;
            case Zend_View_Helper_Placeholder_Container::SET :
                $placeMe = 'set';
                break;
            default :
                $placeMe = 'append';
        }
        $this->_placeholder->$placeMe
        (
        	" jQuery.registry.set( '$var', "
        .   ( is_array($val) || is_object($val) ? json_encode( $val ) : ( is_string($val) ? "'$val'" : $val ) )
        .	" ); " );
    }

    /**
     *
     * @uses Admin_View_Helper_JQueryUtils::registerVar() with $placement null
     * @param string $var
     * @param string $val
     */
    public function  __set( $var, $val )
    {
        $this->registerVar( $var, $val );
    }

    /**
     * get variable
     * @param string $var
     */
    public function __get( $var )
    {
        return @$this->_repo[$var];
    }

    /**
     * Turn helper into string
     *
     * @return string
     */
    public function __toString()
    {
        $x = '';
        foreach( $this->_repo as $key => $val ) {
            $x .= " $key : $val ;";
        };
        return $x;
    }
}
