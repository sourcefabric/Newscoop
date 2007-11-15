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
 * Class CampRequest
 */
final class CampRequest
{
    /**
     * Language identifier parameter name
     */
    const LANGUAGE_ID = 'IdLanguage';

    /**
     * Publication identifier parameter name
     */
    const PUBLICATION_ID = 'IdPublication';

    /**
     * Issue number parameter name
     */
    const ISSUE_NR = 'NrIssue';

    /**
     * Section number parameter name
     */
    const SECTION_NR = 'NrSection';

    /**
     * Article number parameter name
     */
    const ARTICLE_NR = 'NrArticle';

    /**
     * Template identifier parameter name
     */
    const TEMPLATE_ID = 'tpl';


    /**
     * Gets the current URL.
     *
     * @return string
     *      The current URL
     */
    public static function GetURL()
    {
        $uri = CampSite::GetURIInstance();
        return $uri->getURL();
    } // fn getURI


    /**
     * Gets a var from the input.
     * Allows to fetch the variable value requested from the
     * appropiate input method.
     *
     * @param string $p_varName
     *      The name of the variable to be fetched.
     * @param mixed $p_defaultValue
     *      The default value to be fetched for the given variable
     * @param string $p_reqMethod
     *      The requested input method, default is REQUEST
     * @param string $p_dataType
     *      TODO to be implemented
     *
     * @return mixed $var
     *      The value of the requested variable
     */
    public static function GetVar($p_varName, $p_defaultValue = null,
                                  $p_reqMethod = 'default', $p_dataType = null)
    {
        $p_reqMethod = strtoupper($p_reqMethod);
        if ($p_reqMethod == 'SERVER') {
            $p_reqMethod = strtoupper($_SERVER['REQUEST_METHOD']);
        }
        $p_dataType = strtoupper($p_dataType);
        switch ($p_reqMethod) {
        case 'GET':
            $method = &$_GET;
            break;
        case 'POST':
            $method = &$_POST;
            break;
        case 'FILES':
            $method = &$_FILES;
            break;
        case 'COOKIE':
            $method = &$_COOKIE;
            break;
        default:
            $method = &$_REQUEST;
            break;
        }

        if (isset($method[$p_varName]) && !is_null($method[$p_varName])) {
            $var = $method[$p_varName];
        } else {
            $var = $p_defaultValue;
        }

        return $var;
    } // fn GetVar


    /**
     * Sets the value to the given variable.
     *
     * @param string $p_varName
     *      The name of the variable to be set
     * @param mixed $p_varValue
     *      The variable value to be assigned
     * @param string $p_reqMethod
     *      The input method
     * @param boolean $p_overwrite
     *      Whether overwrite the current value of the variable or not
     *
     * @returns void
     */
    public static function SetVar($p_varName, $p_varValue = null,
                                  $p_reqMethod = 'default', $p_overwrite = true)
    {
        if (!$p_overwrite && isset($_REQUEST[$p_varName])) {
            return $_REQUEST[$p_varName];
        }

        $p_reqMethod = strtoupper($p_reqMethod);
        if ($p_reqMethod == 'SERVER') {
            $p_reqMethod = strtoupper($_SERVER['REQUEST_METHOD']);
        }

        switch ($p_reqMethod) {
        case 'GET':
            $_GET[$p_varName] = $p_varValue;
            $_REQUEST[$p_varName] = $p_varValue;
            break;
        case 'POST':
            $_POST[$p_varName] = $p_varValue;
            $_REQUEST[$p_varName] = $p_varValue;
            break;
        case 'FILES':
            $_FILES[$p_varName] = $p_varValue;
            $_REQUEST[$p_varName] = $p_varValue;
            break;
        case 'COOKIE':
            $_COOKIE[$p_varName] = $p_varValue;
            $_REQUEST[$p_varName] = $p_varValue;
            break;
        case 'FILES':
            $_FILES[$p_varName] = $p_varValue;
            $_REQUEST[$p_varName] = $p_varValue;
            break;
        default:
            $_GET[$p_varName] = $p_varValue;
            $_POST[$p_varName] = $p_varValue;
            $_REQUEST[$p_varName] = $p_varValue;
            break;
        }
    } // fn SetVar


    public static function GetInput($p_reqMethod = 'default')
    {
        $input = array();

        $p_reqMethod = strtoupper($p_reqMethod);
        switch($p_reqMethod) {
        case 'GET':
            $input = $_GET;
            break;
        case 'POST':
            $input = $_POST;
            break;
        case 'COOKIE':
            $input = $_COOKIE;
            break;
        case 'FILES':
            $input = $_POST;
            break;
        default:
            $input = $_REQUEST;
            break;
        }

        return $input;
    } // fn GetInput


} // class CampRequest

?>