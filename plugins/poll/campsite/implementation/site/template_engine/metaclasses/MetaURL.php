<?php
/**
 * @package Campsite
 */

/**
 * Includes
 */
$g_documentRoot = $_SERVER['DOCUMENT_ROOT'];

require_once($g_documentRoot.'/classes/UrlType.php');
require_once($g_documentRoot.'/template_engine/include/constants.php');
require_once($g_documentRoot.'/template_engine/classes/Exceptions.php');

/**
 * Class MetaURL
 */
final class MetaURL
{
    /**
     * @var object
     */
    private $m_uriObj = null;

    /**
     * @var string
     */
    private $m_uri_parameter = null;

    /**
     * @var array
     */
    private $m_customProperties = null;


    public function __construct()
    {
        $this->m_uriObj = CampSite::GetURI();

        $this->m_customProperties['uri'] = 'getURI';
        $this->m_customProperties['uri_path'] = 'getURIPath';
        $this->m_customProperties['url'] = 'getURL';
        $this->m_customProperties['url_parameters'] = 'getURLParameters';

        $this->m_customProperties['base'] = 'getBase';
        $this->m_customProperties['path'] = 'getPath';
        $this->m_customProperties['query'] = 'getQuery';
        $this->m_customProperties['type'] = 'getURLType';
        $this->m_customProperties['request_uri'] = 'getRequestURI';
    } // fn __construct


    /**
     *
     */
    public function __get($p_property)
    {
        try {
            $property = 'm_'.$p_property;
            if (!property_exists($this, $property)) {
                throw new InvalidPropertyException(get_class($this), $p_property);
            }
            return $this->$property;
        } catch (InvalidPropertyException $e) {
            try {
                return $this->getCustomProperty($p_property);
            } catch (InvalidPropertyException $e) {
                $this->trigger_invalid_property_error($p_property);
                return null;
            }
        }
    } // fn __get


    /**
     *
     */
    final public function __set($p_property, $p_value)
    {
        if (strtolower($p_property) == 'uri_parameter') {
            $this->m_uri_parameter = $p_value;
        } else {
            throw new InvalidFunctionException(get_class($this), '__set');
        }
    } // fn __set


    /**
     *
     */
    public function getURI()
    {
        return $this->m_uriObj->getURI($this->m_uri_parameter);
    } // fn getURL


    /**
     *
     */
    public function getURIPath()
    {
        return $this->m_uriObj->getURIPath($this->m_uri_parameter);
    } // fn getURL


    /**
     *
     */
    public function getURLParameters()
    {
        return $this->m_uriObj->getURLParameters($this->m_uri_parameter);
    } // fn getURL


    /**
     *
     */
    public function getURL()
    {
        return $this->m_uriObj->getURL();
    } // fn getURL


    /**
     *
     */
    public function getBase()
    {
        return $this->m_uriObj->getBase();
    } // fn getBase


    /**
     *
     */
    public function getPath()
    {
        return $this->m_uriObj->getPath();
    } // fn getPath


    /**
     *
     */
    public function getQuery()
    {
        return $this->m_uriObj->getQuery();
    } // fn getQuery


    /**
     *
     */
    public function getURLType()
    {
        $urlTypeObj = new UrlType($this->m_uriObj->getURLType());
        if (!is_object($urlTypeObj) || !$urlTypeObj->exists()) {
            return null;
        }

        return $urlTypeObj->getId();
    } // fn getURLType


    /**
     *
     */
    public function getRequestURI()
    {
        return $this->m_uriObj->getRequestURI();
    } // fn getRequestURI


    /**
     *
     */
    private function getCustomProperty($p_property)
    {
        if (!is_array($this->m_customProperties)
                || !array_key_exists($p_property, $this->m_customProperties)) {
            throw new InvalidPropertyException(get_class($this), $p_property);
        }
        if (!method_exists($this, $this->m_customProperties[$p_property])) {
            throw new InvalidPropertyHandlerException(get_class($this), $p_property);
        }
        $methodName = $this->m_customProperties[$p_property];
        
        return $this->$methodName();
    } // fn getCustomProperty


    /**
     *
     */
    final public function trigger_invalid_property_error($p_property, $p_smarty = null)
    {
        $errorMessage = INVALID_PROPERTY_STRING . " $p_property "
            . OF_OBJECT_STRING . ' ' . get_class($this);
        CampTemplate::singleton()->trigger_error($errorMessage, $p_smarty);
    } // fn trigger_invalid_property_error

} // class MetaURL

?>