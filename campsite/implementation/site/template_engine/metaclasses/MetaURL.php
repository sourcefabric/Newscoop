<?php
/**
 * @package Campsite
 *
 * @author Holman Romero <holman.romero@gmail.com>
 * @copyright 2007 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @version $Revision$
 * @link http://www.campware.org
 */

/**
 * Includes
 */
$g_documentRoot = $_SERVER['DOCUMENT_ROOT'];

require_once($g_documentRoot.'/classes/UrlType.php');
require_once($g_documentRoot.'/template_engine/classes/Exceptions.php');

/**
 * Class MetaURL
 */
final class MetaURL
{
    /**
     * @var CampURI object
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
        $this->m_uriObj = CampSite::GetURIInstance();

        $this->m_customProperties['uri'] = 'getURI';
        $this->m_customProperties['uri_path'] = 'getURIPath';
        $this->m_customProperties['url'] = 'getURL';
        $this->m_customProperties['url_parameters'] = 'getURLParameters';

        $this->m_customProperties['form_parameters'] = 'getFormParameters';
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
            $p_property = strtolower($p_property);
            $property = 'm_'.$p_property;
            if (!property_exists($this, $property)) {
                return $this->getCustomProperty($p_property);
            }
            return $this->$property;
        } catch (InvalidPropertyException $e) {
            $property = $this->m_uriObj->$p_property;
            $className = CampContext::ObjectType($p_property);
            return is_null($property) ? new $className : $property;
        }
    } // fn __get


    /**
     * Returns the value of the given parameter
     *
     * @param string $p_parameterName
     * @return string
     */
    final public function get_parameter($p_parameterName)
    {
        return $this->m_uriObj->getQueryVar($p_parameterName);
    }


    /**
     * Sets the given parameter to the given value. Returns true if the parameter
     * can be set (is not a restricted parameter name), false otherwise.
     *
     * @param string $p_parameterName
     * @param string $p_parameterValue
     * @return bool
     */
    final public function set_parameter($p_parameterName, $p_parameterValue)
    {
        $isRestricted = $this->m_uriObj->isRestrictedParameter($p_parameterName);
        if ($isRestricted) {
            return false;
        }
        $this->m_uriObj->setQueryVar($p_parameterName, $p_parameterValue);
    }


    /**
     * Resets the given parameter (sets it's value to null). Returns true if the
     * parameter can be set (is not a restricted parameter name), false otherwise.
     *
     * @param string $p_parameterName
     * @return bool
     */
    final public function reset_parameter($p_parameterName)
    {
        return $this->set_parameter($p_parameterName, null);
    }


    /**
     *
     */
    final public function __set($p_property, $p_value)
    {
        if (strtolower($p_property) == 'uri_parameter') {
            $this->m_uri_parameter = $p_value;
        } else {
            $this->m_uriObj->$p_property = $p_value;
            //            throw new InvalidFunctionException(get_class($this), '__set');
        }
    } // fn __set


    /**
     *
     */
    private function getFormParameters()
    {
        return $this->m_uriObj->getFormParameters();
    } // fn getFormParameters


    /**
     *
     */
    private function getURI()
    {
        return $this->m_uriObj->getURI($this->m_uri_parameter);
    } // fn getURL


    /**
     *
     */
    private function getURIPath()
    {
        return $this->m_uriObj->getURIPath($this->m_uri_parameter);
    } // fn getURL


    /**
     *
     */
    private function getURLParameters()
    {
        return $this->m_uriObj->getURLParameters($this->m_uri_parameter);
    } // fn getURL


    /**
     *
     */
    private function getURL()
    {
        return $this->m_uriObj->getURL();
    } // fn getURL


    /**
     *
     */
    private function getBase()
    {
        return $this->m_uriObj->getBase();
    } // fn getBase


    /**
     *
     */
    private function getPath()
    {
        return $this->m_uriObj->getPath();
    } // fn getPath


    /**
     *
     */
    private function getQuery()
    {
        return $this->m_uriObj->getQuery();
    } // fn getQuery


    /**
     *
     */
    private function getURLType()
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
    private function getRequestURI()
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
    final protected function trigger_invalid_property_error($p_property, $p_smarty = null)
    {
        $errorMessage = INVALID_PROPERTY_STRING . " $p_property "
        . OF_OBJECT_STRING . ' url';
        CampTemplate::singleton()->trigger_error($errorMessage, $p_smarty);
    } // fn trigger_invalid_property_error

} // class MetaURL

?>
