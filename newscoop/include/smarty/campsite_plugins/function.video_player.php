<?php
/**
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Get video player
 *
 * @param array $params
 * @param Smarty_Internal_Template $smarty
 * @return string
 */
function smarty_function_video_player(array $params, Smarty_Internal_Template $smarty)
{
    if (!array_key_exists('item', $params)) {
        throw new InvalidArgumentException("Video not set");
    }

    $code = array_pop(explode('/', trim($params['item']->video_url, '/')));
    if (is_numeric($code)) {
        return sprintf('<iframe src="http://player.vimeo.com/video/%d?portrait=0&amp;color=ffffff" width="%d" height="%d" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>',
            $code, $params['item']->width, $params['item']->height);
    } else {
        return sprintf('<iframe width="%d" height="%d" src="http://www.youtube.com/embed/%s?rel=0" frameborder="0" allowfullscreen></iframe>',
            $params['item']->width, $params['item']->height, $code);

    }
}
