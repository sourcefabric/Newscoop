<?php

namespace Newscoop\Api;

use Zend_Service_Abstract;
use Zend_Rest_Client_Result;
use Zend_Validate_Hostname;
use Zend_Uri;
use Zend_Uri_Http;

class Client extends Zend_Service_Abstract
{
    /**
     * Internal accepted content type setting
     * @var string
     */
    private static $acceptContentType = 'xml';

    /**
     * Accepted content type list mapping
     * @var array
     */
    private static $contentTypes = array
    (
        "xml" => "text/xml",
        "json" => "application/json"
    );

    /**
     * Internal content type to make the request on
     * @var string
     */
    private static $requestContentType = 'xml';

    /**
     * uri object to set path to the end point
     * @var Zend_Uri_Http
     */
    protected $uri;

    /**
     * Used to statically set base url upon configure
     * @var string
     */
    protected static $urlString = null;

    /**
     * Debug logger
     * @var Zend_Log
     */
    protected static $debugLogger = null;

    /**
     * Satically configure the client
     * @param array|object $options
     */
    public static function configure( $options )
    {
        foreach( $options as $option => $value )
            switch( $option )
            {
                case 'accept' :
                    self::$acceptContentType = $value;
                    break;
                case 'data-type' :
                    self::$requestContentType = $value;
                    break;
                case 'url' :
                    self::$urlString = $value;
                    break;
                case 'logger' :
                    self::$debugLogger = $value;
                    break;
            }
    }

    /**
     * Performs an HTTP GET
     *
     * @param string $path
     * @param array  $query Array of GET parameters
     * @throws Zend_Http_Client_Exception
     * @return Zend_Http_Response
     */
    final public function restGet(array $query = null )
    {
        $client = self::getHttpClient();
        // build our query with more params of the same name
        if( $query )
        {
            $queryString = http_build_query($query);
            $queryString = preg_replace('/%5B(?:[0-9]|[1-9][0-9]+)%5D=/', '=', $queryString);
            $client->getUri()->setQuery( $queryString );
        }
        return $client->request('GET');
    }

    /**
     * Perform a POST or PUT
     * Encodes and sets raw data
     *
     * @param mixed $method
     * @param mixed $data
     * @return Zend_Http_Response
     */
    protected function performPost($method, $data = null)
    {
        $client = self::getHttpClient();

        $encoder = __NAMESPACE__."\Encoder\\".ucfirst(self::$requestContentType);
        $client->setRawData($encoder::encode($data));

        return $client->request($method);
    }

    /**
     * HTTP POST
     * @param string $path
     * @param mixed $data Raw data to send
     * @throws Zend_Http_Client_Exception
     * @return Zend_Http_Response
     */
    final public function restPost($data = null)
    {
        return $this->performPost('POST', $data);
    }

    /**
     * HTTP PUT
     * @param mixed $data Raw data to send in request
     * @throws Zend_Http_Client_Exception
     * @return Zend_Http_Response
     */
    final public function restPut($data = null)
    {
        return $this->performPost('PUT', $data);
    }

    /**
     * Performs an HTTP DELETE request to $path.
     *
     * @throws Zend_Http_Client_Exception
     * @return Zend_Http_Response
     */
    final public function restDelete($null=null)
    {
        return self::getHttpClient()->request('DELETE');
    }

	/**
     * Set the URI to use in the request
     *
     * @param string|Zend_Uri_Http $uri URI for the web service
     * @return Zend_Rest_Client
     */
    public function setUri($uri)
    {
        if ($uri instanceof Zend_Uri_Http)
            $this->uri = $uri;
        else
            $this->uri = Zend_Uri::factory($uri);

        $this->getHttpClient()->setUri($this->uri);
        return $this;
    }

    /**
     * Retrieve the current request URI object
     *
     * @return Zend_Uri_Http
     */
    public function getUri()
    {
        if( is_null($this->uri) )
            $this->setUri(self::$urlString);
        return $this->uri;
    }

    public function __call( $method, $args )
    {
        $methods = array( 'post', 'get', 'delete', 'put' );

        if( !in_array( strtolower($method), $methods ) )
            return false;

        $result = null;
        try
        {
            // assume $args is not an 1 element array with a false value
            if( !( $reqData = current($args) ) )
                $reqData = null;

            $this->getHttpClient()
                ->setHeaders('Content-Type', self::$contentTypes[self::$requestContentType] )
                ->setHeaders('Accept', self::$contentTypes[self::$acceptContentType] );

            // @todo the extension
            // @todo $this->getUri()->getPath().".".self::$acceptContentType
            $response = $this->{'rest'.$method}( $reqData );
            $decoder = __NAMESPACE__."\Result\\".ucfirst(self::$acceptContentType);
            $result = new $decoder( $response );
        }
        catch( \Exception $e )
        {
            if( self::$debugLogger )
                self::$debugLogger->err($e->getMessage());
        }

        if( self::$debugLogger )
        {
            self::$debugLogger->info($this->getHttpClient()->getLastRequest());
            self::$debugLogger->info($this->getHttpClient()->getLastResponse());
        }

        return $result;
    }
}