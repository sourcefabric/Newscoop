<?php

require_once($GLOBALS['g_campsiteDir'].DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'SystemPref.php');
require_once($GLOBALS['g_campsiteDir'].DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'TopicName.php');
require_once($GLOBALS['g_campsiteDir'].DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'Topic.php');

require_once($GLOBALS['g_campsiteDir'].DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'ArticleType.php');
require_once($GLOBALS['g_campsiteDir'].DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'ArticleTypeField.php');

$info = array(
    'name' => 'newsimport',
    'version' => '0.1.0',
    'label' => 'NewsImport',
    'description' => 'This plugin provides import from news agencies.',
    'menu' => array(
        'name' => 'newsimport',
        'label' => 'NewsImport',
        'icon' => '',
        'permission' => 'plugin_newsimport_admin',
        'path' => 'newsimport/admin/newsimport_prefs.php',
    ),
    'userDefaultConfig' => array(
        'plugin_newsimport' => 'N',
    ),
    'permissions' => array(
    /**
     * Do not remove this comment: it is needed for the localizer
     * getGS('User may manage NewsImport');
     */
    	'plugin_newsimport_admin' => 'User may manage NewsImport',
    ),
    'template_engine' => array(
        'objecttypes' => array(),
        'listobjects' => array(),
        'init' => 'plugin_newsimport_init'
    ),
    'localizer' => array(
        'id' => 'plugin_newsimport',
        'path' => '/plugins/newsimport/*/*/*/*/*',
        'screen_name' => 'NewsImport'
    ),
    'no_menu_scripts' => array(),
    'install' => 'plugin_newsimport_install',
    'enable' => 'plugin_newsimport_enable',
    'update' => 'plugin_newsimport_update',
    'disable' => 'plugin_newsimport_disable',
    'uninstall' => 'plugin_newsimport_uninstall'
);

if (!defined('PLUGIN_NEWSIMPORT_FUNCTIONS')) {
    define('PLUGIN_NEWSIMPORT_FUNCTIONS', TRUE);

    function plugin_newsimport_set_url() {
        $plugin_inst_name = dirname(__FILE__).DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'news_feeds_intall.php';

        global $Campsite;
        $campsite_inst_dir = $Campsite['WEBSITE_URL'];

        $campsite_inst_php = '<?php' . "\n\n" . '$newsipmort_install = \'' . $campsite_inst_dir . '\';' . "\n\n";

        try {
            $plugin_inst_file = fopen($plugin_inst_name, 'w');
            fwrite($plugin_inst_file, $campsite_inst_php);
            fclose($plugin_inst_file);
        }
        catch (Exception $exc) {
            // may be some logging
        }
    }

    function plugin_newsimport_set_cron($p_state) {
        exec('crontab -l', $cron_output, $cron_result);
        if (0 != $cron_result) {
            return false;
        }

        $request_file = dirname(__FILE__).DIRECTORY_SEPARATOR.'admin-files'.DIRECTORY_SEPARATOR.'newsimport'.DIRECTORY_SEPARATOR.'cron'.DIRECTORY_SEPARATOR.'request_import.php';

        $new_cron = array();
        foreach ($cron_output as $one_cron_line) {
            if (false !== strpos($one_cron_line, $request_file)) {
                continue;
            }
            $new_cron[] = $one_cron_line;
        }
        if ($p_state) {

            $incl_dir = dirname(__FILE__).DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR;
            require($incl_dir . 'default_cron.php');

            $cron_min = $newsimport_cron['min'];
            $cron_hour = $newsimport_cron['hour'];

            $new_cron[] = $cron_min . ' ' . $cron_hour . ' * * * ' . $request_file;
        }

        $tmp_file_path = tempnam(sys_get_temp_dir(), '' . mt_rand(100, 999));
        $tmp_file = fopen($tmp_file_path, 'w');
        foreach ($new_cron as $one_cron_line) {
            fwrite($tmp_file, $one_cron_line);
            fwrite($tmp_file, "\n");
        }
        fclose($tmp_file);
        exec('crontab ' . escapeshellarg($tmp_file_path), $cron_output, $cron_result);
        unlink($tmp_file_path);
        if (0 != $cron_result) {
            return false;
        }

        return true;
    }


    function plugin_newsimport_create_event_type() {
        $art_type_name = 'event_general';

        $art_type_obj = new ArticleType($art_type_name);
        if (!$art_type_obj->exists()) {
            $art_type_obj->create();
        }

        $art_fields = array(
            // ids - auxiliary, hidden
            'provider_id' => array('type' => 'numeric', 'params' => array('precision' => 0), 'hidden' => true), // source of the news file
            'event_id' => array('type' => 'numeric', 'params' => array('precision' => 0), 'hidden' => true), // an event at an day from a provider should have unique id
            'tour_id' => array('type' => 'numeric', 'params' => array('precision' => 0), 'hidden' => true), // for grouping of repeated events, e.g. an exhibition available for more days
            'location_id' => array('type' => 'numeric', 'params' => array('precision' => 0), 'hidden' => true), // should be unique per place/provider
            // main event info - free form
            'headline' => array('type' => 'text', 'params' => array(), 'hidden' => false), // even/tour_name (or movie name)
            'organizer' => array('type' => 'text', 'params' => array(), 'hidden' => false), // either tour_organizer (if filled) or location_name (or cinema name)
            // address - free form
            'country' => array('type' => 'text', 'params' => array(), 'hidden' => false), // ch (i.e. Swiss country code)
            'zipcode' => array('type' => 'text', 'params' => array(), 'hidden' => false),
            'town' => array('type' => 'text', 'params' => array(), 'hidden' => false),
            'street' => array('type' => 'text', 'params' => array(), 'hidden' => false), // street address, including house number
            // date/time - fixed form
            'date' => array('type' => 'date', 'params' => array(), 'hidden' => false), // text, 2010-08-31
            'date_year' => array('type' => 'numeric', 'params' => array('precision' => 0), 'hidden' => false), // number, 2010
            'date_month' => array('type' => 'numeric', 'params' => array('precision' => 0), 'hidden' => false), // number, 8
            'date_day' => array('type' => 'numeric', 'params' => array('precision' => 0), 'hidden' => false), // number, 31
            'time' => array('type' => 'text', 'params' => array(), 'hidden' => false), // event_time, like 10:30 (or a list for movie screenings at a day)
            // date/time - free form
            'date_time_text' => array('type' => 'body', 'params' => array('editor_size' => 250, 'is_content' => 1), 'hidden' => false), // comprises other textual date/time information, if available
            // contact - free form
            'web' => array('type' => 'text', 'params' => array(), 'hidden' => false), // location_url if filled, or event/tour_link if some there
            'email' => array('type' => 'text', 'params' => array(), 'hidden' => false),
            'phone' => array('type' => 'text', 'params' => array(), 'hidden' => false),
            // text parts - free form
            'description' => array('type' => 'body', 'params' => array('editor_size' => 250, 'is_content' => 1), 'hidden' => false), // a (longer) text, if some available
            'other' => array('type' => 'body', 'params' => array('editor_size' => 250, 'is_content' => 1), 'hidden' => false), // other texts, web links to audio/video, ...
            // other details - free form
            'genre' => array('type' => 'text', 'params' => array(), 'hidden' => false), // Sonderausstellung/Dauerausstellung; Jazz, Festival, ... (or movie genre)
            'languages' => array('type' => 'text', 'params' => array(), 'hidden' => false), // usually empty
            'prices' => array('type' => 'body', 'params' => array('editor_size' => 250, 'is_content' => 1), 'hidden' => false), // some textual or numerical info, if available
            'minimal_age' => array('type' => 'text', 'params' => array(), 'hidden' => false), // textual or numerical info, if any, but usually empty
            // other details - fixed form
            'rated' => array('type' => 'switch', 'params' => array(), 'hidden' => false), // if of some restricted (hot/explicit) kind
            // category available as article topic
            // images (probably) as article images
            // geolocation (probably) as map POIs
        );

        foreach ($art_fields as $one_field_name => $one_field_params) {
            $art_type_filed_obj = new ArticleTypeField($art_type_name, $one_field_name);
            if (!$art_type_filed_obj->exists()) {
                $art_type_filed_obj->create($one_field_params['type'], $one_field_params['params']);
            }
            if (array_key_exists('hidden', $one_field_params) && $one_field_params['hidden']) {
                $art_type_filed_obj->setStatus('hide');
            }
        }
    }

    function plugin_newsimport_set_preferences() {
        $incl_dir = dirname(__FILE__).DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR;
        require($incl_dir . 'default_access.php');

        // not putting it into sysprefs, since the cron job would not be able to access it
        //$cur_nimp_auth = SystemPref::Get('Plugin_NewsImport_CommandToken');
        //if (empty($cur_nimp_auth)) {
        //    SystemPref::Set('Plugin_NewsImport_CommandToken', $newsimport_default_access);
        //}
    }

    function plugin_newsimport_set_one_topic($p_topicCat, $p_topicNames, $p_parentIds) {
        // setting the given event topic
        $ev_this_ids = array();
        $ev_this_names = array();

        foreach ($p_topicNames as $cat_lan_id => $cat_name) {
            $cat_key_sys_pref = 'EventCat' . $cat_lan_id . ucfirst($p_topicCat);
            $cat_name_sys_pref = SystemPref::Get($cat_key_sys_pref);
            if (!empty($cat_name_sys_pref)) {
                $cat_name = $cat_name_sys_pref;
            }
            $topic_name_obj = new TopicName($cat_name, $cat_lan_id);

            if ($topic_name_obj->m_exists) {
                // found something
                $one_ev_id = $topic_name_obj->getTopicId();
                $ev_this_ids[$cat_lan_id] = $one_ev_id;
            }
            else {
                $ev_this_names[$cat_lan_id] = $cat_name;
            }
        }

        if (!empty($ev_this_names)) {
            // some topic names do not exist

            if (!empty($ev_this_ids)) {
                // we can just translate topic names (e.g. from the first topic name)
                $use_topic_id = null;
                foreach ($ev_this_ids as $cat_lan_id => $the_topic_id) {
                    $use_topic_id = $the_topic_id;
                    break;
                }

                $topic_obj = new Topic($use_topic_id);
                $the_topic_id = $topic_obj->getTopicId();
                foreach ($ev_this_names as $one_cat_lang => $one_cat_name) {
                    $topic_obj->setName($one_cat_lang, $one_cat_name);
                    $ev_this_ids[$cat_lan_id] = $the_topic_id;
                }
            }
            else {
                // we have to create new topics
                if (!empty($p_parentIds)) {
                    // create child topic(s), group by parent ids
                    $parent_id_groups = array();
                    foreach ($p_parentIds as $par_lang => $par_id) {
                        if (!array_key_exists($par_id, $parent_id_groups)) {
                            $parent_id_groups[$par_id] = array();
                        }
                        $parent_id_groups[$par_id][$par_lang] = $ev_this_names[$par_lang];
                    }

                    foreach($parent_id_groups as $par_id => $ev_cat_names) {
                        $topic_obj = new Topic();
                        $topic_obj->create(array('parent_id' => $par_id, 'names' => $ev_cat_names));
                        $the_topic_id = $topic_obj->getTopicId();
                        foreach($ev_cat_names as $cat_lan_id => $cat_name) {
                            $ev_this_ids[$cat_lan_id] = $the_topic_id;
                        }
                    }
                }
                else {
                    // create one root topic
                    $topic_obj = new Topic();
                    $topic_obj->create(array('names' => $ev_this_names));

                    $the_topic_id = $topic_obj->getTopicId();
                    foreach($ev_this_names as $cat_lan_id => $cat_name) {
                        $ev_this_ids[$cat_lan_id] = $the_topic_id;
                    }

                }
            }
        }

        return $ev_this_ids;
    }

    function plugin_newsimport_set_event_topics() {
        $incl_dir = dirname(__FILE__).DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR;
        require($incl_dir . 'default_topics.php');

        // setting the root event topic
        $ev_root_id = null;

        $event_root_names = $newsimport_default_cat_names['event'];
        $ev_root_ids = plugin_newsimport_set_one_topic('event', $event_root_names, null);

        if (empty($ev_root_ids)) {
            // this shall not happen: either already having a root topic, or created one
            return false;
        }

        // setting the particular (non-root) event topics
        foreach ($newsimport_default_cat_names as $topic_cat_key => $topic_cat_names) {
            if ('event' == $topic_cat_key) {
                continue;
            }
            plugin_newsimport_set_one_topic($topic_cat_key, $topic_cat_names, $ev_root_ids);

        }
    }

    function plugin_newsimport_install()
    {
        plugin_newsimport_set_preferences();
        plugin_newsimport_set_event_topics();
        plugin_newsimport_create_event_type();
        SystemPref::Set('NewsImportUsage', '1');
        plugin_newsimport_set_cron(true);
        plugin_newsimport_set_url();
    }
    function plugin_newsimport_enable()
    {
        plugin_newsimport_set_preferences();
        plugin_newsimport_set_event_topics();
        plugin_newsimport_create_event_type();
        SystemPref::Set('NewsImportUsage', '1');
        plugin_newsimport_set_cron(true);
        plugin_newsimport_set_url();
    }
    function plugin_newsimport_disable()
    {
        SystemPref::Set('NewsImportUsage', '0');
        plugin_newsimport_set_cron(false);
    }

    function plugin_newsimport_uninstall()
    {
        SystemPref::Set('NewsImportUsage', '0');
        plugin_newsimport_set_cron(false);
    }

    function plugin_newsimport_update()
    {
    }

    function plugin_newsimport_init(&$p_context)
    {
    }

    function plugin_newsimport_addPermissions()
    {
        $Admin = new UserType(1);
        $Admin->setPermission('plugin_newsimport_admin', true);
    }
}
