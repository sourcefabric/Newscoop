<?php

class NewsImportEnv
{

    /**
     * To omit a possible double run if it would happened.
     * @var string
     */
    private static $s_working = '_newsimport_lock';

    /**
     * To write down datetime of the last work start.
     * @var string
     */
    private static $s_working_date = '_newsimport_date';


    public static function SetProperBaseDir()
    {
        $base_dir = dirname(dirname(dirname(dirname(__FILE__))));
        $base_dir_test = $base_dir.DIRECTORY_SEPARATOR.'newscoop'; // if plugins taken via symlink
        if (is_dir($base_dir_test)) {
            $sys_pref_path = $base_dir.DIRECTORY_SEPARATOR.'system_preferences.php';
            if (!is_file($sys_pref_path)) {
                $base_dir = $base_dir_test;
            }
        }
        $GLOBALS['g_campsiteDir'] = $base_dir;
    }

    public static function BootCli ()
    {
        if (php_sapi_name() != 'cli') {
            return false;
        }

        self::SetProperBaseDir();

        // Define path to application directory
        defined('APPLICATION_PATH')
            || define('APPLICATION_PATH', $GLOBALS['g_campsiteDir'].DIRECTORY_SEPARATOR.'application');

        // Define application environment
        defined('APPLICATION_ENV')
            || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'development'));

        $incl_dir = $GLOBALS['g_campsiteDir'].DIRECTORY_SEPARATOR.'include';

        // Ensure library/ is on include_path
        set_include_path(implode(PATH_SEPARATOR, array(
            realpath(APPLICATION_PATH . '/../library'),
            realpath($incl_dir),
            get_include_path(),
        )));
        if (!is_file('Zend/Application.php')) {
            // include libzend if we dont have zend_application
            set_include_path(implode(PATH_SEPARATOR, array(
                '/usr/share/php/libzend-framework-php',
                get_include_path(),
            )));
        }
        require_once 'Zend/Application.php';

        // Create application, bootstrap, and run
        $application = new Zend_Application(
            APPLICATION_ENV,
            APPLICATION_PATH . '/configs/application.ini'
        );

        $application->bootstrap();

        self::SetProperBaseDir();

        return true;
    }

    /**
     * taking confs for import requests
     *
     * @return array
     */
    public static function TakeConfInfo() {
        $plugin_dir = dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR;

        $incl_dir = $plugin_dir.'include'.DIRECTORY_SEPARATOR;
        require($incl_dir . 'default_access.php');

        $base_url = 'http://localhost';
        $access_token = $newsimport_default_access;
        $http_auth = '';

        if (!isset($GLOBALS['Campsite'])) {
            $GLOBALS['Campsite'] = array();
        }
        global $Campsite;

        $newscoop_dir = $GLOBALS['g_campsiteDir'];
        $sys_pref_path = $newscoop_dir.DIRECTORY_SEPARATOR.'system_preferences.php';
        if (!is_file($sys_pref_path)) {
            $sys_pref_path = $newscoop_dir.DIRECTORY_SEPARATOR.'newscoop'.DIRECTORY_SEPARATOR.'system_preferences.php';
        }
        require($sys_pref_path);

        $keyNewscoopBase = 'NewsImportBaseUrl';
        $keyNewscoopToken = 'NewsImportCommandToken';
        $keyNewscoopAuthUser = 'NewsImportHttpAuthUser';
        $keyNewscoopAuthPwd = 'NewsImportHttpAuthPwd';

        if (isset($GLOBALS['Campsite']['system_preferences'][$keyNewscoopBase])) {
            $base_url = $GLOBALS['Campsite']['system_preferences'][$keyNewscoopBase];
        }
        if (isset($GLOBALS['Campsite']['system_preferences'][$keyNewscoopToken])) {
            $access_token = $GLOBALS['Campsite']['system_preferences'][$keyNewscoopToken];
        }
        if (isset($GLOBALS['Campsite']['system_preferences'][$keyNewscoopAuthUser])) {
            if (isset($GLOBALS['Campsite']['system_preferences'][$keyNewscoopAuthPwd])) {
                $http_auth_usr = $GLOBALS['Campsite']['system_preferences'][$keyNewscoopAuthUser];
                $http_auth_pwd = $GLOBALS['Campsite']['system_preferences'][$keyNewscoopAuthPwd];
                if ( (!empty($http_auth_usr)) && (!empty($http_auth_pwd)) ) {
                    $http_auth = $http_auth_usr . ':' . $http_auth_pwd;
                }
            }
        }

/*
        // if the cached sys-prefs file would not be used
        require($newscoop_dir.DIRECTORY_SEPARATOR.'conf'.DIRECTORY_SEPARATOR.'database_conf.php');
        $dbAccess = $Campsite['db'];

        $db_host = $dbAccess['host'];
        $db_port = $dbAccess['port'];
        $db_user = $dbAccess['user'];
        $db_pwd = $dbAccess['pass'];
        $db_name = $dbAccess['name'];

        $sqlNewscoopBaseSel = 'SELECT value FROM SystemPreferences WHERE varname = \'' . $keyNewscoopBase . '\'';
        $sqlNewscoopTokenSel = 'SELECT value FROM SystemPreferences WHERE varname = \'' . $keyNewscoopToken . '\'';

        try {
            $dbh = new PDO(
                "mysql:host=$db_host;port=$db_port;dbname=$db_name",
                "$db_user",
                "$db_pwd",
                array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8')
            );

            $sth = $dbh->prepare($sqlNewscoopBaseSel);
            $res = $sth->execute();
            if ($res) {
                while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
                    $base_url = $row['value'];
                }
            }

            $sth = $dbh->prepare($sqlNewscoopTokenSel);
            $res = $sth->execute();
            if ($res) {
                while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
                    $access_token = $row['value'];
                }
            }
        }
        catch (Exception $exc) {
        }
*/
        return array('base_url' => $base_url, 'access_token' => md5($access_token), 'http_auth' => $http_auth);
    } // fn newsimport_take_conf_info

    public static function ImportResponse($p_spec)
    {
        set_time_limit(0);

        self::SetProperBaseDir();
        self::TakeConfInfo();

        $msg = '';
        {
            $news_import_active = SystemPref::Get('NewsImportUsage');
            if (!empty($news_import_active)) {
                $news_imp_file_name = $GLOBALS['g_campsiteDir'].DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'newsimport'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'NewsImport.php';
                if (file_exists($news_imp_file_name)) {
                    require_once($news_imp_file_name);

                    $load_params = array();
                    if (!empty($p_spec)) {
                        foreach (array('newsfeed', 'newslimit', 'newsoffset', 'newsprune') as $one_spec) {
                            if (isset($p_spec[$one_spec])) {
                                $load_params[$one_spec] = $p_spec[$one_spec];
                            }
                        }
                    }

                    $msg = NewsImport::ProcessImportCli($load_params);
                }
            }
        }

        return $msg;
    }

    public static function ImportRequest($p_url, $p_access = null, $p_spec = null)
    {

        $msg = '';
        {
            $news_import_active = SystemPref::Get('NewsImportUsage');
            if (!empty($news_import_active)) {
                $news_imp_file_name = $GLOBALS['g_campsiteDir'].DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'newsimport'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'NewsImport.php';
                if (file_exists($news_imp_file_name)) {
                    require_once($news_imp_file_name);

                    $cron_dir = dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'admin-files'.DIRECTORY_SEPARATOR.'newsimport'.DIRECTORY_SEPARATOR.'cron';
                    $command = escapeshellcmd($cron_dir.DIRECTORY_SEPARATOR.'cli_import.php') . ' --';

                    $load_params = array();
                    if (!empty($p_spec)) {
                        foreach (array('newsfeed', 'newslimit', 'newsoffset', 'newsprune') as $one_spec) {
                            if (isset($p_spec[$one_spec])) {
                                $load_params[$one_spec] = $p_spec[$one_spec];
                                $command .= ' --' . escapeshellarg($one_spec) . '=' . escapeshellarg($p_spec[$one_spec]);
                            }
                        }
                    }

                    $var_output = array();
                    $var_return = 0;
                    exec($command, $var_output, $var_return);

                    $msg = implode("\n", $var_output);
                    //$msg = NewsImport::ProcessImportCli($load_params);
                }
            }
        }

        return $msg;
    }

    /**
     * making import requests
     *
     * @return void
     */
    public static function AskForImport() {
        set_time_limit(0);

        $conf_info = self::TakeConfInfo();
        $http_auth = $conf_info['http_auth'];

        $request_url_bare = $conf_info['base_url'];
        if ('/' != $request_url_bare[strlen($request_url_bare)-1]) {
            $request_url_bare .= '/';
        }
        $request_url_bare .= '_newsimport/?';

        $one_limit = 100;
        //$one_limit = 5;
        $request_url_bare .= 'newsauth=' . urlencode($conf_info['access_token']);
        $request_url_bare_prune = $request_url_bare . '&newsprune=1';

        $request_url = $request_url_bare . '&newslimit=' . $one_limit;
        $request_count = 500;
        //$request_count = 1;
        $request_offsets = array(0);
        for ($ind = 1; $ind <= $request_count; $ind++) {
            $request_offsets[] = $ind * $one_limit;
        }

        foreach (array('events_1', 'movies_1') as $one_feed) {
            $request_feed = $request_url . '&newsfeed=' . $one_feed;
            $request_feed_bare = $request_url_bare . '&newsfeed=' . $one_feed;
            $request_feed_prune = $request_url_bare_prune . '&newsfeed=' . $one_feed;

            $req_rank = -1;
            foreach ($request_offsets as $one_offset) {
                $one_limit_use = $one_limit;
                //sleep(1);
                $req_rank += 1;
                $request_feed_use = $request_feed;
                if ($req_rank == $request_count) {
                    $request_feed_use = $request_feed_bare;
                    $one_limit_use = 0;
                }
                try {
                    $one_request = $request_feed_use . '&newsoffset=' . $one_offset;
                    //echo $one_request . "\n";
                    $req_spec = array(
                        'newsfeed' => $one_feed,
                        'newslimit' => $one_limit_use,
                        'newsoffset' => $one_offset,
                        'newsprune' => 0,
                    );
                    $response = self::ImportRequest($one_request, $http_auth, $req_spec);
                    //var_dump($response);
                    if (!is_string($response)) {
                        break;
                    }
                    if (false !== stristr($response, $one_feed.':none')) {
                        break;
                    }
                    if (false !== stristr($response, 'newsimport_locked')) {
                        break;
                    }
                }
                catch (Exception $exc) {
                    //var_dump($exc);
                    break;
                }
            }

            //sleep(1);
            try {
                $one_request = $request_feed_prune;
                //echo $one_request . "\n";
                    $req_spec = array(
                        'newsfeed' => $one_feed,
                        'newsprune' => 1,
                    );
                $response = self::ImportRequest($one_request, $http_auth, $req_spec);
                //var_dump($response);
            }
            catch (Exception $exc) {}
        }

    } // fn newsimport_ask_for_import


	/**
	 * checks whether we can start a job
	 *
     * @param string $p_lockDir
	 * @return bool
	 */
    public static function Start($p_lockDir)
    {
        $max_diff_new = 12; // max hours for taking the lock as a real one

        // stop, if some worker running; return false
        $working_path = $p_lockDir . self::$s_working;
        $working_path_date = $p_lockDir . self::$s_working_date;

        $lock_file = null;

        try {
            $lock_file = fopen($working_path, 'a');
        }
        catch (Exception $exc) {
            return false;
        }
        if (!$lock_file) {
            return false;
        }

        $locked = flock($lock_file, LOCK_EX); // the LOCK_NB does not work, see https://bugs.php.net/bug.php?id=54453
        if (!$locked) {
            fclose($lock_file);
            return false;
        }

        $is_free = true;

        if (is_file($working_path_date)) {
            try {
                $fp_date = fopen($working_path_date, 'rb');
                $last_date = fgets($fp_date);
                fclose($fp_date);
                if (!empty($last_date)) {
                    $last_date_obj = new DateTime($last_date);
                    $curr_date_obj = new DateTime('now');
                    $date_diff = $curr_date_obj->diff($last_date_obj);
                    if (false !== $date_diff->days) {
                        $diff_hours = (24 * $date_diff->days) + $date_diff->h;
                        if ($max_diff_new >= $diff_hours) {
                            $is_free = false;
                        }
                    }
                }
            }
            catch (Exception $exc) {}
        }
        if ($is_free) {
            try {
                $fp_date = fopen($working_path_date, 'wb');
                set_file_buffer($fp_date,0);
                fwrite($fp_date, date('c') . "\n");
                fflush($fp_date);
                fclose($fp_date);
            }
            catch (Exception $exc) {}
        }

        flock($lock_file, LOCK_UN);
        fclose($lock_file);

        return $is_free;

    } // fn start

	/**
	 * closes a job
	 *
     * @param string $p_lockDir
	 * @return bool
	 */
    public static function Stop($p_lockDir)
    {

        $working_path = $p_lockDir . self::$s_working;
        $working_path_date = $p_lockDir . self::$s_working_date;

        $lock_file = fopen($working_path, 'a');
        $locked = flock($lock_file, LOCK_EX); // the LOCK_NB does not work, see https://bugs.php.net/bug.php?id=54453
        if (!$locked) {
            fclose($lock_file);
            return false;
        }

        try {
            $fp_date = fopen($working_path_date, 'wb');
            fwrite($fp_date, '');
            fflush($fp_date);
            fclose($fp_date);
        }
        catch (Exception $exc) {}

        flock($lock_file, LOCK_UN);
        fclose($lock_file);

        return true;

    } // fn stop

    public static function GetLockDir()
    {
        $plugin_dir = $GLOBALS['g_campsiteDir'].DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'newsimport';
        $incl_dir = $plugin_dir.DIRECTORY_SEPARATOR.'include';
        require($incl_dir.DIRECTORY_SEPARATOR.'default_spool.php');

        return $newsimport_default_locks;
    }

	/**
	 * checks whether we can start a job
	 *
     * @param string $p_path
     * @param bool $p_ending
	 * @return string
	 */
    public static function AbsolutePath($p_path, $p_ending = true)
    {
        if ($p_ending) {
            if ( DIRECTORY_SEPARATOR != substr($p_path, (strlen($p_path) - strlen(DIRECTORY_SEPARATOR))) ) {
                $p_path .= DIRECTORY_SEPARATOR;
            }
        }

        // trying to make global path from possibly relative path
        if ( substr($p_path, 0, strlen(DIRECTORY_SEPARATOR)) != DIRECTORY_SEPARATOR ) {
            if (substr($p_path, 0, 2) != substr($GLOBALS['g_campsiteDir'], 0, 2)) {
                $p_path = $GLOBALS['g_campsiteDir'].DIRECTORY_SEPARATOR.$p_path;
            }
        }

        return $p_path;
    } // fn absolutePath

}
