<?php
require_once $GLOBALS['g_campsiteDir'] . '/classes/LiveUserMock.php';

$info = array(
    'name' => 'recaptcha',
    'version' => '0.1.0',
    'label' => 'reCAPTCHA',
    'description' => 'This plugin provides reCAPTCHA protection.',
    'menu' => array(
        'name' => 'recaptcha',
        'label' => 'reCAPTCHA',
        'icon' => '',
        'permission' => 'plugin_recaptcha_admin',
        'path' => 'recaptcha/admin/recaptcha_prefs.php',
    ),
    'userDefaultConfig' => array(
        'plugin_recaptcha_admin' => 'N',
    ),
    'permissions' => array(
    /**
     * Do not remove this comment: it is needed for the localizer
     * getGS('User may manage reCAPTCHA');
     */
    	'plugin_recaptcha_admin' => 'User may manage reCAPTCHA',
    ),
    'template_engine' => array(
        'objecttypes' => array(
            array('recaptcha' => array('class' => 'ReCAPTCHA')),
        ),
        'listobjects' => array(),
        'init' => 'plugin_recaptcha_init'
    ),
    'localizer' => array(
        'id' => 'plugin_recaptcha',
        'path' => '/plugins/recaptcha/*/*/*/*/*',
        'screen_name' => 'reCAPTCHA'
    ),
    'no_menu_scripts' => array(),
    'install' => 'plugin_recaptcha_install',
    'enable' => 'plugin_recaptcha_install',
    'update' => 'plugin_recaptcha_update',
    'disable' => '',
    'uninstall' => 'plugin_recaptcha_uninstall'
);

if (!defined('PLUGIN_RECAPTCHA_FUNCTIONS')) {
    define('PLUGIN_RECAPTCHA_FUNCTIONS', TRUE);

    function plugin_recaptcha_install()
    {
        global $LiveUserAdmin;

        $LiveUserAdmin->addRight(array('area_id' => 0, 'right_define_name' => 'plugin_recaptcha_admin', 'has_implied' => 1));
    }

    function plugin_recaptcha_uninstall()
    {
        global $LiveUserAdmin;

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
    }

    function plugin_recaptcha_update()
    {
    }

    function plugin_recaptcha_init(&$p_context)
    {
    }

    function plugin_recaptcha_addPermissions()
    {
        $Admin = new UserType(1);
        $Admin->setPermission('plugin_recaptcha_admin', true);
    }
}
