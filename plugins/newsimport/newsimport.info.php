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
/*
        'objecttypes' => array(
            array('newsimport' => array('class' => 'NewsImport')),
        ),
        'listobjects' => array(),
        'init' => 'plugin_newsimport_init'
*/
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

    function plugin_newsimport_create_event_type() {
        $art_type_name = 'event_type';

        $art_type_obj = new ArticleType($art_type_name);
        if (!$art_type_obj->exists()) {
            $art_type_obj->create();
        }

        $art_fields = array(
            'event_id' => array('type' => 'numeric', 'params' => array('precision' => 0)),
            'event_name' => array('type' => 'text', 'params' => array()),
        );

        foreach ($art_fields as $one_field_name => $one_field_params) {
            $art_type_filed_obj = new ArticleTypeField($art_type_name, $one_field_name);
            if (!$art_type_filed_obj->exists()) {
                $art_type_filed_obj->create($one_field_params['type'], $one_field_params['params']);
            }
        }
    }

    function plugin_newsimport_set_preferences() {
        $incl_dir = dirname(__FILE__) . '/include/';
        require($incl_dir . 'default_access.php');

        $cur_nimp_auth = SystemPref::Get('NewsImportAuthorization');
        if (empty($cur_nimp_auth)) {
            SystemPref::Set('NewsImportAuthorization', $newsimport_default_access);
        }
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
        $incl_dir = dirname(__FILE__) . '/include/';
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
    }
    function plugin_newsimport_enable()
    {
        plugin_newsimport_set_preferences();
        plugin_newsimport_set_event_topics();
        plugin_newsimport_create_event_type();
        SystemPref::Set('NewsImportUsage', '1');
    }
    function plugin_newsimport_disable()
    {
        SystemPref::Set('NewsImportUsage', '0');
    }

    function plugin_newsimport_uninstall()
    {
        SystemPref::Set('NewsImportUsage', '0');
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
