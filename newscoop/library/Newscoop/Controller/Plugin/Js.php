<?php
/**
 *
 * @package Newscoop
 * @author mihaibalaceanu
 * @uses Zend_Controller_Plugin_Abstract
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */
namespace Newscoop\Controller\Plugin;

use Zend_Controller_Plugin_Abstract;
use Zend_Controller_Front;
use Zend_Controller_Request_Abstract;

class Js extends Zend_Controller_Plugin_Abstract
{

    /**
     * ! will be incomplete after constructor, full in postDispatch
     * @var string
     */
    protected $_baseUrn = null;

    /**
     * physical path, if you don't set this you need to have a 404 handler with .htaccess, like originally designed
     * @var string
     */
    protected $_basePath = null;

    /**
     * @var \Zend_View
     */
    public $view = null;

    /**
     * Js file suffix/extension
     * @var string
     */
    protected $_fileSuffix = "js";

    /**
     * The shared js file to be included with every request
     * @var string
     */
    protected $_sharedFileName = "_shared";

    /**
     * jsPath is for those who don't have a propper server config in the js folder
     * @param array $p_opts {..., layout : { jsUrl : string, [ jsPath : string ] }, ... }
     */
    public function __construct( $p_opts )
    {
        // base path from options - incomplete
        $this->_baseUrn = trim( $p_opts["resources"]["layout"]["jsUrl"], DIR_SEP ) . DIR_SEP;
        $this->_basePath = ( $p = $p_opts["resources"]["layout"]["jsPath"] ) ? $p . DIR_SEP : false;
        $this->view      = \Zend_Registry::get( 'view' );
    }

    public function postDispatch( Zend_Controller_Request_Abstract $p_request )
    {
        // stick the baseUrl to the basePath because we have a dispatched request now
        // and format those god damn slashes!!
        $baseUrl = trim( Zend_Controller_Front::getInstance()->getBaseUrl(), DIR_SEP );

        $currentUrn = ( $baseUrl != "" ? DIR_SEP . $baseUrl : "" )
                    . DIR_SEP
                    . trim( $this->_baseUrn, DIR_SEP )
                    . DIR_SEP;

        $filesToAppend = array
        (
            "{$this->_basePath}{$this->_sharedFileName}.{$this->_fileSuffix}" => // adding the shared file first for utils
            	"{$currentUrn}{$this->_sharedFileName}.{$this->_fileSuffix}",
            'script' => $this->view->jQueryReady()->toString(), // then the document ready scripts
            "{$this->_basePath}{$p_request->getControllerName()}.{$this->_fileSuffix}" => // controller shared
                "{$currentUrn}{$p_request->getControllerName()}.{$this->_fileSuffix}",
            "{$this->_basePath}{$p_request->getControllerName()}".DIR_SEP."{$p_request->getActionName()}.{$this->_fileSuffix}" => // action specific
            	"{$currentUrn}{$p_request->getControllerName()}".DIR_SEP."{$p_request->getActionName()}.{$this->_fileSuffix}"
        );

        foreach( $filesToAppend as $path => $urn )
        {
            if( $path == 'script' ) {
                $this->view->headScript()->appendScript( $urn );
            }
            if( $this->_basePath && file_exists( $path ) ) {
                 $this->view->headScript()->appendFile( $urn );
            }
        }
    }
}