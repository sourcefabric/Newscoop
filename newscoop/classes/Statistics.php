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
            if (('reader' == $stat_info_arr[0]) && ('article' == $stat_info_arr[1])) {
                $art_read_action =  true;
            }
        }

        if (!$art_read_action) {
            return false;
        }

        // if the article was read by a user (incl. an anonymous one)
        if ($art_read_action) {
            $object_id = (int) $stat_info_arr[2];

            $correct = self::WriteStats('article', $object_id);
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
     *
     * @param string $p_type
     *      article or, for future, other types
     * @param int $p_specifier
     *      object_id of the read article
     * @return bool
     */
    public static function WriteStats($p_type, $p_specifier)
    {
        if ('article' != $p_type) {
            return false;
        }
        $object_id = 0 + $p_specifier;

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

        $application_path = $newscoop_path . '/application';
        $config = parse_ini_file($application_path . '/configs/application.ini');
        $session_name = $config['resources.session.name'];

        session_start($session_name);
        $session_id = session_id();

        $sqlReqSel1 = 'SELECT last_stats_update FROM Requests WHERE session_id = :session_id AND object_id = :object_id LIMIT 1';
        $sqlReqIns1 = 'INSERT INTO Requests (session_id, object_id, last_stats_update) VALUES (:session_id, :object_id, :last_stats_update)';

        $sqlReqSel2 = 'SELECT request_count FROM RequestStats WHERE object_id = :object_id AND date = :date AND hour = :hour';
        $sqlReqIns2 = 'INSERT INTO RequestStats (object_id, date, hour, request_count) VALUES (:object_id, :date, :hour, 1)';
        $sqlReqUpd2 = 'UPDATE RequestStats SET request_count = :request_count WHERE (object_id, date, hour) = (:object_id, :date, :hour)';

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

        $last_stats_update = null;

        // does the user already read this article
        try {
            $sth = $dbh->prepare($sqlReqSel1);
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
            $sth = $dbh->prepare($sqlReqIns1);
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

        $request_count = 0;
        $request_count_update = false;

        // how many has read this article this hour
        try {
            $sth = $dbh->prepare($sqlReqSel2);
            $sth->bindValue(':object_id', (string) $object_id, PDO::PARAM_INT);
            $sth->bindValue(':date', (string) $current_date, PDO::PARAM_STR);
            $sth->bindValue(':hour', (string) $current_hour, PDO::PARAM_INT);

            $res = $sth->execute();
            if (!$res) {
                return false;
            }

            while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
                $request_count = $row['request_count'];
                $request_count_update = true;
            }

            $sth = null;
        }
        catch (Exception $exc) {
            return false;
        }

        // the user has read it
        $request_count += 1;

        // save the (increased) count of read access
        try {
            if ($request_count_update) {
                $sth = $dbh->prepare($sqlReqUpd2);
                $sth->bindValue(':request_count', (string) $request_count, PDO::PARAM_INT);
            } else {
                $sth = $dbh->prepare($sqlReqIns2);
            }
            $sth->bindValue(':object_id', (string) $object_id, PDO::PARAM_INT);
            $sth->bindValue(':date', (string) $current_date, PDO::PARAM_STR);
            $sth->bindValue(':hour', (string) $current_hour, PDO::PARAM_INT);

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

} // class Statistics

