<?php
/**
 * @package Newscoop
 * @subpackage Soundcloud plugin
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

function smarty_block_list_soundcloud_tracks($p_params, $p_content, &$p_smarty, &$p_repeat)
{
    $p_smarty->smarty->loadPlugin('smarty_shared_escape_special_chars');

    $gimme = $p_smarty->getTemplateVars('gimme');
    $soundcloud = $p_smarty->getTemplateVars('soundcloud');
    $article = empty($p_params['article']) ? $gimme->article->number : $p_params['article'];
    $length = empty($p_params['length']) ? 0 : (int) $p_params['length'];
    $order = empty($p_params['order']) ? 'asc' : $p_params['order'];
    $start = empty($p_params['start']) ? 0 : $p_params['start'];
    $search = empty($p_params['search']) ? null : $p_params['search'];
    $set = empty($p_params['set']) ? null : $p_params['set'];
    $api = empty($p_params['api']) ? false : true;

    if ($p_repeat && (empty($soundcloud) || !$soundcloud->list)) {
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
        if (empty($tracks)) {
            $p_repeat = false;
            return null;
        }
    }
    if (empty($soundcloud) || !$soundcloud->list) {
        $soundcloud = new stdClass();
        $soundcloud->list = true;
        $soundcloud->tracks = $tracks;
        $soundcloud->current = 0;
        $soundcloud->track = $tracks[0];
        $p_smarty->assign('soundcloud', $soundcloud);
        $p_repeat = true;
    } else {
        ++$soundcloud->current;
        if (empty($soundcloud->tracks[$soundcloud->current])) {
            $p_smarty->assign('soundcloud', null);
            $p_repeat = false;
        } else {
            $soundcloud->track = $soundcloud->tracks[$soundcloud->current];
            $p_repeat = true;
        }
    }
    return $p_content;
}

?>