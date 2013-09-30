<?php
/**
 * @package Newscoop
 * @subpackage SoundCloud plugin
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

include 'data.php';
require_once CS_PATH_PLUGINS.DIR_SEP.'soundcloud'.DIR_SEP.'classes'.DIR_SEP.'soundcloud.api.php';
$translator = \Zend_Registry::get('container')->getService('translator');

if (!$g_user->hasPermission('plugin_soundcloud_browser')) {
    camp_html_display_error($translator->trans('You do not have the right to manage SoundCloud tracks.', array(), 'plugin_soundcloud'));
    exit;
}

$soundcloud = new SoundcloudAPI();
$limit = 5;
$offset = 0;
$showMessage = array();
$track = array();
$js = null;
$article = Input::Get('article_id', 'string', null);
$action = Input::Get('action', 'string', null);
$track = array(
    'title' => Input::Get('title', 'string', null),
    'description' => Input::Get('description', 'string', null),
    'track_type' => Input::Get('track_type', 'string', null),
    'genre' => Input::Get('genre', 'string', null),
    'license' => Input::Get('license', 'string', null),
    'tag_list' => Input::Get('tag_list', 'string', null),
    'label_name' => Input::Get('label_name', 'string', null),
    'release' => Input::Get('release', 'string', null),
    'isrc' => Input::Get('isrc', 'string', null),
    'bpm' => Input::Get('bpm', 'float', null),
    'key_signature' => Input::Get('key_signature', 'string', null),
    'purchase_url' => Input::Get('purchase_url', 'string', null),
    'video_url' => Input::Get('video_url', 'string', null),
    'sharing' => Input::Get('sharing', 'string', 'public'),
    'downloadable' => Input::Get('downloadable', 'string', null) == "0" ? false : true,
    'streamable' => Input::Get('streamable', 'string', null) == "0" ? false : true,
    'sharing_note' =>  Input::Get('sharing_note', 'string', null),
    'release_date' => Input::Get('release_date', 'string', null),
);
if ($action) {
    if (!$g_user->hasPermission('plugin_soundcloud_upload')) {
        camp_html_display_error($translator->trans('You do not have the right to upload SoundCloud tracks.', array(), 'plugin_soundcloud'));
        exit;
    }

    if (!empty($_FILES['asset_data']['error'])) {
        $showMessage = array (
            'title' => $translator->trans('Upload error', array(), 'plugin_soundcloud'),
            'message' => $translator->trans('Please check php settings for file uploading', array(), 'plugin_soundcloud'),
            'type' => 'error',
            'fixed' => 'false',
        );
    }
    if (empty($_FILES['asset_data']['name'])) {
        $showMessage = array (
            'title' => $translator->trans('Upload error!', array(), 'plugin_soundcloud'),
            'message' => $translator->trans('Please choose the track file', array(), 'plugin_soundcloud'),
            'type' => 'error',
            'fixed' => 'false',
        );
    }

    if (!empty($_FILES['asset_data']['type'])
    && substr($_FILES['asset_data']['type'], 0, 5) != 'audio') {
        $showMessage = array (
            'title' => $translator->trans('Upload error', array(), 'plugin_soundcloud'),
            'message' => $translator->trans('Wrong file format', array(), 'plugin_soundcloud'),
            'type' => 'error',
            'fixed' => 'false',
        );
    }

    if (!$track['title']) {
        $showMessage = array (
            'title' => $translator->trans('Upload error', array(), 'plugin_soundcloud'),
            'message' => $translator->trans('Please define the track title', array(), 'plugin_soundcloud'),
            'type' => 'error',
            'fixed' => 'false',
        );
    }

    if (!$showMessage) {
        $track['asset_data'] = '@' . $_FILES['asset_data']['tmp_name'];
        if (!empty($_FILES['artwork_data']['name'])
        && empty($_FILES['artwork_data']['error'])) {
            $track['artwork_data'] = '@' . $_FILES['artwork_data']['tmp_name'];
        }
        if (preg_match('!(\d\d\d\d)-(\d\d)-(\d\d)!', $track['release_date'], $aMatch)) {
            $track['release_year'] = $aMatch[1];
            $track['release_month'] = $aMatch[2];
            $track['release_day'] = $aMatch[3];
        }
        $result = $soundcloud->trackUpload($track);
        if (!$result) {
            $showMessage = array (
                'title' => $translator->trans('Upload error', array(), 'plugin_soundcloud'),
                'message' => $translator->trans('SoundCloud reports an error:', array(), 'plugin_soundcloud') . ' ' . $soundcloud->error,
                'type' => 'error',
                'fixed' => 'true',
            );
        } else {
            $showMessage = array (
                'title' => $translator->trans('Upload successful', array(), 'plugin_soundcloud'),
                'message' => $translator->trans('Track $1 has been uploaded to SoundCloud. Click to close', array('$1' => $result['id']), 'plugin_soundcloud'),
                'type' => 'success',
                'fixed' => 'true',
            );
            $track = array();
            if ($action == 'attach') {
                $article = Input::Get('article_id', 'string', null);
                $soundcloudAttach = new Soundcloud((int) $article, (int) $track);
                $soundcloudAttach->delete();
                $soundcloudAttach = new Soundcloud();
                if($soundcloudAttach->create((int) $article, (int) $result['id'], $result)) {
                    $js = 'parent.$.fancybox.reload = true;';
                }
            }
        }
    }
}

$trackListParams = array(
    'order' => 'created_at',
    'offset' => 0,
    'limit' => $limit,
);

$trackList = $soundcloud->trackSearch($trackListParams);
$attached = array();
if ($article) {
    $attachments = Soundcloud::getAssignments($article);
    foreach ($attachments as $value) {
        $attached["{$value['id']}"] = $value['id'];
    }
}

require_once 'templates/master.php';
