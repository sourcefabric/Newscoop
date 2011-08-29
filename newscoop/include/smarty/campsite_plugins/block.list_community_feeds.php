<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Newscoop list_community_feeds block plugin
 *
 * Type:     block
 * Name:     community_feeds
 *
 * @param array $params
 * @param mixed $content
 * @param object $smarty
 * @param bool $repeat
 * @return string
 */
function smarty_block_list_community_feeds($params, $content, &$smarty, &$repeat)
{
    $context = $smarty->getTemplateVars('gimme');

    if (!isset($content)) { // init
        $start = $context->next_list_start('CommunityFeed');
        $list = new CommunityFeedsList($start, $params);
        if ($list->isEmpty()) {
            $context->setCurrentList($list, array());
            $context->resetCurrentList();
            $repeat = false;
            return;
        }

        $context->setCurrentList($list, array('community_feeds'));
        $context->community_feed = $context->current_community_feeds_list->current;
        $repeat = true;
    } else { // next
        $context->current_community_feeds_list->defaultIterator()->next();
        if (!is_null($context->current_community_feeds_list->current)) {
            $context->community_feed = $context->current_community_feeds_list->current;
            $repeat = true;
        } else {
            $context->resetCurrentList();
            $repeat = false;
        }
    }

    return $content;
}
