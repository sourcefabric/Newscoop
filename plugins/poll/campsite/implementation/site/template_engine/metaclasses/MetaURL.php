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
    private $m_uri = null;

    /**
     * @var array
     */
    private $m_customProperties = null;


    public function __construct()
    {
        $this->m_uri = CampSite::GetURI();

        $this->m_customProperties['url'] = 'getURL';
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
        throw new InvalidFunctionException(get_class($this), '__set');
    } // fn __set


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
    public function getURL()
    {
        return $this->m_uri->getURL();
    } // fn getURL


    /**
     *
     */
    public function getBase()
    {
        return $this->m_uri->getBase();
    } // fn getBase


    /**
     *
     */
    public function getPath()
    {
        return $this->m_uri->getPath();
    } // fn getPath


    /**
     *
     */
    public function getQuery()
    {
        return $this->m_uri->getQuery();
    } // fn getQuery


    /**
     *
     */
    public function getURLType()
    {
        $urlTypeObj = new UrlType($this->m_uri->getURLType());
        if (!is_object($urlTypeObj) || $urlTypeObj->exists()) {
            return null;
        }

        return $urlTypeObj->getId();
    } // fn getURLType


    /**
     *
     */
    public function getRequestURI()
    {
        return $this->m_uri->getRequestURI();
    } // fn getRequestURI


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