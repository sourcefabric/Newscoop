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
        if (empty($p_configFile)) {
            $p_configFile = CS_PATH_CONFIG.DIR_SEP.'configuration.php';
        }

        try {
            if (!file_exists($p_configFile)) {
                throw new InvalidFileException($p_configFile);
            }
            require_once($p_configFile);
        } catch (InvalidFileException $e) {
            $this->trigger_invalid_file_error($p_configFile);
            return null;
        }

        foreach($CampCfg as $key => $value) {
            $this->m_config[$key] = $value;
        }
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
    public function getSettings()
    {
        return $this->m_config;
    } // fn getSettings


    /**
     * Gets a var value from configuration.
     *
     * @param string
     *    $p_varName The name of the directive
     *
     * @return mixed
     *    null Var passed is no valid
     *    mixed The value of the directive
     */
    public function getVar($p_varName)
    {
        if (!array_key_exists($p_varName, $this->m_config)) {
            return null;
        }
        return $this->m_config[$p_varName];
    } // fn getVar

} // class CampConfig

?>