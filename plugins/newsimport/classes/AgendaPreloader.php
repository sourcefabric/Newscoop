<?php

class AgendaPreloader {

    private static $s_http_protocol = 'http';
    private static $s_web_subdir = '';

    private static $s_publication_id = 1;
    private static $s_language_id = 5;

    private static $s_sleep_secs = 1;

    public static function PreloadMovies($p_reqInfo)
    {
        $req_url_start = $p_reqInfo['url_start'];

        $auth_user = $p_reqInfo['auth_user'];
        $auth_pass = $p_reqInfo['auth_pass'];

        $use_dates = array(date('Y-m-d'), date('Y-m-d', (time()+86400)));
        $use_regions = array('kanton-basel-stadt', 'kanton-basel-landschaft', 'region-basel');

        foreach ($use_dates as $cur_date) {
            foreach ($use_regions as $cur_region) {
                $req_url = $req_url_start . '/kino?load=1&type=kino&date=' . $cur_date . '&period=1&region=' . $cur_region;

                $curlHandle = curl_init($req_url);

                if ((!empty($auth_user)) && (!empty($auth_pass))) {
                    curl_setopt($curlHandle, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
                    curl_setopt($curlHandle, CURLOPT_USERPWD, $auth_user . ':' . $auth_pass);
                }

                curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curlHandle, CURLOPT_BINARYTRANSFER, true);
                curl_setopt($curlHandle, CURLOPT_NOSIGNAL, true);
                curl_setopt($curlHandle, CURLOPT_TIMEOUT_MS, 25000);

                $response = curl_exec($curlHandle);
                curl_close($curlHandle);

                if (!empty(self::$s_sleep_secs)) {
                    sleep(self::$s_sleep_secs);
                }
            }
        }
    }

    public static function Preload()
    {
        $ret_code_false = false;

        require_once($GLOBALS['g_campsiteDir'].DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'SystemPref.php');
        require_once($GLOBALS['g_campsiteDir'].DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'Publication.php');
        require_once($GLOBALS['g_campsiteDir'].DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'Alias.php');
        require_once($GLOBALS['g_campsiteDir'].DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'Language.php');
        require_once($GLOBALS['g_campsiteDir'].DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'Issue.php');

        $auth_user = '' . \SystemPref::Get('NewsImportHttpAuthUser');
        $auth_pass = '' . \SystemPref::Get('NewsImportHttpAuthPwd');

        $cur_publ_obj = new \Publication(self::$s_publication_id);
        if (empty($cur_publ_obj)) {
            return $ret_code_false;
        }
        if (!$cur_publ_obj->exists()) {
            return $ret_code_false;
        }
        $cur_alias_id = $cur_publ_obj->getDefaultAliasId();

        $cur_alias_obj = new \Alias($cur_alias_id);
        if (empty($cur_alias_obj)) {
            return $ret_code_false;
        }
        if (!$cur_alias_obj->exists()) {
            return $ret_code_false;
        }
        $cur_alias_name = $cur_alias_obj->getName();

        $cur_lang_obj = new \Language(self::$s_language_id);
        $cur_lang_code = $cur_lang_obj->getCode();

        $cur_issue_obj = \Issue::GetCurrentIssue(self::$s_publication_id, self::$s_language_id);
        if (empty($cur_issue_obj)) {
            return $ret_code_false;
        }
        if (!$cur_issue_obj->exists()) {
            return $ret_code_false;
        }
        $cur_issue_url_name = $cur_issue_obj->getUrlName();

        $req_url_start = self::$s_http_protocol . '://' . $cur_alias_name;
        if (!empty(self::$s_web_subdir)) {
            $req_url_start .= self::$s_web_subdir;
        }
        $req_url_start .= '/' . $cur_lang_code . '/' . $cur_issue_url_name;

        $req_info = array(
            'url_start' => $req_url_start,
            'auth_user' => $auth_user,
            'auth_pass' => $auth_pass,
        );

        $ret = self::PreloadMovies($req_info);

        return $ret;
    }

}


