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

use Zend_Controller_Plugin_Abstract,
    Zend_Controller_Front,
    Zend_Controller_Request_Abstract;

class Js extends Zend_Controller_Plugin_Abstract
{

    /**
     * ! will be incomplete after constructor, full in postDispatch
     * @var string
     */
    private $_baseUrn = null;

    /**
     * physical path, if you don't set this you need to have a 404 handler with .htaccess, like originally designed
     * @var string
     */
    private $_basePath = null;

    /**
     * @var \Zend_View
     */
    public $view = null;

    /**
     * Js file suffix/extension
     * @var string
     */
    private $_fileSuffix = "js";

    /**
     * The shared js file to be included with every request
     * @var string
     */
    private $_sharedFileName = "_shared";

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
//        $x = '';
//        foreach ($p_request->getParams() as $k => $v)
//            if (is_string($v))
//                $x .= "$k : $v, ";
//        syslog(LOG_WARNING, "!!!!" . $x);
        // stick the baseUrl to the basePath because we have a dispatched request now
        // and format those god damn slashes!!
        $baseUrl = trim( Zend_Controller_Front::getInstance()->getBaseUrl(), DIR_SEP );
        $this->_baseUrn = ( $baseUrl != "" ? DIR_SEP . $baseUrl : "" )
                         . DIR_SEP
                         . trim( $this->_baseUrn, DIR_SEP )
                         . DIR_SEP;

        $filesToAppend = array
        (
            "{$this->_basePath}{$this->_sharedFileName}.{$this->_fileSuffix}" => // adding the shared file first for utils
            	"{$this->_baseUrn}{$this->_sharedFileName}.{$this->_fileSuffix}",
            'script' => $this->view->jQueryReady()->toString(),
            "{$this->_basePath}{$p_request->getControllerName()}.{$this->_fileSuffix}" => // controller shared
                "{$this->_baseUrn}{$p_request->getControllerName()}.{$this->_fileSuffix}",
            "{$this->_basePath}{$p_request->getControllerName()}".DIR_SEP."{$p_request->getActionName()}.{$this->_fileSuffix}" => // action specific
            	"{$this->_baseUrn}{$p_request->getControllerName()}".DIR_SEP."{$p_request->getActionName()}.{$this->_fileSuffix}"
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
        /* $this->view->headScript()
          ->appendFile
          (
          $this->_baseUrn
          .   $this->_sharedFileName
          .	".{$this->_fileSuffix}"
          )
          ->appendScript( $this->view->jQueryReady()->toString() )
          ->appendFile
          (
          $this->_baseUrn
          .   $p_request->getControllerName()
          .	".{$this->_fileSuffix}"
          )
          ->appendFile
          (
          $this->_baseUrn
          .   $p_request->getControllerName()
          .   DIR_SEP
          .   $p_request->getActionName()
          .	".{$this->_fileSuffix}"
          ); */
    }
}