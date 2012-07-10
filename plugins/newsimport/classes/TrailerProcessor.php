<?php

/*

cron outside import 1:
  * look into db for videos that should be downloaded
  * take them one by one:
      * download them, put into a dir, name by movie key
      * fill video table, set state as to_upload


cron outside import 2:
  * look into db for videos that should be uploaded
  * take one by one, cycle:
      * upload into vimeo
      * fill vimeo id into db

*/

$class_dir = dirname(__FILE__);

$vimeolib_path = $class_dir . DIRECTORY_SEPARATOR . 'vimeolib' . DIRECTORY_SEPARATOR . 'vimeo.php';
require_once($vimeolib_path);

class TrailerProcessor {

    private static $s_table_name = 'trailers';

    private static $s_max_run = 100;
    //private static $s_max_run = 10;
    //private static $s_max_run = 2;
    private static $s_max_errors = 5;

    private static $s_dir_mode = 0755;

    public function trailerDbExists($p_dbPath)
    {
        if (!file_exists($p_dbPath)) {
            $pre_db_file = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'trailers_info.sqlite';
            if (file_exists($pre_db_file)) {
                copy($pre_db_file, $p_dbPath);
            }
        }

        $path_mode = self::$s_dir_mode;

        $table_name = self::$s_table_name;

        $db_dir = dirname($p_dbPath);
        if (!is_dir($db_dir)) {
            mkdir($db_dir, $path_mode, true);
        }

        $queryStr_cre = 'CREATE TABLE IF NOT EXISTS ' . $table_name . ' (
            movie_key TEXT PRIMARY KEY,
            vimeo_id TEXT DEFAULT "",
            source_timestamp INTEGER DEFAULT 0,
            source_url TEXT DEFAULT "",
            local_name TEXT DEFAULT "",
            video_codec TEXT DEFAULT "",
            video_width TEXT DEFAULT "",
            video_height TEXT DEFAULT "",
            state TEXT DEFAULT "",
            error_count INTEGER DEFAULT 0
        )';


        @$db = new PDO ('sqlite:' . $p_dbPath);
        $stmt = $db->prepare($queryStr_cre);
        $res = $stmt->execute();
        if (!$res) {
            return false;
        }

        if (!is_file($p_dbPath)) {
            return false;
        }

        return true;
    }


    public function downloadOneTrailer($p_moviesDatabase, $p_localDir)
    {
        $sqlite_name = $p_moviesDatabase;
        $table_name = self::$s_table_name;
        //$max_error_count = $s_max_errors;

        $queryStr_sel = 'SELECT movie_key, source_url, source_timestamp, error_count FROM ' . $table_name . ' WHERE state = "to_download" ';
        if (0 < self::$s_max_errors) {
            $queryStr_sel .= 'AND error_count <= ' . self::$s_max_errors . ' ';
        }
        $queryStr_sel .= 'ORDER BY movie_key LIMIT 1';

        $queryStr_upd = 'UPDATE ' . $table_name . ' SET local_name = :local_name, state = "to_upload", error_count = 0 WHERE movie_key = :movie_key';
        $queryStr_err = 'UPDATE ' . $table_name . ' SET error_count = :error_count WHERE movie_key = :movie_key';

        $movie_key = null;
        $source_url = '';
        $source_timestamp = '';
        $error_count = 0;
        $local_name = '';
        $video_suffix = '';

        @$db = new PDO ('sqlite:' . $sqlite_name);
        $stmt = $db->prepare($queryStr_sel);
        $res = $stmt->execute();
        if ($res) {
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($res) {
                $movie_key = '' . $res['movie_key'];
                $source_url = trim('' . $res['source_url']);
                $source_timestamp = 0 + $res['source_timestamp'];
                $error_count = 0 + $res['error_count'];
            }
        }

        if (empty($movie_key) || empty($source_url)) {
            return false;
        }

        $max_suffix_len = 10;
        $other_suffixes = array('php', 'js', 'jsp', 'html', 'htm', 'css', 'txt', 'json', 'xml', 'rss');
        $source_url_arr = explode('.', $source_url);
        if (1 < count($source_url_arr)) {
            $source_url_end = trim(strtolower($source_url_arr[count($source_url_arr) - 1]));
            if ((0 < strlen($source_url_end)) && (strlen($source_url_end) <= $max_suffix_len)) {
                if (!in_array($source_url_end, $other_suffixes)) {
                    $video_suffix = '.' . $source_url_end;
                }
            }
        }

        $source_timedate = gmdate('Y-m-d_H-i-s', $source_timestamp);
        $local_name = $movie_key . '_' . $source_timedate . $video_suffix;

        $file_store_path = $p_localDir . DIRECTORY_SEPARATOR . $local_name;

        try {
            file_put_contents($file_store_path, file_get_contents($source_url));
        }
        catch (Exception $exc) {
            $error_count += 1;
            $stmt = $db->prepare($queryStr_err);

            $stmt->bindParam(':movie_key', $movie_key, PDO::PARAM_STR);
            $stmt->bindParam(':error_count', $error_count, PDO::PARAM_INT);

            $res = $stmt->execute();

            return false;
        }

        $stmt = $db->prepare($queryStr_upd);

        $stmt->bindParam(':movie_key', $movie_key, PDO::PARAM_STR);
        $stmt->bindParam(':local_name', $local_name, PDO::PARAM_STR);

        $res = $stmt->execute();

        return true;
    }

/*
    public function downloadAllTrailers($p_moviesDatabase, $p_saveDir)
    {

        while (true) {
            $left = downloadOneTrailer();
            if (!$left) {
                break;
            }

        }


    }
*/


    public function uploadOneTrailer($p_moviesDatabase, $p_localDir, $p_chunkDir, $p_vimeoAccess)
    {
        $sqlite_name = $p_moviesDatabase;
        $table_name = self::$s_table_name;

        $movie_key = null;
        $local_name = '';
        $vimeo_id = '';
        $error_count = 0;
        $state = '';

        $queryStr_sel = 'SELECT movie_key, local_name, vimeo_id, error_count, state FROM ' . $table_name . ' WHERE state IN ("to_upload", "to_title") ';
        if (0 < self::$s_max_errors) {
            $queryStr_sel .= 'AND error_count <= ' . self::$s_max_errors . ' ';
        }
        $queryStr_sel .= 'ORDER BY movie_key LIMIT 1';

        $queryStr_par = 'UPDATE ' . $table_name . ' SET vimeo_id = :vimeo_id, state = "to_title" WHERE movie_key = :movie_key';
        $queryStr_upd = 'UPDATE ' . $table_name . ' SET vimeo_id = :vimeo_id, state = "to_use", error_count = 0 WHERE movie_key = :movie_key';
        $queryStr_err = 'UPDATE ' . $table_name . ' SET error_count = :error_count WHERE movie_key = :movie_key';

        // take the info from db, with $queryStr_sel
        @$db = new PDO ('sqlite:' . $sqlite_name);
        $stmt = $db->prepare($queryStr_sel);
        $res = $stmt->execute();
        if (!$res) {
            return false;
        }
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        if (empty($res)) {
            return false;
        }
        $movie_key = '' . $res['movie_key'];
        $local_name = '' . $res['local_name'];
        $vimeo_id = '' . $res['vimeo_id'];
        $error_count = 0 + $res['error_count'];
        $state = '' . $res['state'];

        if (empty($movie_key) || empty($local_name)) {
            return false;
        }

        $consumer_key = $p_vimeoAccess['consumer_key'];
        $consumer_secret = $p_vimeoAccess['consumer_secret'];
        $access_token = $p_vimeoAccess['access_token'];
        $access_token_secret = $p_vimeoAccess['access_token_secret'];

        $file_local_path = $p_localDir . DIRECTORY_SEPARATOR . $local_name;
        $vimeo_obj = new phpVimeo($consumer_key, $consumer_secret, $access_token, $access_token_secret);


        $replace_id = null;
        if (!empty($vimeo_id)) {
            $replace_id = 0 + $vimeo_id;
        }
        try {
            if ('to_upload' == $state) {
                //$vimeo_id = $vimeo_obj->upload($file_local_path, true, $p_chunkDir, 2097152, $replace_id);
                $vimeo_id = $vimeo_obj->upload($file_local_path, false, $p_chunkDir, 2097152, $replace_id);
            }
        }
        catch (Exception $exc) {
//echo "\n";
//echo "exception:\n";
//var_dump($exc);
//echo "\n";

            $error_count += 1;
            $stmt = $db->prepare($queryStr_err);

            $stmt->bindParam(':movie_key', $movie_key, PDO::PARAM_STR);
            $stmt->bindParam(':error_count', $error_count, PDO::PARAM_INT);

            $res = $stmt->execute();

            return false;
        }

        if (empty($vimeo_id)) {
            return false;
        }

        $title_set = false;
        $title_set_max_attempts = 10;
        $title_set_cur_run = 0;

        while (true) {
            $title_set_cur_run += 1;
            if ($title_set_cur_run > $title_set_max_attempts) {
                break;
            }

            try {
                $vimeo_obj->call('vimeo.videos.setTitle', array('title' => $movie_key, 'video_id' => $vimeo_id));
                $title_set = true;
            }
            catch (Exception $exc) {
                $title_set = false;
            }
            if ($title_set) {
                break;
            }
            sleep(10);
        }

        $vimeo_id = '' . $vimeo_id;

        if (('to_title' == $state) && (!$title_set)) {
            $error_count += 1;
            $stmt = $db->prepare($queryStr_err);

            $stmt->bindParam(':movie_key', $movie_key, PDO::PARAM_STR);
            $stmt->bindParam(':error_count', $error_count, PDO::PARAM_INT);

            $res = $stmt->execute();

            return false;
        }

        $queryStr_cur = $queryStr_par;
        if ($title_set) {
            $queryStr_cur = $queryStr_upd;
        }

        // put the vimeo_id into db, with $queryStr_upd
        //$db->beginTransaction();
        $stmt = $db->prepare($queryStr_cur);
        $stmt->bindParam(':movie_key', $movie_key, PDO::PARAM_STR);
        $stmt->bindParam(':vimeo_id', $vimeo_id, PDO::PARAM_STR);
        $res = $stmt->execute();
        if (!$res) {
            return false;
        }
        //$db->commit();

        try {
            unlink($file_local_path);
        }
        catch (Exception $exc) {
        }

        return true;
    }

/*
    public function uploadAllVideos()
    {
        while (true) {
            $uploaded = uploadOneVideo();
            if (!$uploaded) {
                break;
            }
        }

    }
*/

    public function someLeft($p_moviesDatabase, $p_mode) {

        $known_modes = array('download' => 'to_download', 'upload' => 'to_upload');
        if (!array_key_exists($p_mode, $known_modes)) {
            return false;
        }
        $mode_state = $known_modes[$p_mode];

        $table_name = self::$s_table_name;
        $sqlite_name = $p_moviesDatabase;

        $sel_req = 'SELECT count(*) AS count_left FROM ' . $table_name . ' WHERE state = "' . $mode_state . '"';
        if (0 < self::$s_max_errors) {
            $sel_req .= ' AND error_count <= ' . self::$s_max_errors . '';
        }

        $count_left = 0;

        @$db = new PDO ('sqlite:' . $sqlite_name);
        $stmt = $db->prepare($sel_req);
        $res = $stmt->execute();
        if ($res) {
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$res) {
                return false;
            }
            $count_left = $res['count_left'];
        }

        if (empty($count_left)) {
            return false;
        }

        return true;
    }

    public function getVimeoAccess()
    {


        $vimeo_access_info = array(
            'consumer_key' => '',
            'consumer_secret' => '',
            'access_token' => '',
            'access_token_secret' => '',
        );

        require_once($GLOBALS['g_campsiteDir'].DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'SystemPref.php');

        $vimeo_access_key = SystemPref::Get('NewsImportVimeoAccessKey');
        $vimeo_access_secret = SystemPref::Get('NewsImportVimeoAccessSecret');

        if (!empty($vimeo_access_key)) {
            $vimeo_access_info['consumer_key'] = trim('' . $vimeo_access_key);
        }
        if (!empty($vimeo_access_secret)) {
            $vimeo_access_info['consumer_secret'] = trim('' . $vimeo_access_secret);
        }

        $vimeo_access_token = SystemPref::Get('NewsImportVimeoAccessToken');
        $vimeo_access_token_secret = SystemPref::Get('NewsImportVimeoAccessTokenSecret');

        if (!empty($vimeo_access_token)) {
            $vimeo_access_info['access_token'] = trim('' . $vimeo_access_token);
        }
        if (!empty($vimeo_access_token_secret)) {
            $vimeo_access_info['access_token_secret'] = trim('' . $vimeo_access_token_secret);
        }

        return $vimeo_access_info;



/*
    The code below is just for taking an access token, if the current one expired
*/

        $vimeo_obj = new phpVimeo($vimeo_access_key, $vimeo_access_secret);

        $request_token = array('oauth_token' => '', 'oauth_token_secret' => '');
        $request_token = $vimeo_obj->getRequestToken();
//echo "request token:\n";
//var_dump($request_token);
//echo "\n";

        $vimeo_obj->setToken($request_token['oauth_token'], $request_token['oauth_token_secret']);
        $auth_url = $vimeo_obj->getAuthorizeUrl($request_token['oauth_token'], 'write');
/*
echo "\n";
echo "AUTH URL:\n";
var_dump($auth_url);
echo "\n";
exit(1);
*/
        $verifier = '';
        $access_token = $vimeo_obj->getAccessToken($verifier);
/*
echo "access token:\n";
var_dump($access_token);
echo "\n";
exit(1);
*/

    }

    public function cleanTrailers($p_localDir)
    {
        $days_old = 30;

        $dir_to_clean = $p_localDir;
        if (empty($dir_to_clean) || !is_dir($dir_to_clean)) {
            return;
        }

        $file_name_set = scandir($dir_to_clean);
        if (empty($file_name_set)) {
            return;
        }

        $current_time = time();
        $threshold_time = $current_time - ($days_old * 24 * 60 * 60);

        foreach ($file_name_set as $one_file_name) {
            $check_path = $dir_to_clean . DIRECTORY_SEPARATOR . $one_file_name;
            if (!is_file($check_path)) {
                continue;
            }

            $last_modified = filemtime($check_path);
            if ($threshold_time > $last_modified) {
                try {
                    unlink($check_path);
                }
                catch (Exception $exc) {
                    continue;
                }
            }

        }

    }

    public static function AskForTrailers()
    {
        // process upto $max (... 100) trailers at one run

        $incl_dir = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'include';
        $defspool_path = $incl_dir . DIRECTORY_SEPARATOR . 'default_spool.php';
        require($defspool_path);
        // $newsimport_default_cache;

        $cache_dir = NewsImportEnv::AbsolutePath($newsimport_default_cache, true);

        $local_trailer_dir = $cache_dir . 'trailers';
        //$trailres_db_path = $cache_dir . 'movies_info.sqlite';
        $trailres_db_path = $cache_dir . 'trailers_info.sqlite';
        $local_chunk_dir = $cache_dir . 'trailers' . DIRECTORY_SEPARATOR . 'chunks';

        $max_run = self::$s_max_run;

        $trail_proc = new TrailerProcessor();
        $trail_proc->trailerDbExists($trailres_db_path);

        $vimeo_access_info = $trail_proc->getVimeoAccess();

        $dir_mode = self::$s_dir_mode;
        try {
            mkdir($local_trailer_dir, $dir_mode, true);
        }
        catch (Exception $exc) {
        }
        try {
            mkdir($local_chunk_dir, $dir_mode, true);
        }
        catch (Exception $exc) {
        }

        $cur_run = 0;
        while (true) {
            //break;
            $cur_run += 1;
            if ($cur_run > $max_run) {
                break;
            }

            $trail_proc->downloadOneTrailer($trailres_db_path, $local_trailer_dir);
            if (!$trail_proc->someLeft($trailres_db_path, 'download')) {
                break;
            }

        }

        if (empty($vimeo_access_info['consumer_key']) || empty($vimeo_access_info['consumer_secret'])) {
            return;
        }
        if (empty($vimeo_access_info['access_token']) || empty($vimeo_access_info['access_token_secret'])) {
            return;
        }

        $cur_run = 0;
        while (true) {
            $cur_run += 1;
            if ($cur_run > $max_run) {
                break;
            }

            $res = $trail_proc->uploadOneTrailer($trailres_db_path, $local_trailer_dir, $local_chunk_dir, $vimeo_access_info);
            if (!$res) {
                sleep(30);
            }

            if (!$trail_proc->someLeft($trailres_db_path, 'upload')) {
                break;
            }

        }

        $trail_proc->cleanTrailers($local_trailer_dir);

    }



}


