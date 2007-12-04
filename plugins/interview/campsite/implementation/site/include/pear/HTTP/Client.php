<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | PHP version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2003 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 3.0 of the PHP license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available through the world-wide-web at                              |
// | http://www.php.net/license/3_0.txt.                                  |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Author: Alexey Borzov <avb@php.net>                                  |
// +----------------------------------------------------------------------+
//
// $Id: Client.php,v 1.4 2004/03/23 13:35:37 avb Exp $

require_once 'HTTP/Request.php';
require_once 'HTTP/Client/CookieManager.php';

/**
 * A simple HTTP client class.
 * 
 * The class wraps around HTTP_Request providing a higher-level
 * API for performing multiple HTTP requests
 * 
 * @package HTTP_Client
 * @author Alexey Borzov <avb@php.net>
 * @version $Revision: 1.4 $
 */
class HTTP_Client
{
   /**
    * An HTTP_Client_CookieManager instance
    * @var object
    */
    var $_cookieManager;

   /**
    * Received HTTP responses
    * @var array
    */
    var $_responses;

   /**
    * Default headers to send on every request
    * @var array
    */
    var $_defaultHeaders = array();

   /**
    * Default parameters for HTTP_Request's constructor
    * @var array
    */
    var $_defaultRequestParams = array();

   /**
    * How many redirects were done
    * @var integer
    */
    var $_redirectCount = 0;

   /**
    * Maximum allowed redirects
    * @var integer
    */
    var $_maxRedirects = 5;

   /**
    * Listeners attached to the client  
    * @var array
    */
    var $_listeners = array();

   /**
    * Whether the listener should be propagated to Request objects
    * @var array
    */
    var $_propagate = array();

   /**
    * Whether to keep all the responses or just the most recent one
    * @var boolean
    */
    var $_isHistoryEnabled = true;

   /**
    * Constructor
    * 
    * @access   public
    * @param    array   Parameters to pass to HTTP_Request's constructor
    * @param    array   Default headers to send on every request
    */
    function HTTP_Client($defaultRequestParams = null, $defaultHeaders = null)
    {
        $this->_cookieManager =& new HTTP_Client_CookieManager();
        if (isset($defaultHeaders)) {
            $this->setDefaultHeader($defaultHeaders);
        }
        if (isset($defaultRequestParams)) {
            $this->setRequestParameter($defaultRequestParams);
        }
    }


   /**
    * Sets the maximum redirects that will be processed.
    * 
    * Setting this to 0 disables redirect processing. If not 0 and the 
    * number of redirects in a request is bigger than this number, then an
    * error will be raised.
    * 
    * @access   public
    * @param    int     Max number of redirects to process
    */
    function setMaxRedirects($value)
    {
        $this->_maxRedirects = $value;
    }


   /**
    * Sets whether to keep all the responses or just the most recent one
    *
    * @access public
    * @param  bool      Whether to enable history
    */
    function enableHistory($enable)
    {
        $this->_isHistoryEnabled = (bool)$enable;
    }

   /**
    * Creates a HTTP_Request objects, applying all the necessary defaults
    *
    * @param    string   URL
    * @param    integer  Method, constants are defined in HTTP_Request
    * @access   private
    * @return   object   HTTP_Request object with all defaults applied
    */
    function &_createRequest($url, $method = HTTP_REQUEST_METHOD_GET)
    {
        $req =& new HTTP_Request($url, $this->_defaultRequestParams);
        $req->setMethod($method);
        foreach ($this->_defaultHeaders as $name => $value) {
            $req->addHeader($name, $value);
        }
        $this->_cookieManager->passCookies($req);
        foreach ($this->_propagate as $id => $propagate) {
            if ($propagate) {
                $req->attach($this->_listeners[$id]);
            }
        }
        return $req;
    }
    

   /**
    * Sends a 'HEAD' HTTP request
    *
    * @param    string  URL
    * @access   public
    * @return   integer HTTP response code
    * @throws   PEAR_Error
    */
    function head($url)
    {
        $request =& $this->_createRequest($url, HTTP_REQUEST_METHOD_HEAD);
        return $this->_performRequest($request);
    }
   

   /**
    * Sends a 'GET' HTTP request
    * 
    * @param    string  URL
    * @param    mixed   additional data to send
    * @param    boolean Whether the data is already urlencoded
    * @access   public
    * @return   integer HTTP response code
    * @throws   PEAR_Error
    */
    function get($url, $data = null, $preEncoded = false)
    {
        $request =& $this->_createRequest($url);
        if (is_array($data)) {
            foreach ($data as $name => $value) {
                $request->addQueryString($name, $value, $preEncoded);
            }
        } elseif (isset($data)) {
            $request->addRawQueryString($data, $preEncoded);
        }
        return $this->_performRequest($request);
    }


   /**
    * Sends a 'POST' HTTP request
    *
    * @param    string  URL
    * @param    mixed   Data to send
    * @param    boolean Whether the data is already urlencoded
    * @param    array   Files to upload. Elements of the array should have the form:
    *                   array(name, filename(s)[, content type]), see HTTP_Request::addFile()
    * @access   public
    * @return   integer HTTP response code
    * @throws   PEAR_Error
    */
    function post($url, $data, $preEncoded = false, $files = array())
    {
        $request =& $this->_createRequest($url, HTTP_REQUEST_METHOD_POST);
        if (is_array($data)) {
            foreach ($data as $name => $value) {
                $request->addPostData($name, $value, $preEncoded);
            }
        } else {
            $request->addRawPostData($data, $preEncoded);
        }
        foreach ($files as $fileData) {
            $res = call_user_func_array(array(&$request, 'addFile'), $fileData);
            if (PEAR::isError($res)) {
                return $res;
            }
        }
        return $this->_performRequest($request);
    }


   /**
    * Sets default header(s) for HTTP requests
    *
    * @param    mixed   header name or array ('header name' => 'header value')
    * @param    string  header value if $name is not an array
    * @access   public
    */
    function setDefaultHeader($name, $value = null)
    {
        if (is_array($name)) {
            $this->_defaultHeaders = array_merge($this->_defaultHeaders, $name);
        } else {
            $this->_defaultHeaders[$name] = $value;
        }
    }


   /**
    * Sets parameter(s) for HTTP requests
    *
    * @param    mixed   parameter name or array ('parameter name' => 'parameter value')
    * @param    string  parameter value if $name is not an array
    * @access   public
    */
    function setRequestParameter($name, $value = null)
    {
        if (is_array($name)) {
            $this->_defaultRequestParams = array_merge($this->_defaultRequestParams, $name);
        } else {
            $this->_defaultRequestParams[$name] = $value;
        }
    }
      

   /**
    * Performs a request, processes redirects
    *
    * @param    object  HTTP_Request object
    * @access   private
    * @return   integer HTTP response code
    * @throws   PEAR_Error
    */
    function _performRequest(&$request)
    {
        // If this is not a redirect, notify the listeners of new request
        if (0 == $this->_redirectCount) {
            $this->_notify('request', $request->_url->getUrl());
        }
        if (PEAR::isError($err = $request->sendRequest())) {
            return $err;
        }
        $this->_pushResponse($request);

        $code = $request->getResponseCode();
        if ($this->_maxRedirects > 0 && in_array($code, array(300, 301, 302, 303, 307))) {
            if (++$this->_redirectCount > $this->_maxRedirects) {
                return PEAR::raiseError('Too many redirects');
            }
            $location = $request->getResponseHeader('Location');
            if ('' == $location) {
                return PEAR::raiseError("No 'Location' field on redirect");
            }
            $url = $this->_redirectUrl($request->_url, $location);
            // Notify of redirection
            $this->_notify('httpRedirect', $url);
            // we access the private properties directly, as there are no accessors for them
            switch ($request->_method) {
                case HTTP_REQUEST_METHOD_POST: 
                    if (302 == $code || 303 == $code) {
                        return $this->get($url);
                    } else {
                        $postFiles = array();
                        foreach ($request->_postFiles as $name => $data) {
                            $postFiles[] = array($name, $data['name'], $data['type']);
                        }
                        return $this->post($url, $request->_postData, true, $postFiles);
                    }
                case HTTP_REQUEST_METHOD_HEAD:
                    return (303 == $code? $this->get($url): $this->head($url));
                case HTTP_REQUEST_METHOD_GET: 
                default:
                    return $this->get($url);
            } // switch

        } else {
            $this->_redirectCount = 0;
            if (400 >= $code) {
                $this->_notify('httpSuccess');
                $this->setDefaultHeader('Referer', $request->_url->getUrl());
                // some result processing should go here
            } else {
                $this->_notify('httpError');
            }
        }
        return $code;
    }


   /**
    * Returns the most recent HTTP response
    * 
    * @access public
    * @return array
    */
    function &currentResponse()
    {
        return $this->_responses[count($this->_responses) - 1];
    }


   /**
    * Saves the server's response to responses list
    *
    * @param    object  HTTP_Request object, with request already sent
    * @access   private
    */
    function _pushResponse(&$request)
    {
        $this->_cookieManager->updateCookies($request);
        $idx   = $this->_isHistoryEnabled? count($this->_responses): 0;
        $this->_responses[$idx] = array(
            'code'    => $request->getResponseCode(),
            'headers' => $request->getResponseHeader(),
            'body'    => $request->getResponseBody()
        );
    }


   /**
    * Clears object's internal properties
    *
    * @access public
    */
    function reset()
    {
        $this->_cookieManager->reset();
        $this->_responses            = array();
        $this->_defaultHeaders       = array();
        $this->_defaultRequestParams = array();
    }


   /**
    * Adds a Listener to the list of listeners that are notified of
    * the object's events
    * 
    * @param    object   HTTP_Request_Listener instance to attach
    * @param    boolean  Whether the listener should be attached to the 
    *                    created HTTP_Request objects
    * @return   boolean  whether the listener was successfully attached
    * @access   public
    */
    function attach(&$listener, $propagate = false)
    {
        if (!is_a($listener, 'HTTP_Request_Listener')) {
            return false;
        }
        $this->_listeners[$listener->getId()] =& $listener;
        $this->_propagate[$listener->getId()] =  $propagate;
        return true;
    }


   /**
    * Removes a Listener from the list of listeners 
    * 
    * @param    object   HTTP_Request_Listener instance to detach
    * @return   boolean  whether the listener was successfully detached
    * @access   public
    */
    function detach(&$listener)
    {
        if (!is_a($listener, 'HTTP_Request_Listener') || 
            !isset($this->_listeners[$listener->getId()])) {
            return false;
        }
        unset($this->_listeners[$listener->getId()], $this->_propagate[$listener->getId()]);
        return true;
    }


   /**
    * Notifies all registered listeners of an event.
    * 
    * Currently available events are:
    * 'request': sent on HTTP request that is not a redirect
    * 'httpSuccess': sent when we receive a successfull 2xx response
    * 'httpRedirect': sent when we receive a redirection response
    * 'httpError': sent on 4xx, 5xx response
    * 
    * @param    string  Event name
    * @param    mixed   Additional data
    * @access   private
    */
    function _notify($event, $data = null)
    {
        foreach (array_keys($this->_listeners) as $id) {
            $this->_listeners[$id]->update($this, $event, $data);
        }
    }


   /**
    * Calculates the absolute URL of a redirect
    *  
    * @param    object  Net_Url object containing the request URL
    * @param    string  Value of the 'Location' response header
    * @return   string  Absolute URL we are being redirected to
    * @access   private
    */
    function _redirectUrl($url, $location)
    {
        if (preg_match('!^https?://!i', $location)) {
            return $location;
        } else {
            if ('/' == $location{0}) {
                $url->path = Net_URL::resolvePath($location);
            } elseif('/' == substr($url->path, -1)) {
                $url->path = Net_URL::resolvePath($url->path . $location);
            } else {
                $dirname = (DIRECTORY_SEPARATOR == dirname($url->path)? '/': dirname($url->path));
                $url->path = Net_URL::resolvePath($dirname . '/' . $location);
            }
            $url->querystring = array();
            $url->anchor      = '';
            return $url->getUrl();
        }
    }
}
?>
