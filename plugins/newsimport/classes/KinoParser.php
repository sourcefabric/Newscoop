<?php

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'FileLoad.php');

/**
 * KinoData Importer class for managing parsing of KinoData files.
 */
class KinoData_Parser {

    /**
     * What is this.
     * @var array
     */
    var $m_source = null;

    /**
     * Where to take this.
     * @var array
     */
    var $m_dirs = null;

    /**
     * Who provides this.
     * @var integer
     */
    var $m_provider = null;

    /**
     * Mode of (possibly) created directories.
     * @var integer
     */
    var $m_dirmode = 0755;

    /**
     * Suffix parts for json data files
     * @var array
     */
    var $m_saved_parts = array('dif' => 'cin_dif', 'all' => 'cin_set');

    /**
     * constructor
     * @param array $p_source
     *
     * @return void
     */
    public function __construct($p_source)
    {
        $this->m_source = $p_source;
        $this->m_dirs = $p_source['source_dirs'];
        $this->m_provider = $p_source['provider_id'];
    } // fn __construct

	/**
	 * (re)moves files after parsing && importing
	 *
	 * @return bool
	 */
    public function cleanup()
    {
        // moving all the files from the interim into the old dir
        $dir_handle = null;
        try {
            $dir_handle = opendir($this->m_dirs['use']);
        }
        catch (Exception $exc) {
            return false;
        }

        if (!$dir_handle) {
            return false;
        }

        while (false !== ($event_file = readdir($dir_handle))) {
            $one_use_path = $this->m_dirs['use'] . DIRECTORY_SEPARATOR . $event_file;
            $one_old_path = $this->m_dirs['old'] . DIRECTORY_SEPARATOR . $event_file;

            if (!is_file($one_use_path)) {
                continue;
            }

            try {
                rename($one_use_path, $one_old_path);
            }
            catch (Exception $exc) {
                continue;
            }
        }
        closedir($dir_handle);

        return true;
    } // fn cleanup

	/**
	 * prepares files for the parsing
     *
     * @param array $p_categories
     * @param array $p_limits
     * @param array $p_cancels
	 *
	 * @return bool
	 */
    public function prepare($p_categories, $p_limits, $p_cancels, $p_env, $p_regionObj, $p_regionTopics)
    {

        // we need that conf info
        if ((!isset($this->m_dirs['source'])) || (!isset($this->m_dirs['source']['programs']))) {
            return false;
        }
        if ((!isset($this->m_dirs['source']['movies'])) || (!isset($this->m_dirs['source']['genres'])) || (!isset($this->m_dirs['source']['timestamps']))) {
            return false;
        }

        foreach (array($this->m_dirs['use'], $this->m_dirs['new'], $this->m_dirs['old']) as $one_dir) {
            if (!is_dir($one_dir)) {
                try {
                    $created = mkdir($one_dir, $this->m_dirmode, true);
                    if (!$created) {
                        return false;
                    }
                }
                catch (Exception $exc) {
                    return false;
                }
            }
        }

        $parser = new KinoData_Parser_SimpleXML($p_regionObj, $p_regionTopics);

        $movies_dir = $this->m_dirs['old'];
        if ( isset($p_env['cache_dir']) && (!empty($p_env['cache_dir'])) ) {
            $movies_dir = $p_env['cache_dir'];
        }
        $sqlite_name = $movies_dir . 'movies_info.sqlite';

        // first copy and use movies files, if any
        // this is an addition wrt the general event import

        $cur_time = date('YmdHis');

        $to_copy_files_movies = array();
        $to_copy_files_genres = array();
        $to_copy_files_timestamps = array();

        $movies_infos_files = array();
        $movies_genres_files = array();
        $movies_links_files = array();

        // if available_movies_main
        $ready_file_movies = $this->m_dirs['new'] . $this->m_dirs['ready']['movies'];
        $ready_file_genres = $this->m_dirs['new'] . $this->m_dirs['ready']['genres'];
        $ready_file_timestamps = $this->m_dirs['new'] . $this->m_dirs['ready']['timestamps'];

        if (file_exists($ready_file_movies)) {

            // copy_movies_main;
            foreach ($this->m_dirs['source']['movies'] as $one_file_glob) {
                $one_path_glob = $this->m_dirs['new'] . $one_file_glob;
                $one_file_set = glob($one_path_glob);
                if (false === $one_file_set) {
                    continue;
                }
                foreach ($one_file_set as $movie_file_path) {
                    if (!is_file($movie_file_path)) {
                        continue;
                    }
                    if (!array_key_exists($movie_file_path, $to_copy_files_movies)) {
                        $to_copy_files_movies[$movie_file_path] = $this->m_dirs['use'] . $cur_time . '-' . basename($movie_file_path);
                    }
                }
            }
            foreach ($to_copy_files_movies as $one_file_src => $one_file_dest) {
                try {
                    rename($one_file_src, $one_file_dest);
                    $movies_infos_files[] = $one_file_dest;
                }
                catch (Exception $exc) {}
            }

            // if available_movies_genres
            $genres_ready = false;
            if (file_exists($ready_file_genres)) {
                $genres_ready = true;

                // copy_movies_genres;
                foreach ($this->m_dirs['source']['genres'] as $one_file_glob) {
                    $one_path_glob = $this->m_dirs['new'] . $one_file_glob;
                    $one_file_set = glob($one_path_glob);
                    if (false === $one_file_set) {
                        continue;
                    }
                    foreach ($one_file_set as $genre_file_path) {
                        if (!is_file($genre_file_path)) {
                            continue;
                        }
                        if (!array_key_exists($genre_file_path, $to_copy_files_genres)) {
                            $to_copy_files_genres[$genre_file_path] = $this->m_dirs['use'] . $cur_time . '-' . basename($genre_file_path);
                        }
                    }
                }
                foreach ($to_copy_files_genres as $one_file_src => $one_file_dest) {
                    try {
                        rename($one_file_src, $one_file_dest);
                        $movies_genres_files[] = $one_file_dest;
                    }
                    catch (Exception $exc) {}
                }

            }
            // if available_movies_timestamps
            $timestamps_ready = false;
            if (file_exists($ready_file_genres)) {
                $timestamps_ready = true;

                //copy_movies_timestamps;
                foreach ($this->m_dirs['source']['timestamps'] as $one_file_glob) {
                    $one_path_glob = $this->m_dirs['new'] . $one_file_glob;
                    $one_file_set = glob($one_path_glob);
                    if (false === $one_file_set) {
                        continue;
                    }
                    foreach ($one_file_set as $timestamp_file_path) {
                        if (!is_file($timestamp_file_path)) {
                            continue;
                        }
                        if (!array_key_exists($timestamp_file_path, $to_copy_files_timestamps)) {
                            $to_copy_files_timestamps[$timestamp_file_path] = $this->m_dirs['use'] . $cur_time . '-' . basename($timestamp_file_path);
                        }
                    }
                }
                foreach ($to_copy_files_timestamps as $one_file_src => $one_file_dest) {
                    try {
                        rename($one_file_src, $one_file_dest);
                        $movies_links_files[] = $one_file_dest;
                    }
                    catch (Exception $exc) {}
                }
            }

            $movies_ready_files = array();
            $movies_ready_files[] = $ready_file_movies;
            if (!in_array($ready_file_genres, $movies_ready_files)) {
                $movies_ready_files[] = $ready_file_genres;
            }
            if (!in_array($ready_file_timestamps, $movies_ready_files)) {
                $movies_ready_files[] = $ready_file_timestamps;
            }
            foreach ($movies_ready_files as $one_ready_file) {
                try {
                    unlink($one_ready_file); // i.e. ci_done.txt
                }
                catch (Exception $exc) {}
            }

            // process_movies_files;
            $parser->updateMoviesInfo($sqlite_name, $movies_infos_files, $movies_genres_files, $movies_links_files);

        }


        $to_copy_files_programs = array();
        $programs_infos_files = array();

        $ready_file_programs = $this->m_dirs['new'] . $this->m_dirs['ready']['programs'];
        if (file_exists($ready_file_programs)) {
            // copy_programs_main;
            foreach ($this->m_dirs['source']['programs'] as $one_file_glob) {
                $one_path_glob = $this->m_dirs['new'] . $one_file_glob;
                $one_file_set = glob($one_path_glob);
                if (false === $one_file_set) {
                    continue;
                }
                foreach ($one_file_set as $program_file_path) {
                    if (!is_file($program_file_path)) {
                        continue;
                    }
                    if (!array_key_exists($program_file_path, $to_copy_files_programs)) {
                        $to_copy_files_programs[$program_file_path] = $this->m_dirs['use'] . $cur_time . '-' . basename($program_file_path);
                    }
                }
            }
            foreach ($to_copy_files_programs as $one_file_src => $one_file_dest) {
                try {
                    rename($one_file_src, $one_file_dest);
                    $programs_infos_files[] = $one_file_dest;
                }
                catch (Exception $exc) {}
            }

            try {
                unlink($ready_file_programs); // i.e. wvag_cine_done.txt
            }
            catch (Exception $exc) {}

            $events_last = $this->load(true);
            $parser->setLastEvents($events_last);

            $lim_span_past = null;
            $lim_span_next = null;
            if ((!empty($p_limits)) && (array_key_exists('dates', $p_limits))) {
                $date_limits = $p_limits['dates'];
                if (is_array($date_limits) && isset($date_limits['past'])) {
                    $lim_span_past = 0 + $date_limits['past'];
                }
                if (is_array($date_limits) && isset($date_limits['next'])) {
                    $lim_span_next = 0 + $date_limits['next'];
                }
            }

            $cat_limits = null;
            if ((!empty($p_limits)) && (array_key_exists('categories', $p_limits))) {
                $cat_limits = $p_limits['categories'];
            }

            $event_load = $parser->prepareKinosEvents($programs_infos_files, $sqlite_name, $this->m_provider, $p_categories, $lim_span_past, $lim_span_next, $cat_limits);

            if (!empty($event_load)) {
                $event_all = $event_load['events_all'];
                unset($event_load['events_all']);
                $event_dif = $event_load['events_dif'];
                unset($event_load['events_dif']);

                $event_all_json = json_encode($event_all);
                $event_all_json_path = 'compress.zlib://' . $this->m_dirs['use'] . $cur_time . '-' . $this->m_saved_parts['all'] . '.json.gz';

                $event_dif_json = json_encode($event_dif);
                $event_dif_json_path = 'compress.zlib://' . $this->m_dirs['use'] . $cur_time . '-' . $this->m_saved_parts['dif'] . '.json.gz';

                try {
                    $event_all_json_file = fopen($event_all_json_path, 'w');
                    fwrite($event_all_json_file, $event_all_json);
                    fclose($event_all_json_file);
                }
                catch (Exception $exc) {
                }

                try {
                    $event_dif_json_file = fopen($event_dif_json_path, 'w');
                    fwrite($event_dif_json_file, $event_dif_json);
                    fclose($event_dif_json_file);
                }
                catch (Exception $exc) {
                }

            }

        }

        $files_found = glob($this->m_dirs['use'] . '*-' . $this->m_saved_parts['dif'] . '.json.gz');
        if (!empty($files_found)) {
            foreach ($files_found as $one_file) {
                if (is_file($one_file)) {
                    return true;
                }
            }
        }

        return false;

    } // fn prepare

    /**
     * Loads current or old parsed data
     *
     * @param bool $p_old
     *
     * @return array
     */
    public function load($p_old = false) {

        $dir_type = 'use';
        $set_type = $this->m_saved_parts['dif'];
        if ($p_old) {
            $dir_type = 'old';
            $set_type = $this->m_saved_parts['all'];
        }

        $dir_handle = null;
        try {
            $dir_handle = opendir($this->m_dirs[$dir_type]);
        }
        catch (Exception $exc) {
            return false;
        }

        $search_dir = $this->m_dirs[$dir_type];
        if ( DIRECTORY_SEPARATOR != substr($search_dir, (strlen($search_dir) - strlen(DIRECTORY_SEPARATOR))) ) {
            $search_dir .= DIRECTORY_SEPARATOR;
        }

        $datetime_length = 14;
        $proc_files = array();
        $proc_files_gzipped = array();
        if ($dir_handle) {
            while (false !== ($event_file = readdir($dir_handle))) {

                $event_file_path = $search_dir . $event_file;

                if (!is_file($event_file_path)) {
                    continue;
                }

                $event_file_base_arr = explode('.', $event_file);
                if ((3 == count($event_file_base_arr)) && ('gz' == strtolower($event_file_base_arr[2]))) {
                    $event_file_base_arr = array_slice($event_file_base_arr, 0, 2);
                    $proc_files_gzipped[$event_file_path] = true;
                }

                if (2 != count($event_file_base_arr)) {
                    continue;
                }
                if ('json' != strtolower($event_file_base_arr[count($event_file_base_arr) - 1])) {
                    continue;
                }
                if (strlen($event_file_base_arr[0]) != ($datetime_length + strlen($set_type) + 1)) {
                    continue;
                }
                $event_file_name_arr = explode('-', $event_file_base_arr[0]);
                if (2 != count($event_file_name_arr)) {
                    continue;
                }
                if ($set_type != $event_file_name_arr[1]) {
                    continue;
                }
                if (!is_numeric($event_file_name_arr[0])) {
                    continue;
                }

                $proc_files[] = $event_file_path;
            }
            closedir($dir_handle);
        }

        if (!$p_old) {
            sort($proc_files); // the newest as last to overwrite other ones, for the current
        }
        else {
            if (0 < count($proc_files)) {
                rsort($proc_files); // just the newest one, for the passed
                $proc_files = array($proc_files[0]);
            }
        }

        $events = array();

        foreach ($proc_files as $one_proc_file) {
            if (isset($proc_files_gzipped[$one_proc_file]) && $proc_files_gzipped[$one_proc_file]) {
                $one_proc_file = 'compress.zlib://' . $one_proc_file;
            }

            $one_json = null;
            try {
                $one_json_string = @file_get_contents($one_proc_file);
                if (false !== $one_json_string) {
                    $one_json = json_decode($one_json_string, true);
                    foreach ($one_json as $event_id => $event_info) {
                        if ($p_old) {
                            $event_info = json_encode($event_info);
                        }
                        $events[$event_id] = $event_info;
                    }
                }
            }
            catch (Exception $exc) {
            }
        }
        return $events;
    } // fn load


} // class KinoData_Parser

/**
 * KinoData Parser that makes use of the SimpleXML PHP extension.
 */
class KinoData_Parser_SimpleXML {

    /**
     * Storage of loaded events of last data dosis
     * @var mixed
     */
    var $m_last_events = null;

    /**
     * Name of table where the movies info are stored
     * @var string
     */
    var $m_table_name = 'movies';

    /**
     * Specification string of poster images
     * @var string
     */
    var $m_poster_spec = 'artw';

    var $m_region_info = array();
    var $m_region_topics = array();

    public function __construct($p_regionInfo, $p_regionTopics)
    {
        $this->m_region_info = $p_regionInfo;
        $this->m_region_topics = $p_regionTopics;
    }

    /**
     * Setter of the last used data dosis
     *
     * @param array $p_lastEvents
     * @return void
     */
    public function setLastEvents($p_lastEvents)
    {
        $this->m_last_events = $p_lastEvents;
    } // fn setLastEvents

    /**
     * Auxiliary function to prepare data files to the parser
     *
     * @param array $p_fileNamesIn
     * @param array $p_fileNamesOut
     * @param array $p_fileToUnlink
     * @return void
     */
    private function setSourceFiles($p_fileNamesIn, &$p_fileNamesOut, &$p_filesToUnlink)
    {
        if (empty($p_filesToUnlink)) {
            $p_filesToUnlink = array();
        }
        if (empty($p_fileNamesOut)) {
            $p_fileNamesOut = array();
        }
        if (empty($p_fileNamesIn)) {
            return;
        }

        foreach ($p_fileNamesIn as $one_file_name) {
            $one_file_name_arr = explode('.', $one_file_name);
            $one_suffix = strtolower($one_file_name_arr[count($one_file_name_arr) - 1]);

            if ('zip' == $one_suffix) {
                $zip_hnd = zip_open($one_file_name);
                if (is_numeric($zip_hnd)) {continue;}
                while (true) {
                    $zip_entry = zip_read($zip_hnd);
                    if ((!$zip_entry) || is_numeric($zip_entry)) {
                        break;
                    }
                    if (!zip_entry_open($zip_hnd, $zip_entry, 'rb')) {
                        continue;
                    }
                    $entry_content = '';
                    $entry_name = zip_entry_name($zip_entry);
                    $entry_name_arr = explode('.', $entry_name);
                    $entry_name_suff = strtolower($entry_name_arr[count($entry_name_arr) - 1]);
                    $is_valid = false;
                    $add_suffix = '';
                    if ('xml' == $entry_name_suff) {
                        $entry_is_valid = true;
                        $entry_add_suffix = '.xml';
                    }
                    elseif ('gz' == $entry_name_suff) {
                        if (2 <= count($entry_name_arr)) {
                            $entry_name_suff_sub = strtolower($entry_name_arr[count($entry_name_arr) - 2]);
                            if ('xml' == $entry_name_suff_sub) {
                                $entry_is_valid = true;
                                $entry_add_suffix = '.xml.gz';
                            }
                        }
                    }
                    if ($entry_is_valid) {
                        $entry_content = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
                    }
                    zip_entry_close($zip_entry);
                    if (!$entry_is_valid) {
                        continue;
                    }

                    try {
                        $zip_temp_name_ini = tempnam(sys_get_temp_dir(), '' . mt_rand(100, 999));
                        $zip_temp_name = $zip_temp_name_ini . $entry_add_suffix;

                        $zip_temp_hnd = fopen($zip_temp_name, 'wb');
                        fwrite($zip_temp_hnd, $entry_content);
                        fclose($zip_temp_hnd);

                        $p_fileNamesOut[] = $zip_temp_name;
                        $p_filesToUnlink[] = $zip_temp_name;
                        if ($zip_temp_name != $zip_temp_name_ini) {
                            $p_filesToUnlink[] = $zip_temp_name_ini;
                        }
                    }
                    catch (Exception $exc) {
                        continue;
                    }

                }
                zip_close($zip_hnd);
                continue;
            }

            if ('gz' == $one_suffix) {
                $p_fileNamesOut[] = 'compress.zlib://' . $one_file_name;
                continue;
            }

            $p_fileNamesOut[] = $one_file_name;
        }

    } // fn setSourceFiles

    /**
     * Parses info on movies
     *
     * @param array $p_moviesInfosFiles
     * @param array $p_moviesGenresFiles
     * @param array $p_moviesLinksFiles
     * @return array
     */
    public function parseMoviesInfo($p_moviesInfosFiles, $p_moviesGenresFiles, $p_moviesLinksFiles)
    {

        $movies_infos_files = array();
        $movies_genres_files = array();
        $movies_links_files = array();

        $files_to_unlink = array();

        if (!empty($p_moviesInfosFiles)) {
            $this->setSourceFiles($p_moviesInfosFiles, $movies_infos_files, $files_to_unlink);

        }
        if (!empty($p_moviesGenresFiles)) {
            $this->setSourceFiles($p_moviesGenresFiles, $movies_genres_files, $files_to_unlink);
        }
        if (!empty($p_moviesLinksFiles)) {
            $this->setSourceFiles($p_moviesLinksFiles, $movies_links_files, $files_to_unlink);
        }

        $movies_genres = array();
        $movies_infos = array();

        // movies general info
        $mov_infos_parts = array(
            'key' => 'movkey', 'imdb' => 'movimb', 'suisa' => 'movsui', 'country' => 'movcou',
            'title' => 'movtit', 'lead' => 'movlea', 'link' => 'movlnk', 'trailer' => 'movtra',
            'distributor' => 'disnam', 'distributor_link' => 'dislnk',
        );

        $mov_infos_people = array(
            'director' => 'movdir', 'producer' => 'movpro', 'cast' => 'movcas', 'script' => 'movscr', 'camera' => 'movcam',
            'cutter' => 'movcut', 'sound' => 'movsnd', 'score' => 'movsco', 'production_design' => 'movpde',
            'costume_design' => 'movcde', 'visual_effects' => 'movvfx',
        );

        $mov_infos_times = array('release_ch_d' => 'movred', 'release_ch_f' => 'movref', 'release_ch_i' => 'movrei',);

        $mov_infos_numbers = array('flag' => 'movspc', 'year' => 'movyea', 'duration' => 'movdur',  'oscars' => 'movosc',);

        foreach($movies_infos_files as $one_mov_file) {
            //$one_mov_xml = simplexml_load_file($one_mov_file);
            $one_mov_xml = simplexml_load_string(FileLoad::LoadFix($one_mov_file));
            foreach ($one_mov_xml->movie as $one_movie) {
                $one_mov_key = trim('' . $one_movie->movkey);
                if (empty($one_mov_key)) {
                    continue;
                }
                $one_mov_info = array();
                if (isset($movies_infos[$one_mov_key])) {
                    $one_mov_info = $movies_infos[$one_mov_key];
                }

                $one_mov_desc = trim('' . $one_movie->movsyd);
                if (empty($one_mov_desc)) {
                    $one_mov_desc = trim('' . $one_movie->movcgd);
                }
                //if (empty($one_mov_desc)) {
                //    $one_mov_desc = trim('' . $one_movie->movlea);
                //}
                if ((!isset($one_mov_info['desc'])) || (empty($one_mov_info['desc']))) {
                    $one_mov_info['desc'] = $one_mov_desc;
                }

                //specific text parts
                foreach ($mov_infos_parts as $one_mov_infos_key => $one_mov_infos_spec) {
                    $one_mov_infos_value = trim('' . $one_movie->$one_mov_infos_spec);
                    if ((!isset($one_mov_info[$one_mov_infos_key])) || (empty($one_mov_info[$one_mov_infos_key])) || (!empty($one_mov_infos_value))) {
                        $one_mov_info[$one_mov_infos_key] = '' . $one_mov_infos_value;
                    }
                }

                //specific people parts
                foreach ($mov_infos_people as $one_mov_infos_key => $one_mov_infos_spec) {
                    $one_mov_infos_value = trim('' . $one_movie->$one_mov_infos_spec);
                    if ((!isset($one_mov_info[$one_mov_infos_key])) || (empty($one_mov_info[$one_mov_infos_key])) || (!empty($one_mov_infos_value))) {
                        $one_mov_info_people = array();
                        foreach (explode("\n", '' . $one_mov_infos_value) as $one_mov_info_people_line) {
                            $one_mov_info_people_line = trim($one_mov_info_people_line);
                            if (empty($one_mov_info_people_line)) {continue;}
                            $one_mov_info_people[] = $one_mov_info_people_line;
                        }

                        $one_mov_info[$one_mov_infos_key] = implode(',', $one_mov_info_people);

                    }
                }

                //specific date-time parts
                foreach ($mov_infos_times as $one_mov_infos_key => $one_mov_infos_spec) {
                    $one_mov_infos_value = trim('' . $one_movie->$one_mov_infos_spec);
                    if ((!isset($one_mov_info[$one_mov_infos_key])) || (empty($one_mov_info[$one_mov_infos_key])) || (!empty($one_mov_infos_value))) {
                        if ( (!empty($one_mov_infos_value)) && (is_numeric($one_mov_infos_value)) ) {
                            $one_mov_infos_value = gmdate('Y-m-d', $one_mov_infos_value);
                        }
                        $one_mov_info[$one_mov_infos_key] = $one_mov_infos_value;
                    }
                }

                //specific numeric parts
                foreach ($mov_infos_numbers as $one_mov_infos_key => $one_mov_infos_spec) {
                    $one_mov_infos_value = trim('' . $one_movie->$one_mov_infos_spec);
                    if ((!isset($one_mov_info[$one_mov_infos_key])) || (empty($one_mov_info[$one_mov_infos_key])) || (!empty($one_mov_infos_value))) {
                        $one_mov_info[$one_mov_infos_key] = 0 + $one_mov_infos_value;
                    }
                }

                $movies_infos[$one_mov_key] = $one_mov_info;

            }
        }


        // movies genres info
        foreach($movies_genres_files as $one_mov_file) {
            //$one_mov_xml = simplexml_load_file($one_mov_file);
            $one_mov_xml = simplexml_load_string(FileLoad::LoadFix($one_mov_file));
            foreach ($one_mov_xml->genre as $one_genre) {
                $one_gen_id = trim('' . $one_genre->genid);
                if (empty($one_gen_id)) {
                    continue;
                }
                $one_gen_info = array();
                if (isset($movies_genres[$one_gen_id])) {
                    $one_gen_info = $movies_infos[$one_gen_id];
                }

                $one_gen_de = trim('' . $one_genre->gennad);
                if (!empty($one_gen_de)) {
                    $one_gen_info['de'] = $one_gen_de;
                }
                $one_gen_en = trim('' . $one_genre->gennae);
                if (!empty($one_gen_en)) {
                    $one_gen_info['en'] = $one_gen_en;
                }

                $movies_genres[$one_gen_id] = $one_gen_info;
            }

            foreach ($one_mov_xml->movie as $one_movie) {
                $one_mov_key = trim('' . $one_movie->movkey);
                if (empty($one_mov_key)) {
                    continue;
                }
                $one_mov_info = array();
                if (isset($movies_infos[$one_mov_key])) {
                    $one_mov_info = $movies_infos[$one_mov_key];
                }

                $one_mov_genres = array();
                foreach ($one_movie->movgen->genreid as $one_mov_gen) {
                    $one_mov_gen_id = trim('' . $one_mov_gen);
                    if (isset($movies_genres[$one_mov_gen_id])) {
                        $one_genres_names = $movies_genres[$one_mov_gen_id];
                        if ((isset($one_genres_names['de'])) && (!empty($one_genres_names['de']))) {
                            $one_mov_genres[$one_mov_gen_id] = $one_genres_names['de'];
                        }
                        elseif ((isset($one_genres_names['en'])) && (!empty($one_genres_names['en']))) {
                            $one_mov_genres[$one_mov_gen_id] = $one_genres_names['en'];
                        }
                    }
                }

                if (!empty($one_mov_genres)) {
                    $one_mov_info['genres'] = $one_mov_genres;
                    $movies_infos[$one_mov_key] = $one_mov_info;
                }

            }
        }

        // movies links info
        foreach($movies_links_files as $one_lnk_file) {
            //$one_lnk_xml = simplexml_load_file($one_lnk_file);
            $one_lnk_xml = simplexml_load_string(FileLoad::LoadFix($one_lnk_file));
            foreach ($one_lnk_xml->movie as $one_movie) {
                $one_mov_key = trim('' . $one_movie->m_movkey);
                if (empty($one_mov_key)) {
                    continue;
                }
                $one_mov_info = array();
                if (isset($movies_infos[$one_mov_key])) {
                    $one_mov_info = $movies_infos[$one_mov_key];
                }
                $one_mov_url = trim('' . $one_movie->m_movurl);
                if (!empty($one_mov_url)) {
                    $one_mov_info['link_url'] = $one_mov_url;
                    $movies_infos[$one_mov_key] = $one_mov_info;
                }
            }

            foreach ($one_lnk_xml->image as $one_lnk_image) {
                $one_mov_key = trim('' . $one_lnk_image->i_movkey);
                if (empty($one_mov_key)) {
                    continue;
                }
                $one_mov_info = array();
                if (isset($movies_infos[$one_mov_key])) {
                    $one_mov_info = $movies_infos[$one_mov_key];
                }
                $one_mov_info_images = array();
                if (isset($one_mov_info['link_images'])) {
                    $one_mov_info_images = $one_mov_info['link_images'];
                }

                $one_img_id = trim('' . $one_lnk_image->i_imgid);
                if (empty($one_img_id)) {
                    continue;
                }

                $one_img_type = trim('' . $one_lnk_image->i_imgcatkey);
                $one_img_url = trim('' . $one_lnk_image->i_imgurl);
                $one_img_w = trim('' . $one_lnk_image->i_imgsizsxm);
                $one_img_h = trim('' . $one_lnk_image->i_imgsizsym);

                if (!empty($one_img_url)) {
                    $one_mov_info_images[$one_img_id] = array('url' => $one_img_url, 'width' => $one_img_w, 'height' => $one_img_h, 'type' => $one_img_type);
                    $one_mov_info['link_images'] = $one_mov_info_images;
                    $movies_infos[$one_mov_key] = $one_mov_info;
                }
            }

            foreach ($one_lnk_xml->trailer as $one_lnk_trailer) {
                $one_mov_key = trim('' . $one_lnk_trailer->t_movkey);
                if (empty($one_mov_key)) {
                    continue;
                }
                $one_mov_info = array();
                if (isset($movies_infos[$one_mov_key])) {
                    $one_mov_info = $movies_infos[$one_mov_key];
                }
                $one_trl_w = trim('' . $one_lnk_trailer->t_movtrw);
                $one_trl_h = trim('' . $one_lnk_trailer->t_movtrh);
                $one_trl_format = trim('' . $one_lnk_trailer->t_movtrc);
                $one_trl_url = trim('' . $one_lnk_trailer->t_traurl);
                if (!empty($one_trl_url)) {
                    $one_mov_info['link_trailer'] = array('url' => $one_trl_url, 'width' => $one_trl_w, 'height' => $one_trl_h, 'format' => $one_trl_format);
                    $movies_infos[$one_mov_key] = $one_mov_info;
                }
            }
        }


        // unlink tmp (zip extracted) files
        foreach ($files_to_unlink as $one_temp_name) {
            try {
                unlink($one_temp_name);
            }
            catch (Exception $exc) {
                continue;
            }
        }

        // return parsed info on movies
        return $movies_infos;

    } // fn parseMoviesInfo

    /**
     * Puts new info on movies into the sqlite db
     *
     * @param array $p_moviesDatabase
     * @param array $p_moviesInfosFiles
     * @param array $p_moviesGenresFiles
     * @param array $p_moviesLinksFiles
     * @return bool
     */
    public function updateMoviesInfo($p_moviesDatabase, $p_moviesInfosFiles, $p_moviesGenresFiles, $p_moviesLinksFiles)
    {
        $movies_info = $this->parseMoviesInfo($p_moviesInfosFiles, $p_moviesGenresFiles, $p_moviesLinksFiles);

        $sqlite_name = $p_moviesDatabase;
        $table_name = $this->m_table_name;

        ksort($movies_info);

        $cre_req = 'CREATE TABLE IF NOT EXISTS ' . $table_name . ' (movie_key TEXT PRIMARY KEY, movie_info TEXT)';
        $ins_req = 'INSERT OR REPLACE INTO ' . $table_name . ' (movie_key, movie_info) VALUES (:movie_key, :movie_info)';

        @$db = new PDO ('sqlite:' . $sqlite_name);
        $stmt = $db->prepare($cre_req);
        $res = $stmt->execute();
        if (!$res) {
            return false;
        }

        $movies_to_preload = array();
        foreach ($movies_info as $mov_key => $mov_info) {
            $movies_to_preload[$mov_key] = true;
        }
        $movies_preloaded = $this->loadMoviesByKeys($movies_to_preload, $p_moviesDatabase);
        foreach ($movies_preloaded as $mov_key => $one_movie_data_pre) {
            if (isset($movies_info[$mov_key])) {
                $mov_info = $movies_info[$mov_key];
                foreach ($one_movie_data_pre as $mov_pre_key => $mov_pre_value) {
                    $mov_new_value = (isset($mov_info[$mov_pre_key])) ? $mov_info[$mov_pre_key] : '';
                    if ( (empty($mov_new_value)) && (!empty($mov_pre_value)) ) {
                        $mov_info[$mov_pre_key] = $mov_pre_value;
                    }
                }
                $movies_info[$mov_key] = $mov_info;
            }
        }

        $db->beginTransaction();
        $stmt = $db->prepare($ins_req);
        foreach ($movies_info as $mov_key => $mov_info) {
            //$mov_key = '';
            //if (isset($mov_info['key'])) {
            //    $mov_key = $mov_info['key'];
            //}
            $mov_info = json_encode($mov_info);

            //$stmt->bindParam(':movie_id', $mov_id, PDO::PARAM_INT);
            $stmt->bindParam(':movie_key', $mov_key, PDO::PARAM_STR);
            $stmt->bindParam(':movie_info', $mov_info, PDO::PARAM_STR);

            $res = $stmt->execute();
            if (!$res) {
                return false;
            }
        }
        $db->commit();

        return true;
    } // fn updateMoviesInfo


    /**
     * Parses KinoData data (by KinoData_Parser_SimpleXML)
     *
     * @param array $p_kinosInfosFiles file name of the kino file
     * @return array
     */
    public function parseKinosInfo($p_kinosInfosFiles)
    {

        $kinos_infos_files = array();
        $files_to_unlink = array();

        $movies_screens = array();

        if (!empty($p_kinosInfosFiles)) {
            $this->setSourceFiles($p_kinosInfosFiles, $kinos_infos_files, $files_to_unlink);
        }

        $other_desc_parts = array('weewee', 'weelea', 'weetxs');
        foreach ($kinos_infos_files as $one_kino_file) {
            //$one_kino_xml = simplexml_load_file($one_kino_file);
            $one_kino_xml = simplexml_load_string(FileLoad::LoadFix($one_kino_file));
            $export_start_date = '0000-00-01';
            $export_start_date_time = explode('-', trim('' . $one_kino_xml->export->date_min));
            $export_start_date_info = explode('.', $export_start_date_time[0]);
            if (3 == count($export_start_date_info)) {
                $exp_year = '0000';
                $exp_month = '00';
                $exp_day = '00';
                if (4 == strlen($export_start_date_info[2])) {
                    $exp_year = $export_start_date_info[2];
                }
                if (2 == strlen($export_start_date_info[1])) {
                    $exp_month = $export_start_date_info[1];
                }
                if (2 == strlen($export_start_date_info[0])) {
                    $exp_day = $export_start_date_info[0];
                }
                $export_start_date = $exp_year . '-' . $exp_month . '-' . $exp_day;
            }

            foreach ($one_kino_xml->kino as $one_kino) {
                $one_kino_id = trim('' . $one_kino->theaterid);
                //if (empty($one_kino_id)) {
                //    continue;
                //}
                $one_kino_name = trim('' . $one_kino->theatername);
                $one_kino_town = trim('' . $one_kino->theatertown);
                $one_kino_zip = trim('' . $one_kino->theaterzip);
                $one_kino_street = trim('' . $one_kino->theateradress);
                $one_kino_latitude = trim('' . $one_kino->theaterlat);
                $one_kino_longitude = trim('' . $one_kino->theaterlong);
                $one_kino_phone = trim('' . $one_kino->theaterphone);
                $k_telcost = trim('' . $one_kino->theatertelcostde);
                if (empty($k_telcost)) {
                    $k_telcost = trim('' . $one_kino->theatertelcost);
                }
                if (!empty($k_telcost)) {
                    $one_kino_phone .= ' ' . $k_telcost;
                }
                $one_kino_url = trim('' . $one_kino->theaterurl);

                foreach ($one_kino->movie as $one_movie) {
                    $one_movie_id = trim('' . $one_movie->filmid);
                    if (empty($one_movie_id)) {
                        continue;
                    }
                    $one_movie_key = trim('' . $one_movie->filmkey); // connector to the movies data, but can be empty!
                    $one_movie_title = trim('' . $one_movie->filmtitle);
                    $one_movie_desc = trim('' . $one_movie->filmcig_d);
                    $one_movie_other = array();
                    foreach ($other_desc_parts as $one_desc_part) {
                        $one_movie_oth_one = trim('' . $one_movie->$one_desc_part);
                        if (!empty($one_movie_oth_one)) {
                            $one_movie_other[] = $one_movie_oth_one;
                        }
                    }

                    $one_movie_age = trim('' . $one_movie->movcatnam);
                    $one_movie_age_matches = array();
                    if (preg_match('/^([^\s]+)[\s]*J$/i', $one_movie_age, $one_movie_age_matches)) {
                        $one_movie_age = $one_movie_age_matches[1];
                    }

                    $one_movie_dates = trim('' . $one_movie->prolis);
                    if (empty($one_movie_dates)) {
                        continue;
                    }
                    $one_movie_dates_arr = explode(';', $one_movie_dates);
                    $one_movie_dates_cnt = count($one_movie_dates_arr) - 1;

                    $one_screen_dates = array();
                    foreach ($one_movie_dates_arr as $one_movie_screen) {
                        $one_movie_screen_arr = explode(':', $one_movie_screen);
                        if (2 > count($one_movie_screen_arr)) {
                            continue;
                        }
                        $one_movie_screen_info = array();
                        if (4 <= count($one_movie_screen_arr)) {
                            $one_movie_screen_info['flag'] = $one_movie_screen_arr[3];
                        }
                        if (3 <= count($one_movie_screen_arr)) {
                            $one_movie_screen_info['lang'] = $one_movie_screen_arr[2];
                        }
                        $one_movie_screen_info['time'] = $one_movie_screen_arr[1];

                        $one_movie_screen_date = $one_movie_screen_arr[0];
                        $one_movie_screen_date_arr = explode('.', $one_movie_screen_date);
                        if (3 != count($one_movie_screen_date_arr)) {
                            continue;
                        }
                        $one_screen_date_use = $one_movie_screen_date_arr[2] . '-' . $one_movie_screen_date_arr[1] . '-' . $one_movie_screen_date_arr[0];
                        if (isset($one_screen_dates[$one_screen_date_use])) {
                            $one_screen_dates[$one_screen_date_use][] = $one_movie_screen_info;
                        }
                        else {
                            $one_screen_dates[$one_screen_date_use] = array($one_movie_screen_info);
                        }
                    }

                    $movies_screens[] = array(
                        'start_date' => $export_start_date,

                        'kino_id' => $one_kino_id,
                        'kino_name' => $one_kino_name,
                        'kino_town' => $one_kino_town,
                        'kino_zip' => $one_kino_zip,
                        'kino_street' => $one_kino_street,
                        'kino_latitude' => $one_kino_latitude,
                        'kino_longitude' => $one_kino_longitude,
                        'kino_phone' => $one_kino_phone,
                        'kino_url' => $one_kino_url,

                        'movie_id' => $one_movie_id,
                        'movie_key' => $one_movie_key,
                        'title' => $one_movie_title,
                        'desc' => $one_movie_desc,
                        'other' => $one_movie_other,
                        'dates' => $one_screen_dates,

                        'allowed_age' => $one_movie_age,
                    );
                }

            }
        }


        return $movies_screens;
    } // fn parseKinosInfo

    /**
     * Takes info of movies by movie keys
     *
     * @param array $p_moviesKeys
     * @param string $p_sqliteName
     * @return array
     */
    private function loadMoviesByKeys($p_moviesKeys, $p_sqliteName)
    {
        $movies_infos = array();

        $table_name = $this->m_table_name;
        $mov_req = 'SELECT movie_info FROM ' . $table_name . ' WHERE movie_key = :movie_key LIMIT 1';

        @$db = new PDO ('sqlite:' . $p_sqliteName);
        $stmt = $db->prepare($mov_req);

        foreach ($p_moviesKeys as $mov_key => $mov_aux) {
            $mov_info = null;
            $stmt->bindParam(':movie_key', $mov_key, PDO::PARAM_STR);
            $res = $stmt->execute();
            if (!$res) {
                continue;
            }
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            if (empty($res)) {
                continue;
            }
            $mov_info = $res['movie_info'];
            try {
                $mov_info = json_decode($mov_info, true);
            }
            catch (Exception $exc) {
                $mov_info = null;
            }
            $movies_infos[$mov_key] = $mov_info;
        }

        return $movies_infos;
    } // fn loadMoviesByKeys

    private function formatDateText($p_dateTimes)
    {
        $screens = '';

        $line_sep = "\n<br />\n";
        $field_sep = ':';

        foreach ($p_dateTimes as $cur_date => $cur_screenings) {
            $screens .= $cur_date . $line_sep;
            foreach ($cur_screenings as $one_scr) {
                $one_time = (isset($one_scr['time']) ? ('' . $one_scr['time']) : '');
                $one_lang = (isset($one_scr['lang']) ? ('' . $one_scr['lang']) : '');
                $one_flag = (isset($one_scr['flag']) ? ('' . $one_scr['flag']) : '');
                $screens .= $one_time . $field_sep . $one_lang . $field_sep . $one_flag . $line_sep;
            }
            $screens .= $line_sep;
        }

        return $screens;
    }


    /**
     * Puts together info on movie events
     *
     * @param array $p_kinosInfosFiles
     * @param string $p_moviesDatabase
     * @param int $p_providerId
     * @param array $p_categories
     * @param mixed $p_daysPast
     * @param mixed $p_daysNext
     * @param mixed $p_catLimits
     * @return array
     */
    public function prepareKinosEvents($p_kinosInfosFiles, $p_moviesDatabase, $p_providerId, $p_categories, $p_daysPast = null, $p_daysNext = null, $p_catLimits = null)
    {
        $provider_id = $p_providerId;
        $kino_country = 'ch';

        $limit_date_start = null;
        $limit_date_end = null;

        $cur_time = time();
        if (!empty($p_daysPast)) {
            $limit_date_start = date('Y-m-d', ($cur_time - ($p_daysPast * 24 * 60 * 60)));
        }
        if (!empty($p_daysNext)) {
            $limit_date_end = date('Y-m-d', ($cur_time + ($p_daysNext * 24 * 60 * 60)));
        }

        $movies_screens = $this->parseKinosInfo($p_kinosInfosFiles);

        $movies_keys = array();

        foreach ($movies_screens as $one_screen) {
            if (isset($one_screen['movie_key']) && (!empty($kino_country))) {
                $movies_keys[$one_screen['movie_key']] = true;
            }
        }

        $movies_infos = $this->loadMoviesByKeys($movies_keys, $p_moviesDatabase);

        $screen_events_all = array();
        $screen_events_dif = array();

        //$set_date = '';
        //$set_date_times = array();

        foreach ($movies_screens as $one_screen) {
            // TODO: put it as a (full) week start of date/time screen listing (lists per days)
            $set_date = $one_screen['start_date'];
            $set_date_obj = new DateTime($set_date);
            $set_date_times = array($set_date => array());
            foreach (array(1, 2, 3, 4, 5, 6) as $cur_day_add) {
                $set_date_obj->add(new DateInterval('P1D'));
                $set_date_times[$set_date_obj->format('Y-m-d')] = array();
            }

            // region info
            $e_region = '';
            $e_subregion = '';

            $topics_regions = array();
            $loc_regions = $this->m_region_info->ZipRegions($one_screen['kino_zip'], $kino_country);
            foreach ($loc_regions as $region_name) {
                if (isset($this->m_region_topics[$region_name])) {
                    $cur_reg_top = $this->m_region_topics[$region_name];
                    $cur_reg_top['key'] = $region_name;
                    $topics_regions[] = $cur_reg_top;
                }
            }

            $event_topics = array();

            $c_other = null;

            $one_movie = null;

            if (isset($one_screen['movie_key']) && (!empty($one_screen['movie_key']))) {
                $one_mov_key = $one_screen['movie_key'];
                if (isset($movies_infos[$one_mov_key]) && (!empty($movies_infos[$one_mov_key]))) {
                    $one_movie = $movies_infos[$one_mov_key];
                }
            }

            $x_genres = array();
            if (isset($one_movie['genres']) && (!empty($one_movie['genres']))) {
                $x_genres = $one_movie['genres'];
            }

            $e_rated = false;

            $c_other = null;
            foreach ($p_categories as $one_category) {
                if (!is_array($one_category)) {
                    continue;
                }

                $one_cat_key = $one_category['key'];

                if (array_key_exists('other', $one_category)) {
                    $c_other = $one_category['other'];
                    continue;
                }

                foreach ($x_genres as $x_catnam) {
                    $x_catnam = strtolower(trim($x_catnam));
                    if ((array_key_exists('match_xml', $one_category)) && (array_key_exists('match_topic', $one_category))) {
                        $one_cat_match_xml = $one_category['match_xml'];
                        $one_cat_match_topic = $one_category['match_topic'];
                        if ((!is_array($one_cat_match_xml)) || (!is_array($one_cat_match_topic))) {
                            continue;
                        }
                        if (in_array($x_catnam, $one_cat_match_xml)) {
                            $event_topics[] = $one_cat_match_topic;

                            if ('adult' == $one_cat_key) {
                                $e_rated = true;
                            }

                            continue;
                        }
                    }
                }

            }

            if (empty($event_topics)) {
                if (!empty($c_other)) {
                    $event_topics[] = $c_other;
                }
            }

            foreach ($topics_regions as $one_regtopic) {
                $event_topics[] = $one_regtopic;
            }

            $one_mov_genre = '';
            $one_mov_desc = '';
            $one_mov_images = array();

            $one_mov_trailers = array();
            $trailer_official = '';

            if (!empty($one_movie)) {
                if (isset($one_movie['genres'])) {
                    $one_mov_genre = implode(',', $one_movie['genres']);
                }
                if (isset($one_movie['desc'])) {
                    $one_mov_desc = $one_movie['desc'];
                }
                if ( isset($one_movie['link_images']) && (!empty($one_movie['link_images'])) ) {
                    foreach($one_movie['link_images'] as $one_img_info) {
                        if (isset($one_img_info['url'])) {
                            $one_link_url = $one_img_info['url'];
                            $one_link_label = '';
                            $one_link_image = array('url' => $one_link_url, 'label' => $one_link_label);
                            if (isset($one_img_info['type']) && ($this->m_poster_spec == $one_img_info['type'])) {
                                array_unshift($one_mov_images, $one_link_image);
                            }
                            else {
                                $one_mov_images[] = $one_link_image;
                            }
                        }
                    }
                }

                if ( isset($one_movie['link_trailer']) && (!empty($one_movie['link_trailer'])) ) {
                    if ( isset($one_movie['link_trailer']['url']) && (!empty($one_movie['link_trailer']['url'])) ) {
                        $one_mov_trailers[] = $one_movie['link_trailer']['url'];
                        $trailer_official = $one_movie['link_trailer']['url'];
                    }
                }
                if ( isset($one_movie['trailer']) && (!empty($one_movie['trailer'])) ) {
                    $one_mov_trailers[] = $one_movie['trailer'];
                }
            }

            $one_use_desc = $one_mov_desc;
            if (empty($one_use_desc)) {
                $one_use_desc = $one_screen['desc'];
            }

            $one_event = array();

            //$one_event['date'] = $set_date;
            //$one_date_max = '0000-00-01';
            $one_date_max = $set_date;

            foreach ($one_screen['dates'] as $one_date => $one_times) {
                if ($one_date_max < $one_date) {
                    $one_date_max = $one_date;
                }

                if (!isset($set_date_times[$one_date])) {
                    $set_date_times[$one_date] = array(); // this shall not occur
                }

                foreach ($one_times as $one_screen_info) {
                    $set_date_times[$one_date][] = $one_screen_info;
                }
                //$one_event_screen[$one_date] = $one_times; // flag, lang, time
            }
            ksort($set_date_times);

            $one_event['date'] = $one_date_max;
            $one_event['date_time_tree'] = json_encode($set_date_times);
            $one_event['date_time_text'] = $this->formatDateText($set_date_times);


/*
yyyy-mm-dd
hh.mm:langs:flags
hh.mm:langs:flags
....
yyyy-mm-dd
....
yyyy-mm-dd
hh.mm:langs:flags
hh.mm:langs:flags
....
*/
            {
                $one_event['provider_id'] = $provider_id;
                $one_event['event_id'] = '' . $one_screen['kino_id'] . '-' . $one_screen['movie_id'];

                $one_event['tour_id'] = $one_screen['movie_id'];
                $one_event['location_id'] = $one_screen['kino_id'];

                $one_event['movie_key'] = (isset($one_screen['movie_key']) && (!empty($one_screen['movie_key']))) ? $one_screen['movie_key'] : '';
                $one_event['movie_info'] = $one_movie;

                $one_event['headline'] = $one_screen['title'];
                $one_event['organizer'] = $one_screen['kino_name'];
                $one_event['keywords'] = $one_screen['kino_name'];

                $one_event['country'] = $kino_country;
                $one_event['zipcode'] = $one_screen['kino_zip'];
                $one_event['town'] = $one_screen['kino_town'];
                $one_event['street'] = $one_screen['kino_street'];

                $one_event['region'] = $e_region;
                $one_event['subregion'] = $e_subregion;

                if ($limit_date_start) {
                    if ($one_date < $limit_date_start) {
                        continue;
                    }
                }
                if ($limit_date_end) {
                    if ($one_date > $limit_date_end) {
                        continue;
                    }
                }

                $one_event['time'] = '';

                $one_event['web'] = $this->makeLink($one_screen['kino_url'], null);
                $one_event['email'] = '';
                $one_event['phone'] = $one_screen['kino_phone'];

                $one_event['description'] = str_replace("\n", "\n<br />\n", $one_use_desc);
                $one_event['other'] = $one_screen['other'];

                $one_event['movie_trailers'] = array();
                foreach ($one_mov_trailers as $cur_trailer) {
                    $one_event['movie_trailers'][] = $this->makeLink($cur_trailer, 'Trailer', true, true);
                }
                $one_event['movie_trailer'] = $trailer_official;

                $one_event['genre'] = $one_mov_genre;
                $one_event['languages'] = '';
                $one_event['prices'] = '';
                $one_event['minimal_age'] = $one_screen['allowed_age'];

                $one_event['canceled'] = false;
                $one_event['rated'] = $e_rated;

                $one_event['geo'] = array();
                if ( (!empty($one_screen['kino_latitude'])) && (!empty($one_screen['kino_longitude'])) ) {
                    $one_event['geo']['longitude'] = $one_screen['kino_longitude'];
                    $one_event['geo']['latitude'] = $one_screen['kino_latitude'];
                }

                $one_event['images'] = $one_mov_images;

                $one_event['topics'] = $event_topics;

                $screen_events_all[$one_event['event_id']] = $one_event;

                if (!empty($this->m_last_events)) {
                    if (isset($this->m_last_events[$one_event['event_id']])) {
                        if ($this->m_last_events[$one_event['event_id']] == json_encode($one_event)) {
                            continue;
                        }
                    }
                }

                $screen_events_dif[$one_event['event_id']] = $one_event;
            }

        }

        return array('events_all' => $screen_events_all, 'events_dif' => $screen_events_dif);
    } // fn prepareKinosEvents

    /**
     * Creates (html) link on given (partial) link and label
     *
     * @param string $p_target
     * @param mixed $p_label
     * @param bool $p_fullLink
     *
     * @return string
     */
    private function makeLink($p_target, $p_label = '', $p_fullLink = true, $p_remote = false) {
        $link = '' . $p_target;
        if ($p_fullLink) {
            if ('http' != substr($link, 0, strlen('http'))) {
                $link = 'http://' . $link;
            }
        }
        if (!empty($p_label)) {
            $target_part = '';
            if ($p_remote) {
                $target_part = ' target="_blank"';
            }

            $link = '<a href="' . $link . $target_part . '">' . $p_label . '</a>';
        }

        return $link;
    } // fn makeLink

} // class KinoData_Parser_SimpleXML


