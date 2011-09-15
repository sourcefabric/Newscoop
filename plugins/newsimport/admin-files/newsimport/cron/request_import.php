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

    if (isset($GLOBALS['Campsite']['system_preferences'][$keyNewscoopBase])) {
        $base_url = $GLOBALS['Campsite']['system_preferences'][$keyNewscoopBase];
    }
    if (isset($GLOBALS['Campsite']['system_preferences'][$keyNewscoopToken])) {
        $access_token = $GLOBALS['Campsite']['system_preferences'][$keyNewscoopToken];
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

    return array('base_url' => $base_url, 'access_token' => md5($access_token));
} // fn newsimport_take_conf_info

/**
 * making import requests
 *
 * @return void
 */
function newsimport_ask_for_import() {
    set_time_limit(0);

    $conf_info = newsimport_take_conf_info();

    $request_url = $conf_info['base_url'];
    if ('/' != $request_url[strlen($request_url)-1]) {
        $request_url .= '/';
    }
    $request_url .= '_newsimport/?';
    //&newsfeed=events_1

    $one_limit = 500;
    //$one_limit = 5;
    $request_url .= 'newsauth=' . urlencode($conf_info['access_token']);
    $request_url_prune = $request_url . '&newsprune=1';

    $request_url .=  '&newslimit=' . $one_limit;
    $request_count = 100;
    $request_offsets = array(0);
    for ($ind = 1; $ind <= $request_count; $ind++) {
        $request_offsets[] = $ind * $one_limit;
    }

    foreach ($request_offsets as $one_offset) {
        //sleep(1);
        try {
            $one_request = $request_url . '&newsoffset=' . $one_offset;
            //echo $one_request . "\n";
            file_get_contents($one_request);
        }
        catch (Exception $exc) {}
    }

    //sleep(1);
    try {
        $one_request = $request_url_prune;
        //echo $one_request . "\n";
        file_get_contents($one_request);
    }
    catch (Exception $exc) {}

} // fn newsimport_ask_for_import

newsimport_ask_for_import();

?>