<?php
/**
 * @package Newscoop
 * @subpackage Soundcloud plugin
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

function smarty_function_assign_soundcloud_tracks($p_params, &$p_smarty)
{
    $gimme = $p_smarty->getTemplateVars('gimme');
    $article = empty($p_params['article']) ? $gimme->article->number : $p_params['article'];
    $length = empty($p_params['length']) ? 0 : (int) $p_params['length'];
    $order = empty($p_params['order']) ? 'asc' : $p_params['order'];
    $start = empty($p_params['start']) ? 0 : $p_params['start'];
    $current = empty($p_params['current']) ? 0 : $p_params['current'];
    $search = empty($p_params['search']) ? null : $p_params['search'];
    $api = empty($p_params['api']) ? false : true;
    $set = empty($p_params['set']) ? null : $p_params['set'];

    if (!$api) {
        $tracks = Soundcloud::getAssignments($article, $order, $start, $length);
    } else {
        require_once CS_PATH_PLUGINS.DIR_SEP.'soundcloud'.DIR_SEP.'classes'.DIR_SEP.'soundcloud.api.php';
        $soundcloudAPI = new SoundcloudAPI();
        if ($set) {
            if ($set = $soundcloudAPI->setLoad($set)) {
                $tracks = $set['tracks'];
                if ($order == 'desc') {
                    $tracks = array_reverse($tracks);
                }
                for ($i = 0; $i < $start; $i++) {
                    array_shift($tracks);
                }
                if ($length) {
                    for ($i = sizeof($tracks); $i > $length; $i--) {
                        array_pop($tracks);
                    }
                }
            }
        } else {
            $tracks = $soundcloudAPI->trackSearch(array(
                'order' => 'created_at',
                'limit' => $length,
                'offest' => $start,
                'q' => $search,
            ));
        }
    }

    if (!empty($tracks)) {
        $soundcloud = new stdClass();
        $soundcloud->list = false;
        $soundcloud->track = empty($tracks[$current]) ? $tracks[0] : $tracks[$current];
        $soundcloud->total = sizeof($tracks);
        $soundcloud->tracks = $tracks;
        $p_smarty->assign('soundcloud', $soundcloud);
    } else {
        $p_smarty->assign('soundcloud', null);
    }
    return null;
}

?>