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
     * @return Admin_View_Helper_JQueryUtils
     */
    public function jQueryUtils(  )
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
     * Turn helper into string
     *
     * @return string
     */
    public function toString( )
    {
        /*foreach( $this as $item ) {
            $output .= $item;
        }

        $output = ( $this->_autoEscape ) ? $this->_escape( $output ) : $output;
        return sprintf( $this->_scriptFormat, $output );
        */
    }
}
