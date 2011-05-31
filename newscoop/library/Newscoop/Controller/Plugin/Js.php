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
     
    public function __construct( $p_opts )
    {
        $this->_basePath = trim( Zend_Controller_Front::getInstance()->getBaseUrl(), DIR_SEP ) 
                         . DIR_SEP 
                         . trim( $p_opts["resources"]["layout"]["jsUrl"], DIR_SEP ) 
                         . DIR_SEP;
        $this->view      = \Zend_Registry::get( 'view' );
    }

    public function postDispatch( Zend_Controller_Request_Abstract $p_request )
    {
        $this->view->headScript()
            ->appendFile // adding the shared file first for utils
            (
                $this->_basePath 
            .   $this->_sharedFileName 
            .	".{$this->_fileSuffix}" 
            )
            ->appendScript( $this->view->jQueryReady()->toString() )
            ->appendFile // controller shared
            (
                $this->_basePath 
            .   $p_request->getControllerName() 
            .	".{$this->_fileSuffix}" 
            )
            ->appendFile // action specific
            ( 
                $this->_basePath 
            .   $p_request->getControllerName() 
            .   DIR_SEP 
            .   $p_request->getActionName() 
            .	".{$this->_fileSuffix}" 
            );
    }
}