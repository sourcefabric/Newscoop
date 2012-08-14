<?php

//use Newscoop\ArticleDatetime as ArticleDatetimeHelper;
use Newscoop\ArticleDatetime as ArticleDatetime;

/*
use Newscoop,
    Doctrine\ORM\EntityRepository,
    Newscoop\ArticleDatetime as ArticleDatetimeHelper,
    Newscoop\Entity\Article,
    Newscoop\Entity\ArticleDatetime;
*/

/**
 * NewsImport manages the event importing.
 */
class NewsImport
{

    /**
     * Handler of cached id / url data of images.
     * @var mixed
     */
    private static $s_img_cache = null;

    /**
     * To omit a possible double run if it would happened.
     * @var bool
     */
    private static $s_already_run = false;

    /**
     * Makes necessary initialization for Newscoop
     *
    * @return void
    */
    private static function LoadInit()
    {
        // it looks that just the localizer path is missing in the include_path here
        $admin_path = $GLOBALS['g_campsiteDir'].DIRECTORY_SEPARATOR.'admin-files'.DIRECTORY_SEPARATOR.'localizer';
        set_include_path($admin_path . PATH_SEPARATOR . get_include_path());

    } // fn LoadInit

    /**
     * Checks whether a request for event import, calls that import if asked
     *
    * @param bool $p_importOnly
    * @return mixed
    */
    public static function ProcessImport(&$p_importOnly) {

        global $Campsite;

        $p_importOnly = false;
        $output_html = ' ';

        //looking whether the request is of form used for xml import, i.e.
        //http(s)://newscoop_domain/(newscoop_dir/)_xmlimport(/...)(?...)

        if (!isset($_SERVER['REQUEST_URI'])) {
            return false;
        }

        $path_request_parts = explode('?', $_SERVER['REQUEST_URI']);
        $path_request = strtolower($path_request_parts[0]);
        if (('' == $path_request) || ('/' != $path_request[strlen($path_request)-1])) {
            $path_request .= '/';
        }

        $campsite_subdir = substr($_SERVER['SCRIPT_NAME'], 0, strrpos($_SERVER['SCRIPT_NAME'], '/', -2));

        // the path prefix that should be considered when checking the xml import directory
        // it is an empty string for domain based installations
        $xmlimp_start = strtolower($campsite_subdir);
        if (('' == $xmlimp_start) || ('/' != $xmlimp_start[strlen($xmlimp_start)-1])) {
            $xmlimp_start .= '/';
        }

        // the path (as of request_uri) that is for the import part
        $xmlimp_start .= '_newsimport/';
        $xmlimp_start_len = strlen($xmlimp_start);
        // if request_uri starts with the import path, it is just for the import things
        if (substr($path_request, 0, $xmlimp_start_len) == $xmlimp_start) {
            $p_importOnly = true;
        }
        // if not on import, just return and let run the standard newscoop processing
        if (!$p_importOnly) {
            return true;
        }

        if (self::$s_already_run) {
            return false;
        }
        self::$s_already_run = true;

        if (!array_key_exists('newsauth', $_GET)) {
            return false;
        }

        $news_auth = $_GET['newsauth'];
        $news_feed = null;

        if (array_key_exists('newsfeed', $_GET)) {
            $news_feed = $_GET['newsfeed'];
        }

        require_once($GLOBALS['g_campsiteDir'].DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'SystemPref.php');

        $incl_dir = dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR;
        require($incl_dir . 'default_access.php');
        // taking the default access token from config file
        $news_auth_sys_pref = $newsimport_default_access;
        // taking access token from sysprefs if any changed there
        $news_auth_sys_pref_changed = SystemPref::Get('NewsImportCommandToken');
        if (!empty($news_auth_sys_pref_changed)) {
            $news_auth_sys_pref = $news_auth_sys_pref_changed;
        }
        $news_auth_sys_pref = md5($news_auth_sys_pref);

        if ((!empty($news_auth_sys_pref)) && ($news_auth != $news_auth_sys_pref)) {
            return false;
        }
        self::LoadInit();

        require_once($GLOBALS['g_campsiteDir'].DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'Topic.php');
        require_once($GLOBALS['g_campsiteDir'].DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'TopicName.php');
        require_once($GLOBALS['g_campsiteDir'].DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'GeoMap.php');
        require_once($GLOBALS['g_campsiteDir'].DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'Article.php');
        require_once($GLOBALS['g_campsiteDir'].DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'Issue.php');
        require_once($GLOBALS['g_campsiteDir'].DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'Log.php');

        require_once($GLOBALS['g_campsiteDir'].DIRECTORY_SEPARATOR.'library'.DIRECTORY_SEPARATOR.'Newscoop'.DIRECTORY_SEPARATOR.'ArticleDatetime.php');

        $conf_dir = $GLOBALS['g_campsiteDir'].DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'newsimport'.DIRECTORY_SEPARATOR.'include';
        $class_dir = $GLOBALS['g_campsiteDir'].DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'newsimport'.DIRECTORY_SEPARATOR.'classes';
        require_once($conf_dir.DIR_SEP.'default_topics.php');
        require_once($conf_dir.DIR_SEP.'default_limits.php');

        $feed_conf_path = $GLOBALS['g_campsiteDir'].DIRECTORY_SEPARATOR.'conf'.DIRECTORY_SEPARATOR.'newsimport'.DIRECTORY_SEPARATOR.'news_feeds_conf.php';
        if (!is_file($feed_conf_path)) {
            $feed_conf_path = $conf_dir.DIR_SEP.'news_feeds_conf_inst.php';
        }
        require_once($feed_conf_path);

        require_once($class_dir.DIR_SEP.'RegionInfo.php');
        require_once($class_dir.DIR_SEP.'EventImage.php');

        // take the category topics, as array by [language][category] of [name,id]
        $cat_topics = self::ReadEventTopics($newsimport_default_cat_names);

        $events_limit = 0;
        $events_skip = 0;
        $events_prune = false;
        if (array_key_exists('newslimit', $_GET)) {
            $events_limit = 0 + $_GET['newslimit'];
            $events_limit = max(0, $events_limit);
        }
        if (array_key_exists('newsoffset', $_GET)) {
            $events_skip = 0 + $_GET['newsoffset'];
            $events_skip = max(0, $events_skip);
        }
        if (array_key_exists('newsprune', $_GET)) {
            $events_prune_tmp = 0 + $_GET['newsprune'];
            $events_prune_tmp = max(0, $events_prune_tmp);
            if ($events_prune_tmp) {
                $events_prune = true;
            }
        }

        $params_other = array(
            'skip' => $events_skip,
            'limit' => $events_limit,
            'pruning' => $events_prune,
        );

        set_time_limit(0);
        ob_end_flush();
        flush();

        $msg = self::LoadEventData($event_data_sources, $news_feed, $cat_topics, $event_data_limits, $event_data_cancel, $params_other);
        if (!empty($msg)) {
            echo $msg;
        }

        return;
    } // fn ProcessImport

	/**
     * Checks whether a request for event import, calls that import if asked
     *
	 * @param bool $p_importOnly
	 * @return mixed
	 */
    public static function ProcessImportCli($p_loadSpec) {
        if (empty($p_loadSpec)) {
            $p_loadSpec = array();
        }
        self::LoadInit();

        require_once($GLOBALS['g_campsiteDir'].DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'Topic.php');
        require_once($GLOBALS['g_campsiteDir'].DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'TopicName.php');
        require_once($GLOBALS['g_campsiteDir'].DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'GeoMap.php');
        require_once($GLOBALS['g_campsiteDir'].DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'Article.php');
        require_once($GLOBALS['g_campsiteDir'].DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'Issue.php');
        require_once($GLOBALS['g_campsiteDir'].DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'Log.php');

        require_once($GLOBALS['g_campsiteDir'].DIRECTORY_SEPARATOR.'library'.DIRECTORY_SEPARATOR.'Newscoop'.DIRECTORY_SEPARATOR.'ArticleDatetime.php');

        $conf_dir = $GLOBALS['g_campsiteDir'].DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'newsimport'.DIRECTORY_SEPARATOR.'include';
        $class_dir = $GLOBALS['g_campsiteDir'].DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'newsimport'.DIRECTORY_SEPARATOR.'classes';
        require($conf_dir.DIR_SEP.'default_topics.php');
        require($conf_dir.DIR_SEP.'default_limits.php');

        $feed_conf_path = $GLOBALS['g_campsiteDir'].DIRECTORY_SEPARATOR.'conf'.DIRECTORY_SEPARATOR.'newsimport'.DIRECTORY_SEPARATOR.'news_feeds_conf.php';
        if (!is_file($feed_conf_path)) {
            $feed_conf_path = $conf_dir.DIR_SEP.'news_feeds_conf_inst.php';
        }
        require($feed_conf_path);

        require_once($class_dir.DIR_SEP.'RegionInfo.php');
        require_once($class_dir.DIR_SEP.'EventImage.php');

        // take the category topics, as array by [language][category] of [name,id]
        $cat_topics = self::ReadEventTopics($newsimport_default_cat_names);

        $news_feed = null;
        if (array_key_exists('newsfeed', $p_loadSpec)) {
            $news_feed = $p_loadSpec['newsfeed'];
        }

        $events_limit = 0;
        $events_skip = 0;
        $events_prune = false;
        if (array_key_exists('newslimit', $p_loadSpec)) {
            $events_limit = 0 + $p_loadSpec['newslimit'];
            $events_limit = max(0, $events_limit);
        }
        if (array_key_exists('newsoffset', $p_loadSpec)) {
            $events_skip = 0 + $p_loadSpec['newsoffset'];
            $events_skip = max(0, $events_skip);
        }
        if (array_key_exists('newsprune', $p_loadSpec)) {
            $events_prune_tmp = 0 + $p_loadSpec['newsprune'];
            $events_prune_tmp = max(0, $events_prune_tmp);
            if ($events_prune_tmp) {
                $events_prune = true;
            }
        }

        $params_other = array(
            'skip' => $events_skip,
            'limit' => $events_limit,
            'pruning' => $events_prune,
        );

        set_time_limit(0);
        //ob_end_flush();
        //flush();
        $msg = self::LoadEventData($event_data_sources, $news_feed, $cat_topics, $event_data_limits, $event_data_cancel, $params_other);
        return $msg;
    } // fn ProcessImportCli

	/**
     * Takes topics for event categories
     *
	 * @param array $p_defaultTopicNames
	 * @return array
	 */
    public static function ReadEventTopics($p_defaultTopicNames) {
        $event_spec_topics = array();

        foreach ($p_defaultTopicNames as $one_set_name => $one_set_topics) {
            $cur_spec_topics = array();

            foreach ($one_set_topics as $cat_key => $event_spec_names) {
                foreach ($event_spec_names as $cat_lan_id => $cat_name) {
                    $cat_key_sys_pref = 'EventCat' . $cat_lan_id . ucfirst($cat_name);
                    $cat_name_sys_pref = SystemPref::Get($cat_key_sys_pref);
                    if (!empty($cat_name_sys_pref)) {
                        $cat_name = $cat_name_sys_pref;
                    }
                    if (!array_key_exists($cat_lan_id, $cur_spec_topics)) {
                        $cur_spec_topics[$cat_lan_id] = array();
                    }
                    $topic_name_obj = new TopicName($cat_name, $cat_lan_id);
                    $cur_spec_topics[$cat_lan_id][$cat_key] = array('name' => $cat_name, 'id' => $topic_name_obj->getTopicId());
                }
            }

            $event_spec_topics[$one_set_name] = $cur_spec_topics;
        }

        return $event_spec_topics;
    } // fn ReadEventTopics

	/**
     * Takes categories specifications for events
     *
	 * @param array $p_source
     * @param array $p_catTopics
	 * @return array
	 */
    public static function ReadEventCategories($p_source, $p_catTopics) {
        if (!is_array($p_source)) {
            return false;
        }
        if (!array_key_exists('language_id', $p_source)) {
            return false;
        }
        if (!array_key_exists('categories', $p_source)) {
            return false;
        }
        $language_id = $p_source['language_id'];
        $categories_info = $p_source['categories'];
        if (!is_array($categories_info)) {
            return null;
        }

        if (!array_key_exists($language_id, $p_catTopics)) {
            return null;
        }
        $lang_topics = $p_catTopics[$language_id];

        $topics = array();
        foreach ($categories_info as $one_spec_key => $one_spec_value) {
            if (!is_string($one_spec_key)) {
                continue;
            }

            if ('*' == $one_spec_value) {
                if (array_key_exists($one_spec_key, $lang_topics)) {
                    $topics[] = array('key' => $one_spec_key, 'fixed' => $lang_topics[$one_spec_key]);
                }
            }
            if ('x' == $one_spec_value) {
                if (array_key_exists($one_spec_key, $lang_topics)) {
                    $topics[] = array('key' => $one_spec_key, 'other' => $lang_topics[$one_spec_key]);
                }
            }
            if (is_array($one_spec_value)) {
                if (array_key_exists($one_spec_key, $lang_topics)) {
                    $topics[] = array('key' => $one_spec_key, 'match_xml' => $one_spec_value, 'match_topic' => $lang_topics[$one_spec_key]);
                }
            }
        }

        return $topics;
    } // fn ReadEventCategories

	/**
     * Puts parsed event data into (new) articles
     *
     * @param array $p_events
	 * @param array $p_source
	 * @return void
	 */
    public static function StoreEventData($p_events, $p_source) {
/*
    for multi-date based events:
    a) multi-dates ... for frontend usage
        * add/update dates from the given data, set them as active
        * set events which are marked as canceled, to be passive
        * set possible left old dates as passive
        //* remove those dates that are too old
    b) check dates ... for the pruning phase
        * set the (checked) date as the current date
        //* if already no dates left, set (checked) date as an old (enough) one
*/
        if (empty($p_events)) {
            return;
        }

        global $g_user;

        $images_to_check = array();

        $today_date_str = date('Y-m-d');

        $scr_type = 'screening';
        $evt_type = 'event';

        if ((!isset($g_user)) || (empty($g_user))) {
            $user_id = $p_source['admin_user_id'];
            $g_user = new User($user_id);
        }

        $art_type = $p_source['article_type'];
        $art_publication = $p_source['publication_id'];
        $art_issue = $p_source['issue_number'];
        $art_section = $p_source['section_number'];
        $art_lang = $p_source['language_id'];

        $status_public = $p_source['status']['public'];
        $status_comments = $p_source['status']['comments'];
        $status_publish = $p_source['status']['publish'];
        $status_publish_by_event_date = $p_source['status']['publish_date_by_event_date'];

        $images_local = $p_source['images_local'];

        $geo_sys_def = $p_source['geo'];

        $art_author = new Author($p_source['provider_name']);
        if (!$art_author->exists()) {
            $art_author->create();
        }

        $movie_keys_text = array(
            'imdb', 'suisa', 'country', 'lead', 'link',
            'director', 'producer', 'cast', 'script', 'camera',
            'cutter', 'sound', 'score', 'production_design',
            'costume_design', 'visual_effects',
            'distributor', 'distributor_link', 'distributor_copyright',
        );
        $movie_keys_numeric = array(
            'flag', 'year', 'duration', 'oscars',
        );
        $movie_keys_date = array(
            'release_ch_d', 'release_ch_f', 'release_ch_i',
        );

        foreach ($p_events as $one_event) {
            $article = null;
            $article_new = false;

            //First, try to load event (possibly created by former imports), and if there, remove it - will be put in with the current, possibly more correct info.
            $p_count = 0;
            $event_art_list = Article::GetList(array(
                new ComparisonOperation('idlanguage', new Operator('is', 'sql'), $art_lang),
                //new ComparisonOperation('IdPublication', new Operator('is', 'sql'), $art_publication),
                //new ComparisonOperation('NrIssue', new Operator('is', 'sql'), $art_issue),
                //new ComparisonOperation('NrSection', new Operator('is', 'sql'), $art_section),
                new ComparisonOperation('Type', new Operator('is', 'sql'), $art_type),
                new ComparisonOperation($art_type . '.event_id', new Operator('is', 'sql'), $one_event['event_id']),
                new ComparisonOperation($art_type . '.provider_id', new Operator('is', 'sql'), $one_event['provider_id']),
            ), null, null, 0, $p_count, true);

            $event_data_test = null;
            if (is_array($event_art_list) && (0 < count($event_art_list))) {
                foreach ($event_art_list as $event_art_test) {
                    $event_data_test = $event_art_test->getArticleData();
                    if (($event_data_test->getFieldValue('event_id')) == $one_event['event_id']) {
                        $article = $event_art_test;
                        break;
                    }
                }
            }

            if ($article) {
                if ($article->getIssueNumber() != $art_issue) {
                    $article->setIssueNumber($art_issue);
                }
                if ($article->getSectionNumber() != $art_section) {
                    $article->setSectionNumber($art_section);
                }
                if ($article->getPublicationId() != $art_publication) {
                    $article->setPublicationId($art_publication);
                }
            }

            if ($article && $event_data_test) {
                if ($event_data_test->getFieldValue('edited')) {
                    continue;
                }
            }

            $uses_multidates = false;
            if (isset($one_event['uses_multidates']) && $one_event['uses_multidates']) {
                $uses_multidates = true;
            }

            // $one_event['headline'] = str_replace(array('\\', '&#92;'), array('\'', '&#39;'), $one_event['headline']); // this was already fixed upstream

            // no date part for movie screenings (all together), and may be the same way for events too lately
            $art_name_date_part = '';
            if ($scr_type != $art_type) {
                if (!$uses_multidates) {
                    $art_name_date_part = ' - ' . $one_event['date'];
                }
            }
            $art_name = $one_event['headline'] . $art_name_date_part . ' (' . $one_event['event_id'] . ')';
            $art_name = NewsImportEnv::entities2Utf8($art_name, array());

            if (!$article) {
                $article = new Article($art_lang);
                $article->create($art_type, $art_name, $art_publication, $art_issue, $art_section);
                $article_new = true;
            }
            else {
                $article->setTitle($art_name);
            }

            $art_number = $article->getArticleNumber();

            if (isset($one_event['keywords']) && (null !== $one_event['keywords'])) {
                $cur_keywords = NewsImportEnv::entities2Utf8($one_event['keywords'], array());
                if (is_array($cur_keywords)) {
                    $cur_keywords = implode(',', $cur_keywords);
                }
                $article->setKeywords($cur_keywords);
            }

            $article_data = $article->getArticleData();
            $old_description = $article_data->getProperty('Fdescription');
            $new_description = $one_event['description'];

            $article_data->setProperty('Fprovider_id', $one_event['provider_id']);
            $article_data->setProperty('Fevent_id', $one_event['event_id']);
            $article_data->setProperty('Ftour_id', $one_event['tour_id']);
            $article_data->setProperty('Flocation_id', $one_event['location_id']);

            if ($evt_type == $art_type) {
                if (isset($one_event['event_location_name'])) {
                    $article_data->setProperty('Fevent_location_name', $one_event['event_location_name']);
                }
            }

            if ($scr_type == $art_type) {
                // date_time_data => movie_screening
                //$cur_screening_dates = array();
                $md_removals = array();

                $em = Zend_Registry::get('container')->getService('em');
                $repository = $em->getRepository('Newscoop\Entity\ArticleDatetime');
                foreach ($repository->findBy(array('articleId' => $article->getArticleNumber(), 'fieldName' => 'movie_screening')) as $one_screening_entry) {
                    //$cur_screening_dates[] = date_format($one_screening_entry->getStartDate(), 'Y-m-d');
                    $cur_screening_date = date_format($one_screening_entry->getStartDate(), 'Y-m-d');
                    //$one_scr_start_date = $em->getStartDate();
                    //if ($one_screening_date >= $today_date_str) {
                        //$em->remove($one_screening_entry);
                        $md_removals[] = $one_screening_entry->getId();
                    //}
                }

                foreach ($md_removals as $one_md) {
                    $repository->deleteById($one_md);
                }

                foreach ($one_event['date_time_data'] as $one_scr_date => $one_date_screenings) {
                    if (empty($one_date_screenings)) {
                        continue;
                    }
                    foreach ($one_date_screenings as $one_screening) {
                        if (!isset($one_screening['time'])) {
                            continue;
                        }
                        $one_scr_time = $one_screening['time'];
                        $one_scr_lang = (isset($one_screening['lang'])) ? $one_screening['lang'] : '';
                        $one_scr_flag = (isset($one_screening['flag'])) ? $one_screening['flag'] : '';
                        $one_scr_comment = 'lang:' . $one_scr_lang . "\n" . 'flag:' . $one_scr_flag;

                        $scr_datetime = new ArticleDatetime(
                            array(
                                'start_date' => $one_scr_date,
                                'end_date' => $one_scr_date,
                                'start_time' => $one_scr_time,
                                'recurring' => false,
                            )
                        );
                        $repository->add($scr_datetime, $article->getArticleNumber(), 'movie_screening', false, false, array('eventComment' => $one_scr_comment));
                    }
                }

                $f_movie_key = (isset($one_event['movie_key']) && (!empty($one_event['movie_key']))) ? $one_event['movie_key'] : '';
                $article_data->setProperty('Fmovie_key', $f_movie_key);
                $f_movie_cinema_key = (isset($one_event['movie_cinema_key']) && (!empty($one_event['movie_cinema_key']))) ? $one_event['movie_cinema_key'] : '';
                $article_data->setProperty('Fmovie_cinema_key', $f_movie_cinema_key);

                $f_rating_wv = (isset($one_event['rating_wv']) && (!empty($one_event['rating_wv']))) ? $one_event['rating_wv'] : 0;
                $article_data->setProperty('Fmovie_rating_wv', 0 + $f_rating_wv);

                $f_movie_trailer = (isset($one_event['movie_trailer']) && (!empty($one_event['movie_trailer']))) ? $one_event['movie_trailer'] : '';
                $article_data->setProperty('Fmovie_trailer', $f_movie_trailer);

                $f_movie_trailer_vimeo = (isset($one_event['movie_trailer_vimeo']) && (!empty($one_event['movie_trailer_vimeo']))) ? $one_event['movie_trailer_vimeo'] : '';
                $article_data->setProperty('Fmovie_trailer_vimeo', $f_movie_trailer_vimeo);

                $f_movie_trailer_width = 0;
                $f_movie_trailer_height = 0;
                $f_movie_trailer_codec = '';
                if (isset($one_event['movie_trailer_info']) && (!empty($one_event['movie_trailer_info']))) {
                    $one_trailer_info = $one_event['movie_trailer_info'];
                    if (isset($one_trailer_info['width']) && (!empty($one_trailer_info['width']))) {
                        $f_movie_trailer_width = 0 + $one_trailer_info['width'];
                    }
                    if (isset($one_trailer_info['height']) && (!empty($one_trailer_info['height']))) {
                        $f_movie_trailer_height = 0 + $one_trailer_info['height'];
                    }
                    if (isset($one_trailer_info['codec']) && (!empty($one_trailer_info['codec']))) {
                        $f_movie_trailer_codec = $one_trailer_info['codec'];
                    }
                    if (empty($f_movie_trailer_codec)) {
                        if (isset($one_trailer_info['format']) && (!empty($one_trailer_info['format']))) {
                            $f_movie_trailer_codec = $one_trailer_info['format'];
                        }
                    }
                }
                $article_data->setProperty('Fmovie_trailer_width', $f_movie_trailer_width);
                $article_data->setProperty('Fmovie_trailer_height', $f_movie_trailer_height);
                $article_data->setProperty('Fmovie_trailer_codec', $f_movie_trailer_codec);

                $f_movie_info = (isset($one_event['movie_info']) && (!empty($one_event['movie_info']))) ? $one_event['movie_info'] : '';
                if (empty($f_movie_info)) {
                    $f_movie_info = array();
                }

                foreach ($movie_keys_text as $one_movie_info_key) {
                    $f_movie_val = (isset($f_movie_info[$one_movie_info_key]) && (!empty($f_movie_info[$one_movie_info_key]))) ? $f_movie_info[$one_movie_info_key] : '';
                    $article_data->setProperty('Fmovie_' . $one_movie_info_key, $f_movie_val);
                }

                $ev_movie_trailers = implode("\n<br />\n", $one_event['movie_trailers']);
                $article_data->setProperty('Fmovie_trailers', $ev_movie_trailers);

                foreach ($movie_keys_numeric as $one_movie_info_key) {
                    $f_movie_val = (isset($f_movie_info[$one_movie_info_key]) && (!empty($f_movie_info[$one_movie_info_key]))) ? $f_movie_info[$one_movie_info_key] : 0;
                    $article_data->setProperty('Fmovie_' . $one_movie_info_key, $f_movie_val);
                }

                foreach ($movie_keys_date as $one_movie_info_key) {
                    $f_movie_val = (isset($f_movie_info[$one_movie_info_key]) && (!empty($f_movie_info[$one_movie_info_key]))) ? $f_movie_info[$one_movie_info_key] : '0000-00-01';
                    $article_data->setProperty('Fmovie_' . $one_movie_info_key, $f_movie_val);
                }

            }

            $article_data->setProperty('Fheadline', NewsImportEnv::entities2Utf8($one_event['headline'], array()));
            $article_data->setProperty('Forganizer', NewsImportEnv::entities2Utf8($one_event['organizer'], array()));

            $article_data->setProperty('Fcountry', NewsImportEnv::entities2Utf8($one_event['country'], array()));
            $article_data->setProperty('Fzipcode', NewsImportEnv::entities2Utf8($one_event['zipcode'], array()));
            $article_data->setProperty('Ftown', NewsImportEnv::entities2Utf8($one_event['town'], array()));
            $article_data->setProperty('Fstreet', NewsImportEnv::entities2Utf8($one_event['street'], array()));

            if (!$uses_multidates) {
                $article_data->setProperty('Fdate', $one_event['date']);
                $article_data->setProperty('Ftime', $one_event['time']);
                $article_data->setProperty('Fprices', $one_event['prices']);
            }
            if ($uses_multidates) {
                $current_date = date('Y-m-d');
                //$limit_date = date('Y-m-d', (time() - ($_lim_days * 86400)));

                // remove old multidates
                $em = Zend_Registry::get('container')->getService('em');
                $repository = $em->getRepository('Newscoop\Entity\ArticleDatetime');
                //$repository = Zend_Registry::get('doctrine')->getEntityManager()->getRepository('Newscoop\Entity\ArticleDatetime');
                //$repository->deleteByArticle($article->m_data['Number']);

                $md_removals = array();

                $cur_voided_dates = array();
                $cur_postponed_dates = array();
                $cur_withdrawn_dates = array();

                //$cur_voided_dates = $article_data->getFieldValue('voided');
                foreach ($repository->findBy(array('articleId' => $article->getArticleNumber(), 'fieldName' => 'voided')) as $one_void_entry) {
                    //$cur_voided_dates[] = date_format(date_create($one_void_entry->getStartDate()), 'Y-m-d');
                    $cur_voided_dates[] = date_format($one_void_entry->getStartDate(), 'Y-m-d');
                    //$em->remove($one_void_entry);
                    $md_removals[] = $one_void_entry->getId();
                }
                foreach ($repository->findBy(array('articleId' => $article->getArticleNumber(), 'fieldName' => 'postponed')) as $one_postpone_entry) {
                    $cur_postponed_dates[] = date_format($one_postpone_entry->getStartDate(), 'Y-m-d');
                    //$em->remove($one_postpone_entry);
                    $md_removals[] = $one_postpone_entry->getId();
                }
                foreach ($repository->findBy(array('articleId' => $article->getArticleNumber(), 'fieldName' => 'withdrawn')) as $one_withdraw_entry) {
                    $cur_withdrawn_dates[] = date_format($one_withdraw_entry->getStartDate(), 'Y-m-d');
                    //$em->remove($one_withdraw_entry);
                    $md_removals[] = $one_withdraw_entry->getId();
                }

                foreach ($md_removals as $one_md) {
                    $repository->deleteById($one_md);
                }

                // take all previously set dates
                $all_event_dates = array();
                foreach ($repository->findBy(array('articleId' => $article->getArticleNumber(), 'fieldName' => 'schedule')) as $one_date_entry) {
                    //$old_date_str = date_format(date_create($one_date_entry->getStartDate()), 'Y-m-d');
                    $old_date_str = date_format($one_date_entry->getStartDate(), 'Y-m-d');

/*
                    $old_voided = true;
                    if ($old_date_str < $current_date) {
                        //$old_voided = false; // should we set passed events as non-voided?
                    }
*/

                    $old_voided = false;
                    $old_postponed = false;
                    $old_withdrawn = false;
                    //if (false !== stristr($cur_voided_dates, $old_date_str)) {
                    if (in_array($old_date_str, $cur_voided_dates)) {
                        $old_voided = true;
                    }
                    if (in_array($old_date_str, $cur_withdrawn_dates)) {
                        $old_withdrawn = true;
                    }
                    // old info on future is set as canceled here; if we've just got that info at new data, it is overwritten then
                    if ($old_date_str >= $today_date_str) {
                        //$old_voided = true;
                        $old_withdrawn = true;
                    }

                    if (in_array($old_date_str, $cur_postponed_dates)) {
                        $old_postponed = true;
                    }

                    $use_description = $one_date_entry->getEventComment();
                    if (empty($use_description)) {
                        $use_description = $old_description;
                    }
                    $old_start_time = null;
                    $old_start_time_obj = $one_date_entry->getStartTime();
                    if ($old_start_time_obj) {
                        $old_start_time = date_format($old_start_time_obj, 'H.i');
                    }
                    $old_info = array(
                        'row_id' => $one_date_entry->getId(),
                        'voided' => $old_voided,
                        'postponed' => $old_postponed,
                        'withdrawn' => $old_withdrawn,
                        'prices' => '',
                        'date' => $old_date_str,
                        'time' => $old_start_time,
                        'about' => $use_description,
                    );
                    $all_event_dates[$old_date_str] = $old_info;
                }
/*
                try {
                    $em->flush();
                }
                catch (Exception $exc) {
                }
*/
                // set new multidates
                $new_event_dates = array();
                if (isset($one_event['multidates']) && $one_event['multidates']) {
                    $new_event_dates = $one_event['multidates'];
                }
                $prices_info = array();
                $prices_line_sep = "\n<br />\n";
                $multi_time_info = array();
                $multi_time_line_sep = "\n<br />\n";
                $voided_info = array();
                $postponed_info = array();
                $withdrawn_info = array();
                //$voided_line_sep = "\n<br />\n";

                foreach ($new_event_dates as $one_date) {
                    $new_voided = false;
                    if ($one_date['canceled']) {
                        $new_voided = true;
                    }
                    $new_postponed = false;
                    if ($one_date['postponed']) {
                        $new_postponed = true;
                    }

                    $new_info = array(
                        'row_id' => 0,
                        'voided' => $new_voided,
                        'postponed' => $new_postponed,
                        'withdrawn' => false,
                        'prices' => $one_date['prices'],
                        'date' => $one_date['date'],
                        'time' => $one_date['time'],
                        'about' => ((isset($one_date['about'])) ? $one_date['about'] : ''),
                    );
                    if (array_key_exists($one_date['date'], $all_event_dates)) {
                        $new_info['row_id'] = $all_event_dates[$one_date['date']]['row_id'];
                    }

                    $all_event_dates[$one_date['date']] = $new_info;
                }

                $md_removals_wd = array();
                $newest_date = '0000-00-00';
                //foreach ($event_dates as $one_date) {
                foreach ($all_event_dates as $one_date) {
                    //if ($one_date['canceled']) {
                    //    continue;
                    //}
                    //if ($limit_date > $one_date['date']) {
                    //    continue;
                    //}

                    if ($newest_date < $one_date['date']) {
                        $newest_date = $one_date['date'];
                    }

                    //$use_datetime = new ArticleDatetimeHelper();
                    $use_datetime = new ArticleDatetime(
                        array(
                            'start_date' => $one_date['date'],
                            'end_date' => $one_date['date'],
                            'start_time' => $one_date['time'],
                            'recurring' => false,
                            //'comment' => $one_date['about'],
                        )
                    );
                    $other_info = array();
                    if ($one_date['about'] != $new_description) {
                        $other_info['eventComment'] = $one_date['about'];
                    }

                    if ($one_date['withdrawn']) {
                        if (!empty($one_date['row_id'])) {
                            $md_removals_wd[] = $one_date['row_id'];
                        }
                        $withdrawn_info[] = $one_date['date'];
                    }
                    else {

                        if (empty($one_date['row_id'])) {
                            $repository->add($use_datetime, $article->getArticleNumber(), 'schedule', false, false, $other_info);
                        }
                        else {
                            $repository->update($one_date['row_id'], $use_datetime, $article->getArticleNumber(), 'schedule', false, $other_info);
                        }
    
                        if (!empty($one_date['prices'])) {
                            $prices_info[] = $one_date['date'];
                            $prices_info[] = $one_date['prices'];
                        }
    
                        if (!empty($one_date['time'])) {
                            $multi_time_info[] = $one_date['date'];
                            $multi_time_info[] = $one_date['time'];
                        }
    
                        if ($one_date['voided']) {
                            $voided_info[] = $one_date['date'];
                        }
                        if ($one_date['postponed']) {
                            $postponed_info[] = $one_date['date'];
                        }

                    }
                }

                foreach ($md_removals_wd as $one_md_wd) {
                    $repository->deleteById($one_md_wd);
                }

                //$article_data->setProperty('Fdate', $newest_date);
                $article_data->setProperty('Fdate', $current_date);

                $prices_info_str = implode($prices_line_sep, $prices_info);
                $article_data->setProperty('Fprices', $prices_info_str);

                $multi_time_info_str = implode($multi_time_line_sep, $multi_time_info);
                $article_data->setProperty('Fmulti_time', $multi_time_info_str);

                //$voided_info_str = implode($voided_line_sep, $voided_info);
                //$article_data->setProperty('Fvoided', $voided_info_str);
                foreach ($voided_info as $one_voided_date) {
                    $void_datetime = new ArticleDatetime(
                        array(
                            'start_date' => $one_voided_date,
                            'end_date' => $one_voided_date,
                            'recurring' => false,
                        )
                    );
                    $repository->add($void_datetime, $article->getArticleNumber(), 'voided', false, false);
                }
                foreach ($postponed_info as $one_postponed_date) {
                    $postpone_datetime = new ArticleDatetime(
                        array(
                            'start_date' => $one_postponed_date,
                            'end_date' => $one_postponed_date,
                            'recurring' => false,
                        )
                    );
                    $repository->add($postpone_datetime, $article->getArticleNumber(), 'postponed', false, false);
                }
                foreach ($withdrawn_info as $one_withdrawn_date) {
                    $withdraw_datetime = new ArticleDatetime(
                        array(
                            'start_date' => $one_withdrawn_date,
                            'end_date' => $one_withdrawn_date,
                            'recurring' => false,
                        )
                    );
                    $repository->add($withdraw_datetime, $article->getArticleNumber(), 'withdrawn', false, false);
                }
            }

            $article_data->setProperty('Fdate_time_text', $one_event['date_time_text']);

            $article_data->setProperty('Fweb', $one_event['web']);
            $article_data->setProperty('Femail', $one_event['email']);
            $article_data->setProperty('Fphone', $one_event['phone']);

            $article_data->setProperty('Fdescription', $one_event['description']);
            $ev_other_info = implode("\n<br />\n", $one_event['other']);
            $article_data->setProperty('Fother', $ev_other_info);

            $article_data->setProperty('Fgenre', $one_event['genre']);
            $article_data->setProperty('Flanguages', $one_event['languages']);

            $article_data->setProperty('Fminimal_age', $one_event['minimal_age']);
            $article_data->setProperty('Fminimal_age_category', (isset($one_event['minimal_age_category'])) ? $one_event['minimal_age_category'] : '');

            if (!$uses_multidates) {
                $article_data->setProperty('Fcanceled', ($one_event['canceled'] ? 'on' : 'off'));
            }
            $article_data->setProperty('Frated', ($one_event['rated'] ? 'on' : 'off'));
            $article_data->setProperty('Fedited', 'off');

            // set topics

            $old_topics = ArticleTopic::GetArticleTopics($art_number);
            foreach ($old_topics as $one_topic) {
                ArticleTopic::RemoveTopicFromArticle($one_topic->getTopicId(), $art_number);
            }

            $art_topics = null;
            if (array_key_exists('topics', $one_event)) {
                $art_topics = $one_event['topics'];
            }

            if (is_array($art_topics)) {
                foreach ($art_topics as $topic_info) {
                    $topic_id = null;
                    if (is_array($topic_info) && array_key_exists('id', $topic_info)) {
                        $topic_id = $topic_info['id'];
                    }
                    if (empty($topic_id)) {
                        continue;
                    }
                    ArticleTopic::AddTopicToArticle($topic_id, $art_number);
                }
            }

            // set geo

            if (!empty($one_event['geo'])) {
                $ev_map_info = array();
                $ev_map_info['cen_lon'] = 0 + $one_event['geo']['longitude'];
                $ev_map_info['cen_lat'] = 0 + $one_event['geo']['latitude'];
                $ev_map_info['zoom'] = 0 + $geo_sys_def['map_zoom'];
                $ev_map_info['provider'] = '' . $geo_sys_def['map_provider'];
                $ev_map_info['width'] = 0 + $geo_sys_def['map_width'];
                $ev_map_info['height'] = 0 + $geo_sys_def['map_height'];
                $ev_map_info['name'] = $one_event['headline'];

                $ev_map_id = Geo_Map::ReadMapId($art_number);
                if (empty($ev_map_id)) {
                    $ev_map_id = 0;
                }
                else {
                    $ev_map_obj = new Geo_Map($ev_map_id);
                    $ev_map_point_ids = array();
                    foreach ($ev_map_obj->getLocations() as $one_map_point) {
                        $ev_map_point_ids[] = array('location_id' => $one_map_point->getId());
                    }
                    if (!empty($ev_map_point_ids)) {
                        Geo_Map::RemovePoints($ev_map_id, $ev_map_point_ids);
                    }
                }

                Geo_Map::UpdateMap($ev_map_id, $art_number, $ev_map_info);
                //$ev_poi_desc = mb_substr($one_event['description'], 0, (0 + $geo_sys_def['poi_desc_len']));
                //if ($ev_poi_desc != $one_event['description']) {
                //    $ev_poi_desc .= ' ...';
                //}
                $ev_poi_desc = $one_event['street'] . ', ' . $one_event['zipcode'] . ' ' . $one_event['town'];
                $ev_poi_desc .= "\n";
                $ev_poi_desc .= $one_event['organizer'];

                $ev_points = array(
                    array(
                        'index' => 0,
                        'longitude' => 0 + $one_event['geo']['longitude'],
                        'latitude' => 0 + $one_event['geo']['latitude'],
                        'style' => '' . $geo_sys_def['poi_marker_name'],
                        'display' => 1,
                        'name' => $one_event['headline'],
                        'link' => '',
                        'perex' => '',
                        'content_type' => 1,
                        'content' => '',
                        'text' => $ev_poi_desc,
                        'image_src' => '',
                        'video_id' => '',
                    ),
                );
                $poi_indices = array();
                Geo_Map::InsertPoints($ev_map_id, $art_lang, $art_number, $ev_points, $poi_indices);

            }

            // preparing for setting the images
            $images_to_check[] = array(
                'article_number' => $art_number,
                'images' => $one_event['images'],
                'provider_id' => $one_event['provider_id'],
            );

            // setting the authors

            ArticleAuthor::OnArticleLanguageDelete($art_number, $art_lang);

            $article->setAuthor($art_author);

            $article->setIsPublic($status_public);
            $article->setCommentsEnabled($status_comments);
            //$article->setIsIndexed(true);

            // unless edited (and thus already skipped) we force to publish it; oterwise problems come along issue moving!
            //if ($article_new) {
                if ($status_publish) {
                    $article->setWorkflowStatus('Y');
                }
            //}

            if ($status_publish_by_event_date) {
                if ($article->isPublished()) {
                    if (!$uses_multidates) {
                        $article->setPublishDate($one_event['date']); // necessary to reset after changing the workflow status
                    }
                    else {
                        $today_date = date('Y-m-d');
                        $article->setPublishDate($today_date); // necessary to reset after changing the workflow status
                    }
                }
            }
        }

        // calling to set the images
        self::AddEventImages($images_to_check, $p_source);

    } // fn StoreEventData


	/**
     * Manages images of provided events
     *
     * @param array $p_events
     * @param array $p_source
	 * @return void
	 */
    private static function AddEventImages($p_events, $p_source) {
        if (empty($p_events)) {
            return;
        }

        $images_local = $p_source['images_local'];

        foreach ($p_events as $one_event) {
            // setting images

            $art_number = $one_event['article_number'];

            $images_to_delete = array();
            $one_old_images = ArticleImage::GetImagesByArticleNumber($art_number);
            foreach ($one_old_images as $one_old_img_link) {
                $one_old_img = $one_old_img_link->getImage();
                $one_old_img_link->delete();
                $images_to_delete[] = $one_old_img->getImageId();
            }

            if (!empty($one_event['images'])) {

                $one_img_rank = -1;
                foreach ($one_event['images'] as $one_image) {

                    // check if image already in archive
                    $image_found = false;
                    $use_image_obj = null;

                    $img_time = 0;
                    if (isset($one_image['time']) && (!empty($one_image['time']))) {
                        $img_time = $one_image['time'];
                    }
                    $img_used = self::$s_img_cache->checkImageInfoCache($one_image['url'], $img_time);
                    if (!empty($img_used)) {
                        $images_local_check = '' . ($images_local ? 1 : 0);
                        foreach ($img_used as $one_img_used) {
                            if ($images_local_check == ('' . $one_img_used['local'])) {
                                $use_image_obj = new Image('' . $one_img_used['image_id']);
                                if ($use_image_obj->exists()) {
                                    $image_found = true;
                                    break;
                                }
                            }
                        }
                    }

                    if ($image_found) {
                        $one_img_res = ArticleImage::AddImageToArticle($use_image_obj->getImageId(), $art_number, null);
                        continue;
                    }

                    $one_file_info = array();
                    if ($images_local) {
                        $one_img_rank += 1;
                        $one_image_cont = false;
                        try {
                            $one_image_cont = @file_get_contents($one_image['url']);
                        }
                        catch (Exception $exc) {
                            continue;
                        }
                        if (false === $one_image_cont) {
                            continue;
                        }

                        $one_file_path = tempnam(sys_get_temp_dir(), '' . mt_rand(100, 999));
                        $one_file_fndl = fopen($one_file_path, 'w');
                        fwrite($one_file_fndl, $one_image_cont);
                        fclose($one_file_fndl);
                        $one_image_bad = false;
                        try {
                            if (false === exif_imagetype($one_file_path)) {
                                $one_image_bad = true;
                            }
                        }
                        catch (Exception $exc) {
                            $one_image_bad = true;
                        }
                        if ($one_image_bad) {
                            unlink($one_file_path);
                            continue;
                        }

                        $one_image_mime = '';
                        $one_image_info = getimagesize($one_file_path);
                        if (false === $one_image_info) {
                            unlink($one_file_path);
                            continue;
                        }

                        if (isset($one_image_info['mime'])) {
                            $one_image_mime = $one_image_info['mime'];
                        }

                        $one_url_arr = explode('/', $one_image['url']);
                        $one_url_end = $one_url_arr[count($one_url_arr) - 1];
                        $one_file_info = array(
                            'name' => $one_url_end,
                            'type' => $one_image_mime,
                            'tmp_name' => $one_file_path,
                            'size' => filesize($one_file_path),
                            'error' => 0,
                        );
                    }

                    $one_image_attributes = array();
                    $one_image_attributes['Photographer'] = $p_source['provider_name'];
                    $one_image_attributes['Source'] = 'newsfeed';
                    if (!empty($one_image['label'])) {
                        $one_image_attributes['Description'] = $one_image['label'];
                    }

                    $one_image_obj = null;
                    try {
                        if ($images_local) {
                            $one_image_obj = Image::OnImageUpload($one_file_info, $one_image_attributes);
                        }
                        else {
                            $one_image_obj = Image::OnAddRemoteImage($one_image['url'], $one_image_attributes, null, null);
                        }

                        if (is_a($one_image_obj, 'Image')) {
                            $one_image_info = array(
                                'image_id' => $one_image_obj->getImageId(),
                                'local' => ($images_local ? 1 : 0),
                                'url' => $one_image['url'],
                                'label' => (isset($one_image['label']) ? $one_image['label'] : ''),
                                'provider_id' => $one_event['provider_id'],
                                'time' => 0,
                            );
                            if (isset($one_image['time']) && (!empty($one_image['time']))) {
                                $one_image_info['time'] = $one_image['time'];
                            }
                            self::$s_img_cache->insertImageIntoCache($one_image_info);
                            $one_img_res = ArticleImage::AddImageToArticle($one_image_obj->getImageId(), $art_number, null);
                        }
                    }
                    catch (Exception $exc) {
                        continue;
                    }
                }
            }

            foreach ($images_to_delete as $one_del_img_id) {
                if (0 == count(ArticleImage::GetArticlesThatUseImage($one_del_img_id))) {
                    self::$s_img_cache->removeImageFromCache($one_del_img_id);
                    $one_old_img = new Image($one_del_img_id);
                    if ($one_old_img->exists()) {
                        $one_old_img->delete();
                    }
                }
            }

        }
    } // fn AddEventImages

    /**
     * Cleans files of the given source
     *
     * @param array $p_source
     * @return void
     */
     public static function RemoveOldFiles($p_source) {
        $days_old = 30;

        $dir_to_clean = '';
        if (isset($p_source['source_dirs']) && isset($p_source['source_dirs']['old'])) {
            $dir_to_clean = $p_source['source_dirs']['old'];
        }
        if (empty($dir_to_clean) || !is_dir($dir_to_clean)) {
            return;
        }

        $file_name_set = scandir($dir_to_clean);
        if (empty($file_name_set)) {
            return;
        }

        $current_time = time();
        $threshold_time = $current_time - ($days_old * 24 * 60 * 60);

        foreach ($file_name_set as $one_file_name) {
            $check_path = $dir_to_clean . DIRECTORY_SEPARATOR . $one_file_name;
            if (!is_file($check_path)) {
                continue;
            }

            $last_modified = filemtime($check_path);
            if ($threshold_time > $last_modified) {
                try {
                    unlink($check_path);
                }
                catch (Exception $exc) {
                    continue;
                }
            }

        }

    }

	/**
     * Prunes out passed events on a given source
     *
     * @param array $p_source
     * @param array $p_limits
	 * @return void
	 */
    public static function PruneEventData($p_source, $p_limits) {
/*
    for multi-date based events:
    * take all events
        * those with (moderately) old checked dates
        ... were not updated last (say, two) imports,
            thus the whole tour is either withdrawn or passed
        ... set all its dates as passive (if not already set)
    * remove too old (according to limits) dates
    * remove events without dates
*/

        $art_multidates = false;
        if (isset($p_source['multi_dates']) && (!empty($p_source['multi_dates']))) {
            $art_multidates = true;
        }

        $art_provider = $p_source['provider_id'];

        $art_type = $p_source['article_type'];
        $art_publication = $p_source['publication_id'];
        //$art_issue = $p_source['issue_number'];
        $art_section = $p_source['section_number'];
        $art_lang = $p_source['language_id'];

        if (!isset($p_limits['dates'])) {
            return;
        }
        if (!isset($p_limits['dates']['past'])) {
            return;
        }
        //$passed_span = max(0, $p_limits['dates']['past']);
        $passed_span = 0 + $p_limits['dates']['past'];
        if (!$passed_span) {
            return;
        }

        $passed_time = time() - ($passed_span * 24 * 60 * 60);
        $passed_date = date('Y-m-d', $passed_time);

        // Load all already passed event articles of that feed.
        $p_count = 0;

        $event_art_list = array();

        if ($art_multidates) {
            $event_art_list = Article::GetList(array(
                new ComparisonOperation('idlanguage', new Operator('is', 'sql'), $art_lang),
                new ComparisonOperation('Type', new Operator('is', 'sql'), $art_type),
                new ComparisonOperation('' . $art_type . '.provider_id', new Operator('is', 'sql'), $art_provider),
            ), null, null, 0, $p_count, true);
        }
        else {
            $event_art_list = Article::GetList(array(
                new ComparisonOperation('idlanguage', new Operator('is', 'sql'), $art_lang),
                //new ComparisonOperation('IdPublication', new Operator('is', 'sql'), $art_publication),
                //new ComparisonOperation('NrIssue', new Operator('is', 'sql'), $art_issue),
                //new ComparisonOperation('NrSection', new Operator('is', 'sql'), $art_section),
                new ComparisonOperation('Type', new Operator('is', 'sql'), $art_type),
                //new ComparisonOperation('' . $art_type . '.date', new Operator('smaller_equal', 'sql'), $passed_date), // for newer (but not used) way
                new ComparisonOperation('' . $art_type . '.date', new Operator('smaller', 'sql'), $passed_date), // for older (but probably safer) way
                new ComparisonOperation('' . $art_type . '.provider_id', new Operator('is', 'sql'), $art_provider),
            ), null, null, 0, $p_count, true);
        }

        $event_data_test = null;
        if (!is_array($event_art_list) || (0 == count($event_art_list))) {
            return;
        }

        global $Campsite;
        if (empty($Campsite)) {
            $Campsite = array();
        }
        $Campsite['OMIT_LOGGING'] = true;

        $current_date = date('Y-m-d');
        $withdrawn_limit_date = date('Y-m-d', (time() - 86400)); // those not checked two days at all are withdrawn
        foreach ($event_art_list as $event_art_rem) {
            $remove_art = false;
            $event_data_rem = $event_art_rem->getArticleData();

            if ($art_multidates) {

                $em = Zend_Registry::get('container')->getService('em');
                $repository = $em->getRepository('Newscoop\Entity\ArticleDatetime');

                $all_event_dates = array();
                $rem_event_dates = array();

                $md_removals = array();

                $cur_voided_dates = array();
                $cur_postponed_dates = array();
                $cur_withdrawn_dates = array();

                //$cur_voided_dates = $event_data_rem->getFieldValue('voided');
                foreach ($repository->findBy(array('articleId' => $event_art_rem->getArticleNumber(), 'fieldName' => 'voided')) as $one_void_entry) {
                    //$cur_voided_dates[] = date_format(date_create($one_void_entry->getStartDate()), 'Y-m-d');
                    $cur_voided_dates[] = date_format($one_void_entry->getStartDate(), 'Y-m-d');
                    //$em->remove($one_void_entry);
                    $md_removals[] = $one_void_entry->getId();
                }
                foreach ($repository->findBy(array('articleId' => $event_art_rem->getArticleNumber(), 'fieldName' => 'postponed')) as $one_postpone_entry) {
                    //$cur_voided_dates[] = date_format(date_create($one_void_entry->getStartDate()), 'Y-m-d');
                    $cur_postponed_dates[] = date_format($one_postpone_entry->getStartDate(), 'Y-m-d');
                    //$em->remove($one_postpone_entry);
                    $md_removals[] = $one_postpone_entry->getId();
                }
                // withdraw dates live without corresponding schedule dates
                foreach ($repository->findBy(array('articleId' => $event_art_rem->getArticleNumber(), 'fieldName' => 'withdrawn')) as $one_withdraw_entry) {
                    $one_withdraw_date_str = date_format($one_withdraw_entry->getStartDate(), 'Y-m-d');

                    if ($passed_date && ($one_withdraw_date_str < $passed_date)) {
                        $md_removals[] = $one_withdraw_entry->getId();
                        //$em->remove($one_postpone_entry);
                    }
                    else {
                        //$cur_voided_dates[] = date_format(date_create($one_void_entry->getStartDate()), 'Y-m-d');
                        $cur_withdrawn_dates[] = $one_withdraw_date_str; // date_format($one_withdraw_entry->getStartDate(), 'Y-m-d');
                    }
                }

                $new_voided_dates = array();
                $new_postponed_dates = array();
                $new_withdrawn_dates = array();

                $md_schedule_pool = array();
                $md_schedule_wd = array();
                foreach ($repository->findBy(array('articleId' => $event_art_rem->getArticleNumber(), 'fieldName' => 'schedule')) as $one_date_entry) {
                    //$one_date_str = date_format(date_create($one_date_entry->getStartDate()), 'Y-m-d');
                    $one_date_str = date_format($one_date_entry->getStartDate(), 'Y-m-d');

                    if ($passed_date && ($one_date_str < $passed_date)) {
                        //$em->remove($one_date_entry);
                        $md_removals[] = $one_date_entry->getId();
                        continue;
                    }
                    else {
                        $md_schedule_pool[$one_date_str] = $one_date_entry->getId();
                    }

                    //if (false !== stristr($cur_voided_dates, $one_date_str)) {
                    if (in_array($one_date_str, $cur_voided_dates)) {
                        $new_voided_dates[$one_date_str] = $one_date_str;
                    }
                    if (in_array($one_date_str, $cur_postponed_dates)) {
                        $new_postponed_dates[$one_date_str] = $one_date_str;
                    }
                    //if (in_array($one_date_str, $cur_withdrawn_dates)) {
                    //    $new_withdrawn_dates[] = $one_date_str;
                    //}

                    $all_event_dates[] = $one_date_str;
                }

                foreach ($md_removals as $one_md) {
                    $repository->deleteById($one_md);
                }

                if (($event_data_rem->getFieldValue('date')) < $withdrawn_limit_date) {
                    foreach ($all_event_dates as $one_date_str) {
                        if ($current_date <= $one_date_str) {
                            if (!in_array($one_date_str, $new_withdrawn_dates)) {
                                $new_withdrawn_dates[] = $one_date_str;
                            }
                        }
                    }
                    //sort($new_voided_dates);
                }

                foreach ($new_withdrawn_dates as $one_wd_date) {
                    if (array_key_exists($one_wd_date, $md_schedule_pool)) {
                        $md_schedule_wd[] = $md_schedule_pool[$one_wd_date];
                        if (array_key_exists($one_wd_date, $new_voided_dates)) {
                            unset($new_voided_dates[$one_wd_date]);
                        }
                        if (array_key_exists($one_wd_date, $new_postponed_dates)) {
                            unset($new_postponed_dates[$one_wd_date]);
                        }
                    }
                }

                foreach ($md_schedule_wd as $one_md_wd) {
                    $repository->deleteById($one_md_wd);
                }

                //if (0 < count($all_event_dates)) {
                //    $voided_line_sep = "\n<br />\n";
                //    $voided_info_str = implode($voided_line_sep, $new_voided_dates);
                //    $event_data_rem->setProperty('Fvoided', $voided_info_str);
                //}
                foreach ($new_voided_dates as $one_voided_date) {
                    if (in_array($one_voided_date, $new_withdrawn_dates)) {
                        continue;
                    }

                    $void_datetime = new ArticleDatetime(
                        array(
                            'start_date' => $one_voided_date,
                            'end_date' => $one_voided_date,
                            'recurring' => false,
                        )
                    );
                    $repository->add($void_datetime, $event_art_rem->getArticleNumber(), 'voided', false, false);
                }
                foreach ($new_postponed_dates as $one_postponed_date) {
                    if (in_array($one_postponed_date, $new_withdrawn_dates)) {
                        continue;
                    }

                    $postpone_datetime = new ArticleDatetime(
                        array(
                            'start_date' => $one_postponed_date,
                            'end_date' => $one_postponed_date,
                            'recurring' => false,
                        )
                    );
                    $repository->add($postpone_datetime, $event_art_rem->getArticleNumber(), 'postponed', false, false);
                }
                foreach ($new_withdrawn_dates as $one_withdrawn_date) {
                    $withdraw_datetime = new ArticleDatetime(
                        array(
                            'start_date' => $one_withdrawn_date,
                            'end_date' => $one_withdrawn_date,
                            'recurring' => false,
                        )
                    );
                    $repository->add($withdraw_datetime, $event_art_rem->getArticleNumber(), 'withdrawn', false, false);
                }

                if ((0 == count($all_event_dates)) && (0 == count($cur_withdrawn_dates)) && (0 == count($new_withdrawn_dates))) {
                    $remove_art = true;
                }

                $dates_left_count = 0;

            }
            else {

                $remove_art = true;
                try {
                    //if (($event_data_rem->getFieldValue('date')) > $passed_date) { // for newer (but not used) way
                    if (($event_data_rem->getFieldValue('date')) >= $passed_date) { // for older (but probably safer) way
                        $remove_art = false;
                        //continue;
                    }
                }
                catch (Exception $exc) {
                    //$remove_art = false;
                    continue;
                }

            }

            if (!$remove_art) {
                continue;
            }

            $art_number = $event_art_rem->getArticleNumber();

            // remove images
            $one_rem_images = ArticleImage::GetImagesByArticleNumber($art_number);
            foreach ($one_rem_images as $one_rem_img_link) {
                $one_rem_img = $one_rem_img_link->getImage();
                $one_rem_img_link->delete();
                if (0 == count(ArticleImage::GetArticlesThatUseImage($one_rem_img->getImageId()))) {
                    if (self::$s_img_cache) {
                        self::$s_img_cache->removeImageFromCache($one_rem_img->getImageId());
                    }
                    $one_rem_img->delete();
                }
            }

            // get map
            $ev_map_id = Geo_Map::ReadMapId($art_number);

            // delete article (with map unlinking)
            try {
                $event_art_rem->setWorkflowStatus('N');
                $event_art_rem->delete();
            }
            catch (Exception $exc) {
                //continue;
            }

            // remove map
            if (!empty($ev_map_id)) {
                $ev_map_obj = new Geo_Map($ev_map_id);
                if ($ev_map_obj->exists()) {
                    if (!$ev_map_obj->getArticleNumber()) {
                        $ev_map_obj->delete();
                    }
                }
            }

        }

        $Campsite['OMIT_LOGGING'] = false;

    } // fn PruneEventData

	/**
     * Does the cycle of event data parsing and storing
     *
     * @param array $p_eventSources
	 * @param array $p_newsFeed
     * @param array $p_catTopics
     * @param array $p_limits
     * @param array $p_cancels
	 * @param array $p_otherParams
	 * @return string
	 */
    public static function LoadEventData($p_eventSources, $p_newsFeed, $p_catTopics, $p_limits, $p_cancels, $p_otherParams)
    {
        $plugin_dir = $GLOBALS['g_campsiteDir'].DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'newsimport';
        $class_dir = $plugin_dir.DIRECTORY_SEPARATOR.'classes';
        $incl_dir = $plugin_dir.DIRECTORY_SEPARATOR.'include';
        require_once($class_dir.DIRECTORY_SEPARATOR.'NewsImportEnv.php');
        require_once($class_dir.DIRECTORY_SEPARATOR.'RegionInfo.php');
        require_once($class_dir.DIRECTORY_SEPARATOR.'EventParser.php');
        require_once($class_dir.DIRECTORY_SEPARATOR.'KinoParser.php');
        require($incl_dir.DIRECTORY_SEPARATOR.'default_spool.php');

        $region_info = new RegionInfo();
        $region_topics = $p_catTopics['regions'];

        if ( (!function_exists('plugin_newsimport_create_event_type')) && (!function_exists('plugin_newsimport_make_dirs')) ) {
            require($plugin_dir.DIRECTORY_SEPARATOR.'newsimport.info.php');
        }
        plugin_newsimport_create_event_type();
        plugin_newsimport_make_dirs();

        global $Campsite;
        if (empty($Campsite)) {
            $Campsite = array();
        }

        $cache_path_dir = NewsImportEnv::AbsolutePath($newsimport_default_cache);
        $img_cache_path = $cache_path_dir . 'images_info.sqlite';
        self::$s_img_cache = new EventImage($img_cache_path);

        $import_env = array(
            'cache_dir' => $cache_path_dir,
        );

        foreach ($p_eventSources as $one_source_name => $one_source) {
            if (!array_key_exists($p_newsFeed, $p_eventSources)) {
                echo $p_newsFeed.':none'."\n";
                break;
            }

            if ((!empty($p_newsFeed)) && ($one_source_name != $p_newsFeed)) {
                continue;
            }

            if (isset($one_source['source_dirs']) && is_array($one_source['source_dirs'])) {
                $parsed_src_dirs = array();
                foreach ($one_source['source_dirs'] as $one_src_dir_key => $one_src_dir_val) {
                    if (is_string($one_src_dir_val)) {
                        $one_src_dir_val = NewsImportEnv::AbsolutePath($one_src_dir_val);
                    }
                    $parsed_src_dirs[$one_src_dir_key] = $one_src_dir_val;
                }
                $one_source['source_dirs'] = $parsed_src_dirs;
            }

            $feed_key = base64_encode($one_source_name);
            $sp_images_local = trim('' . SystemPref::Get('NewsImportImagesLocal:' . $feed_key));
            if (!empty($sp_images_local)) {
                if ('Y' == $sp_images_local) {
                    $one_source['images_local'] = true;
                }
                else {
                    $one_source['images_local'] = false;
                }
            }

            $sp_publication_id = trim('' . SystemPref::Get('NewsImportPublicationId:' . $feed_key));
            if (!empty($sp_publication_id)) {
                $one_source['publication_id'] = 0 + $sp_publication_id;
            }
            $sp_issue_number = trim('' . SystemPref::Get('NewsImportIssueNumber:' . $feed_key));
            if (!empty($sp_issue_number)) {
                $one_source['issue_number'] = 0 + $sp_issue_number;
            }
            $sp_section_number = trim('' . SystemPref::Get('NewsImportSectionNumber:' . $feed_key));
            if (!empty($sp_section_number)) {
                $one_source['section_number'] = 0 + $sp_section_number;
            }
            if (0 >= $one_source['publication_id']) {
                echo $one_source_name.':none'."\n";
                continue;
            }
            if (0 >= $one_source['section_number']) {
                echo $one_source_name.':none'."\n";
                continue;
            }
            if (0 >= $one_source['issue_number']) {
                $cur_issue_obj = Issue::GetCurrentIssue($one_source['publication_id'], $one_source['language_id']);
                if (empty($cur_issue_obj)) {
                    continue;
                }
                if (!$cur_issue_obj->exists()) {
                    continue;
                }
                $one_source['issue_number'] = $cur_issue_obj->getIssueNumber();
            }
            if (0 >= $one_source['issue_number']) {
                continue;
            }

            $limits = null;
            $cancels = null;
            if (array_key_exists($one_source_name, $p_limits)) {
                $limits  = $p_limits[$one_source_name];
            }
            if (array_key_exists($one_source_name, $p_cancels)) {
                $cancels  = $p_cancels[$one_source_name];
            }

            if (isset($p_otherParams['pruning']) && $p_otherParams['pruning']) {
                self::RemoveOldFiles($one_source);
                self::PruneEventData($one_source, $limits);
                continue;
            }

            $Campsite['OMIT_LOGGING'] = true;

            $categories = array();
            if (isset($p_catTopics[$one_source['event_type']])) {
                $categories = self::ReadEventCategories($one_source, $p_catTopics[$one_source['event_type']]);
            }

            $parser_obj = null;
            $event_set = null;

            if ('general' == $one_source['event_type']) {
                $parser_obj = new EventData_Parser($one_source);
            }
            if ('movie' == $one_source['event_type']) {
                $parser_obj = new KinoData_Parser($one_source);
            }
            if (!$parser_obj) {
                continue;
            }

            $ev_limit = 0;
            $ev_skip = 0;
            if (array_key_exists('limit', $p_otherParams)) {
                $ev_limit = $p_otherParams['limit'];
            }
            if (array_key_exists('skip', $p_otherParams)) {
                $ev_skip = $p_otherParams['skip'];
            }

            $region_topics_lang = null;
            if (isset($region_topics[$one_source['language_id']])) {
                $region_topics_lang = $region_topics[$one_source['language_id']];
            }
            if (empty($ev_skip)) {
                // shoud we process something (new)
                if (isset($one_source['source_dirs'])) {
                    if (isset($one_source['source_dirs']['new']) && isset($one_source['source_dirs']['lock'])) {
                        $remote_lock_path = $one_source['source_dirs']['new'] . $one_source['source_dirs']['lock'];
                        while (file_exists($remote_lock_path)) {
                            set_time_limit(0);
                            sleep(20);
                        }
                    }
                }

                $res = $parser_obj->prepare($categories, $limits, $cancels, $import_env, $region_info, $region_topics_lang);
                if (!$res) {
                    echo $one_source_name.':none'."\n";
                    continue;
                }
            }

            $event_set = $parser_obj->load();
            if (empty($event_set)) {
                //continue;
                $event_set = array();
            }

            $ev_count = count($event_set);

            if (0 < $ev_skip) {
                if (0 < $ev_limit) {
                    $event_set = array_slice($event_set, $ev_skip, $ev_limit);
                }
                else {
                    $event_set = array_slice($event_set, $ev_skip);
                }
            }
            else {
                if (0 < $ev_limit) {
                    $event_set = array_slice($event_set, 0, $ev_limit);
                }
            }

            $omit_cleanup = false;
            if (0 < $ev_limit) {
                if (count($event_set) == $ev_limit) {
                    $omit_cleanup = true;
                }
            }

            self::StoreEventData($event_set, $one_source);

            if (!$omit_cleanup) {
                $parser_obj->cleanup();
            }

            $Campsite['OMIT_LOGGING'] = false;

            Log::Message(getGS('$1 articles of $2 events set.', count($event_set), $one_source_name), null, 31);

        }

        self::$s_img_cache = null;

        return '';

    } // fn LoadEventData

} // class NewsImport


?>
