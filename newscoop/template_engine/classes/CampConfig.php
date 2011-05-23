<?php
/**
 * @package Campsite
 *
 * @author Holman Romero <holman.romero@gmail.com>
 * @copyright 2007 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @version $Revision$
 * @link http://www.sourcefabric.org
 */

/**
 * Class CampConfig
 */
final class CampConfig
{
    /**
     * Holds instance of the class
     *
     * @var object
     */
    private static $m_instance = null;

    /**
     * Holds configuration settings
     *
     * @var m_config
     */
    private $m_config = array();


    /**
     * Class constructor
     *
     * @param string $p_configFile
     *      The full path to the configuration file
     */
    private function __construct($p_configFile = null)
    {
        global $Campsite;

        if (empty($p_configFile)) {
            $p_configFile = $GLOBALS['g_campsiteDir'].'/conf/configuration.php';
        }

        require_once($p_configFile);
        $this->m_config = $Campsite;
    } // fn __construct


    /**
     * Builds an instance object of this class only if there is no one.
     *
     * @param string $p_configFile
     *      The full path to the configuration file
     *
     * @return object $m_instance
     *      The CampConfig instance
     */
    public static function singleton($p_configFile = null)
    {
        if (!isset(self::$m_instance)) {
            self::$m_instance = new CampConfig($p_configFile);
        }

        return self::$m_instance;
    } // fn singleton


    /**
     * Gets all the configuration settings
     *
     * @return array
     *    m_config The array of settings
     */
    public function getAllSettings()
    {
        return $this->m_config;
    } // fn getAllSettings


    /**
     * Gets a setting var value from configuration.
     *
     * @param string
     *    $p_varName The name of the setting variable
     *
     * @return mixed
     *    null Var name passed is no valid
     *    mixed The value of the setting variable
     */
    public function getSetting($p_varName)
    {
        if (empty($p_varName)) {
            return null;
        }

        $settingVar = CampConfig::TranslateSettingName($p_varName);
        if (!$settingVar) {
            return array_key_exists($p_varName, $this->m_config) ? $this->m_config[$p_varName] : null;
        }

        $varname = $settingVar['varname'];
        $namespace = $settingVar['namespace'];

        if (!isset($this->m_config[$namespace]) || !is_array($this->m_config[$namespace])
        		|| !array_key_exists($varname, $this->m_config[$namespace])) {
            return null;
        }

        return $this->m_config[$namespace][$varname];
    } // fn getSetting


    /**
     * Translates the given setting variable name into the form
     * namespace and var name.
     *
     * @param string
     *    p_varName The full variable name
     *
     * @return mixed
     *    null If the given value is not appropriate
     *    settingVar An array containing the namespace and variable actual name
     */
    public static function TranslateSettingName($p_varName)
    {
        $settingVar = null;
        if (strpos($p_varName, '.') !== FALSE) {
            @list($namespace, $varname) = explode('.', $p_varName);
        }
        if (!empty($namespace) && !empty($varname)) {
            $settingVar['namespace'] = $namespace;
            $settingVar['varname'] = $varname;
        }

        return $settingVar;
    } // fn TranslateVarName

} // class CampConfig

?>
