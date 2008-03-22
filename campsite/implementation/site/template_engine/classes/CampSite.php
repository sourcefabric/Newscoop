<?php
/**
 * @package Campsite
 *
 * @author Holman Romero <holman.romero@gmail.com>
 * @author Mugur Rus <mugur.rus@gmail.com>
 * @copyright 2007 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @version $Revision$
 * @link http://www.campware.org
 */


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

        // gets the context
        CampTemplate::singleton()->context();
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
        $document = self::GetHTMLDocumentInstance();
        $config = self::GetConfigInstance();

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
        $document = self::GetHTMLDocumentInstance();

        // sets the appropiate template if site is not in mode online
        if ($this->getSetting('site.online') == 'N') {
            $template = '_campsite_offline.tpl';
            $templates_dir = CS_PATH_SMARTY_SYS_TEMPLATES;
        } else {
            // gets the template file name
            $template = $this->getTemplateName();
            $templates_dir = CS_PATH_SMARTY_TEMPLATES;
        }

        $params = array(
                        'context' => CampTemplate::singleton()->context(),
                        'template' => $template,
                        'templates_dir' => $templates_dir
                        );
        $document->render($params);
    } // fn render


    /**
     * @param string $p_eventName
     */
    public function event($p_eventName)
    {
        global $g_errorList;

        switch ($p_eventName) {
        case 'beforeRender':
            return CampRequest::GetVar('previewLang', null);
        case 'afterRender':
            $doPreview = CampRequest::GetVar('preview', 'off');
            if ($doPreview == 'on') {
                print("\n<script LANGUAGE=\"JavaScript\">parent.e.document.open();\n"
                    ."parent.e.document.write(\"<html><head><title>Errors</title>"
                    ."</head><body bgcolor=white text=black>\\\n<pre>\\\n"
                    ."\\\n<b>Parse errors:</b>\\\n");

                foreach ($g_errorList as $error) {
                    print("<p>".addslashes($error->getMessage())."</p>\\\n");
                }

                print("</pre></body></html>\\\n\");\nparent.e.document.close();\n</script>\n");
            }
            break;
        }
    } // fn event


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
        $config = self::GetConfigInstance();
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
     * @return CampURI
     */
    public static function GetURIInstance($p_uri = 'SELF')
    {
        global $g_ado_db;

        $urlType = 0;

        // tries to get the url type from publication attributes
        $sqlQuery = 'SELECT p.Id, p.IdDefaultLanguage, p.IdURLType '
            . 'FROM Publications p, Aliases a '
            . 'WHERE p.Id = a.IdPublication AND '
            . "a.Name = '" . $g_ado_db->Escape($_SERVER['HTTP_HOST']) . "'";
        $data = $g_ado_db->GetRow($sqlQuery);
        if (!empty($data)) {
            $urlTypeObj = new UrlType(CampRequest::GetVar('URLType'));
            if (is_object($urlTypeObj) && $urlTypeObj->exists()) {
                $urlType = $urlTypeObj->getId();
            }
        }

        // sets url type to default if necessary
        if (!$urlType) {
            $config = self::GetConfigInstance();
            $urlType = $config->getSetting('campsite.url_default_type');
        }

        // instanciates the corresponding URI object
        switch ($urlType) {
        case 1:
            $uriInstance = new CampURITemplatePath($p_uri);
            break;
        case 2:
            $uriInstance = new CampURIShortNames($p_uri);
            break;
        }

        return $uriInstance;
    } // fn GetURI

} // class CampSite

?>