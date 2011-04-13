<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Class used to store cookies and pass them between HTTP requests.
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
 * @category   HTTP
 * @package    HTTP_Client
 * @author     Alexey Borzov <avb@php.net>
 * @license    http://opensource.org/licenses/bsd-license.php New BSD License
 * @version    CVS: $Id: CookieManager.php,v 1.9 2008/10/25 17:05:40 avb Exp $
 * @link       http://pear.php.net/package/HTTP_Client
 */

/**
 * Class used to store cookies and pass them between HTTP requests.
 * 
 * @category    HTTP
 * @package     HTTP_Client
 * @author      Alexey Borzov <avb@php.net>
 * @version     Release: 1.2.1
 */
class HTTP_Client_CookieManager
{
   /**
    * An array containing cookie values
    * @var      array
    * @access   private
    */
    var $_cookies = array();

   /**
    * Whether session cookies should be serialized on object serialization
    * @var      boolean
    * @access   private
    */
    var $_serializeSessionCookies = false;
   

   /**
    * Constructor
    * 
    * @param    boolean     Whether session cookies should be serialized
    * @access   public
    * @see      serializeSessionCookies()
    */
    function HTTP_Client_CookieManager($serializeSession = false)
    {
        $this->serializeSessionCookies($serializeSession);
    }

   /**
    * Sets whether session cookies should be serialized when serializing object
    *
    * @param    boolean
    * @access   public
    */
    function serializeSessionCookies($serialize)
    {
        $this->_serializeSessionCookies = (bool)$serialize;
    }


   /**
    * Adds cookies to the request
    * 
    * @access   public
    * @param    HTTP_Request    Request object
    */
    function passCookies(&$request)
    {
        if (!empty($this->_cookies)) {
            $url =& $request->_url;
            // We do not check cookie's "expires" field, as we do not store deleted
            // cookies in the array and our client does not work long enough for other
            // cookies to expire.
            $cookies = array();
            foreach ($this->_cookies as $cookie) {
                if ($this->_domainMatch($url->host, $cookie['domain']) && (0 === strpos($url->path, $cookie['path']))
                    && (empty($cookie['secure']) || $url->protocol == 'https')) {
                    $cookies[$cookie['name']][strlen($cookie['path'])] = $cookie['value'];
                }
            }
            // cookies with longer paths go first
            foreach ($cookies as $name => $values) {
                krsort($values);
                foreach ($values as $value) {
                    $request->addCookie($name, $value);
                }
            }
        }
        return true;
    }


   /**
    * Explicitly adds cookie to the list
    * 
    * @param array An array representing cookie, this function expects all of the array's 
    *              fields to be set
    * @access public
    */
    function addCookie($cookie)
    {
        $hash = $this->_makeHash($cookie['name'], $cookie['domain'], $cookie['path']);
        $this->_cookies[$hash] = $cookie;
    }


   /**
    * Updates cookie list from HTTP server response
    *
    * @access   public
    * @param    HTTP_Request    Request object already containing the response
    */
    function updateCookies(&$request)
    {
        if (false !== ($cookies = $request->getResponseCookies())) {
            $url =& $request->_url;
            foreach ($cookies as $cookie) {
                // use the current domain by default
                if (!isset($cookie['domain'])) {
                    $cookie['domain'] = $url->host;
                }
                // use the path to the current page by default
                if (empty($cookie['path'])) {
                    $cookie['path'] = DIRECTORY_SEPARATOR == dirname($url->path)? '/': dirname($url->path);
                }
                // check if the domains match
                if ($this->_domainMatch($url->host, $cookie['domain'])) {
                    $hash = $this->_makeHash($cookie['name'], $cookie['domain'], $cookie['path']);
                    // if value is empty or the time is in the past the cookie is deleted, else added
                    if (strlen($cookie['value'])
                        && (!isset($cookie['expires']) || (strtotime($cookie['expires']) > time()))) {
                        $this->_cookies[$hash] = $cookie;
                    } elseif (isset($this->_cookies[$hash])) {
                        unset($this->_cookies[$hash]);
                    }
                }
            }
        }
    }


   /**
    * Generates a key for the $_cookies array.
    * 
    * The cookies is uniquely identified by its name, domain and path.
    * Thus we cannot make f.e. an associative array with name as a key, we should
    * generate a key from these 3 values.
    * 
    * @access private
    * @param string    Cookie name
    * @param string    Cookie domain
    * @param string    Cookie path
    * @return string   a key 
    */
    function _makeHash($name, $domain, $path)
    {
        return md5($name . "\r\n" . $domain . "\r\n" . $path);
    }


   /**
    * Checks whether a cookie domain matches a request host.
    * 
    * Cookie domain can begin with a dot, it also must contain at least
    * two dots.
    * 
    * @access private
    * @param string     request host
    * @param string     cookie domain
    * @return bool      match success
    */
    function _domainMatch($requestHost, $cookieDomain)
    {
        if ($requestHost == $cookieDomain) {
            return true;
        }
        // IP address, we require exact match
        if (preg_match('/^(?:\d{1,3}\.){3}\d{1,3}$/', $requestHost)) {
            return false;
        }
        if ('.' != $cookieDomain[0]) {
            $cookieDomain = '.' . $cookieDomain;
        }
        // prevents setting cookies for '.com'
        if (substr_count($cookieDomain, '.') < 2) {
            return false;
        }
        return substr('.' . $requestHost, -strlen($cookieDomain)) == $cookieDomain;
    }


   /**
    * Clears the $_cookies array
    *
    * @access public
    */
    function reset()
    {
        $this->_cookies = array();
    }


   /**
    * Magic serialization function
    *
    * Removes session cookies if $_serializeSessionCookies is false (default)
    */
    function __sleep()
    {
        if (!$this->_serializeSessionCookies) {
            foreach ($this->_cookies as $hash => $cookie) {
                if (empty($cookie['expires'])) {
                    unset($this->_cookies[$hash]);
                }
            }
        }
        return array('_cookies', '_serializeSessionCookies');
    }


   /**
    * Magic unserialization function, purges expired cookies  
    */
    function __wakeup()
    {
        foreach ($this->_cookies as $hash => $cookie) {
            if (!empty($cookie['expires']) && strtotime($cookie['expires']) < time()) {
                unset($this->_cookies[$hash]);
            }
        }
    }
}
?>
