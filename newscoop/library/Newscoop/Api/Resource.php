<?php
/**
 * @author Mihai Balaceanu <mihai.balaceanu@sourcefabric.org>
 * @package Newscoop
 * @subpackage ApiClient
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Api;

use Zend_Uri_Http;

class Resource
{
    /**
     * handles the requests
     * @var Newscoop\Api\Client
     */
    private $client;

    /**
     * the name of the resource
     * @var string
     */
    protected $name = '';

    /**
     * primary id key name
     * @var string
     */
    protected $id = 'id';

    /**
     * the url path like parameters
     * @var array
     */
    protected $data = array();

    /**
     * the url style key-value request parameters
     * @var array
     */
    protected $params = array();

    /**
     * Decide whether to wrap insert and updates to the resource's name.
     * @var bool
     */
    protected $wrapToName = true;

    /**
     * Request uri separator
     * @var string
     */
    const REQ_URI_SEP = "/";

    /**
     * Contruct and set the rest client
     */
    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * building the request uri based on name and self::$_data if set
     */
    public function setRequestUri()
    {
        $uri = $this->client->getUri();
        $uri->setQuery('');
        $uri->setPath
        (
            ( trim($this->name) != "" ? self::REQ_URI_SEP.$this->name : "" ) . self::REQ_URI_SEP .
            implode( self::REQ_URI_SEP, $this->data )
        );
        $this->client->setUri($uri);
        return $uri;
    }

    /**
     * adds request parameters, in self::$_data will be appended to uri
     * @param string $name
     * @param array $values
     */
    public function setUriParam($name, $values)
    {
        foreach( $values as &$val )
            $val = urlencode($val);

        if( $name == $this->id )
            $this->data['id'] = implode( self::REQ_URI_SEP, $values );
        else
            $this->data[] = $name.self::REQ_URI_SEP.implode( self::REQ_URI_SEP, $values );

        return $this;
    }

    /**
     * set a request parameter
     * @param string $name
     * @param mixed $value
     */
    public function setRequestParam( $name, $value )
    {
        if( isset($this->params[$name]) && is_array($this->params[$name]) )
        {
            $this->params[$name][] = $value;
            return $this;
        }
        $this->params[$name] = array($value);
        return $this;
    }

    /**
     * set multiple request parameters
     * @param array $params
     */
    public function setRequestParams( $params )
    {
        foreach( $params as $key => $val )
            $this->setRequestParam( $key, $val );
    }

    /**
     * Sort ascending
     * @param string $field column to sort on
     */
    public function asc( $field )
    {
        $this->setRequestParam('asc', $field);
        return $this;
    }

    /**
     * Sort descending
     * @param string $field column to sort on
     */
    public function desc( $field )
    {
        $this->setRequestParam('desc', $field);
        return $this;
    }

    /**
     * Url decode to this object
     * @param string $url url to decode
     */
    public function understand($url)
    {
        $url = parse_url($url);
        $pathParts = explode( "/", trim( $url['path'], "/" ) );
        $this->name = $pathParts[0];
        if( isset($pathParts[1]) )
            $this->{$this->id}($pathParts[1]);
        return $this;
    }

    /**
     * decides whether to set params or call a request type or set a parameter
     * @param string $name
     * @param array $args
     * @return Resource
     */
    public function __call( $name, $args )
    {
        // invoke an request type
        $methods = array( 'delete' => 'delete', 'put' => 'update', 'post' => 'insert', 'get' => 'get' );
        if( in_array( strtolower($name), $methods ) && !in_array( 'isparam', $args ) )
        {
            $this->setRequestUri();
            $method = array_search( $name, $methods );

            // accept 1 array value per call
            if( is_array($args) )
                $args = current($args);

            // wrap update and insert in an object with the same resource name
            if( $this->wrapToName && in_array( $name, array( 'update', 'insert' ) ) )
                $args = array( $this->name => $args );

            // merge params and passed argumens
            if( $args )
                $this->params = array_merge( (array)$args, $this->params );

            // return the called for method
            $ret = $this->client->$method( $this->params );

            // reset data for future requests
            $this->data = array();
            $this->params = array();

            return $ret;
        }
        // set a parameter
        return $this->setUriParam( $name, $args );
    }
}