#!/usr/bin/env php
<?php

function newsimport_take_conf_info() {
    $plugin_dir = dirname(dirname(dirname(dirname(__FILE__)))).DIRECTORY_SEPARATOR;

    $incl_dir = $plugin_dir.'include'.DIRECTORY_SEPARATOR;
    //require($incl_dir . 'news_feeds_intall.php');
    require($incl_dir . 'default_access.php');

    $base_url = 'http://localhost';
    $access_token = $newsimport_default_access;

    if (!isset($GLOBALS['Campsite'])) {
        $GLOBALS['Campsite'] = array();
    }

    $newscoop_dir = dirname(dirname($plugin_dir));
    //require($newscoop_dir.DIRECTORY_SEPARATOR.'conf'.DIRECTORY_SEPARATOR.'database_conf.php');
    require($newscoop_dir.DIRECTORY_SEPARATOR.'system_preferences.php');

    $keyNewscoopBase = 'NewsImportBaseUrl';
    $keyNewscoopToken = 'NewsImportCommandToken';

    if (isset($GLOBALS['Campsite'][$keyNewscoopBase])) {
        $base_url = $GLOBALS['Campsite'][$keyNewscoopBase];
    }
    if (isset($GLOBALS['Campsite'][$keyNewscoopToken])) {
        $access_token = $GLOBALS['Campsite'][$keyNewscoopToken];
    }

/*
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

    $return array('base_url' => $base_url, 'access_token' => $access_token);
}

function newsimport_ask_for_import() {
    set_time_limit(0);

    //$incl_dir = dirname(dirname(dirname(dirname(__FILE__)))).DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR;
    //require($incl_dir . 'default_access.php');
    //require($incl_dir . 'news_feeds_intall.php');

    $conf_info = newsimport_take_conf_info();

    //$request_url = $newsipmort_install;
    //$request_url = newsimport_take_base_url();

    $request_url = $conf_info['base_url'];
    if ('/' != $request_url[strlen($request_url)-1]) {
        $request_url .= '/';
    }
    $request_url .= '_newsimport/?';
    //&newsfeed=events_1

    $one_limit = 500;
    $request_url .= 'newsauth=' . urlencode($conf_info['access_token']) . '&newslimit=' . $one_limit;

    $request_offsets = array(0);
    for ($ind = 1; $ind <= 20; $ind++) {
        $request_offsets[] = $ind * $one_limit;
    }

    foreach ($request_offsets as $one_offset) {
        try {
            $one_request = $request_url . '&newsoffset=' . $one_offset;
            echo $one_request . "\n";
            file_get_contents($one_request);
        }
        catch (Exception $exc) {}
    }

    //$fh = fopen('/tmp/d006', 'a');
    //fwrite($fh, "aaa\n");
    //fclose($fh);

}

newsimport_ask_for_import();

?>
