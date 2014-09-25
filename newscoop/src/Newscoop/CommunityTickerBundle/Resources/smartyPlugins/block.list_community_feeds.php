<?php
/**
 * @package Newscoop\CommunityTickerBundle
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2013 Sourcefabric o.p.s.
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
        $start = $context->next_list_start('Newscoop\CommunityTickerBundle\TemplateList\CommunityFeedsList');
        $list = new \Newscoop\CommunityTickerBundle\TemplateList\CommunityFeedsList(new \Newscoop\CommunityTickerBundle\TemplateList\ListCriteria());
        $list->getList($start, $params);
        if ($list->isEmpty()) {
            $context->setCurrentList($list, array());
            $context->resetCurrentList();
            $repeat = false;
            return;
        }

        $context->setCurrentList($list, array('community_feed'));
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