<?php
/**
 * @package Newscoop
 * @subpackage Soundcloud plugin
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

include 'data.php';
require_once CS_PATH_PLUGINS.DIR_SEP.'soundcloud'.DIR_SEP.'classes'.DIR_SEP.'soundcloud.api.php';
camp_load_translation_strings('plugin_soundcloud');

if (!$g_user->hasPermission('plugin_soundcloud_browser')) {
    jsonOutput(null, array(
        'title' => getGS('Error'),
        'text' => getGS('You do not have the right to manage Soundcloud tracks.'),
        'type' => 'error',
    ));
    exit;
}

$limit = 5;
$offset = 0;
$paging = null;
$message = array();
$js = null;
$content = null;
$otherParams = array();
$soundcloud = new SoundcloudAPI();

$action = Input::Get('action', 'string', null);
$article = Input::Get('article', 'string', null);
$attachement = Input::Get('attachement', 'string', null) ? true : false;
$track = Input::Get('track', 'string', null);

if ($action == 'addtoset') {
    if (!$g_user->hasPermission('plugin_soundcloud_update')) {
        jsonOutput(null, array(
            'title' => getGS('Error'),
            'text' => getGS('You do not have the right to update Soundcloud tracks.'),
            'type' => 'error',
        ), null, array(), true);
        exit;
    }
    $set = Input::Get('set', 'string', null);
    if ($setTracks = $soundcloud->setLoad($set)) {
        $tracks = $setTracks['tracks'];
        $tracks[]['id'] = $track;
        $setTracks = array();
        $setTracks['id'] = $set;
        $setTracks['tracks'] = $tracks;
    }
    if (!$setTracks || !$result = $soundcloud->setUpdate($setTracks)) {
        jsonOutput(null, array(
            'title' => getGS('Soundcloud reports an error:'),
            'text' => $soundcloud->error,
            'type' => 'error',
        ));
    } else {
        jsonOutput(null, array(), null, array ('ok' => true));
    }
}

if ($action == 'removefromset') {
    if (!$g_user->hasPermission('plugin_soundcloud_update')) {
        jsonOutput(null, array(
            'title' => getGS('Error'),
            'text' => getGS('You do not have the right to update Soundcloud tracks.'),
            'type' => 'error',
        ), null, array(), true);
        exit;
    }
    $set = Input::Get('set', 'string', null);
    if ($setTracks = $soundcloud->setLoad($set)) {
        $tracks = $setTracks['tracks'];
        $index = null;
        foreach ($tracks as $key => $value) {
            if ($value['id'] == $track) $index = $key;
        }
        unset($tracks[$index]);
        $setTracks = array();
        $setTracks['id'] = $set;
        $setTracks['tracks'] = $tracks;
    }
    if (!$setTracks || !$result = $soundcloud->setUpdate($setTracks)) {
        jsonOutput(null, array(
            'title' => getGS('Soundcloud reports an error:'),
            'text' => $soundcloud->error,
            'type' => 'error',
        ));
    } else {
        jsonOutput(null, array(), null, array ('ok' => true));
    }
}

if ($action == 'setlist') {
    $setList = $soundcloud->setList();
    ob_start();
    require_once 'templates/setlist.php';
    $content = ob_get_clean();
    jsonOutput($content, array(), null, array ('ok' => true));
}

if ($action == 'unlink') {
    $soundcloudAttach = new Soundcloud((int) $article, (int) $track);
    if ($soundcloudAttach->delete()) {
        jsonOutput(null, array(), null, array ('ok' => true));
    }
}

if ($action == 'attach') {
    $trackData = $soundcloud->trackLoad($track);
    if (empty($trackData)) {
        jsonOutput(null, array(
            'title' => getGS('Attach error'),
            'text' => getGS('Track not found'),
            'type' => 'error',
        ));
        exit;
    }
    $soundcloudAttach = new Soundcloud((int) $article, (int) $track);
    $soundcloudAttach->delete();
    $soundcloudAttach = new Soundcloud();
    if(!$soundcloudAttach->create((int) $article, (int) $track, $trackData)) {
        jsonOutput(null, array(
            'title' => getGS('Attach error'),
            'text' => getGS('Error during create attachement'),
            'type' => 'error',
        ));
        exit;
    } else {
        jsonOutput(null, array(), null, array ('ok' => true));
    }
}

if ($action == 'delete') {
    if (!$g_user->hasPermission('plugin_soundcloud_delete')) {
        jsonOutput(null, array(
            'title' => getGS('Error'),
            'text' => getGS('You do not have the right to delete Soundcloud tracks.'),
            'type' => 'error',
        ));
        exit;
    }
    Soundcloud::deleteAllTracks($track);
    if (!$soundcloud->trackDelete($track)) {
        jsonOutput(null, array(
            'title' => getGS('Soundcloud reports an error:'),
            'text' => $soundcloud->error,
            'type' => 'error',
        ));
        exit;
    }
    $action = 'search';
}

if ($action == 'save') {
    $attach = false;
    if (!$g_user->hasPermission('plugin_soundcloud_update')) {
        jsonOutput(null, array(
            'title' => getGS('Error'),
            'text' => getGS('You do not have the right to update Soundcloud tracks.'),
            'type' => 'error',
        ), null, array(), true);
        exit;
    }
    foreach ((array)$_POST as $key => $value) {
        switch ($key) {
            case 'article':
                $article = (int) $value;
                break;
            case 'attach':
                $attach = $value == '1' ? true : false;
                break;
            default:
                $track[$key] = $value;
        }
    }
    if (!$track['title']) {
        jsonOutput(null, array(
            'title' => getGS('Save error'),
            'text' => getGS('Please define the track title'),
            'type' => 'error',
        ), null, array(), true);
        exit;
    } else {
        if (preg_match('!(\d\d\d\d)-(\d\d)-(\d\d)!', $track['release_date'], $aMatch)) {
            $track['release_year'] = $aMatch[1];
            $track['release_month'] = $aMatch[2];
            $track['release_day'] = $aMatch[3];
        }

        if (!empty($_FILES['artwork_data']['name'])
        && empty($_FILES['artwork_data']['error'])) {
            $track['artwork_data'] = '@' . $_FILES['artwork_data']['tmp_name'];
        }

        $result = $soundcloud->trackUpdate($track);
        if (empty($result['id'])) {
            jsonOutput(null, array(
                'title' => getGS('Save error'),
                'text' => getGS('Soundcloud reports an error:') . $soundcloud->error,
                'type' => 'error',
            ), null, array(), true);
            exit;
        } else {
            if ($attach) {
                $soundcloudAttach = new Soundcloud((int) $article, (int) $track['id']);
                $soundcloudAttach->delete();
                $soundcloudAttach = new Soundcloud();
                if(!$soundcloudAttach->create((int) $article, (int) $track['id'], $result)) {
                    jsonOutput(null, array(
                        'title' => getGS('Attach error'),
                        'text' => getGS('Error during create attachement'),
                        'type' => 'error',
                    ), null, array(), true);
                    exit;
                }
                $messageTitle = getGS('Save and attach successful');
            } else {
                $messageTitle = getGS('Save successful');
            }
            jsonOutput(null, array (
                'title' => $messageTitle,
                'text' => getGS('Track has been updated on Soundcloud'),
                'type' => 'success',
            ), null, array('ok' => true), false);
        }
    }
    exit;
}

if ($action == 'search') {
    $trackListParams = array(
        'order' => 'created_at',
        'offset' => $offset,
        'limit' => $limit,
    );
    foreach ((array)$_POST['search'] as $pair) {
        switch ($pair['name']) {
            case 'offset':
                $offset = (int) $pair['value'];
                break;
            case 'article':
                $article = (int) $pair['value'];
                break;
            case 'attachement':
                $attachement = $pair['value'] == '1' ? true : false;
                break;
            case 'paging-action':
                $paging = $pair['value'];
                break;
            default:
                $trackListParams[$pair['name']] = $pair['value'];
        }
    }
    switch ($paging) {
        case 'next':
            $offset = $offset + $limit;
            break;
        case 'prev':
            $offset = $offset > $limit? $offset - $limit : 0;
            break;
        case 'reload':
            break;
        default:
            $offset = 0;
    }
    $trackListParams['offset'] = $offset;
    $trackList = $soundcloud->trackSearch($trackListParams);
    if (empty($trackList)) {
        jsonOutput(null, array(
            'title' => getGS('Search error'),
            'text' => getGS('Tracks not found'),
            'type' => 'error',
        ));
        exit;
    }
    $attached = array();
    if ($article) {
        $attachments = Soundcloud::getAssignments($article);
        foreach ((array)$attachments as $value) {
            $attached[(string)$value['id']] = $value['id'];
        }
    }
    ob_start();
    require_once 'templates/tracklist.php';
    $content = ob_get_clean();
    jsonOutput($content, $message, $js, $otherParams);
    exit;
}

if ($action == 'edit') {
    if (!$g_user->hasPermission('plugin_soundcloud_update')) {
        jsonOutput(null, array(
            'title' => getGS('Error'),
            'text' => getGS('You do not have the right to update Soundcloud tracks.'),
            'type' => 'error',
        ));
        exit;
    }
    $track = $soundcloud->trackLoad($track);
    if (empty($track)) {
        jsonOutput(null, array(
            'title' => getGS('Edit error'),
            'text' => getGS('Track not found'),
            'type' => 'error',
        ));
        exit;
    }
    $track['release_date'] = empty($track['release_year']) ? ''
    : "{$track['release_year']}-{$track['release_month']}-{$track['release_day']}";
    ob_start();
    require_once 'templates/edit.php';
    $content = ob_get_clean();
    jsonOutput($content, $message, $js, $otherParams);
}


function jsonOutput($content, $message = array(), $js = null, $otherParams = array(), $json = true)
{
    if ($json) {
        header('Content-Type: application/json');
    }
    $output = array();
    if ($content) {
        $output['html'] = $content;
    }
    if ($message) {
        $output['message'] = $message;
    }
    if ($js) {
        $output['js'] = $js;
    }
    if ($otherParams) {
        $output = $output + (array) $otherParams;
    }
    echo json_encode($output);
}

?>