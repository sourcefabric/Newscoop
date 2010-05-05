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
     * Stores the input parameters 
     *
     * @var array
     */
    private static $m_input = array();


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
        CampRequest::InitInput($p_reqMethod);

        if (isset(CampRequest::$m_input[$p_reqMethod][$p_varName])
        && !is_null(CampRequest::$m_input[$p_reqMethod][$p_varName])) {
            $var = CampRequest::$m_input[$p_reqMethod][$p_varName];
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
        CampRequest::InitInput($p_reqMethod);
        if (!$p_overwrite && isset(CampRequest::$m_input[$p_reqMethod][$p_varName])) {
            return CampRequest::$m_input[$p_reqMethod][$p_varName];
        }

        CampRequest::$m_input[$p_reqMethod][$p_varName] = $p_varValue;
        if ($p_reqMethod == 'DEFAULT') {
            CampRequest::InitInput($method = 'get');
            CampRequest::InitInput($method = 'post');
            if (!is_null($p_varValue)) {
                CampRequest::$m_input['GET'][$p_varName] = $p_varValue;
                CampRequest::$m_input['POST'][$p_varName] = $p_varValue;
            } else {
                unset(CampRequest::$m_input['GET'][$p_varName]);
                unset(CampRequest::$m_input['POST'][$p_varName]);
            }
        } else {
            CampRequest::InitInput($method = 'DEFAULT');
            if (!is_null($p_varValue)) {
                CampRequest::$m_input['DEFAULT'][$p_varName] = $p_varValue;
            } else {
                unset(CampRequest::$m_input['DEFAULT'][$p_varName]);
            }
        }
    } // fn SetVar


    /**
     * Returns the whole parameters array for the given input method.
     *
     * @param string $p_reqMethod
     * @return array
     */
    public static function GetInput($p_reqMethod = 'default')
    {
        CampRequest::InitInput($p_reqMethod);
        return CampRequest::$m_input[$p_reqMethod];
    } // fn GetInput

    
    /**
     * Returns the method used to read the input data: GET, POST etc.
     *
     * @return string
     */
    public static function GetMethod() {
        return $_SERVER['REQUEST_METHOD'];
    }
    
    /**
     * Initializes the input parameters array
     *
     * @param string $p_reqMethod
     */
    private static function InitInput(&$p_reqMethod) {
        CampRequest::TranslateMethod($p_reqMethod);
        if (!isset(CampRequest::$m_input[$p_reqMethod])) {
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
        			$input = $_FILES;
        			break;
        		case 'DEFAULT':
        			$input = $_REQUEST;
        			break;
        		default:
        			return;
        	}
        	require_once($GLOBALS['g_campsiteDir'].'/classes/Input.php');
            CampRequest::$m_input[$p_reqMethod] = Input::CleanMagicQuotes($input);
        }
    }


    /**
     * Returns a valid input method name
     *
     * @param string &$p_reqMethod
     * @return string
     */
    private static function TranslateMethod(&$p_reqMethod) {
        $p_reqMethod = strtoupper($p_reqMethod);
        if ($p_reqMethod == 'SERVER') {
            $p_reqMethod = strtoupper($_SERVER['REQUEST_METHOD']);
        }
        if ($p_reqMethod != 'GET' && $p_reqMethod != 'POST'
        && $p_reqMethod != 'FILES' && $p_reqMethod != 'COOKIE') {
            $p_reqMethod = 'DEFAULT';
        }
        return $p_reqMethod;
    }

} // class CampRequest

?>