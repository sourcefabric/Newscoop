#!/usr/bin/env php
<?php

/*
$cli_load_path = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'start_req.php';
require_once($cli_load_path);
*/

if (!isset($GLOBALS['g_campsiteDir'])) {
    $GLOBALS['g_campsiteDir'] = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))));
}

if (!isset($GLOBALS['g_cliInited'])) {

    function plugin_newsimport_boot_cli ()
    {
        if (php_sapi_name() != 'cli') {
            return false;
        }
        // Define path to application directory
        defined('APPLICATION_PATH')
            || define('APPLICATION_PATH', $GLOBALS['g_campsiteDir'].DIRECTORY_SEPARATOR.'application');

        // Define application environment
        defined('APPLICATION_ENV')
            || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'development'));

        //$incl_dir = $GLOBALS['g_campsiteDir'].DIRECTORY_SEPARATOR.'include';
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

        //require_once dirname(__FILE__) . '/newscoop_bootstrap.php';
        //require_once $CAMPSITE_DIR . '/classes/CampPlugin.php';

        return true;
    }

    $GLOBALS['g_cliInited'] = true;

    plugin_newsimport_boot_cli();
}


/**
 * taking confs for import requests
 *
 * @return array
 */
function newsimport_take_conf_info() {
    $plugin_dir = dirname(dirname(dirname(dirname(__FILE__)))).DIRECTORY_SEPARATOR;

    $incl_dir = $plugin_dir.'include'.DIRECTORY_SEPARATOR;
    require($incl_dir . 'default_access.php');

    $base_url = 'http://localhost';
    $access_token = $newsimport_default_access;
    $http_auth = '';

    if (!isset($GLOBALS['Campsite'])) {
        $GLOBALS['Campsite'] = array();
    }
    global $Campsite;

    $newscoop_dir = dirname(dirname($plugin_dir));
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

function newsimport_import_request($p_url, $p_access = null, $p_spec = null)
{

    //$plug_dir = dirname(dirname(dirname(dirname(__FILE__))));
    //require_once($plug_dir.DIRECTORY_SEPARATOR.'newsimport.info.php');

    $msg = '';
    //plugin_newsimport_test();
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

    //$news_import_only = false;
    //NewsImport::ProcessImport($news_import_only);

    return $msg;

/*
    // the http request way
    $c_hnd = curl_init($p_url);
    if (!empty($p_access)) {
        curl_setopt($c_hnd, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($c_hnd, CURLOPT_USERPWD, $p_access);
    }
    curl_setopt($c_hnd, CURLOPT_HEADER, 0);
    curl_setopt($c_hnd, CURLOPT_TIMEOUT, 0);
    curl_setopt($c_hnd, CURLOPT_RETURNTRANSFER, 1);

    $c_res = @curl_exec($c_hnd);

    return $c_res;
*/
}

/**
 * making import requests
 *
 * @return void
 */
function newsimport_ask_for_import() {
    set_time_limit(0);

    $conf_info = newsimport_take_conf_info();
    $http_auth = $conf_info['http_auth'];

    $request_url_bare = $conf_info['base_url'];
    if ('/' != $request_url_bare[strlen($request_url_bare)-1]) {
        $request_url_bare .= '/';
    }
    $request_url_bare .= '_newsimport/?';

    $one_limit = 500;
    //$one_limit = 5;
    $request_url_bare .= 'newsauth=' . urlencode($conf_info['access_token']);
    $request_url_bare_prune = $request_url_bare . '&newsprune=1';

    $request_url = $request_url_bare . '&newslimit=' . $one_limit;
    $request_count = 100;
    //$request_count = 1;
    $request_offsets = array(0);
    for ($ind = 1; $ind <= $request_count; $ind++) {
        $request_offsets[] = $ind * $one_limit;
    }

    foreach (array('events_1', 'movies_1') as $one_feed) {
    //foreach (array('movies_1') as $one_feed) {
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
                $response = newsimport_import_request($one_request, $http_auth, $req_spec);
                //var_dump($response);
                if (!is_string($response)) {
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
                    'newsprune' => 1,
                );
            $response = newsimport_import_request($one_request, $http_auth, $req_spec);
            //var_dump($response);
        }
        catch (Exception $exc) {}
    }

} // fn newsimport_ask_for_import

newsimport_ask_for_import();

?>