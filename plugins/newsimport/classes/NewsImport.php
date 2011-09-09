<?php


/**
 * NewsImport manages the event importing.
 */
class NewsImport
{

	/**
     * checks whether a request for event import, calls that import if asked
     *
	 * @param bool $p_importOnly
	 * @return bool
	 */
    public static function ProcessImport(&$p_importOnly) {

        global $Campsite;

        $p_importOnly = false;
        $output_html = ' ';

        //looking whether the request is of form used for xml import, i.e.
        //http(s)://newscoop_domain/(newscoop_dir/)_xmlimport(/...)(?...)

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

        // the path (as of request_uri) that is for the statistics part
        $xmlimp_start .= '_newsimport/';
        $xmlimp_start_len = strlen($xmlimp_start);
        // if request_uri starts with the statistics path, it is just for the statistics things
        if (substr($path_request, 0, $xmlimp_start_len) == $xmlimp_start) {
            $p_importOnly = true;
        }
        // if not on statistics, just return and let run the standard newscoop processing
        if (!$p_importOnly) {
            return true;
        }

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

        if ((!empty($news_auth_sys_pref)) && ($news_auth != $news_auth_sys_pref)) {
            return false;
        }

        require_once($GLOBALS['g_campsiteDir'].DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'Topic.php');
        require_once($GLOBALS['g_campsiteDir'].DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'TopicName.php');
        require_once($GLOBALS['g_campsiteDir'].DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'GeoMap.php');
        require_once($GLOBALS['g_campsiteDir'].DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'Article.php');
        require_once($GLOBALS['g_campsiteDir'].DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'Log.php');

        $conf_dir = $GLOBALS['g_campsiteDir'].DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'newsimport'.DIRECTORY_SEPARATOR.'include';
        $class_dir = $GLOBALS['g_campsiteDir'].DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'newsimport'.DIRECTORY_SEPARATOR.'classes';
        require_once($conf_dir.DIR_SEP.'default_topics.php');
        require_once($conf_dir.DIR_SEP.'news_feeds_conf.php');
        require_once($class_dir.DIR_SEP.'NewsImport.php');

        // take the category topics, as array by [language][category] of [name,id]
        $cat_topics = self::ReadEventTopics($newsimport_default_cat_names);

        $events_limit = 0;
        $events_skip = 0;
        if (array_key_exists('newslimit', $_GET)) {
            $events_limit = 0 + $_GET['newslimit'];
            $events_limit = max(0, $events_limit);
        }
        if (array_key_exists('newsoffset', $_GET)) {
            $events_skip = 0 + $_GET['newsoffset'];
            $events_skip = max(0, $events_skip);
        }

        $params_other = array(
            'skip' => $events_skip,
            'limit' => $events_limit,
        );

        $events_ignore_passed = false;
        if (array_key_exists('newsignorepassed', $_GET)) {
            if (in_array(strtolower($_GET['newsignorepassed']), array('1', 'true', 't', 'yes', 'y', 'on'))) {
                $events_ignore_passed = true;
            }
            if (in_array(strtolower($_GET['newsignorepassed']), array('0', 'false', 'f', 'no', 'n', 'off'))) {
                $events_ignore_passed = false;
            }
        }
        if ($events_ignore_passed) {
            $params_other['start_date'] = date('Y-m-d', localtime());
        }

        set_time_limit(0);
        ob_end_flush();
        flush();

        return self::LoadEventData($event_data_sources, $news_feed, $cat_topics, $params_other);
    } // fn ProcessImport

	/**
     * Takes topics for event categories
     *
	 * @param array $p_defaultTopicNames
	 * @return array
	 */
    public static function ReadEventTopics($p_defaultTopicNames) {
        $event_spec_topics = array();

        foreach ($p_defaultTopicNames as $cat_key => $event_spec_names) {
            foreach ($event_spec_names as $cat_lan_id => $cat_name) {
                $cat_key_sys_pref = 'EventCat' . $cat_lan_id . ucfirst($cat_name);
                $cat_name_sys_pref = SystemPref::Get($cat_key_sys_pref);
                if (!empty($cat_name_sys_pref)) {
                    $cat_name = $cat_name_sys_pref;
                }
                if (!array_key_exists($cat_lan_id, $event_spec_topics)) {
                    $event_spec_topics[$cat_lan_id] = array();
                }
                $topic_name_obj = new TopicName($cat_name, $cat_lan_id);
                $event_spec_topics[$cat_lan_id][$cat_key] = array('name' => $cat_name, 'id' => $topic_name_obj->getTopicId());
            }
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
                    $topics[] = array('fixed' => $lang_topics[$one_spec_key]);
                }
            }
            if (is_array($one_spec_value)) {
                if (array_key_exists($one_spec_key, $lang_topics)) {
                    $topics[] = array('match_xml' => $one_spec_value, 'match_topic' => $lang_topics[$one_spec_key]);
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
        if (empty($p_events)) {
            return;
        }

        global $g_user;

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

        foreach ($p_events as $one_event) {
            $article = null;
            $article_new = false;

            //First, try to load event (possibly created by former imports), and if there, remove it - will be put in with the current, possible more correct info.
            $p_count = 0;
            $event_art_list = Article::GetList(array(
                new ComparisonOperation('idlanguage', new Operator('is', 'sql'), $art_lang),
                new ComparisonOperation('IdPublication', new Operator('is', 'sql'), $art_publication),
                new ComparisonOperation('NrIssue', new Operator('is', 'sql'), $art_issue),
                new ComparisonOperation('NrSection', new Operator('is', 'sql'), $art_section),
                new ComparisonOperation('Type', new Operator('is', 'sql'), $art_type),
                new ComparisonOperation($art_type . '.event_id', new Operator('is', 'sql'), $one_event['event_id']),
            ), null, null, 0, $p_count, true);

            if (is_array($event_art_list) && (0 < count($event_art_list))) {
                foreach ($event_art_list as $event_art_test) {
                    $event_data_test = $event_art_test->getArticleData();
                    if (($event_data_test->getFieldValue('event_id')) == $one_event['event_id']) {
                        $article = $event_art_test;
                        break;
                    }
                }
            }

            $art_name = $one_event['headline'] . ' - ' . $one_event['date'] . ' (' . $one_event['event_id'] . ')';

            if (!$article) {
                $article = new Article($art_lang);
                $article->create($art_type, $art_name, $art_publication, $art_issue, $art_section);
                $article_new = true;
            }

            $art_number = $article->getArticleNumber();

            $article_data = $article->getArticleData();

            $article_data->setProperty('Fprovider_id', $one_event['provider_id']);
            $article_data->setProperty('Fevent_id', $one_event['event_id']);
            $article_data->setProperty('Ftour_id', $one_event['tour_id']);
            $article_data->setProperty('Flocation_id', $one_event['location_id']);

            $article_data->setProperty('Fheadline', $one_event['headline']);
            $article_data->setProperty('Forganizer', $one_event['organizer']);

            $article_data->setProperty('Fcountry', $one_event['country']);
            $article_data->setProperty('Fzipcode', $one_event['zipcode']);
            $article_data->setProperty('Ftown', $one_event['town']);
            $article_data->setProperty('Fstreet', $one_event['street']);

            $article_data->setProperty('Fdate', $one_event['date']);
            $article_data->setProperty('Fdate_year', $one_event['date_year']);
            $article_data->setProperty('Fdate_month', $one_event['date_month']);
            $article_data->setProperty('Fdate_day', $one_event['date_day']);
            $article_data->setProperty('Ftime', $one_event['time']);

            $article_data->setProperty('Fdate_time_text', $one_event['date_time_text']);

            $article_data->setProperty('Fweb', $one_event['web']);
            $article_data->setProperty('Femail', $one_event['email']);
            $article_data->setProperty('Fphone', $one_event['phone']);

            $article_data->setProperty('Fdescription', $one_event['description']);
            $ev_other_info = implode("\n", $one_event['other']);
            $article_data->setProperty('Fother', $ev_other_info);

            $article_data->setProperty('Fgenre', $one_event['genre']);
            $article_data->setProperty('Flanguages', $one_event['languages']);
            $article_data->setProperty('Fprices', $one_event['prices']);
            $article_data->setProperty('Fminimal_age', $one_event['minimal_age']);

            $article_data->setProperty('Frated', ($one_event['rated'] ? 1 : 0));

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
                $ev_poi_desc = mb_substr($one_event['description'], 0, (0 + $geo_sys_def['poi_desc_len']));
                if ($ev_poi_desc != $one_event['description']) {
                    $ev_poi_desc .= ' ...';
                }

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

            // setting images

            $one_old_images = ArticleImage::GetImagesByArticleNumber($art_number);
            foreach ($one_old_images as $one_old_img_link) {
                $one_old_img = $one_old_img_link->getImage();
                $one_old_img_link->delete();
                if (0 == count(ArticleImage::GetArticlesThatUseImage($one_old_img->getImageId()))) {
                    $one_old_img->delete();
                }
            }

            if (!empty($one_event['images'])) {

                $one_img_rank = -1;
                foreach ($one_event['images'] as $one_image) {

                    $one_file_info = array();
                    if ($images_local) {
                        $one_img_rank += 1;
                        $one_image_cont = false;
                        try {
                            $one_image_cont = file_get_contents($one_image['url']);
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
                        if (false === exif_imagetype($one_file_path)) {
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
                            $one_img_res = ArticleImage::AddImageToArticle($one_image_obj->getImageId(), $art_number, null);
                        }
                    }
                    catch (Exception $exc) {
                        continue;
                    }
                }
            }

            // setting the authors

            ArticleAuthor::OnArticleLanguageDelete($art_number, $art_lang);

            $article->setAuthor($art_author);

            $article->setIsPublic($status_public);
            $article->setCommentsEnabled($status_comments);
            //$article->setIsIndexed(true);

            if ($article_new) {
                if ($status_publish) {
                    $article->setWorkflowStatus('Y');
                }
            }

            if ($status_publish_by_event_date) {
                if ($article->isPublished()) {
                    $article->setPublishDate($one_event['date']); // necessary to reset after changing the workflow status
                }
            }
        }
    } // fn StoreEventData

	/**
     * Does the cycle of event data parsing and storing
     *
     * @param array $p_eventSources
	 * @param array $p_newsFeed
     * @param array $p_catTopics
	 * @param array $p_otherParams
	 * @return void
	 */
    public static function LoadEventData($p_eventSources, $p_newsFeed, $p_catTopics, $p_otherParams) {

        $class_dir = $GLOBALS['g_campsiteDir'].DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'newsimport'.DIRECTORY_SEPARATOR.'classes';
        require_once($class_dir.DIR_SEP.'EventParser.php');

        global $Campsite;

        foreach ($p_eventSources as $one_source_name => $one_source) {
            if ((!empty($p_newsFeed)) && ($one_source_name != $p_newsFeed)) {
                continue;
            }

            $Campsite['OMIT_LOGGING'] = true;

            $categories = self::ReadEventCategories($one_source, $p_catTopics);

            $parser_obj = null;
            $event_set = null;

            if ('general' == $one_source['event_type']) {
                $parser_obj = new EventData_Parser($one_source);
            }

            if (!$parser_obj) {
                continue;
            }

            // whether we can start on this feed now
            $res = $parser_obj->start();
            if (!$res) {
                continue;
            }

            // shoud we process something
            $res = $parser_obj->prepare();
            if (!$res) {
                $parser_obj->stop();
                continue;
            }

            $event_load = $parser_obj->parse($categories, $p_otherParams);
            if (empty($event_load)) {
                $parser_obj->stop();
                continue;
            }

            $event_set = $event_load['events'];
            unset($event_load['events']);

            $ev_limit = 0;
            $ev_skip = 0;
            if (array_key_exists('limit', $p_otherParams)) {
                $ev_limit = $p_otherParams['limit'];
            }
            if (array_key_exists('skip', $p_otherParams)) {
                $ev_skip = $p_otherParams['skip'];
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

            $parser_obj->stop();

            $Campsite['OMIT_LOGGING'] = false;

            Log::Message(getGS('$1 articles of $2 events set.', count($event_set), $one_source_name), null, 31);

        }

    } // fn LoadEventData


} // class NewsImport


?>