<?php
/**
 * @package Campsite
 *
 * @author Holman Romero <holman.romero@gmail.com>
 * @author Mugur Rus <mugur.rus@gmail.com>
 * @copyright 2007 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @version $Revision$
 * @link http://www.campware.org
 */

define('URLTYPE_TEMPLATE_PATH', 1);
define('URLTYPE_SHORT_NAMES', 2);

/**
 * Class CampURI
 */
abstract class CampURI {
    /**
     * The URI type
     * It can be either:
     * Template Path = 1
     * Short Names = 2
     *
     * @var integer
     */
    protected $m_type = null;

    /**
     * The URI value
     *
     * @var string
     */
    protected $m_uri = null;

    /**
     * The URI parts
     *
     * @var array
     */
    protected $m_parts = array(
                            'scheme',
                            'user',
                            'password',
                            'host',
                            'port',
                            'path',
                            'query',
                            'fragment'
                            );

    /**
     * @var string
     */
    protected $m_scheme = null;

    /**
     * @var string
     */
    protected $m_host = null;

    /**
     * @var int
     */
    protected $m_port = null;

    /**
     * @var string
     */
    protected $m_user = null;

    /**
     * @var string
     */
    protected $m_password = null;

    /**
     * @var string
     */
    protected $m_path = null;

    /**
     * @var string
     */
    protected $m_query = null;

    /**
     * @var string
     */
    protected $m_fragment = null;

    /**
     * @var array
     */
    private $m_queryArray = null;

    /**
     * Language object
     *
     * @var MetaLanguage
     */
    protected $m_language = null;

    /**
     * Publication object
     *
     * @var MetaPublication
     */
    protected $m_publication = null;

    /**
     * Issue object
     *
     * @var MetaIssue
     */
    protected $m_issue = null;

    /**
     * Section object
     *
     * @var MetaSection
     */
    protected $m_section = null;

    /**
     * Article object
     *
     * @var MetaArticle
     */
    protected $m_article = null;

    /**
     *
     * @var bool
     */
    private $m_validCache = false;


    /**
     * Class constructor
     *
     * @param string $p_uri
     *    The full URI string
     */
    public function __construct($p_uri = 'SELF')
    {
        if (isset($p_uri) && $p_uri != 'SELF') {
            $uriString = $p_uri;
        } else {
            // ... otherwise we build the uri from the server itself.
            //
            // checks whether the site is being queried through SSL
            if (isset($_SERVER['HTTPS'])
                    && !empty($_SERVER['HTTPS'])
                    && (strtolower($_SERVER['HTTPS']) != 'off')) {
                $scheme = 'https://';
            } else {
                $scheme = 'http://';
            }

            // this works at least for apache, some research is needed
            // in order to support other web servers.
            if (!empty($_SERVER['PHP_SELF'])) {
                $uriString = $scheme . $_SERVER['HTTP_HOST'];
            }
            if (isset($_SERVER['REQUEST_URI'])) {
                $uriString .= $_SERVER['REQUEST_URI'];
            }

            // some cleaning directives
            $uriString = urldecode($uriString);
            $uriString = str_replace('"', '&quot;', $uriString);
            $uriString = str_replace('<', '&lt;', $uriString);
            $uriString = str_replace('>', '&gt;', $uriString);
            $uriString = preg_replace('/eval\((.*)\)/', '', $uriString);
            $uriString = preg_replace('/[\\\"\\\'][\\s]*javascript:(.*)[\\\"\\\']/', '""', $uriString);
        }

        $this->parse($uriString);
    } // fn __construct


    /**
     * Returns the URI string based on given URL parameter.
     *
     * @param string $p_param
     *      The URL parameter
     *
     * @return string
     *      The URI string requested
     */
    abstract public function getURI($p_param = null);

    /**
     * Returns the URI path based on given URL parameter.
     *
     * @param string $p_param
     *      The URL parameter
     *
     * @return string
     *      The URI path string requested
     */
    abstract public function getURIPath($p_param = null);

    /**
     * Returns the URI query parameters based on given URL parameter.
     *
     * @param string $p_param
     *
     * @return string
     *      The URI query string requested
     */
    abstract public function getURLParameters($p_param = null);

    /**
     * Returns true if the given parameter is restricted and can not 
     * be set from outside the URL object.
     *
     * @param string $p_parameterName
     * @return bool
     */
    abstract public function isRestrictedParameter($p_parameterName);
    
    
    /**
     * Parses the given URI.
     *
     * @param string $p_uri
     *      The URI string
     *
     * @return boolean
     *    true on success, false on failure
     */
    private function parse($p_uri)
    {
        $success = false;
        if (empty($p_uri)) {
            return $success;
        }

        $this->m_uri = $p_uri;
        $p_uri = urldecode($p_uri);

        if ($parts = parse_url($p_uri)) {
            $success = true;
        }

        // sets the value for every URI part
        foreach ($this->m_parts as $part) {
            $property = 'm_'.$part;
            if (property_exists($this, $property)) {
                $this->$property = (isset($parts[$part])) ? $parts[$part] : null;
            }
        }

        // populates the query array
        if (isset($parts['query'])) {
            parse_str($parts['query'], $this->m_queryArray);
        }

        return $success;
    } // fn parse


    /**
     * Builds a URI string from the given parts.
     *
     * @param array $p_parts
     *      The array of URI parts
     *
     * @return string $uriString
     *      The rendered URI
     */
    protected function render(array $p_parts = array())
    {
        if (empty($p_parts)) {
            $p_parts = $this->m_parts;
        }
        if (!$this->isValidCache()) {
            $this->m_path = $this->getURIPath();
            $this->m_query = $this->getQuery();
        }
        $uriString = '';
        foreach ($p_parts as $part) {
            $property = 'm_'.$part;
            if (!empty($this->$property)) {
                $uriString .= ($part == 'scheme') ? $this->$property.'://' : '';
                $uriString .= ($part == 'user') ? $this->$property : '';
                $uriString .= ($part == 'password') ? ':'.$this->$property.'@' : '';
                $uriString .= ($part == 'host') ? $this->$property : '';
                $uriString .= ($part == 'port') ? ':'.$this->$property : '';
                $uriString .= ($part == 'path') ? $this->$property : '';
                $uriString .= ($part == 'query') ? '?'.$this->$property : '';
                $uriString .= ($part == 'fragment') ? '#'.$this->$property : '';
            }
        }

        return $uriString;
    } // fn render


    /**
     * Gets the URL type.
     *
     * @return integer
     */
    public function getURLType()
    {
        return $this->m_type;
    } // fn getURLType


    /**
     * Gets the URL from the object attributes.
     *
     * @return string $url
     *      The full URL
     */
    public function getURL()
    {
        $url = $this->render(array(
                                    'scheme',
                                    'user',
                                    'password',
                                    'host',
                                    'port',
                                    'path',
                                    'query',
                                    'fragment'
                                   )
                            );
        return $url;
    } // fn getURL


    /**
     * Gets the requested URI.
     *
	 * @return string
     *      The requested URI string
	 */
	public function getRequestURI()
	{
		if (empty($this->m_query)) {
			return $this->m_path;
		}

		return $this->render(array('path', 'query'));
	} // fn getRequestURI


    /**
     * Gets the URI base, it is the scheme, host and (if exists) port.
     *
     * @return string $base
     *      The URI base
     */
    public function getBase()
    {
        $base = $this->m_scheme.'://'.$this->m_host;
        if (is_numeric($this->m_port)) {
            $base .= ':'.$this->m_port;
        }

        return $base;
    } // fn getBase


    /**
     * Gets the base plus the path from the current URI.
     *
     * @return string
     *    The URI base path
     */
    public function getBasePath()
    {
        return $this->render(array('scheme','host','port','path'));
    } // fn getBasePath


    /**
     * Gets the query part from the current URI.
     *
     * @return string $m_query
     *      The query part
     */
    public function getQuery()
    {
        if (!$this->isValidCache()) {
            $this->m_query = CampURI::QueryArrayToString($this->m_queryArray);
        }
        return $this->m_query;
    } // fn getQuery


    /**
     * Gets the given variable from the URI query.
     *
     * @param string $p_varName
     *      The variable name
     *
     * @return string
     *      null on failure, otherwise the variable value
     */
    public function getQueryVar($p_varName)
    {
        if (!isset($this->m_queryArray[$p_varName])) {
            return null;
        }

        return $this->m_queryArray[$p_varName];
    } // fn getQueryVar


    /**
     * Gets the array containing the query variables.
     *
     * @return array $m_queryArray
     *      The array of query vars
     */
    public function getQueryArray()
    {
        return $this->m_queryArray;
    } // fn getQueryArray


    /**
     * Gets the scheme part from the current URI.
     *
     * @return string $m_scheme
     *      The scheme value
     */
    public function getScheme()
    {
        return $this->m_scheme;
    } // fn getScheme


    /**
     * Gets the host part from the current URI.
     *
     * @return string $m_host
     *      The host value
     */
    public function getHost()
    {
        return $this->m_host;
    } // fn getHost


    /**
     * Gets the port part from the current URI.
     *
     * @return int $m_port
     *      The port value
     */
    public function getPort()
    {
        return $this->m_port;
    } // fn getPort


    /**
     * Returns the user part from the current URI.
     *
     * @return string
     *      The username value
     */
    public function getUser()
    {
        return $this->m_user;
    } // fn getUser


    /**
     * Gets the password part from the current URI.
     *
     * @return string $m_password
     *      The password value
     */
    public function getPassword()
    {
        return $this->m_password;
    } // fn getPassword


    /**
     * Gets the path part from the current URI.
     *
     * @return string $m_path
     *      The path value
     */
    public function getPath()
    {
        return $this->m_path;
    } // fn getPath


    /**
     * Gets the fragment part from the current URI.
     *
     * @return string $m_fragment
     *      The fragment value
     */
    public function getFragment()
    {
        return $this->m_fragment;
    } // fn getFragment


    /**
     * Sets the URL type.
     *
     * @param integer $p_type
     *      The URL type number
     *
     * @return void
     */
    protected function setURLType($p_type)
    {
        $this->m_type = (int)$p_type;
    } // fn setURLType


    /**
     * Gets the query part from the current URI.
     *
     * @return string $m_query
     *      The query value
     *
     * @return void
     */
    public function setQuery($p_query)
    {
        $this->m_query = $p_query;
        parse_str($p_query, $this->m_queryArray);
    } // fn setQuery


    /**
     * Sets the given URI query variable.
     *
     * @param string $p_varName
     *      The name of the URI query variable
     * @param string $p_value
     *      The value for the variable
     *
     * @return void
     */
    public function setQueryVar($p_varName, $p_value)
    {
        if (is_null($p_value)) {
            unset($this->m_queryArray[$p_varName]);
        } else {
            $this->m_queryArray[$p_varName] = $p_value;
        }
        $this->validateCache(false);
    } // fn setQueryVar


    /**
     * Sets the URI scheme.
     *
     * @param string $p_scheme
     *      The scheme value
     *
     * @return void
     */
    public function setScheme($p_scheme)
    {
        $this->m_scheme = $p_scheme;
    } // fn setScheme


    /**
     * Sets the URI host.
     *
     * @param string $p_host
     *      The host name
     *
     * @return void
     */
    public function setHost($p_host)
    {
        $this->m_host = $p_host;
    } // fn setHost


    /**
     * Sets the URI port.
     *
     * @param int $p_port
     *      The port number
     *
     * @return void
     */
    public function setPort($p_port)
    {
        $this->m_port = (int)$p_port;
    } // fn setPort


    /**
     * Sets the URI user part.
     *
     * @param string $p_user
     *      The user name
     *
     * @return void
     */
    public function setUser($p_user)
    {
        $this->m_user = $p_user;
    } // fn setUser


    /**
     * Sets the URI password part.
     *
     * @param string @p_password
     *      The user password
     *
     * @return void
     */
    public function setPassword($p_password)
    {
        $this->m_password = $p_password;
    } // fn setPassword


    /**
     * Sets the URI path.
     *
     * @param string $p_path
     *      The path
     *
     * @return void
     */
    public function setPath($p_path)
    {
        $this->m_path = $p_path;
    } // fn setPath


    /**
     * Sets the URI fragment.
     *
     * @param string $p_fragment
     *      The fragment part
     *
     * @return void
     */
    public function setFragment($p_fragment)
    {
        $this->m_fragment = $p_fragment;
    } // fn setFragment


    /**
     * Sets an object property
     *
     * @param string $p_property
     * @return bool
     */
    public function __get($p_property)
    {
        $p_property = strtolower($p_property);
        if (!property_exists($this, "m_$p_property")) {
            return null;
        }
        $memberName = "m_$p_property";
        return $this->$memberName;
    } // fn __get


    /**
     * Sets an object property
     *
     * @param string $p_property
     * @param mixed $p_value
     * @return bool
     */
    public function __set($p_property, $p_value)
    {
        $p_property = strtolower($p_property);
        $searchResult = array_search($p_property, $this->m_parts);
        if ($searchResult !== false) {
            return false;
        }
        if (!property_exists($this, "m_$p_property")) {
            return false;
        }
        if (!is_a($p_value, 'Meta'.$p_property) && !is_null($p_value)) {
            return false;
        }
        $memberName = "m_$p_property";
        $this->$memberName = $p_value;
        $this->validateCache(false);
        return true;
    } // fn __set


    /**
     * Sets the cache validation for URI rendering
     *
     * @param bool $p_valid
     */
    protected function validateCache($p_valid)
    {
        $this->m_validCache = $p_valid;
    } // fn validateCache


    /**
     * Returns the cache valid state
     *
     * @return bool
     */
    protected function isValidCache()
    {
        return $this->m_validCache;
    } // fn isValidCache


    /**
     * Returns whether the site is running over SSL or not.
     *
     * @return boolean
     *    true on success, false on failure
     */
    public function isSSL()
    {
        return ($this->m_scheme == 'https') ? true : false;
    } // fn isSSL


    /**
     * Builds a URI query string from the given query array.
     *
     * @param array $p_queryArray
     *      An array of query variables
     *
     * @return string $queryString
     *      The generated query string
     */
    protected static function QueryArrayToString($p_queryArray)
    {
        if (!is_array($p_queryArray) || sizeof($p_queryArray) < 1) {
            return false;
        }

        $queryString = '';
        $queryVars = array();
        foreach ($p_queryArray as $var => $value) {
            $queryVars[] = $var.'='.urlencode($value);
        }
        $queryString = implode('&', $queryVars);

        return $queryString;
    } // fn QueryArrayToString

} // class CampURI

?>