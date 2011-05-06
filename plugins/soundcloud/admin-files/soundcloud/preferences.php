<?php
/**
 * @package Newscoop
 * @subpackage Soundcloud plugin
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

require_once CS_PATH_PLUGINS.DIR_SEP.'soundcloud'.DIR_SEP.'classes'.DIR_SEP.'soundcloud.api.php';
camp_load_translation_strings('plugin_soundcloud');

if (!$g_user->hasPermission('plugin_soundcloud_preferences')) {
    camp_html_display_error(getGS('You do not have the right to manage Soundcloud preferences.'));
    exit;
}

if (Input::Get('save') || Input::Get('check')) {
    if (!SecurityToken::isValid()) {
        camp_html_display_error(getGS('Invalid security token!'));
        exit;
    }

    $f_soundcloud_client_id = Input::Get('f_soundcloud_client_id', 'string');
    $f_soundcloud_client_secret = Input::Get('f_soundcloud_client_secret', 'string');
    $f_soundcloud_username = Input::Get('f_soundcloud_username', 'string');
    $f_soundcloud_password = Input::Get('f_soundcloud_password', 'string');

    SystemPref::Set('PLUGIN_SOUNDCLOUD_CLIENT_ID', $f_soundcloud_client_id);
    SystemPref::Set('PLUGIN_SOUNDCLOUD_CLIENT_SECRET', $f_soundcloud_client_secret);
    SystemPref::Set('PLUGIN_SOUNDCLOUD_USERNAME', $f_soundcloud_username);
    SystemPref::Set('PLUGIN_SOUNDCLOUD_PASSWORD', $f_soundcloud_password);
    SystemPref::Set('PLUGIN_SOUNDCLOUD_USER_ID', '');

    if (Input::Get('check')) {
        $soundcloud = new SoundcloudAPI();
        if ($soundcloud->login()) {
            camp_html_add_msg(getGS('Soundcloud checked successfully.'), 'ok');
        } else {
            camp_html_add_msg(getGS('Soundcloud reports an error:') . ' ' . $soundcloud->error, 'error');
        }
    } else {
        camp_html_add_msg(getGS('Soundcloud preferences updated.'), 'ok');
    }
}

echo camp_html_breadcrumbs(array(
    array(getGS('Plugins'), $Campsite['WEBSITE_URL'] . '/admin/plugins/manage.php'),
    array('Soundcloud', ''),
    array(getGS('Soundcloud Preferences'), ''),
));

camp_html_display_msgs();

include 'templates/preferences.php';
?>