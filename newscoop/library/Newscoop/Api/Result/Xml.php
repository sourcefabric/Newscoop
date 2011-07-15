<?php

namespace Newscoop\Api\Result;

use Zend_Rest_Client_Result;

class Xml extends Zend_Rest_Client_Result
{
    private $ok = false;

    /**
     * Construct response
     * @param Zend_Http_Response $response
     */
    public function __construct( $response )
    {
        set_error_handler( function( $errno, $errstr, $errfile = null, $errline = null, array $errcontext = null )
        {
            throw new \Exception( $errstr, $errno );
        });
        $this->_sxml = simplexml_load_string( $response->getBody() );
        restore_error_handler();

        if( $response->getStatus() >= 400 )
            throw new \Exception( (string) $this->_sxml->message, (float) $this->_sxml->code );

        if( $response->getStatus() >= 200 )
            $this->ok = true;
    }

    public function ok()
    {
        return $this->ok;
    }

    public function handleXmlErrors()
    {
        return null;
    }
}