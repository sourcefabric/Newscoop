<?php
/**
 *
 * @author mihaibalaceanu
 * @version 
 * 
 */

class Admin_View_Helper_FlashMsg extends Zend_View_Helper_Abstract
{

	/**
	 * @var Zend_View_Interface 
	 */
	public $view;

	/**
	 * @var Ext_Controller_Helper_FlashMessenger
	 */
	private $_adapter 	= null;
	
	/**
	 * @var array
	 */
	private $_templates	= array
	(
		'normal'	=> '<span class="flash-message normal">%s</span>',
		'error'		=> '<span class="flash-message error">%s</span>',
		'warn'		=> '<span class="flash-message warn">%s</span>',
	);
	
	/**
	 * @var string
	 */
	private $_closeButton = "<a class='fr button-close' href='javascript:void(0)' onclick='$(this).parent(\"span\").fadeOut()'>x</a>"; 
	
	/**
	 *  @param string 	$type 	normal|current|both
	 *  @param mixed	$key	if null all messages outputted
	 */
	public function flashMsg( $type='normal', $key=null, $sticky=false )
	{  
		if( is_null( $this->_adapter ) ) return;
		$message 	= $outputMessages = array();
		$output		= ""; 
		switch( $type )
		{
			case 'normal'	: $messages = $this->_adapter->getMessages(); break;
			case 'current'	: $messages = $this->_adapter->getCurrentMessages(); break;
			default			: $messages = array_merge(	$this->_adapter->getMessages(), $this->_adapter->getCurrentMessages() ); break;
		}
		if( !is_null($key) )
		{
			if( isset($messages[$key]) ) $outputMessages[] 	= $messages[$key];
		}	
		else
			$outputMessages		= $messages;

		foreach( $outputMessages as $msg )
		{
			if( is_object($msg) && isset($msg->type) )
			{
				$replaceMsg = $msg->message;
				$tpl		= $this->_templates[$msg->type];
			}
			else
			{
				$replaceMsg = $msg;
				$tpl		= $this->_templates[0];
			}
				
			$output .= sprintf( $tpl, ( !$sticky ? $this->_closeButton : "" ) . $replaceMsg );
		}
		
		return $output;
	}

	/**
	 * Sets the view field 
	 * @param $view Zend_View_Interface
	 */
	public function setView( Zend_View_Interface $view )
	{
		$this->view = $view;
	}
	
	/**
	 *                         
	 */
	public function setAdapter( $adapter )
	{
		$this->_adapter	= $adapter;
	}
}

