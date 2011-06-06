<?php

/**
 * Class Statistics
 *
 * Statistics for article read requests.
 */
class Statistics {

    /**
     * Process the statistics for the request.
     *
     * @param bool $p_statsOnly
     *      Is this request just for statistics.
     * @return bool
     */
    public static function ProcessStats(&$p_statsOnly)
    {
        global $Campsite;

        $p_statsOnly = false;
        $output_html = " ";

        /*
            looking whether the request is of form used for statistics, i.e.
            http(s)://newscoop_domain/(newscoop_dir/)_statistics(/...)(?...)
        */

        $path_request_parts = explode("?", $_SERVER['REQUEST_URI']);
        $path_request = strtolower($path_request_parts[0]);
        if (("" == $path_request) || ("/" != $path_request[strlen($path_request)-1])) {
            $path_request .= "/";
        }

        // the path prefix that should be considered when checking the statistics directory
        // it is an empty string for domain based installations
        $stat_start = strtolower($Campsite['SUBDIR']);
        if (("" == $stat_start) || ("/" != $stat_start[strlen($stat_start)-1])) {
            $stat_start .= "/";
        }

        // the path (as of request_uri) that is for the statistics part
        $stat_start .= "_statistics/";
        $stat_start_len = strlen($stat_start);
        // if request_uri starts with the statistics path, it is just for the statistics things
        if (substr($path_request, 0, $stat_start_len) == $stat_start) {
            $p_statsOnly = true;
        }
        // if not on statistics, just return and let run the standard newscoop processing
        if (!$p_statsOnly) {
            return true;
        }

        // taking the statistics specification part of the request uri
        $stat_info = substr($path_request, $stat_start_len);

        $stat_info_arr = array();
        foreach (explode("/", $stat_info) as $one_part) {
            $one_part = trim($one_part);
            // here we take that '0' is not valid id for any db object
            if (!empty($one_part)) {
                $stat_info_arr[] = $one_part;
            }
        }

        $art_read_action = false;

        // for now, the only known action is to update statistics on article readering, i.e. for
        // uri path of form (/newscoop_path)/statistics/reader/article/article_number/language_code/?...
        if (4 <= count($stat_info_arr)) {
            if (("reader" == $stat_info_arr[0]) && ("article" == $stat_info_arr[1])) {
                $art_read_action =  true;
            }
        }

        if (!$art_read_action) {
            return false;
        }

        // if the article was read by a user (incl. an anonymous one)
        if ($art_read_action) {
            $article_number = (int) $stat_info_arr[2];
            $language_code = $stat_info_arr[3];

            $written = self::WriteStats($article_number, $language_code);
            if (!$written) {
                return false;
            }
        } // end of the stats action on article reading

        // the output string for stats only requests; nothing for now
        echo $output_html;

        // whether the stats processing was correct
        // the return value not used actually anywhere now
        return true;
    } // fn ProcessStats

    /**
     * Writes the statistics for the request.
     *
     * @param int $p_articleNumber
     *      number of article whose stats shall be updated
     * @param string $p_languageCode
     *      language of article whose stats shall be updated
     * @return bool
     */
    private static function WriteStats($p_articleNumber, $p_languageCode)
    {
        if ((!$p_articleNumber) || (!$p_languageCode)) {
            return false;
        }

        // taking the language id, if it exists
        $language_id = Language::GetLanguageIdByCode($p_languageCode);
        if (!$language_id) {
            return false;
        }

        // taking the article object, if it exists
        $art_obj = new Article($language_id, $p_articleNumber);
        if ((!$art_obj) || (!$art_obj->exists())) {
            return false;
        }

        // no new stats for non-published articles
        if (!$art_obj->isPublished()) {
            return false;
        }

        // session used for stats writing (not to take an article reading more than once per session)
        // session may be new when reading an externally cached article, thus not checking sessions here
        $session_id = session_id();

        /*
            a user can read (and thus update stats) just for articles with correct access rights to
        */
        // if a public article, can write stats

        // if not a public article, we have to have read access to it
        // taking user id (article not public) from CampURI that is contained via an URIInstance
        $user_id = 0;
        $uri_inst = CampSite::GetURIInstance();

        $meta_user = $uri_inst->user;
        if ($meta_user) {
            $user_id = $meta_user->identifier;
        }

        // to save processing time, we push the statistics requests even on articles we do not have access to
/*
        $is_accessible = false;
        if ($art_obj->isPublic()) {
            $is_accessible = true;
        }

        if (!$is_accessible) {
            require_once($GLOBALS['g_campsiteDir'].'/include/pear/Date.php');

            // user info
            $user = new User($user_id);

            // article info
            $publ_id = $art_obj->getPublicationId();
            $section_number = $art_obj->getSectionNumber();

            // if having a user
            if ($user_id) {
                // taking all subscriptions of the user on the current publication
                $subs = Subscription::GetSubscriptions($publ_id, $user_id);

                $sub_sec_valid = false;
                foreach ($subs as $one_sub) {
                    $today = new Date(time());
                    $today_date = $today->getDate();
                    // section can be subscribed either under current language or for any language
                    if ($one_sub->isActive()) {
                        $sub_sec = new SubscriptionSection($one_sub->getSubscriptionId(), $section_number, $language_id);
                        if ($sub_sec && $sub_sec->exists()) {
                            $exp_day = $sub_sec->getExpirationDate();
                            if ($exp_date >= $today->getDate()) {
                                $sub_sec_valid = true;
                            }
                        }
                        if (!$sub_sec_valid) {
                            $sub_sec = new SubscriptionSection($one_sub->getSubscriptionId(), $section_number, 0);
                            if ($sub_sec && $sub_sec->exists()) {
                                $exp_date = $sub_sec->getExpirationDate();
                                if ($exp_date >= $today_date) {
                                    $sub_sec_valid = true;
                                }
                            }
                        }
                    }
                    // do not need to search more if found
                    if ($sub_sec_valid) {
                        break;
                    }

                }

                if ($sub_sec_valid) {
                    $is_accessible = true;
                }
            }
        }

        // if the article not open for us, no stats on that (since reading not possible)
        if (!$is_accessible) {
            return false;
        }
*/

        // the object_id is used for actual statistics
        $objId = $art_obj->getProperty('object_id');
        // if no object_id on the article, then no statistics
        if ($objId) {
            // the stats writing itself; going through session checking/creation for situations when a cached article was read
            // thus calling SessionRequest::Create() instead of doing direct statistics updates via SessionRequest::UpdateStats()
            $objectType = new ObjectType('article');
            SessionRequest::Create($session_id, $objId, $objectType->getObjectTypeId(), $user_id, true);
        }

        return true;
    } // fn WriteStats


} // class Statistics
