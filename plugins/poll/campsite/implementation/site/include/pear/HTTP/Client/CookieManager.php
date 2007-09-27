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
// $Id: CookieManager.php,v 1.3 2004/04/10 10:04:52 avb Exp $

/**
 * This class is used to store cookies and pass them between HTTP requests.
 * 
 * @package HTTP_Client
 * @author  Alexey Borzov <avb@php.net>
 * @version $Revision: 1.3 $
 */
class HTTP_Client_CookieManager
{
   /**
    * An array containing cookie values
    * @var array
    */
    var $_cookies = array();


   /**
    * Constructor
    * 
    * @access public
    */
    function HTTP_Client_CookieManager()
    {
        // abstract
    }


   /**
    * Adds cookies to the request
    * 
    * @access public
    * @param object An HTTP_Request object
    */
    function passCookies(&$request)
    {
        if (!empty($this->_cookies)) {
            $url =& $request->_url;
            // We do not check cookie's "expires" field, as we do not store deleted
            // cookies in the array and our client does not work long enough for other
            // cookies to expire. If some kind of persistence is added to this object,
            // then expiration should be checked upon loading and session cookies should
            // be cleared on saving.
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
    * @access public
    * @param object An HTTP_Request object with sendRequest() already done
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
                if (!isset($cookie['path'])) {
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
        if ('.' != $cookieDomain{0}) {
            return $requestHost == $cookieDomain;
        } elseif (substr_count($cookieDomain, '.') < 2) {
            return false;
        } else {
            return substr('.'. $requestHost, - strlen($cookieDomain)) == $cookieDomain;
        }
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
}
?>
