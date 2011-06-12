<?php
/**
 *
 * @package Newscoop
 * @author mihaibalaceanu
 * @uses Zend_Controller_Plugin_Abstract
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Doctrine\DBAL\Types\BooleanType;
class Admin_View_Helper_JQueryReady extends Zend_View_Helper_Placeholder_Container_Standalone
{

    /**
     * Registry key for placeholder
     * @var string
     */
    protected $_regKey = 'Newscoop_View_Helper_JQueryReady';

    /**
     *
     * the code formatting for document load ready around the scripts to be rendered
     *
     * @var string
     */
    protected $_scriptFormat = 'jQuery( function() { %s } )';

    /**
     * Override this cause we need some other escaping like json encode for variables
     * @var bool
     */
    protected $_autoEscape = false;

    /**
     * This helper add a document.ready script type for jquery
     * Retrieve placeholder for title element and optionally add new script
     *
     * @param  string $script
     * @return Admin_View_Helper_JQueryReady
     */
    public function jQueryReady( $script = null )
    {
        if( is_string( $script ) ) {
            $this->append( $script );
        }
        return $this;
    }

    /**
     * Turn helper into string
     *
     * @return string
     */
    public function toString()
    {
        $output = '';
        foreach( $this as $item ) {
            $output .= $item;
        }

        $output = ( $this->_autoEscape ) ? $this->_escape( $output ) : $output;
        return sprintf( $this->_scriptFormat, $output );
    }
}
