#!/usr/bin/env php
<?php

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

function newsimport_import_request($p_url, $p_access = null)
{
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
    $request_url_bare .= 'newsauth=' . urlencode($conf_info['access_token']);
    $request_url_bare_prune = $request_url_bare . '&newsprune=1';

    $request_url = $request_url_bare . '&newslimit=' . $one_limit;
    $request_count = 100;
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
            //sleep(1);
            $req_rank += 1;
            $request_feed_use = $request_feed;
            if ($req_rank == $request_count) {
                $request_feed_use = $request_feed_bare;
            }
            try {
                $one_request = $request_feed_use . '&newsoffset=' . $one_offset;
                //echo $one_request . "\n";
                $response = newsimport_import_request($one_request, $http_auth);
                if (!is_string($response)) {
                    break;
                }
                if (false !== stristr($response, 'newsimport_locked')) {
                    break;
                }
            }
            catch (Exception $exc) {
                break;
            }
        }

        //sleep(1);
        try {
            $one_request = $request_feed_prune;
            //echo $one_request . "\n";
            $response = newsimport_import_request($one_request, $http_auth);
        }
        catch (Exception $exc) {}
    }

} // fn newsimport_ask_for_import

newsimport_ask_for_import();

?>