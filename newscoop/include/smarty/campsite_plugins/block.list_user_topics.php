<?php
/**
 * @package Newscoop
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\View\ViewCollection;

/**
 * Newscoop list_user_topics block plugin
 *
 * @param array $params
 * @param mixed $content
 * @param object $smarty
 * @param bool $repeat
 * @return string
 */
function smarty_block_list_user_topics($params, $content, $smarty, &$repeat)
{
    $gimme = $smarty->getTemplateVars('gimme');
    if (!$gimme->user->defined) {
        $repeat = false;
        return;
    }

    static $lists;
    if (!isset($lists)) {
        $lists = new SplStack();
    }

    if (!isset($content)) {
        $service = Zend_Registry::get('container')->getService('user.topic');
        $lists->push(new ViewCollection($service->getTopics($gimme->user->identifier)));
    }

    $list = $lists->pop();
    if ($repeat = $list->valid()) {
        $smarty->assign('topic', $list->current());
        $list->next();
        $smarty->assign('last', !$list->valid());
        $lists->push($list);
    }

    return $content;
}
