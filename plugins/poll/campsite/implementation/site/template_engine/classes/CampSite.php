<?php
/**
 * @package Campsite
 */


/**
 * Includes
 */
// We indirectly reference the DOCUMENT_ROOT so we can enable
// scripts to use this file from the command line, $_SERVER['DOCUMENT_ROOT']
// is not defined in these cases.
$g_documentRoot = $_SERVER['DOCUMENT_ROOT'];

require_once($g_documentRoot.'/template_engine/classes/CampConfig.php');
require_once($g_documentRoot.'/template_engine/classes/CampDatabase.php');
require_once($g_documentRoot.'/template_engine/classes/CampSession.php');
require_once($g_documentRoot.'/template_engine/classes/CampContext.php');
//require_once($g_documentRoot.'/template_engine/classes/CampLocator.php');

require_once($g_documentRoot.'/template_engine/metaclasses/MetaLanguage.php');


/**
 * @package Campsite
 */
final class CampSite {
    /**
     * Object to process the request
     */
    private $m_locator = null;


    /**
     * Class constructor
     */
    final public function __construct()
    {
        
    } // fn __construct


    /**
     *
     */
    public function init($p_context = array())
    {
        if (empty($p_context['language'])) {
            $config = CampSite::GetConfig();
            $p_context['language'] = $config->getSetting('lang_id');
        }

        $context = new CampContext();
        $this->setLanguage($p_context['language'], $context);


        $this->createLocator();
    } // fn init


    /**
     * Loads the configuration options
     *
     * @param string The path to the config file
     */
    public function loadConfiguration($p_file)
    {
        if (!file_exists($p_file)) {
            return null;
        }

        CampConfig::singleton($p_file);
    } // fn loadConfiguration


    /**
     *
     */
    public function initSession()
    {
        $session = CampSession::singleton();
    } // fn initSession


    /**
     *
     */
    public function locator()
    {
        $uri = CampURI::singleton();
        $locator = $this->getLocator();

        if (!$locator->parser($uri->render())) {
            return new PEAR_Error('Unable to locate the request');
        }
    } // fn locator


    /**
     *
     */
    private function setLanguage($p_langId, &$p_context)
    {
        $p_context->language = new MetaLanguage($p_langId);
        if (!$p_context->language) {
            $this->setDefaultLanguage();
        }
    } // fn setLanguage


    /**
     *
     */
    public function getLocator()
    {
        return $this->m_locator;
    } // fn getLocator


    /**
     * Sets the page title
     *
     * @param string
     *    $title The page title
     */
    public function setPageTitle($title = null)
    {
        $config = CampSite::GetConfig();

        $siteName = $config->getSetting('site.name');
        if(!$config->getSetting('site.online')) {
            $siteName .= ' [ Offline ]';
        }
    } // fn setPageTitle


    /**
     *
     */
    private function createLocator()
    {
        $this->m_locator = CampLocator::singleton();
    } // fn createLocator


    /**
     * Returns a CampConfig object.
     *
     * @param string
     *    p_file The full path to the configuration file
     *
     * @return object
     *    A CampConfig instance
     */
    public static function GetConfig($p_file = null)
    {
        return CampConfig::singleton($p_file);
    } // fn getConfig


    /**
     * Returns a CampDatabase object.
     *
     * @return object
     *    A CampDatabase instance.
     */
    public static function GetDatabase()
    {
        return CampDatabase::singleton();
    } // fn getDatabase


    /**
     * Returns a CampSession object.
     *
     * @param string
     *    p_name The name for the session
     *
     * @return object
     *    A CampSession instance
     */
    public static function GetSession()
    {
        return CampSession::singleton();
    } // fn getSession

} // class CampSite

?>