<?php

namespace Newscoop\Api;
use Zend_Rest_Client;
use Zend_Rest_Client_Result;

class Client extends Zend_Rest_Client
{
    public function __call($method, $args)
    {
        $methods = array('post', 'get', 'delete', 'put');

        if (in_array(strtolower($method), $methods)) {
            if (!isset($args[0])) {
                $args[0] = $this->_uri->getPath();
            }
            $this->_data['rest'] = 1;
            $response = $this->{'rest' . $method}($args[0]);
            $this->_data = array();//Initializes for next Rest method.
            $body = $response->getBody();
            echo "<pre>request: "."\n".$this->getHttpClient()->getLastRequest()."</pre>";
            if( !empty($body) )
                return new Zend_Rest_Client_Result($response->getBody());
            return null;
        } else {
            // More than one arg means it's definitely a Zend_Rest_Server
            if (sizeof($args) == 1) {
                // Uses first called function name as method name
                if (!isset($this->_data['method'])) {
                    $this->_data['method'] = $method;
                    $this->_data['arg1']  = $args[0];
                }
                $this->_data[$method]  = $args[0];
            } else {
                $this->_data['method'] = $method;
                if (sizeof($args) > 0) {
                    foreach ($args as $key => $arg) {
                        $key = 'arg' . $key;
                        $this->_data[$key] = $arg;
                    }
                }
            }
            return $this;
        }
    }
}