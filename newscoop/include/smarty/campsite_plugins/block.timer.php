<?php
/**
 * @package Newscoop
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Util for measuring time needed for rendering a block of template
 *
 * @param array $params
 * @param string $content
 * @param object $smarty
 * @param bool $repeat
 * @return string
 */
function smarty_block_timer($params, $content, $smarty, &$repeat)
{
    static $timers;

    if ($timers === null) {
        $timers = new SplStack();
    }

    if ($content === null) {
        $repeat = true;
        $timers->push(microtime(true));
    } else {
        $repeat = false;
        $start = $timers->pop();
        return $content . sprintf('<!-- timer: %.3fms -->', (microtime(true) - $start) * 1000);
    }
}
