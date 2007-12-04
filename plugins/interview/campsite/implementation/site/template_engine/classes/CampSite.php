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
 *
 * We indirectly reference the DOCUMENT_ROOT so we can enable
 * scripts to use this file from the command line, $_SERVER['DOCUMENT_ROOT']
 * is not defined in these cases.
 */
$g_documentRoot = $_SERVER['DOCUMENT_ROOT'];

require_once($g_documentRoot.'/classes/UrlType.php');
require_once($g_documentRoot.'/template_engine/classes/CampSystem.php');
require_once($g_documentRoot.'/template_engine/classes/CampConfig.php');
require_once($g_documentRoot.'/template_engine/classes/CampDatabase.php');
require_once($g_documentRoot.'/template_engine/classes/CampSession.php');
require_once($g_documentRoot.'/template_engine/classes/CampContext.php');
require_once($g_documentRoot.'/template_engine/classes/CampRequest.php');
require_once($g_documentRoot.'/template_engine/classes/CampURIShortNames.php');
require_once($g_documentRoot.'/template_engine/classes/CampURITemplatePath.php');
require_once($g_documentRoot.'/template_engine/metaclasses/MetaLanguage.php');

/**
 * Class CampSite
 */
final class CampSite extends CampSystem
{
    /**
     * Request time from client.
     *
     * @var string
     */
    private $m_requestTime = null;


    /**
     * Class constructor
     */
    final public function __construct()
    {
        parent::__construct();

        $this->m_requestTime = date('Y-m-d H:i:s', time());
    } // fn __construct


    /**
     * Initialises the context.
     *
     * After load the session, the application parse the current URI
     * and starts the context from the request parameters.
     *
     * @return void
     */
    public function init()
    {
        // returns when site is not in online mode
        if ($this->getSetting('site.online') == 'N') {
            return;
        }

        // starts the URI instance
        self::GetURIInstance();

        // gets the context
        $context = CampTemplate::singleton()->context();

        $languageId = CampRequest::GetVar(CampRequest::LANGUAGE_ID);
        if (!empty($languageId)) {
            $this->setLanguage($languageId);
        } else {
            // gets the Config instance
            $config =& self::GetConfigInstance();
            $this->setLanguage($config->getSetting('locale.lang_id'));
        }
        $publicationId = CampRequest::GetVar(CampRequest::PUBLICATION_ID);
        if (!empty($publicationId)) {
            $this->setPublication($publicationId);
        }
        $issueNr = CampRequest::GetVar(CampRequest::ISSUE_NR);
        if (!empty($issueNr)) {
            $this->setIssue($publicationId, $languageId, $issueNr);
        }
        $sectionNr = CampRequest::GetVar(CampRequest::SECTION_NR);
        if (!empty($sectionNr)) {
            $this->setSection($publicationId, $issueNr, $languageId, $sectionNr);
        }
        $articleNr = CampRequest::GetVar(CampRequest::ARTICLE_NR);
        if (!empty($articleNr)) {
            $this->setArticle($languageId, $articleNr);
        }

        // sets the current URL to the context
        $context->url = new MetaURL();
    } // fn initContext


    /**
     * Initialises the session.
     */
    public function initSession()
    {
        $session = CampSession::singleton();
    } // fn initSession


    /**
     * Loads the configuration options.
     *
     * @param string $p_configFile
     *      The path to the config file
     */
    public function loadConfiguration($p_configFile = null)
    {
        global $g_documentRoot;

        if (empty($p_configFile)) {
            $p_configFile = $g_documentRoot.'/conf/configuration.php';
        }
        if (!file_exists($p_configFile)) {
            header('Location: /install/index.php');
        }

        CampConfig::singleton($p_configFile);
    } // fn loadConfiguration


    /**
     * Dispatches the site.
     *
     * Sets attribute values from site configuration to the document
     * to be displayed.
     *
     * @return void
     */
    public function dispatch()
    {
        $document =& self::GetHTMLDocumentInstance();
        $config =& self::GetConfigInstance();

        $document->setMetaTag('description', $config->getSetting('site.description'));
        $document->setMetaTag('keywords', $config->getSetting('site.keywords'));
        $document->setTitle($config->getSetting('site.title'));
    } // fn dispatch


    /**
     * Displays the site.
     *
     * @return void
     */
    public function render()
    {
        $document =& self::GetHTMLDocumentInstance();

        // sets the appropiate template if site is not in mode online
        if ($this->getSetting('site.online') == 'N') {
            $template = '_campsite_offline.tpl';
        } else {
            // gets the template file name
            $template = $this->getTemplateName();
        }

        $params = array(
                        'context' => CampTemplate::singleton()->context(),
                        'template' => $template,
                        'templates_dir', CS_PATH_SMARTY_TEMPLATES
                        );
        $document->render($params);
    } // fn render


    /**
     * Returns the template file name
     *
     * @return string $template
     */
    public function getTemplateName()
    {
        $tplId = CampRequest::GetVar(CampRequest::TEMPLATE_ID);
        if (!empty($tplId)) {
            $template = CampSystem::GetTemplateNameById($tplId);
        } else {
            $uri = self::GetURIInstance();
            $template = $uri->getTemplate();
        }

        return $template;
    } // fn getTemplate


    /**
     * Returns a CampConfig instance.
     *
     * @return object
     *      A CampConfig instance
     */
    public static function GetConfigInstance()
    {
        return CampConfig::singleton();
    } // fn GetConfig


    /**
     * Returns a CampDatabase instance.
     *
     * @return object
     *    A CampDatabase instance.
     */
    public static function GetDatabaseInstance()
    {
        return CampDatabase::singleton();
    } // fn GetDatabase


    /**
     * Returns a CampHTMLDocument instance.
     *
     * @return object
     *      The CampHTMLDocument instance.
     */
    public static function GetHTMLDocumentInstance()
    {
        $config =& self::GetConfigInstance();
        $attributes = array(
                            'type' => CampRequest::GetVar('format', 'html'),
                            'charset' => $config->getSetting('site.charset'),
                            'language' => CampRequest::GetVar('language', 'en')
                            );
        return CampHTMLDocument::singleton($attributes);
    } // fn GetHTMLDocumentInstance


    /**
     * Returns a CampSession instance.
     *
     * @return object
     *    A CampSession instance
     */
    public static function GetSessionInstance()
    {
        return CampSession::singleton();
    } // fn GetSession


    /**
     * Returns the appropiate URI instance.
     *
     * @param string $p_uri
     *      The URI to work with
     */
    public static function GetURIInstance($p_uri = 'SELF')
    {
        $urlType = 0;

        // tries to get the url type from publication attributes
        $urlTypeObj = new UrlType(CampRequest::GetVar('URLType'));
        if (is_object($urlTypeObj) && $urlTypeObj->exists()) {
            $urlType = $urlTypeObj->getId();
        }

        // sets url type to default if necessary
        if (!$urlType) {
            $config =& self::GetConfigInstance();
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