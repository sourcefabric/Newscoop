<?php

namespace Newscoop\Api;
use Zend_Uri_Http;
class Resource
{
    /**
     * @var Newscoop\Api\Client
     */
    private $client;

    /**
     * the default base url
     * @var string
     */
    protected $baseUrl = 'http://localhost:8080/';

    protected $name = '';

    protected $id = 'id';

    /**
     * @param string $baseUrl
     */
    public function __construct( $baseUrl=null )
    {
        $this->client = new Client();
        $this->client->setUri( is_null($baseUrl) ? $this->baseUrl : ($this->baseUrl=$baseUrl) );
    }

    private $_data=array();

    public function makeRequest()
    {
        $uri = $this->client->getUri();
        $uri->setPath( ( trim($this->name)!= "" ? "/".$this->name : "")."/".implode("/", $this->_data));
        $this->client->setUri($uri);
        return $uri;
    }

    public function get()
    {
        $this->makeRequest();
        $ret = $this->client->get();
        $this->_data = array();
        return $ret;
    }

    public function insert()
    {
        $this->makeRequest();
        $ret = $this->client->post();
        $this->_data = array();
        return $ret;
    }

    public function update($args)
    {
        $this->makeRequest();
        $ret = $this->client->put();
        $this->_data = array();
        return $ret;
    }

    public function delete()
    {
        $this->makeRequest();
        $ret = $this->client->delete();
        $this->_data = array();
        return $ret;
    }

    public function setRequestParam($name, $values)
    {
        foreach ($values as &$val) {
            $val = urlencode($val);
        }
        if( $name == $this->id ) {
            $this->_data['id'] = implode('/', $values);
        }
        else {
            $this->_data[] = $name."/".implode('/', $values);
        }
        return $this;
    }

    public function __call($name, $args)
    {
        return $this->setRequestParam($name, $args);
    }
}