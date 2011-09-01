<?php
/**
 * @package Newscoop
 */

/**
 * Newscoop follow topic function
 *
 * Type:     function
 * Name:     follow_topic
 * Purpose:  Render follow topic link
 *
 * @param array $params
 * @param object $smarty
 * @return string
 */
function smarty_function_link_follow_topic($params, $smarty)
{
    $context = $smarty->getTemplateVars('gimme');

    $topic = !empty($params['topic']) ? $params['topic'] : $context->topic;
    if (empty($topic)) {
         '';
    }

    $user = $context->user;
    if (!$user->logged_in) {
        return '';
    }

    if (in_array($topic->identifier, array_keys($user->topics))) { // @todo make unfollow link
        return '';
    }

    return sprintf('<a href="%s">%s</a>',
        $smarty->getTemplateVars('view')
            ->url(array(
                'module' => 'default',
                'controller' => 'dashboard',
                'action' => 'follow-topic',
                'topic' => $topic->identifier,
            ), 'default'),
            !empty($params['link_text']) ? $params['link_text'] : "Follow");
}
