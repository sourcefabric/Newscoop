<?php
/**
 * @package Campsite
 */


/**
 * @package Campsite
 */
final class CampConfig {
    /**
     * Holds instance of the class
     *
     * @var object
     */
    private static $m_instance = null;

    /**
     * Loaded configuration settings
     *
     * @var m_config
     */
    public $m_config = array();


    /**
     * Class constructor
     *
     * @param string
     *    p_configFile The full path to the configuration file
     */
    private function __construct($p_configFile = null)
    {
        global $g_documentRoot;

        if (empty($p_configFile)) {
            $p_configFile = $g_documentRoot.'/template_engine/configuration.php';
        }

        if (!file_exists($p_configFile)) {
            return new PEAR_Error('No such file: '.$p_configFile);
        }

        require_once($p_configFile);
        $this->m_config = $CampCfg;
    } // fn __construct


    /**
     * Builds an instance object of this class only if there is no one.
     *
     * @param string
     *    p_configFile The full path to the configuration file
     *
     * @return object
     *    m_instance A CampConfig instance
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
            return null;
        }

        $varname = $settingVar['varname'];
        $namespace = $settingVar['namespace'];

        if (!array_key_exists($varname, $this->m_config[$namespace])) {
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
        list($namespace, $varname) = explode('.', $p_varName);
        if (!empty($namespace) && !empty($varname)) {
            $settingVar['namespace'] = $namespace;
            $settingVar['varname'] = $varname;
        }

        return $settingVar;
    } // fn TranslateVarName

} // class CampConfig

?>