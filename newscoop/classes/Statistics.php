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
        $output_html = ' ';

        //looking whether the request is of form used for statistics, i.e.
        //http(s)://newscoop_domain/(newscoop_dir/)_statistics(/...)(?...)

        $path_request_parts = explode('?', $_SERVER['REQUEST_URI']);
        $path_request = strtolower($path_request_parts[0]);
        if (('' == $path_request) || ('/' != $path_request[strlen($path_request)-1])) {
            $path_request .= '/';
        }

        $campsite_subdir = substr($_SERVER['SCRIPT_NAME'], 0, strrpos($_SERVER['SCRIPT_NAME'], '/', -2));

        // the path prefix that should be considered when checking the statistics directory
        // it is an empty string for domain based installations
        $stat_start = strtolower($campsite_subdir);
        if (('' == $stat_start) || ('/' != $stat_start[strlen($stat_start)-1])) {
            $stat_start .= '/';
        }

        // the path (as of request_uri) that is for the statistics part
        $stat_start .= '_statistics/';
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
        foreach (explode('/', $stat_info) as $one_part) {
            $one_part = trim($one_part);
            // here we take that '0' is not valid id for any db object
            if (!empty($one_part)) {
                $stat_info_arr[] = $one_part;
            }
        }

        $art_read_action = false;

        // for now, the only known action is to update statistics on article readering, i.e. for
        // uri path of form (/newscoop_path)/statistics/reader/article/object_id/?...
        if (3 <= count($stat_info_arr)) {
            if (('reader' == $stat_info_arr[0]) && (is_numeric($stat_info_arr[1]))) {
                $art_read_action =  true;
            }
        }

        if (!$art_read_action) {
            return false;
        }

        // if the article was read by a user (incl. an anonymous one)
        if ($art_read_action) {
            $object_type_id = (int) $stat_info_arr[1];
            $object_id = (int) $stat_info_arr[2];

            $correct = self::WriteStats($object_type_id, $object_id);
            if (!$correct) {
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
     * This works according to SessionRequest::Create, just done directly to avoid Zend usage
     * to make it as fast as possible. Note that it contains some apparently nonsensical processing
     * that should be probably avoided, but it was at the original workflow and this is just a copy.
     * I suppose that it would really like to have some workflow simplification.
     *
     * @param int $p_type
     *      type_id of article or, for future, other types
     * @param int $p_specifier
     *      object_id of the read article
     * @return bool
     */
    public static function WriteStats($p_type, $p_specifier)
    {
        // The main input info is object_type (e.g. article) and object_id (unique specifier)
        // as for now, it shall be artycle_type and object_id of an article
        // Note that the p_specifier shall be unique within all the type domains,
        // while the current state is that the object_ids are taken unique just within articles.
        // We shall think on it if trying to put other types (e.g. sections, issues) into stats.

        $object_type_id = 0 + $p_type;
        $object_id = 0 + $p_specifier;

        // taking db access info
        global $Campsite;
        if (empty($Campsite)) {
            $Campsite = array('db' => array());
        }

        $newscoop_path = dirname(dirname(__FILE__));
        require_once($newscoop_path . '/conf/database_conf.php');

        $dbAccess = $Campsite['db'];

        $db_host = $dbAccess['host'];
        $db_port = $dbAccess['port'];
        $db_user = $dbAccess['user'];
        $db_pwd = $dbAccess['pass'];
        $db_name = $dbAccess['name'];

        $session_name = session_name();
        if(session_id() == '') {
            session_start($session_name);
            $session_id = session_id();
        } else {
            $session_id = session_id();
        }
        

        // sql commands used along the workflow
        $sqlSessionSel = 'SELECT user_id FROM Sessions WHERE id = :session_id';
        $sqlSessionIns = 'INSERT INTO Sessions (id, start_time) VALUES (:session_id, :start_time)';

        $sqlRequestObjectSel = 'SELECT object_id FROM RequestObjects WHERE object_id = :object_id';
        $sqlRequestObjectIns = 'INSERT INTO RequestObjects (object_id, object_type_id) VALUES (:object_id, :object_type_id)';
        $sqlRequestObjectUpd = 'UPDATE RequestObjects SET request_count = (request_count + 1) WHERE object_id = :object_id';

        $sqlRequestSel = 'SELECT last_stats_update FROM Requests WHERE (session_id, object_id) = (:session_id, :object_id) LIMIT 1';
        $sqlRequestIns = 'INSERT INTO Requests (session_id, object_id, last_stats_update) VALUES (:session_id, :object_id, :last_stats_update)';

        // it looks that this table was used just for having daily/hourly statistics,
        // but can not see where it is used now; was it finally abandoned?
        $sqlRequestStatSel = 'SELECT request_count FROM RequestStats WHERE (object_id, date, hour) = (:object_id, :date, :hour)';
        $sqlRequestStatIns = 'INSERT INTO RequestStats (object_id, date, hour, request_count) VALUES (:object_id, :date, :hour, 1)';
        $sqlRequestStatUpd = 'UPDATE RequestStats SET request_count = (request_count + 1) WHERE (object_id, date, hour) = (:object_id, :date, :hour)';

        // openning the db connection
        $dbh = null;
        $sth = null;

        try {
            $dbh = new PDO(
                "mysql:host=$db_host;port=$db_port;dbname=$db_name", 
                "$db_user",
                "$db_pwd",
                array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8')
            );
        }
        catch (Exception $exc) {
            return false;
        }

        // the old processing read/created session info first
        // it looks to me that we do not need this here, may be to abandon this at a refactoring
        try {
            $user_id = null;
            $session_exists = false;

            $sth = $dbh->prepare($sqlSessionSel);
            $sth->bindValue(':session_id', (string) $session_id, PDO::PARAM_STR);

            $res = $sth->execute();
            if (!$res) {
                return false;
            }

            while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
                $user_id = $row['user_id'];
                $session_exists = true;
            }

            $sth = null;

            if (!$session_exists) {
                $sth = $dbh->prepare($sqlSessionIns);
                $sth->bindValue(':session_id', (string) $session_id, PDO::PARAM_STR);
                $sth->bindValue(':start_time', (string) strftime("%Y-%m-%d %T"), PDO::PARAM_STR);

                $res = $sth->execute();
                if (!$res) {
                    return false;
                }

                $sth = null;
            }

        }
        catch (Exception $exc) {
            return false;
        }

        // we read the stats info from RequestObjects and create empty one, if none there
        // note that RequestObjects and RequestStats contain apparently duplicated info,
        // it shall be rationalized at some refactoring

        try {
            $request_object_exists = false;

            $sth = $dbh->prepare($sqlRequestObjectSel);
            $sth->bindValue(':object_id', (string) $object_id, PDO::PARAM_INT);

            $res = $sth->execute();
            if (!$res) {
                return false;
            }

            while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
                $request_object_exists = true;
            }

            $sth = null;

            if (!$request_object_exists) {
                $sth = $dbh->prepare($sqlRequestObjectIns);
                $sth->bindValue(':object_id', (string) $object_id, PDO::PARAM_INT);
                $sth->bindValue(':object_type_id', (string) $object_type_id, PDO::PARAM_INT);

                $res = $sth->execute();
                if (!$res) {
                    return false;
                }

                $sth = null;
            }

        }
        catch (Exception $exc) {
            return false;
        }

        // does the user already read this article
        // in a distant past the page-reads were taken per user (i.e. session_id) and hour,
        // for the current (and past) workflow a user (i.e. seesion_id) can be taken just once,
        // i.e. either the row (on session_id and object_id) does exists or not at all

        $last_stats_update = null;

        try {
            $sth = $dbh->prepare($sqlRequestSel);
            $sth->bindValue(':session_id', (string) $session_id, PDO::PARAM_STR);
            $sth->bindValue(':object_id', (string) $object_id, PDO::PARAM_INT);

            $res = $sth->execute();
            if (!$res) {
                return false;
            }

            while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
                $last_stats_update = $row['last_stats_update'];
            }

            $sth = null;
        }
        catch (Exception $exc) {
            return false;
        }

        // stop here if already read that
        if (!empty($last_stats_update)) {
            return true;
        }

        $last_stats_update = date('Y-m-d G:i:s');
        $current_date = date('Y-m-d');
        $current_hour = 0 + date('H');

        // save that the user has read the article
        try {
            $sth = $dbh->prepare($sqlRequestIns);
            $sth->bindValue(':session_id', (string) $session_id, PDO::PARAM_STR);
            $sth->bindValue(':object_id', (string) $object_id, PDO::PARAM_INT);
            $sth->bindValue(':last_stats_update', (string) $last_stats_update, PDO::PARAM_STR);

            $res = $sth->execute();
            if (!$res) {
                return false;
            }

            $sth = null;
        }
        catch (Exception $exc) {
            return false;
        }

        $request_count_update = false;

        // how many has read this article this hour
        try {
            $sth = $dbh->prepare($sqlRequestStatSel);
            $sth->bindValue(':object_id', (string) $object_id, PDO::PARAM_INT);
            $sth->bindValue(':date', (string) $current_date, PDO::PARAM_STR);
            $sth->bindValue(':hour', (string) $current_hour, PDO::PARAM_INT);

            $res = $sth->execute();
            if (!$res) {
                return false;
            }

            while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
                $request_count_update = true;
            }

            $sth = null;
        }
        catch (Exception $exc) {
            return false;
        }

        // save the (increased) count of read access
        // both into RequestStats and RequestObjects
        try {
            if ($request_count_update) {
                $sth = $dbh->prepare($sqlRequestStatUpd);
            } else {
                $sth = $dbh->prepare($sqlRequestStatIns);
            }
            $sth->bindValue(':object_id', (string) $object_id, PDO::PARAM_INT);
            $sth->bindValue(':date', (string) $current_date, PDO::PARAM_STR);
            $sth->bindValue(':hour', (string) $current_hour, PDO::PARAM_INT);

            $res = $sth->execute();
            if (!$res) {
                return false;
            }

            $sth = null;

            $sth = $dbh->prepare($sqlRequestObjectUpd);
            $sth->bindValue(':object_id', (string) $object_id, PDO::PARAM_INT);

            $res = $sth->execute();
            if (!$res) {
                return false;
            }

            $sth = null;

        }
        catch (Exception $exc) {
            return false;
        }

        return true;

    } // WriteStats

    /**
     * Prepares JavaScript trigger for statistics requests
     *
     * @param int $p_params
     *      specs to distinguish the request, js variables and functions
     * @return string
     */
    public static function JavaScriptTrigger($p_params)
    {
        global $Campsite;

        if (!isset($p_params['name_spec'])) {
            return '';
        }
        if (!isset($p_params['object_type_id'])) {
            return '';
        }
        if (!isset($p_params['request_object_id'])) {
            return '';
        }

        $name_spec = $p_params['name_spec'];
        $object_type_id = $p_params['object_type_id'];
        $request_object_id = $p_params['request_object_id'];

        $stat_web_url = $Campsite['WEBSITE_URL'];
        if ('/' != $stat_web_url[strlen($stat_web_url)-1]) {
            $stat_web_url .= '/';
        }

        $trigger = '
            <script type="text/javascript">
            <!--
            var stats_getHTTPObject' . $name_spec . ' = function () {
                var xhr = false;
                if (window.XMLHttpRequest) {
                    xhr = new XMLHttpRequest();
                } else if (window.ActiveXObject) {
                    try {
                        xhr = new ActiveXObject("Msxml2.XMLHTTP");
                    } catch(e) {
                        try {
                            xhr = new ActiveXObject("Microsoft.XMLHTTP");
                        } catch(e) {
                            xhr = false;
                        }
                    }
                }
                return xhr;
            };

            var stats_submit' . $name_spec . ' = function () {
                if (undefined !== window.statistics_request_sent_' . $name_spec . ') {
                    return;
                }
                window.statistics_request_sent_' . $name_spec . ' = true;

                var stats_request = stats_getHTTPObject' . $name_spec . '();
                stats_request.onreadystatechange = function() {};

                var read_date = new Date();
                var read_path = "_statistics/reader/' . $object_type_id . '/";
                var request_randomizer = "" + read_date.getTime() + Math.random();
                var stats_url = "' . $stat_web_url . '" + read_path + "' . $request_object_id . '/";
                try {
                    stats_request.open("GET", stats_url + "?randomizer=" + request_randomizer, true);
                    stats_request.send(null);
                    /* not everybody has jquery installed
                    $.ajax({
                        url: stats_url,
                        data: {randomizer: request_randomizer},
                        success: function() {}
                    });
                    */
                } catch (e) {}
            };
            stats_submit' . $name_spec . '();
            -->
            </script>
        ';

        return $trigger;
    } // JavaScriptTrigger

} // class Statistics

