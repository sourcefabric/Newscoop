<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * A simple HTTP client class.
 *
 * PHP versions 4 and 5
 *
 * LICENSE:
 * 
 * Copyright (c) 2003-2008, Alexey Borzov <avb@php.net>
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *    * Redistributions of source code must retain the above copyright
 *      notice, this list of conditions and the following disclaimer.
 *    * Redistributions in binary form must reproduce the above copyright
 *      notice, this list of conditions and the following disclaimer in the 
 *      documentation and/or other materials provided with the distribution.
 *    * The name of the author may not be used to endorse or promote products 
 *      derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS
 * IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO,
 * THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR
 * CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
 * EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR
 * PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY
 * OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 * NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @category    HTTP
 * @package     HTTP_Client
 * @author      Alexey Borzov <avb@php.net>
 * @license     http://opensource.org/licenses/bsd-license.php New BSD License
 * @version     CVS: $Id: Client.php,v 1.11 2008/10/11 14:29:27 avb Exp $
 * @link        http://pear.php.net/package/HTTP_Client
 */

/*
 * Do this define in your script if you wish HTTP_Client to follow browser 
 * quirks rather than HTTP specification (RFC2616). This means:
 *   - do a GET request after redirect with code 301, rather than use the
 *     same method as before redirect.
 */
// define('HTTP_CLIENT_QUIRK_MODE', true);

/**
 * Class for performing HTTP requests
 */
require_once 'HTTP/Request.php';
/**
 * Class used to store cookies and pass them between HTTP requests.
 */
require_once 'HTTP/Client/CookieManager.php';

/**
 * A simple HTTP client class.
 * 
 * The class wraps around HTTP_Request providing a higher-level
 * API for performing multiple HTTP requests
 *
 * Note that the class implements all the methods defined by the Iterator 
 * interface for going over the received responses. Being PHP4-compatible it is 
 * not declared with 'implements Iterator', though. Therefore if you are 
 * running PHP5 and want to use HTTP_Client in 'foreach' context you should do
 * the following:
 * <code>
 * class PHP5_HTTP_Client extends HTTP_Client implements Iterator {}
 * 
 * $client = new PHP5_HTTP_Client();
 * 
 * // ... perform requests ...
 * 
 * foreach ($client as $url => $response) {
 *     // do something
 * } 
 * </code>
 *
 * If you are running PHP4 you'll have to call the methods manually:
 * <code>
 * for ($client->rewind(); $client->valid(); $client->next()) {
 *     $url = $client->key();
 *     $response = $client->current();
 *     // do something
 * }
 * </code>
 * 
 * @category    HTTP
 * @package     HTTP_Client
 * @author      Alexey Borzov <avb@php.net>
 * @version     Release: 1.2.1
 */
class HTTP_Client
{
   /**#@+
    * @access private
    */
   /**
    * Cookie manager object
    * @var HTTP_Client_CookieManager
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
    * Index for iteration over the responses
    * @var integer
    */
    var $_idx = 0;
   /**#@-*/

   /**
    * Constructor
    * 
    * @access   public
    * @param    array                       Parameters to pass to HTTP_Request's constructor
    * @param    array                       Default headers to send on every request
    * @param    HTTP_Client_CookieManager   Cookie manager object to use
    */
    function HTTP_Client($defaultRequestParams = null, $defaultHeaders = null, $cookieManager = null)
    {
        if (!empty($cookieManager) && is_a($cookieManager, 'HTTP_Client_CookieManager')) {
            $this->_cookieManager = $cookieManager;
        } else {
            $this->_cookieManager =& new HTTP_Client_CookieManager();
        }
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
    * @param    string   Method, constants are defined in HTTP_Request
    * @param    array    Extra headers to send
    * @access   private
    * @return   HTTP_Request    Request object with all defaults applied
    */
    function &_createRequest($url, $method = HTTP_REQUEST_METHOD_GET, $headers = array())
    {
        $req =& new HTTP_Request($url, $this->_defaultRequestParams);
        $req->setMethod($method);
        foreach ($this->_defaultHeaders as $name => $value) {
            $req->addHeader($name, $value);
        }
        foreach ($headers as $name => $value) {
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
    * @param    array   Extra headers to send
    * @access   public
    * @return   integer HTTP response code
    * @throws   PEAR_Error
    */
    function head($url, $headers = array())
    {
        $request =& $this->_createRequest($url, HTTP_REQUEST_METHOD_HEAD, $headers);
        return $this->_performRequest($request);
    }
   

   /**
    * Sends a 'GET' HTTP request
    * 
    * @param    string  URL
    * @param    mixed   additional data to send
    * @param    boolean Whether the data is already urlencoded
    * @param    array   Extra headers to send
    * @access   public
    * @return   integer HTTP response code
    * @throws   PEAR_Error
    */
    function get($url, $data = null, $preEncoded = false, $headers = array())
    {
        $request =& $this->_createRequest($url, HTTP_REQUEST_METHOD_GET, $headers);
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
    * @param    array   Extra headers to send
    * @access   public
    * @return   integer HTTP response code
    * @throws   PEAR_Error
    */
    function post($url, $data, $preEncoded = false, $files = array(), $headers = array())
    {
        $request =& $this->_createRequest($url, HTTP_REQUEST_METHOD_POST, $headers);
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
    * Sends a 'PUT' HTTP request
    *
    * @param    string  URL
    * @param    string  Request body
    * @param    array   Extra headers to send
    * @access   public
    * @return   integer HTTP response code
    * @throws   PEAR_Error
    */
    function put($url, $body = '', $headers = array())
    {
        $request =& $this->_createRequest($url, HTTP_REQUEST_METHOD_PUT, $headers);
        $request->setBody($body);
        return $this->_performRequest($request);
    }


   /**
    * Sends a 'DELETE' HTTP request
    *
    * @param    string  URL
    * @param    array   Extra headers to send
    * @access   public
    * @return   integer HTTP response code
    * @throws   PEAR_Error
    */
    function delete($url, $headers = array())
    {
        $request =& $this->_createRequest($url, HTTP_REQUEST_METHOD_DELETE, $headers);
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
    * @param    HTTP_Request    Request object
    * @access   private
    * @return   integer         HTTP response code
    * @throws   PEAR_Error
    */
    function _performRequest(&$request)
    {
        // If this is not a redirect, notify the listeners of new request
        if (0 == $this->_redirectCount && '' != $request->getUrl()) {
            $this->_notify('request', $request->getUrl());
        }
        if (PEAR::isError($err = $request->sendRequest())) {
            $this->_redirectCount = 0;
            return $err;
        }
        $this->_pushResponse($request);

        $code = $request->getResponseCode();
        if ($this->_maxRedirects > 0) {
            if (in_array($code, array(300, 301, 302, 303, 307))) {
                if ('' == ($location = $request->getResponseHeader('Location'))) {
                    $this->_redirectCount = 0;
                    return PEAR::raiseError("No 'Location' field on redirect");
                }
                // Bug #5759: do not try to follow non-HTTP redirects
                if (null === ($redirectUrl = $this->_redirectUrl($request->_url, $location))) {
                    $this->_redirectCount = 0;
                    return $code;
                }
            // Redirect via <meta http-equiv="Refresh"> tag, see request #5734
            } elseif (200 <= $code && $code < 300) {
                $redirectUrl = $this->_getMetaRedirect($request);
            }
        }
        if (!empty($redirectUrl)) {
            if (++$this->_redirectCount > $this->_maxRedirects) {
                $this->_redirectCount = 0;
                return PEAR::raiseError('Too many redirects');
            }
            // Notify of redirection
            $this->_notify('httpRedirect', $redirectUrl);
            // we access the private properties directly, as there are no accessors for them
            switch ($request->_method) {
                case HTTP_REQUEST_METHOD_POST:
                    // Bug #13487: if doing a redirect via <meta>, use GET 
                    if (302 == $code || 303 == $code || $code < 300 ||
                        (301 == $code && defined('HTTP_CLIENT_QUIRK_MODE'))) 
                    {
                        return $this->get($redirectUrl);
                    } elseif (!empty($request->_postData) || !empty($request->_postFiles)) {
                        $postFiles = array();
                        foreach ($request->_postFiles as $name => $data) {
                            $postFiles[] = array($name, $data['name'], $data['type']);
                        }
                        return $this->post($redirectUrl, $request->_postData, true, $postFiles);
                    } else {
                        return $this->post($redirectUrl, $request->_body, true);
                    }
                case HTTP_REQUEST_METHOD_HEAD:
                    return (303 == $code? $this->get($redirectUrl): $this->head($redirectUrl));
                case HTTP_REQUEST_METHOD_GET: 
                default:
                    return $this->get($redirectUrl);
            } // switch

        } else {
            $this->_redirectCount = 0;
            if (400 >= $code) {
                $this->_notify('httpSuccess');
                $this->setDefaultHeader('Referer', $request->getUrl());
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
    * To access previous responses use iteration methods
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
    * @param    HTTP_Request    Request object already containing the response
    * @access   private
    */
    function _pushResponse(&$request)
    {
        $this->_cookieManager->updateCookies($request);
        $idx   = $this->_isHistoryEnabled? count($this->_responses): 0;
        $this->_responses[$idx] = array(
            'url'     => $request->getUrl(),
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
        $this->_idx                  = 0;
    }


   /**
    * Adds a Listener to the list of listeners that are notified of
    * the object's events
    *
    * Events sent by HTTP_Client objects:
    * - 'request': sent on HTTP request that is not a redirect
    * - 'httpSuccess': sent when we receive a successfull 2xx response
    * - 'httpRedirect': sent when we receive a redirection response
    * - 'httpError': sent on 4xx, 5xx response
    *
    * @param    HTTP_Request_Listener   Listener to attach
    * @param    boolean                 Whether the listener should be attached 
    *                                   to the created HTTP_Request objects
    * @return   boolean                 whether the listener was successfully attached
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
    * @param    HTTP_Request_Listener   Listener to detach
    * @return   boolean                 Whether the listener was successfully detached
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
    * @param    Net_Url     Object containing the request URL
    * @param    string      Value of the 'Location' response header
    * @return   string|null Absolute URL we are being redirected to, null in case of non-HTTP URL 
    * @access   private
    */
    function _redirectUrl($url, $location)
    {
        // If it begins with a scheme (as defined in RFC 2396) then it is absolute URI 
        if (preg_match('/^([a-zA-Z][a-zA-Z0-9+.-]*):/', $location, $matches)) {
            // Bug #5759: we shouldn't try to follow non-HTTP redirects
            if ('http' == strtolower($matches[1]) || 'https' == strtolower($matches[1])) {
                return $location;
            } else {
                return null;
            }
        } else {
            if ('/' == $location[0]) {
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


   /**
    * Returns the cookie manager object (e.g. for storing it somewhere)
    *
    * @return HTTP_Client_CookieManager
    * @access public
    */
    function getCookieManager()
    {
        return $this->_cookieManager;
    }


   /**
    * Tries to extract a redirect URL from <<meta http-equiv=Refresh>> tag (request #5734)
    *
    * @param    HTTP_Request    A request object already containing the response
    * @return   string|null     Absolute URI we are being redirected to, null if no redirect / invalid redirect
    * @access   private
    */
    function _getMetaRedirect(&$request)
    {
        // Non-HTML response or empty response body
        if ('text/html' != substr($request->getResponseHeader('content-type'), 0, 9) ||
            '' == ($body = $request->getResponseBody())) {
            return null;
        }
        // No <meta http-equiv=Refresh> tag
        if (!preg_match('!<meta\\s+([^>]*http-equiv\\s*=\\s*("Refresh"|\'Refresh\'|Refresh)[^>]*)>!is', $body, $matches)) {
            return null;
        }
        // Just a refresh, no redirect
        if (!preg_match('!content\\s*=\\s*("[^"]+"|\'[^\']+\'|\\S+)!is', $matches[1], $urlMatches)) {
            return null;
        }
        $parts = explode(';', ('\'' == substr($urlMatches[1], 0, 1) || '"' == substr($urlMatches[1], 0, 1))? 
                               substr($urlMatches[1], 1, -1): $urlMatches[1]);
        if (empty($parts[1]) || !preg_match('/url\\s*=\\s*("[^"]+"|\'[^\']+\'|\\S+)/is', $parts[1], $urlMatches)) {
             return null;
        }
        $url = ('\'' == substr($urlMatches[1], 0, 1) || '"' == substr($urlMatches[1], 0, 1))?
               substr($urlMatches[1], 1, -1): $urlMatches[1];
        // We do finally have an url... Now check that it's:
        // a) HTTP, b) not to the same page
        $previousUrl = $request->getUrl();
        $redirectUrl = $this->_redirectUrl($request->_url, html_entity_decode($url));
        return (null === $redirectUrl || $redirectUrl == $previousUrl)? null: $redirectUrl; 
    }


   /**
    * Returns the current element (HTTP response)
    *
    * @access   public
    * @return   array   
    */ 
    function current()
    {
        $response = $this->_responses[$this->_idx];
        unset($response['url']);
        return $response;
    }


   /**
    * Returns the key of the current element
    *
    * @access   public
    * @return   string  URL producing the response
    */
    function key()
    {
        return $this->_responses[$this->_idx]['url'];
    }


   /**
    * Moves forward to next element
    *
    * @access   public
    */ 
    function next()
    {
        $this->_idx++;
    }


   /**
    * Rewinds to the first element (response)
    *
    * @access   public
    */
    function rewind()
    {
        $this->_idx = 0;
    }


   /**
    * Checks if there is a current element after call to rewind() or next()
    *
    * @access   public
    * @return   bool    
    */ 
    function valid()
    {
        return $this->_idx < count($this->_responses);
    }
}
?>
