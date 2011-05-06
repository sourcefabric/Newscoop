<?php
/**
 * @package Newscoop
 * @subpackage Soundcloud plugin
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

$info = array(
    'name' => 'soundcloud',
    'version' => '0.1.0',
    'label' => 'Soundcloud',
    'description' => 'This plugin provides Soundcloud integration.',
    'menu' => array(
        'name' => 'soundcloud',
        'label' => 'Soundcloud',
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
     * getGS('User may manage Soundcloud preferences');
     * getGS('User may browse Soundcloud tracks');
     * getGS('User may upload tracks to Soundcloud');
     * getGS('User may update Soundcloud track data');
     * getGS('User may delete Soundcloud tracks');
     */
        'plugin_soundcloud_preferences' => 'User may manage Soundcloud preferences',
        'plugin_soundcloud_browser' => 'User may browse Soundcloud tracks',
        'plugin_soundcloud_upload' => 'User may upload tracks to Soundcloud',
        'plugin_soundcloud_update' => 'User may update Soundcloud track data',
        'plugin_soundcloud_delete' => 'User may delete Soundcloud tracks',
    ),
    'template_engine' => array(
        'objecttypes' => array(),
        'listobjects' => array(),
        'init' => 'plugin_soundcloud_init'
    ),
    'localizer' => array(
        'id' => 'plugin_soundcloud',
        'path' => '/plugins/soundcloud/*/*/*/*/*',
        'screen_name' => 'Soundcloud'
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

        require_once($GLOBALS['g_campsiteDir'].'/install/classes/CampInstallationBase.php');
        $GLOBALS['g_db'] = $GLOBALS['g_ado_db'];
        $errors = CampInstallationBaseHelper::ImportDB(CS_PATH_PLUGINS.DIR_SEP.'soundcloud/install/sql/plugin_soundcloud.sql', $error_queries);
        unset($GLOBALS['g_db']);
    }

    function plugin_soundcloud_uninstall()
    {
        global $LiveUserAdmin, $g_ado_db;

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

        $g_ado_db->execute('DROP TABLE plugin_soundcloud');
    }

    function plugin_soundcloud_update()
    {
    }

    function plugin_soundcloud_init(&$p_context)
    {
    }
}

?>