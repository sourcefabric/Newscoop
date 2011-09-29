<?php
/**
 * Parsing the 'eventexport.xml' file
 */

/**
 * EventData Importer class for managing parsing of EventData files.
 */
class EventData_Parser {

    /**
     * To omit a possible double run if it would happened.
     * @var string
     */
    var $m_working = '_lock';
    /**
     * To omit a possible double run if it would happened.
     * @var mixed
     */
    var $m_lockfile = null;

    /**
     * Mode of (possibly) created directories.
     * @var integer
     */
    var $m_dirmode = 0755;

    /**
     * What is this.
     * @var array
     */
    var $m_source = null;
    /**
     * Who provides this.
     * @var integer
     */
    var $m_provider = null;
    /**
     * Where to take this.
     * @var array
     */
    var $m_dirs = null;

    /**
     * Data from the last run
     * @var array
     */
    var $m_last_events = null;

    /**
     * Suffix parts for json data files
     * @var array
     */
    var $m_saved_parts = array('dif' => 'dif', 'all' => 'set');

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
	 * checks whether we can start a job
	 *
	 * @return bool
	 */
    public function start() {
        // stop, if some worker running; return false
        $working_path = $this->m_dirs['use'] . $this->m_working;
        //if (file_exists($working_path)) {
        //    return false;
        //}
        $this->m_lockfile = fopen($working_path, 'a');
        $locked = flock($this->m_lockfile, LOCK_EX);
        if (!$locked) {
            return false;
        }

        foreach (array($this->m_dirs['use'], $this->m_dirs['new']) as $one_dir) {
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

        try {
            $working_file = fopen($working_path, 'w');
            fwrite($working_file, date('Y-m-d') . "\n");
            fclose($working_file);
        }
        catch (Exception $exc) {
            return false;
        }

        return true;
    } // fn start

	/**
	 * closes a job
	 *
	 * @return bool
	 */
    public function stop() {
        // stop, if some worker running; return false
        $working_path = $this->m_dirs['use'] . $this->m_working;
        if (!file_exists($working_path)) {
            return false;
        }
        if (!$this->m_lockfile) {
            return false;
        }

        try {
            //unlink($working_path);
            flock($this->m_lockfile, LOCK_UN);
            fclose($this->m_lockfile);
        }
        catch (Exception $exc) {
            return false;
        }

        return true;
    } // fn stop

	/**
	 * prepares files for the parsing
	 *
	 * @return bool
	 */
    public function prepare($p_categories, $p_limits, $p_cancels) {
        // we need that conf info
        if ((!isset($this->m_dirs['source'])) || (!isset($this->m_dirs['source']['events']))) {
            return false;
        }

        $to_copy_files = true;

        $some_files_to_process = true;

        if ((isset($this->m_dirs['ready'])) && (isset($this->m_dirs['ready']['events']))) {
            $ready_file = $this->m_dirs['new'] . $this->m_dirs['ready']['events'];
            // if no new files (still may be some not fully processed at the interim dir)
            if (!file_exists($ready_file)) {
                $to_copy_files = false;

                $some_files_to_process = false;
                foreach ($this->m_dirs['source']['events'] as $one_file_glob) {
                    $one_path_glob = $this->m_dirs['use'] . '*' . $one_file_glob;
                    $one_file_set = glob($one_path_glob);
                    if (false === $one_file_set) {
                        continue;
                    }
                    foreach ($one_file_set as $event_file_path) {
                        if (!is_file($event_file_path)) {
                            continue;
                        }
                        $some_files_to_process = true;
                        break;
                    }
                }

                // if neither new, no interim
                if (!$some_files_to_process) {
                    return false;
                }

                // some files to process are there
                return true;
            }
        }
        if ($to_copy_files) {
            // copy files with time stamps
            $cur_time = date('YmdHis');
            foreach ($this->m_dirs['source']['events'] as $one_file_glob) {
                $one_path_glob = $ready_file = $this->m_dirs['new'] . $one_file_glob;
                $one_file_set = glob($one_path_glob);
                if (false === $one_file_set) {
                    continue;
                }

                foreach ($one_file_set as $event_file_path_new) {
                    if (!is_file($event_file_path_new)) {
                        continue;
                    }
                    $event_file_name = basename($event_file_path_new);
                    $event_file_path_use = $this->m_dirs['use'] . $cur_time . '-' . $event_file_name;
                    try {
                        rename($event_file_path_new, $event_file_path_use);
                    }
                    catch (Exception $exc) {
                        continue;
                    }
                }
            }

            // remove the ready file
            if ((isset($this->m_dirs['ready'])) && (isset($this->m_dirs['ready']['events']))) {
                $ready_file = $this->m_dirs['new'] . $this->m_dirs['ready']['events'];
                if (file_exists($ready_file)) {
                    try {
                        unlink($ready_file);
                    }
                    catch (Exception $exc) {
                        // return false;
                    }
                }
            }

            $this->m_last_events = $this->load(true);

            // to parse xml, restrict it, and convert to json
            $event_load = $this->parse($p_categories, $p_limits, $p_cancels);
            if (empty($event_load)) {
                //$this->stop();
                //continue;
                return true;
            }

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

        return true;
    } // fn prepare

	/**
	 * (re)moves files after parsing && importing
	 *
	 * @return bool
	 */
    public function cleanup() {
        // we need that conf info
        if ((!isset($this->m_dirs['source'])) || (!isset($this->m_dirs['source']['events']))) {
            return false;
        }

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
            if (basename($one_use_path) == $this->m_working) {
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
                if ($event_file == $this->m_working) {
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
                $one_json_string = file_get_contents($one_proc_file);
                $one_json = json_decode($one_json_string, true);
                foreach ($one_json as $event_id => $event_info) {
                    if ($p_old) {
                        $event_info = json_encode($event_info);
                    }
                    $events[$event_id] = $event_info;
                }
            }
            catch (Exception $exc) {
                //;
            }
        }
        return $events;
    } // fn load

    /**
     * Parses EventData data (by EventData_Parser_SimpleXML)
     *
     * @param array $p_categories
     * @param array $p_limits
     * @param array $p_cancels
     *
     * @return array
     */
    private function parse($p_categories, $p_limits, $p_cancels) {
        if (!is_array($p_categories)) {
            $p_categories = array();
        }

        //$start_date = null;
        //$end_date = null;
        $lim_span_past = null;
        $lim_span_next = null;
        if ((!empty($p_limits)) && (array_key_exists('dates', $p_limits))) {
            $date_limits = $p_limits['dates'];
            if (is_array($date_limits) && isset($date_limits['past'])) {
                $lim_span_past = 0 + $date_limits['past'];
                //$start_date = date('Y-m-d', (time() - ($lim_span_past * 24 * 60 * 60)));
            }
            if (is_array($date_limits) && isset($date_limits['next'])) {
                $lim_span_next = 0 + $date_limits['next'];
                //$end_date = date('Y-m-d', (time() + ($lim_span_past * 24 * 60 * 60)));
            }
        }
        //if (array_key_exists('start_date', $p_otherParams)) {
        //    $start_date = $p_otherParams['start_date'];
        //}
        $cat_limits = null;
        if ((!empty($p_limits)) && (array_key_exists('categories', $p_limits))) {
            $cat_limits = $p_limits['categories'];
        }

        $parser = new EventData_Parser_SimpleXML($this->m_last_events);

        $events_all = array();
        $events_dif = array();
        $files = array();

        $dir_handle = null;
        try {
            $dir_handle = opendir($this->m_dirs['use']);
        }
        catch (Exception $exc) {
            return false;
        }

        $proc_files = array();
        if ($dir_handle) {
            while (false !== ($event_file = readdir($dir_handle))) {
                $event_file_path = $this->m_dirs['use'] . $event_file;

                if (!is_file($event_file_path)) {
                    continue;
                }
                if (basename($event_file_path) == $this->m_working) {
                    continue;
                }
                $event_file_path_arr = explode('.', $event_file_path);
                if ('json' == strtolower($event_file_path_arr[count($event_file_path_arr) - 1])) {
                    continue;
                }
                $proc_files[] = $event_file_path;
            }
            closedir($dir_handle);
        }

        if (!empty($proc_files)) {
            sort($proc_files);
        }

        foreach ($proc_files as $event_file_path) {
            $event_file_path_arr = explode('.', $event_file_path);
            $event_file_path_arr_last_rank = count($event_file_path_arr) - 1;
            if (0 < $event_file_path_arr_last_rank) {
                if ('gz' == strtolower($event_file_path_arr[$event_file_path_arr_last_rank])) {
                    $event_file_path = 'compress.zlib://' . $event_file_path;
                }
            }
            try {
                $result = $parser->parse($events_all, $events_dif, $this->m_provider, $event_file_path, $p_categories, $lim_span_past, $lim_span_next, $cat_limits, $p_cancels);
            }
            catch (Exception $exc) {
                //var_dump($exc);
            }
            $files[] = $event_file;
        }
        return array('files' => $files, 'events_dif' => $events_dif, 'events_all' => $events_all);
    } // fn parse
} // class EventData_Parser

/**
 * EventData Parser that makes use of the SimpleXML PHP extension.
 */
class EventData_Parser_SimpleXML {

    /**
     * Data from the last run.
     * @var array
     */
    var $m_last_events = null;

    /**
     * Takes auxiliary data (possibly) used at other work
     *
     * @param string $p_lastEvents
     * @return void
     */
    public function __construct($p_lastEvents = null)
    {
        $this->m_last_events = $p_lastEvents;
    } // fn __construct

/*
    private function canUseTopic($p_event, $p_category, $p_catLimits)
    {
        $one_cat_skip = false;
        $one_cat_key = $p_category['key'];
        foreach ($p_catLimits as $cat_lim_key => $cat_lim_spec) {
            if ($cat_lim_key != $one_cat_key) {
                continue;
            }
            if (array_key_exists('towns', $cat_lim_spec)) {
                if (!in_array($p_event['town'], $cat_lim_spec['towns'])) {
                    $one_cat_skip = true;
                    break;
                }
            }
        }

        return (!$one_cat_skip);
    }
*/

    /**
     * Parses EventData data (by SimplXML)
     *
     * @param string $p_file file name of the eventdata file
     * @return array
     */
    function parse(&$p_events_all, &$p_events_dif, $p_provider, $p_file, $p_categories, $p_daysPast = null, $p_daysNext = null, $p_catLimits = null, $p_cancels = null) {
        if (empty($p_catLimits)) {
            $p_catLimits = array();
        }
        //if (empty($p_cancels)) {
        //    $p_cancels = array();
        //    $p_cancels[] = array('field' => 'evett1', 'value' => 'Abgesagt');
        //}

        libxml_clear_errors();
        $internal_errors = libxml_use_internal_errors(true);
        $xml = simplexml_load_file($p_file);

        // halt if loading produces an error
        if (!$xml) {
            $error_msg = '';
            foreach (libxml_get_errors() as $err_line) {
                $error_msg .= $err_line->message;
            }
            libxml_clear_errors();
            return false;
            //return array("correct" => false, "errormsg" => $error_msg);
        }

        $limit_date_start = null;
        $limit_date_end = null;

        // we expect the datetime as e.g. '01.09.2011-01:30:27'
        try {
            $exp_date_time = explode('-', (string) date('d.m.Y-H:i:s'));
            //$exp_date_time = explode('-', (string) $xml->export->date);

            $exp_date = explode('.', $exp_date_time[0]);
            $exp_year = 0 + $exp_date[2];
            $exp_month = 0 + ltrim($exp_date[1], '0');
            $exp_day = 0 + ltrim($exp_date[0], '0');

            $exp_time = explode(':', $exp_date_time[1]);
            $exp_hour = 0 + ltrim($exp_time[0], '0');
            $exp_min = 0 + ltrim($exp_time[1], '0');
            $exp_sec = 0 + ltrim($exp_time[2], '0');

            $correct_datetime = true;
            if (2000 >= $exp_year) {$correct_datetime = false;}
            if ((0 >= $exp_month) || (13 <= $exp_month)) {$correct_datetime = false;}
            if ((0 >= $exp_day) || (32 <= $exp_day)) {$correct_datetime = false;}

            if ($correct_datetime) {
                $exp_time = mktime($exp_hour, $exp_min, $exp_sec, $exp_month, $exp_day, $exp_year);
                if (!empty($p_daysPast)) {
                    $limit_date_start = date('Y-m-d', ($exp_time - ($p_daysPast * 24 * 60 * 60)));
                }
                if (!empty($p_daysNext)) {
                    $limit_date_end = date('Y-m-d', ($exp_time + ($p_daysNext * 24 * 60 * 60)));
                }
            }
        }
        catch (Exception $exc) {
            $limit_date_start = null;
            $limit_date_end = null;
        }

        // $xml->date is an array of (per-day) event sets
        foreach ($xml->location as $event_location) {

            // array of events info
            $entry_set = $event_location->entry;

            // note that most of event properties are usually empty
            foreach ($entry_set as $event) {

                $event_info = array('provider_id' => $p_provider);
                $event_other = array();

                $e_canceled = false;
                if (!empty($p_cancels)) {
                    foreach ($p_cancels as $one_cancel_spec) {
                        $one_cancel_field = $one_cancel_spec['field'];
                        $one_cancel_value = $one_cancel_spec['value'];

                        if ($event->$one_cancel_field == $one_cancel_value) {
                            $e_canceled = true;
                        }
                    }
                }
                $event_info['canceled'] = $e_canceled;

                // Date, time - first here, for possible omiting passed events

                // * main date-time info

                $event_date = '0000-00-00';

                // year, four digits
                $x_evedatyeanum2 = trim('' . $event->evedatyeanum2);
                // month, two digits
                $x_evedatmonnum2 = trim('' . $event->evedatmonnum2);
                // day, two digits
                $x_evedatdaynum2 = trim('' . $event->evedatdaynum2);

                if ((!empty($x_evedatyeanum2)) && (!empty($x_evedatmonnum2)) && (!empty($x_evedatdaynum2))) {
                    if ((4 == strlen($x_evedatyeanum2)) && (2 == strlen($x_evedatmonnum2)) && (2 == strlen($x_evedatdaynum2))) {
                        $event_date = $x_evedatyeanum2 . '-' . $x_evedatmonnum2 . '-' . $x_evedatdaynum2;
                    }
                }
                $event_info['date'] = $event_date;

                if ($limit_date_start) {
                    if ($event_date < $limit_date_start) {
                        continue;
                    }
                }
                if ($limit_date_end) {
                    if ($event_date > $limit_date_end) {
                        continue;
                    }
                }

                // Ids: hidden fields, other ones will be visible

                // number, event id, shall be unique
                $x_eveid = trim('' . $event->eveid);
                $event_info['event_id'] = $x_eveid;

                // number, tour id, shall be shared among events of particular repeated actions
                $x_trnid = trim('' . $event->trnid);
                $event_info['tour_id'] = $x_trnid;

                // number, location id
                $x_locid = trim('' . $event_location->locid);
                $event_info['location_id'] = $x_locid;


                // Categories

                // event subcategory
                $x_catsub = trim('' . $event->catsub);
                $event_info['genre'] = $x_catsub;

                // location hot
                $x_lochot = trim('' . $event_location->lochot);
                $event_info['rated'] = ( ((!empty($x_lochot)) && ('0' != $x_lochot)) ? true : false );

                // * main location info
                // town name (used at topics limiting)
                $x_twnnam = trim('' . $event->twnnam);
                $event_info['town'] = $x_twnnam;

                $event_topics = array();
                // * main type fields
                // event category
                $x_catnam = strtolower(trim('' . $event->catnam));
                $c_other = null;
                foreach ($p_categories as $one_category) {
                    if (!is_array($one_category)) {
                        continue;
                    }

                    $one_cat_skip = false;
                    $one_cat_key = $one_category['key'];
                    foreach ($p_catLimits as $cat_lim_key => $cat_lim_spec) {
                        if ($cat_lim_key != $one_cat_key) {
                            continue;
                        }
                        if (array_key_exists('towns', $cat_lim_spec)) {
                            if (!in_array($event_info['town'], $cat_lim_spec['towns'])) {
                                $one_cat_skip = true;
                                break;
                            }
                        }
                    }

                    //if (!canUseTopic($event_info, $one_category, $p_catLimits)) {
                    if ($one_cat_skip) {
                        continue;
                    }

                    if (array_key_exists('fixed', $one_category)) {
                        $event_topics[] = $one_category['fixed'];
                        continue;
                    }
                    if (array_key_exists('other', $one_category)) {
                        $c_other = $one_category['other'];
                        continue;
                    }
                    if ((array_key_exists('match_xml', $one_category)) && (array_key_exists('match_topic', $one_category))) {
                        $one_cat_match_xml = $one_category['match_xml'];
                        $one_cat_match_topic = $one_category['match_topic'];
                        if ((!is_array($one_cat_match_xml)) || (!is_array($one_cat_match_topic))) {
                            continue;
                        }
                        if (in_array($x_catnam, $one_cat_match_xml)) {
                            $event_topics[] = $one_cat_match_topic;
                            continue;
                        }
                    }
                }
                if (empty($event_topics)) {
                    if (!empty($c_other)) {
                        $event_topics[] = $c_other;
                    }
                }
                if (empty($event_topics)) {
                    continue;
                }

                $event_info['topics'] = $event_topics;

                // Location

                // * main display provider name
                // event location name
                // the 'trnorg'/'tour_organization' is a similar/related info
                $x_locnam = trim('' . $event_location->locnam);
                $event_info['organizer'] = $x_locnam; // may be overwritten by tour_organizer

                // !!! no country info
                $event_info['country'] = 'ch';

                // * main location info
                // town name
                //$x_twnnam = trim('' . $event->twnnam);
                //$event_info['town'] = $x_twnnam;

                // zip code
                $x_loczip = trim('' . $event_location->loczip);
                $event_info['zipcode'] = $x_loczip;

                // region info
                $e_region = '';
                $e_subregion = '';
                $e_region_info = RegionInfo::ZipRegion($event_info['zipcode'], $event_info['country']);
                if (!empty($e_region_info)) {
                    if (isset($e_region_info['region'])) {
                        $e_region = $e_region_info['region'];
                    }
                    if (isset($e_region_info['subregion'])) {
                        $e_subregion = $e_region_info['subregion'];
                    }
                }
                $event_info['region'] = $e_region;
                $event_info['subregion'] = $e_subregion;

                // street address, free form, but usually 'street_name house_number'
                $x_locadr = trim('' . $event_location->locadr);
                $event_info['street'] = $x_locadr;

                // * minor location info
                // other location specification
                $x_locade = trim('' . $event_location->locade);
                if (!empty($x_locade)) {
                    $event_other[] = $x_locade;
                }

                // directions to the location
                $x_locacc = trim('' . $event_location->locacc);
                if (!empty($x_locacc)) {
                    $event_other[] = $x_locacc;
                }


                // Tour

                // * main display tour name
                // tour name
                $x_trnnam = trim('' . $event->trnnam);
                $event_info['headline'] = $x_trnnam;

                // tour organizer
                $x_trnorg = trim('' . $event->trnorg);
                if (!empty($x_trnorg)) {
                    $event_info['organizer'] = $x_trnorg;
                }

                // tour language
                $x_trnlan = trim('' . $event->trnlan);
                $event_info['languages'] = $x_trnlan;

                // * additional usually empty info
                // minimal age of tour visitors
                $x_trnage = trim('' . $event->trnage);
                $event_info['minimal_age'] = $x_trnage;

                // additional text, usually (but not always) long-term days if anything at all
                $x_trntxx = trim('' . $event->trntxx);
                if (!empty($x_trntxx)) {
                    $event_other[] = $x_trntxx;
                }

                // Event

                // other event location specification
                $x_eveloz = trim('' . $event->eveloz);
                if (!empty($x_eveloz)) {
                    $event_other[] = $x_eveloz;
                }

                // Descriptions

                $event_info['description'] = '';
                $event_texts = array();

                // * usually the main texts

                $event_tour_text = '';

                // long description
                $x_trntxl = trim('' . $event->trntxl);
                if (empty($event_tour_text)) {
                    if (!empty($x_trntxl)) {
                        $event_tour_text = $x_trntxl;
                    }
                }

                // middle description
                $x_trntxm = trim('' . $event->trntxm);
                if (empty($event_tour_text)) {
                    if (!empty($x_trntxm)) {
                        $event_tour_text = $x_trntxm;
                    }
                }

                // short description
                $x_trntxs = trim('' . $event->trntxs);
                if (empty($event_tour_text)) {
                    if (!empty($x_trntxs)) {
                        $event_tour_text = $x_trntxs;
                    }
                }

                if (!empty($event_tour_text)) {
                    $event_texts[] = $event_tour_text;
                }

                // * short additional info

                $event_tour_subtitle = '';

                // subtitle
                $x_trntt3 = trim('' . $event->trntt3);
                if (empty($event_tour_subtitle)) {
                    if (!empty($x_trntt3)) {
                        $event_tour_subtitle = $x_trntt3;
                    }
                }

                // subtitle
                $x_trntt2 = trim('' . $event->trntt2);
                if (empty($event_tour_subtitle)) {
                    if (!empty($x_trntt2)) {
                        $event_tour_subtitle = $x_trntt2;
                    }
                }

                // subtitle
                $x_trntt1 = trim('' . $event->trntt1);
                if (empty($event_tour_subtitle)) {
                    if (!empty($x_trntt1)) {
                        $event_tour_subtitle = $x_trntt1;
                    }
                }

                if (!empty($event_tour_subtitle)) {
                    $event_texts[] = $event_tour_subtitle;
                }

                // * additional notices

                $event_event_subtitle = '';

                // subtitle
                $x_evett3 = trim('' . $event->evett3);
                if (empty($event_event_subtitle)) {
                    if (!empty($x_evett3)) {
                        $event_event_subtitle = $x_evett3;
                    }
                }

                // subtitle
                $x_evett2 = trim('' . $event->evett2);
                if (empty($event_event_subtitle)) {
                    if (!empty($x_evett2)) {
                        $event_event_subtitle = $x_evett2;
                    }
                }

                // subtitle
                $x_evett1 = trim('' . $event->evett1);
                if (empty($event_event_subtitle)) {
                    if (!empty($x_evett1)) {
                        $event_event_subtitle = $x_evett1;
                    }
                }

                if (!empty($event_event_subtitle)) {
                    $event_texts[] = $event_event_subtitle;
                }

                $one_text_rank = -1;
                foreach ($event_texts as $one_text) {
                    $one_text_rank += 1;
                    if (0 == $one_text_rank) {
                        $event_info['description'] = $one_text;
                        continue;
                    }
                    $event_other[] = $one_text;
                }

                // Date, time

                // * main date-time info

                // year, four digits
                $x_evedatyeanum2 = trim('' . $event->evedatyeanum2);
                $event_info['date_year'] = $x_evedatyeanum2;

                // month, one/two digits
                $x_evedatmonnum1 = trim('' . $event->evedatmonnum1);
                $event_info['date_month'] = $x_evedatmonnum1;

                // day, one/two digits
                $x_evedatdaynum1 = trim('' . $event->evedatdaynum1);
                $event_info['date_day'] = $x_evedatdaynum1;

                // hours, like '14.30'
                $x_eveda2 = trim('' . $event->eveda2);
                $event_info['time'] = $x_eveda2;

                // * plaint text days/hours span info

                $x_eveda2 = trim('' . $event->eveda2);
                $event_info['date_time_text'] = $x_eveda2;

                $event_date_time_text = array();

                // hour span
                $x_evemtx = trim('' . $event->evemtx);
                if (!empty($x_evemtx)) {
                    $event_date_time_text[] = $x_evemtx;
                }

                // long-term hours
                $x_lochou = trim('' . $event_location->lochou);
                if (!empty($x_lochou)) {
                    $event_date_time_text[] = $x_lochou;
                }

                // hour span
                $x_evehou = trim('' . $event->evehou);
                if (!empty($x_evehou)) {
                    $event_date_time_text[] = $x_evehou;
                }

                if (!empty($event_date_time_text)) {
                    $event_info['date_time_text'] = implode("\n", $event_date_time_text);
                }

                // Prices, mostly empty, frequently inconsistent

                $event_info['prices'] = '';

                // number, better to ignore
                $x_trnpri = trim('' . $event->trnpri);
                if (!empty($x_trnpri)) {
                    $event_info['prices'] = $x_trnpri;
                }

                // number, better to ignore
                $x_evepri = trim('' . $event->evepri);
                if (!empty($x_evepri)) {
                    $event_info['prices'] = $x_evepri;
                }

                // plain text, may be used
                $x_trnptx = trim('' . $event->trnptx);
                if (!empty($x_trnptx)) {
                    $event_info['prices'] = $x_trnptx;
                }

                // plain text, may be used
                $x_eveptx = trim('' . $event->eveptx);
                if (!empty($x_eveptx)) {
                    $event_info['prices'] = $x_eveptx;
                }

                // Links

                $event_web = '';

                // location web url
                $x_locurl = trim('' . $event_location->locurl);
                $event_web = $x_locurl;

                // tour web url
                $x_trnurl = trim('' . $event->trnurl);
                if (!empty($x_trnurl)) {
                    if (!empty($event_web)) {
                        $event_other[] = $x_trnurl;
                    }
                    else {
                        $event_web = $x_trnurl;
                    }
                }

                $event_other_links = array();

                // location web links
                $event_other_links[] = trim('' . $event_location->loclnk);

                // tour web links
                $event_other_links[] = trim('' . $event->trnlnk);

                // event web links
                $event_other_links[] = trim('' . $event->evelnk);

                foreach ($event_other_links as $other_links) {
                    if (!empty($other_links)) {
                        if (!empty($event_web)) {
                            $event_other[] = $other_links;
                        }
                        else {
                            $other_links_arr_tmp = explode("\n", $other_links);
                            $other_links_arr = array();
                            foreach ($other_links_arr_tmp as $one_links) {
                                $one_links = trim($one_links);
                                if (!empty($one_links)) {
                                    $other_links_arr_arr[] = $one_links;
                                }
                            }
                            if (0 < count($other_links_arr_arr)) {
                                $event_web = $other_links_arr_arr[0];
                            }
                            if (1 < count($other_links_arr_arr)) {
                                $event_other[] = $other_links;
                            }
                        }
                    }
                }

                $event_info['web'] = $event_web;

                // location email address
                $x_locema = trim('' . $event_location->locema);
                $event_info['email'] = $x_locema;

                // location phone number
                $x_loctel = trim('' . $event_location->loctel);
                $event_info['phone'] = $x_loctel;

                // Multimedia
                // * list (usually by newlines) of links plus names (space separated)
                // images

                $event_images = array();

                $x_eveimg = trim('' . $event->eveimg);
                if (!empty($x_eveimg)) {
                    $x_eveimg = trim($x_eveimg);
                    $x_eveimg_arr = explode("\n", $x_eveimg);
                    foreach ($x_eveimg_arr as $one_image) {
                        $one_image = trim($one_image);
                        if ('http' != substr($one_image, 0, 4)) {
                            continue;
                        }
                        $one_image_desc_start = strpos($one_image, ' ');
                        if (false === $one_image_desc_start) {
                            $event_images[] = array('url' => $one_image, 'label' => null);
                            continue;
                        }
                        $one_image_desc = trim(substr($one_image, $one_image_desc_start));
                        if ('' == $one_image_desc) {
                            $one_image_desc = null;
                        }
                        if ('default image' == strtolower($one_image_desc)) {
                            $one_image_desc = null;
                        }

                        $event_images[] = array('url' => trim(substr($one_image, 0, $one_image_desc_start)), 'label' => $one_image_desc);
                    }
                }

                $event_info['images'] = $event_images;

                // videos
                $x_evevid = trim('' . $event->evevid);
                if (!empty($x_evevid)) {
                    $event_other[] = $x_evevid;
                }

                // audios
                $x_eveaud = trim('' . $event->eveaud);
                if (!empty($x_eveaud)) {
                    $event_other[] = $x_eveaud;
                }

                $event_info['other'] = $event_other;

                // geo data
                $x_loclat = trim('' . $event_location->loclat);
                $x_loclng = trim('' . $event_location->loclng);

                $event_info['geo'] = null;
                if ((!empty($x_loclat)) && (!empty($x_loclng))) {
                    $event_info['geo'] = array('longitude' => $x_loclng, 'latitude' => $x_loclat);
                }

                $p_events_all[$event_info['event_id']] = $event_info;

                if (!empty($this->m_last_events)) {
                    if (array_key_exists($event_info['event_id'], $this->m_last_events)) {
                        if (json_encode($event_info) == $this->m_last_events[$event_info['event_id']]) {
                            continue;
                        }
                    }
                }

                $p_events_dif[$event_info['event_id']] = $event_info;
            }

        }

        //return $p_events;
        return true;
    } // fn parse
} // class EventData_Parser_SimpleXML

