<?php
/**
 * 
 * Enter description here ...
 * @author mihaibalaceanu
 *
 */
namespace Newscoop\Controller\Helper;

use Zend_Controller_Action_Helper_FlashMessenger;

class FlashMessenger extends Zend_Controller_Action_Helper_FlashMessenger
{
	/**
	 * error message types
	 */
	const MESSAGE_TYPE_ERROR 	= 'error';
	const MESSAGE_TYPE_WARN		= 'warn';
	const MESSAGE_TYPE_NORMAL	= 'normal';

	/**
     * Add a *typed* message to flash message, 
     * an object will be stored in the session
     * with members: message, type
     *
     * @param string 	$message
     * @param string 	$type error|warn|normal
     * @param mixed		$key session key name to store it in
     * @return Ext_Controller_Helper_FlashMessenger
     */
    public function addTypedMessage( $message, $type = FlashMessenger::MESSAGE_TYPE_NORMAL, $key=null )
    {
        if( self::$_messageAdded === false ) 
            self::$_session->setExpirationHops( 1, null, true );

        if( !is_array(self::$_session->{$this->_namespace}) ) 
            self::$_session->{$this->_namespace} = array();

		$msgToAdd 	= (object) array
        (
        	"message"	=> $message,
        	"type"		=> $type
       	);
       	if( is_null($key) )
        	self::$_session->{$this->_namespace}[] 		= $msgToAdd;
        else
        	self::$_session->{$this->_namespace}[$key] 	= $msgToAdd;
        
       	return $this;
    }

}