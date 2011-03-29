<?php
/**
 * @package Campsite
 *
 * @author Holman Romero <holman.romero@gmail.com>
 * @author Mugur Rus <mugur.rus@gmail.com>
 * @copyright 2007 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @version $Revision$
 * @link http://www.sourcefabric.org
 */


/**
 * Class CampSite
 */
final class CampSite extends CampSystem
{
    /**
     * Class constructor
     */
    final public function __construct()
    {
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
        if (empty($p_configFile)) {
            $p_configFile = $GLOBALS['g_campsiteDir'].'/conf/configuration.php';
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
        global $g_errorList;

        $uri = self::GetURIInstance();
        $document = self::GetHTMLDocumentInstance();

        $context = CampTemplate::singleton()->context();
        // sets the appropiate template if site is not in mode online
        if ($this->getSetting('site.online') == 'N') {
            $templates_dir = CS_TEMPLATES_DIR;
            $template = CS_SYS_TEMPLATES_DIR.DIR_SEP.'_campsite_offline.tpl';
        } elseif (!$uri->publication->defined) {
            $templates_dir = CS_TEMPLATES_DIR;
            $template = CS_SYS_TEMPLATES_DIR.DIR_SEP.'_campsite_error.tpl';
            $error_message = 'The site alias \'' . $_SERVER['HTTP_HOST']
            . '\' was not assigned to a publication. Please create a publication and '
            . ' assign it the current site alias.';
        } elseif (is_array($g_errorList) && !empty($g_errorList)) {
            $templates_dir = CS_TEMPLATES_DIR;
            $template = CS_SYS_TEMPLATES_DIR.DIR_SEP.'_campsite_error.tpl';
            $error_message = 'At initialization: ' . $g_errorList[0]->getMessage();
        } else {
            $templates_dir = CS_TEMPLATES_DIR;
            $template = $uri->getTemplate();
            if (empty($template)) {
                $tplId = CampRequest::GetVar(CampRequest::TEMPLATE_ID);
                if (is_null($tplId)) {
                    $error_message = "Unable to select a template! "
                    ."Please make sure the following conditions are met:\n"
                    ."<li>there is at least one issue published and it had assigned "
                    ."valid templates for the front, section and article pages;</li>\n"
                    ."<li>a template was assigned for the URL error handling in "
                    ."the publication configuration screen.";
                } else {
                    $error_message = 'The template identified by the number ' . $tplId
                    .' does not exist.';
                }
                $template = CS_SYS_TEMPLATES_DIR.DIR_SEP.'_campsite_error.tpl';
                $templates_dir = CS_TEMPLATES_DIR;
            }
        }

        $params = array(
                        'context' => $context,
                        'template' => $template,
                        'templates_dir' => $templates_dir,
                        'error_message' => isset($error_message) ? $error_message : null
                        );
        $document->render($params);
    } // fn render


    /**
     * @param string $p_eventName
     */
    public function event($p_eventName)
    {
        $preview = CampTemplate::singleton()->context()->preview;
        switch ($p_eventName) {
        case 'beforeRender':
            return $preview ? CampRequest::GetVar('previewLang', null) : null;
        case 'afterRender':
            if ($preview) {
                print("\n<script LANGUAGE=\"JavaScript\">parent.e.document.open();\n"
                    ."parent.e.document.write(\"<html><head><title>Errors</title>"
                    ."</head><body bgcolor=white text=black>\\\n<pre>\\\n"
                    ."\\\n<b>Parse errors:</b>\\\n");

                foreach ($GLOBALS['g_errorList'] as $error) {
                    print("<p>".addslashes($error->getMessage())."</p>\\\n");
                }

                print("</pre></body></html>\\\n\");\nparent.e.document.close();\n</script>\n");
            }
            break;
        }
    } // fn event


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
    public static function GetURIInstance()
    {
        static $uriInstance = null;

        if (!is_null($uriInstance)) {
        	return clone($uriInstance);
        }

        $alias = new Alias($_SERVER['HTTP_HOST']);
        if ($alias->exists()) {
        	$publication = new Publication($alias->getPublicationId());
        	$urlType = $publication->getUrlTypeId();
        }

        // sets url type to default if necessary
        if (!isset($urlType)) {
        	$config = self::GetConfigInstance();
        	$urlType = $config->getSetting('campsite.url_default_type');
        }

        // instanciates the corresponding URI object
        switch ($urlType) {
        case 1:
            $uriInstance = new CampURITemplatePath();
            break;
        case 2:
            $uriInstance = new CampURIShortNames();
            break;
        }

        return $uriInstance;
    } // fn GetURI

    /**
     * Process the statistics for the request.
     *
     * @param bool $p_statsOnly
     *      Is this request just for statistics.
     * @return bool
     */
    public function processStats(&$p_statsOnly)
    {
        global $Campsite;

        $p_statsOnly = false;
        $output_html = " ";

        /*
            looking whether the request is of form used for statistics, i.e.
            http(s)://newscoop_domain/(newscoop_dir/)statistics/...
        */
        // website url without 'http(s)://'
        $path_prefix = substr($Campsite['WEBSITE_URL'], 8);
        // removing domain from website_url (i.e. up to the first '/')
        $path_domain = strpos($path_prefix, "/");
        if (FALSE !== $path_domain) {
            $path_prefix = substr($path_prefix, $path_domain + 1); // the path of website url without the leading '/'
        }
        else {
            $path_prefix = ""; // empty path if website url is just a domain
        }
        $path_prefix = "/" . $path_prefix; // the website url path, w/o domain
        if ((strlen($path_prefix) - 1) != strrpos($path_prefix, "/")) {$path_prefix .= "/";}
        // the path (as of request_uri) that is for the statistics part
        $stat_start = $path_prefix . "statistics";
        $stat_start_len = strlen($stat_start);
        // if request_uri starts with the statistics path, it is just for the statistics things
        if (substr($_SERVER['REQUEST_URI'], 0, $stat_start_len) == $stat_start) {
            $p_statsOnly = true;
        }
        // if not on statistics, just return and let run the standard newscoop processing
        if (!$p_statsOnly) {
            return true;
        }

        // taking the statistics specification part of the request uri
        $stat_info = "" . substr($_SERVER['REQUEST_URI'], $stat_start_len + 1);

        $stat_info_arr = array();
        foreach (explode("/", $stat_info) as $one_part) {
            $one_part = trim($one_part);
            $one_part_arr = explode("?", $one_part); // ignoring parameters used for request randomization
            $one_part = trim($one_part_arr[0]);
            if ("" != $one_part) {
                $stat_info_arr[] = $one_part;
            }
            if (1 < count($one_part_arr)) {break;}
        }
        //var_dump($stat_info_arr);

        $art_read_action = false;

        // for now, the only known action is to update statistics on article readering, i.e. for
        // uri path of form (/newscoop_path)/statistics/reader/article/article_number/language_code/?...
        if (4 <= count($stat_info_arr)) {
            if ("reader" == $stat_info_arr[0]) {
                if ("article" == $stat_info_arr[1]) {
                    $art_read_action =  true;
                }
            }
        }

        if (!$art_read_action) {
            return false;
        }

        // if the article was read by a user (incl. an anonymous one)
        if ($art_read_action) {
            $article_number = (int) $stat_info_arr[2];
            $language_code = $stat_info_arr[3];

            $written = $this->writeStats($article_number, $language_code);
            if (!$written) {return false;}
        } // end of the stats action on article reading

        // the output string for stats only requests; nothing for now
        echo $output_html;

        // whether the stats processing was correct
        // the return value not used actually anywhere now
        return true;
    } // fn processStats

    /**
     * Writes the statistics for the request.
     *
     * @param int $p_articleNumber
     *      number of article whose stats shall be updated
     * @param string $p_languageCode
     *      language of article whose stats shall be updated
     * @return bool
     */
    private function writeStats($p_articleNumber, $p_languageCode)
    {
        if ((!$p_articleNumber) || (!$p_languageCode)) {return false;}

        // taking the language id, if it exists
        $language_id = Language::GetLanguageIdByCode($p_languageCode);
        if (!$language_id) {return false;}

        // taking the article object, if it exists
        $art_obj = new Article($language_id, $p_articleNumber);
        //var_dump($art_obj);
        if ((!$art_obj) || (!$art_obj->exists())) {return false;}

        // no new stats for non-published articles
        if (!$art_obj->isPublished()) {return false;}

        // session used for 1) taking user id if article not public, 2) for stats writing
        // we require to have a session, since it should be already created on article reading
        $session_id = session_id();
        if (!$session_id) {return false;}
        $session = new Session($session_id);
        if (!$session->exists()) {return false;}

        /*
            a user can read (and thus update stats) just for articles with correct access rights to
        */
        // if a public article, can write stats
        $is_accessible = false;
        if ($art_obj->isPublic()) {
            $is_accessible = true;
        }
        //var_dump($is_accessible);

        // if not a public article, we have to have read access to it
        if (!$is_accessible) {
            $section_number = $art_obj->getSectionNumber();
            $sessionUserId = intval($session->getUserId());
            if ($sessionUserId)
            {
                $user = new User($sessionUserId);
                if ($user && $user->exists()) {
                    $is_accessible = (bool) ($user->defined && $user->subscription->is_valid
                    && $user->subscription->has_section($section_number));
                }
            }
        }

        // if the article not open for us, no stats on that (since reading not possible)
        //var_dump($is_accessible);
        if (!$is_accessible) {return false;}

        // the object_id is used for actual statistics
        $objId = $art_obj->m_data['object_id'];
        // if no object_id on the article, then no statistics
        if ($objId)
        {
            echo "<br>\ngoing to write stats for $objId<br>\n";
            //$fh = fopen("/tmp/asdf.txt", "a");
            //fwrite($fh, "\n" . $objId . "\n");
            //fclose($fh);

            // the stats writing itself
            SessionRequest::UpdateStats($session_id, $objId);
        }

    } // fn writeStats

} // class CampSite

?>