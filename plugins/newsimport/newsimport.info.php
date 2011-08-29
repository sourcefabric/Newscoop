<?php

require_once($GLOBALS['g_campsiteDir'].DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'SystemPref.php');
require_once($GLOBALS['g_campsiteDir'].DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'TopicName.php');
require_once($GLOBALS['g_campsiteDir'].DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'Topic.php');

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
/*
    'template_engine' => array(
        'objecttypes' => array(
            array('newsimport' => array('class' => 'NewsImport')),
        ),
        'listobjects' => array(),
        'init' => 'plugin_newsimport_init'
    ),
*/
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

    function plugin_newsimport_set_preferences() {
        $incl_dir = dirname(__FILE__) . '/include/';
        require($incl_dir . 'default_access.php');
        require($incl_dir . 'default_topics.php');

        $cur_nimp_auth = SystemPref::Get('NewsImportAuthorization');
        if (empty($cur_nimp_auth)) {
            SystemPref::Set('NewsImportAuthorization', $newsimport_default_access);
        }

        $cur_nimp_catn = SystemPref::Get('EventsCategoryTheater');
        if (empty($cur_nimp_catn)) {
            SystemPref::Set('EventsCategoryTheater', 'Theaters');
        }
        $cur_nimp_catn = SystemPref::Get('EventsCategoryExhibition');
        if (empty($cur_nimp_catn)) {
            SystemPref::Set('EventsCategoryExhibition', 'Exhibitions');
        }
        $cur_nimp_catn = SystemPref::Get('EventsCategoryParty');
        if (empty($cur_nimp_catn)) {
            SystemPref::Set('EventsCategoryParty', 'Parties');
        }
        $cur_nimp_catn = SystemPref::Get('EventsCategoryMusic');
        if (empty($cur_nimp_catn)) {
            SystemPref::Set('EventsCategoryMusic', 'Music');
        }
        $cur_nimp_catn = SystemPref::Get('EventsCategoryConcert');
        if (empty($cur_nimp_catn)) {
            SystemPref::Set('EventsCategoryConcert', 'Concerts');
        }
    }

    function plugin_newsimport_set_event_topics() {
        $topic_obj = new Topic(); // 'Event', 1
        $topic_obj->create(array('names' => array(1 => 'Event')));
        $ev_root = $topic_obj->getTopicId();

        $topic_obj = new Topic();
        $topic_obj->create(array('parent_id' => $ev_root, 'names' => array(1 => 'Theaters')));

        $topic_obj = new Topic();
        $topic_obj->create(array('parent_id' => $ev_root, 'names' => array(1 => 'Exhibitions')));

        $topic_obj = new Topic();
        $topic_obj->create(array('parent_id' => $ev_root, 'names' => array(1 => 'Parties')));

        $topic_obj = new Topic();
        $topic_obj->create(array('parent_id' => $ev_root, 'names' => array(1 => 'Music')));

        $topic_obj = new Topic();
        $topic_obj->create(array('parent_id' => $ev_root, 'names' => array(1 => 'Concerts')));
    }

    function plugin_newsimport_install()
    {
        plugin_newsimport_set_preferences();
        plugin_newsimport_set_event_topics();
        SystemPref::Set('NewsImportUsage', '1');
    }
    function plugin_newsimport_enable()
    {
        plugin_newsimport_set_preferences();
        SystemPref::Set('NewsImportUsage', '1');
    }
    function plugin_newsimport_disable()
    {
        SystemPref::Set('NewsImportUsage', '0');
    }

    function plugin_newsimport_uninstall()
    {
        SystemPref::Set('NewsImportUsage', '0');
/*
        global $LiveUserAdmin, $g_ado_db;

        foreach (array('plugin_recaptcha_admin') as $right_def_name) {
            $filter = array(
                "fields" => array("right_id"),
                "filters" => array("right_define_name" => $right_def_name)
            );
            $rights = $LiveUserAdmin->getRights($filter);
            if(!empty($rights)) {
                $LiveUserAdmin->removeRight(array('right_id' => $rights[0]['right_id']));
            }
        }
*/
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
