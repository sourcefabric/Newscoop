<?php
/**
 * @package Newscoop
 * @subpackage SoundCloud plugin
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

require_once CS_PATH_PLUGINS.DIR_SEP.'soundcloud'.DIR_SEP.'classes'.DIR_SEP.'soundcloud.api.php';
$translator = \Zend_Registry::get('container')->getService('translator');
$preferencesService = \Zend_Registry::get('container')->getService('system_preferences_service');

if (!$g_user->hasPermission('plugin_soundcloud_preferences')) {
    camp_html_display_error($translator->trans('You do not have the right to manage SoundCloud preferences.', array(), 'plugin_soundcloud'));
    exit;
}

if (Input::Get('save') || Input::Get('check')) {
    if (!SecurityToken::isValid()) {
        camp_html_display_error($translator->trans('Invalid security token!'));
        exit;
    }

    $f_soundcloud_client_id = Input::Get('f_soundcloud_client_id', 'string');
    $f_soundcloud_client_secret = Input::Get('f_soundcloud_client_secret', 'string');
    $f_soundcloud_username = Input::Get('f_soundcloud_username', 'string');
    $f_soundcloud_password = Input::Get('f_soundcloud_password', 'string');

    $preferencesService->set('PLUGIN_SOUNDCLOUD_CLIENT_ID', $f_soundcloud_client_id);
    $preferencesService->set('PLUGIN_SOUNDCLOUD_CLIENT_SECRET', $f_soundcloud_client_secret);
    $preferencesService->set('PLUGIN_SOUNDCLOUD_USERNAME', $f_soundcloud_username);
    $preferencesService->set('PLUGIN_SOUNDCLOUD_PASSWORD', $f_soundcloud_password);
    $preferencesService->set('PLUGIN_SOUNDCLOUD_USER_ID', '');

    if (Input::Get('check')) {
        $soundcloud = new SoundcloudAPI();
        if ($soundcloud->login()) {
            camp_html_add_msg($translator->trans('SoundCloud checked successfully.', array(), 'plugin_soundcloud'), 'ok');
        } else {
            camp_html_add_msg($translator->trans('SoundCloud reports an error:', array(), 'plugin_soundcloud') . ' ' . $soundcloud->error, 'error');
        }
    } else {
        camp_html_add_msg($translator->trans('SoundCloud preferences updated.', array(), 'plugin_soundcloud'), 'ok');
    }
}

echo camp_html_breadcrumbs(array(
    array($translator->trans('Plugins'), $Campsite['WEBSITE_URL'] . '/admin/plugins/manage.php'),
    array($translator->trans('SoundCloud', array(), 'plugin_soundcloud'), ''),
    array($translator->trans('SoundCloud Preferences', array(), 'plugin_soundcloud'), ''),
));

camp_html_display_msgs();

include 'templates/preferences.php';
