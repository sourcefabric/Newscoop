<?php
/**
 * @package Newscoop
 * @subpackage SoundCloud plugin
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

$info = array(
    'name' => 'soundcloud',
    'version' => '0.1.0',
    'label' => 'SoundCloud',
    'description' => 'This plugin provides SoundCloud integration.',
    'menu' => array(
        'name' => 'soundcloud',
        'label' => 'SoundCloud',
        'icon' => '',
        'permission' => 'plugin_soundcloud_browser',
        'path' => 'soundcloud/manager.php',
        'sub' => array(
            array(
                'permission' => 'plugin_soundcloud_preferences',
                'path' => "soundcloud/preferences.php",
                'label' => 'Preferences',
                'icon' => '',
            ),
            array(
                'permission' => 'plugin_soundcloud_browser',
                'path' => "soundcloud/manager.php",
                'label' => 'Track manager',
                'icon' => '',
            ),
        ),
    ),
    'userDefaultConfig' => array(
        'plugin_soundcloud' => 'N',
    ),
    'permissions' => array(
    /**
     * Do not remove this comment: it is needed for the localizer
     * getGS('User may manage SoundCloud preferences');
     * getGS('User may browse SoundCloud tracks');
     * getGS('User may upload tracks to SoundCloud');
     * getGS('User may update SoundCloud track data');
     * getGS('User may delete SoundCloud tracks');
     */
        'plugin_soundcloud_preferences' => 'User may manage SoundCloud preferences',
        'plugin_soundcloud_browser' => 'User may browse SoundCloud tracks',
        'plugin_soundcloud_upload' => 'User may upload tracks to SoundCloud',
        'plugin_soundcloud_update' => 'User may update SoundCloud track data',
        'plugin_soundcloud_delete' => 'User may delete SoundCloud tracks',
    ),
    'template_engine' => array(
        'objecttypes' => array(),
        'listobjects' => array(),
        'init' => 'plugin_soundcloud_init'
    ),
    'localizer' => array(
        'id' => 'plugin_soundcloud',
        'path' => '/plugins/soundcloud/*/*/*/*/*',
        'screen_name' => 'SoundCloud'
    ),
    'no_menu_scripts' => array(
        '/soundcloud/controller.php',
        '/soundcloud/attachement.php',
    ),

    'install' => 'plugin_soundcloud_install',
    'enable' => 'plugin_soundcloud_install',
    'update' => 'plugin_soundcloud_update',
    'disable' => '',
    'uninstall' => 'plugin_soundcloud_uninstall'
);

if (!defined('PLUGIN_SOUNDCLOUD_FUNCTIONS')) {
    define('PLUGIN_SOUNDCLOUD_FUNCTIONS', TRUE);

    function plugin_soundcloud_install()
    {
        global $LiveUserAdmin;

        $LiveUserAdmin->addRight(array('area_id' => 0, 'right_define_name' => 'plugin_soundcloud_preferences', 'has_implied' => 1));
        $LiveUserAdmin->addRight(array('area_id' => 0, 'right_define_name' => 'plugin_soundcloud_browser', 'has_implied' => 1));
        $LiveUserAdmin->addRight(array('area_id' => 0, 'right_define_name' => 'plugin_soundcloud_upload', 'has_implied' => 1));
        $LiveUserAdmin->addRight(array('area_id' => 0, 'right_define_name' => 'plugin_soundcloud_update', 'has_implied' => 1));
        $LiveUserAdmin->addRight(array('area_id' => 0, 'right_define_name' => 'plugin_soundcloud_delete', 'has_implied' => 1));

        $Admin = new UserType(1);
        $Admin->setPermission('plugin_soundcloud_preferences', true);
        $Admin->setPermission('plugin_soundcloud_browser', true);
        $Admin->setPermission('plugin_soundcloud_upload', true);
        $Admin->setPermission('plugin_soundcloud_update', true);
        $Admin->setPermission('plugin_soundcloud_delete', true);

        $container = \Zend_Registry::get('container');
        $databaseConnection = $container->get('database_connection');
        $installerDatabaseService = new \Newscoop\Installer\Services\DatabaseService($container->get('logger'));
        $installerDatabaseService->importDB(CS_PATH_PLUGINS.DIR_SEP.'soundcloud/install/sql/plugin_soundcloud.sql', $databaseConnection);
    }

    function plugin_soundcloud_uninstall()
    {
        global $LiveUserAdmin;

        $aRights = array(
            'plugin_soundcloud_preferences',
            'plugin_soundcloud_browser',
            'plugin_soundcloud_upload',
            'plugin_soundcloud_update',
            'plugin_soundcloud_delete',
        );
        foreach ($aRights as $right_def_name) {
            $filter = array(
                "fields" => array("right_id"),
                "filters" => array("right_define_name" => $right_def_name)
            );
            $rights = $LiveUserAdmin->getRights($filter);
            if(!empty($rights)) {
                $LiveUserAdmin->removeRight(array('right_id' => $rights[0]['right_id']));
            }
        }

        $container = \Zend_Registry::get('container');
        $databaseConnection = $container->get('database_connection');

        $databaseConnection->executeQuery('DROP TABLE plugin_soundcloud');
    }

    function plugin_soundcloud_update()
    {
    }

    function plugin_soundcloud_init(&$p_context)
    {
    }
}
