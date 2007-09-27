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
// We indirectly reference the DOCUMENT_ROOT so we can enable
// scripts to use this file from the command line, $_SERVER['DOCUMENT_ROOT']
// is not defined in these cases.
$g_documentRoot = $_SERVER['DOCUMENT_ROOT'];

require_once($g_documentRoot.'/classes/UrlType.php');

require_once($g_documentRoot.'/template_engine/classes/CampConfig.php');
require_once($g_documentRoot.'/template_engine/classes/CampDatabase.php');
require_once($g_documentRoot.'/template_engine/classes/CampSession.php');
require_once($g_documentRoot.'/template_engine/classes/CampContext.php');
require_once($g_documentRoot.'/template_engine/classes/CampURIShortNames.php');
require_once($g_documentRoot.'/template_engine/classes/CampURITemplatePath.php');
//require_once($g_documentRoot.'/template_engine/classes/CampLocator.php');

require_once($g_documentRoot.'/template_engine/metaclasses/MetaLanguage.php');

/**
 * Class CampSite
 */
final class CampSite {
    /**
     * Object to process the request
     */
    //private $m_locator = null;


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
            $config = self::GetConfig();
            $p_context['language'] = $config->getSetting('lang_id');
        }

        $context = new CampContext();
        $this->setLanguage($p_context['language'], $context);


        //$this->createLocator();
    } // fn init


    /**
     * Loads the configuration options
     *
     * @param string The path to the config file
     */
    public function loadConfiguration($p_file = null)
    {
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
     *
    public function locator()
    {
        $uri = CampURI::singleton();
        $locator = $this->getLocator();

        if (!$locator->parser($uri->render())) {
            return new PEAR_Error('Unable to locate the request');
        }
    } */


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
        //return $this->m_locator;
    } // fn getLocator


    /**
     * Sets the page title
     *
     * @param string
     *    $title The page title
     */
    public function setPageTitle($title = null)
    {
        $config = self::GetConfig();

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
        //$this->m_locator = CampLocator::singleton();
    } // fn createLocator


    /**
     * Returns a CampConfig instance.
     *
     * @return object
     *      A CampConfig instance
     */
    public static function GetConfig()
    {
        return CampConfig::singleton();
    } // fn GetConfig


    /**
     * Returns a CampDatabase instance.
     *
     * @return object
     *    A CampDatabase instance.
     */
    public static function GetDatabase()
    {
        return CampDatabase::singleton();
    } // fn GetDatabase


    /**
     * Returns a CampSession instance.
     *
     * @return object
     *    A CampSession instance
     */
    public static function GetSession()
    {
        return CampSession::singleton();
    } // fn GetSession


    /**
     * Returns the appropiate URI instance.
     *
     * @param string $p_uri
     *      The URI to work with
     */
    public static function GetURI($p_uri = 'SELF')
    {
        $urlType = 0;

        // tries to get the url from config file
        $config = self::GetConfig();
        $urlTypeObj = new UrlType($config->getSetting('campsite.url_type'));
        if (is_object($urlTypeObj) && $urlTypeObj->exists()) {
            $urlType = $urlTypeObj->getId();
        }

        // sets url type to default if necessary
        if (!$urlType) {
            $urlType = $config->getSetting('campsite.url_default_type');
        }

        // instanciates the corresponding URI object
        switch ($urlType) {
            case 1:
                $uriInstance = CampURITemplatePath::singleton($p_uri);
                break;
            case 2:
                $uriInstance = CampURIShortNames::singleton($p_uri);
                break;
        }

        return $uriInstance;
    } // fn GetURI

} // class CampSite

?>