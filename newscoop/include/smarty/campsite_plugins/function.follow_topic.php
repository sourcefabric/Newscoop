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
function smarty_function_follow_topic($params, $smarty)
{
    $context = $smarty->getTemplateVars('gimme');

    $topic = !empty($params['topic']) ? $params['topic'] : $context->topic;
    if (empty($topic)) {
        return '';
    }

    $user = $context->user;
    if (!$user->logged_in) {
        return;
    }

    if (in_array($topic->identifier, array_keys($user->topics))) {
        return;
    }

    return sprintf('<a href="%s" title="Follow topic \'%s\'">Follow topic</a>',
        $smarty->getTemplateVars('view')
            ->url(array(
                'module' => 'default',
                'controller' => 'dashboard',
                'action' => 'follow-topic',
                'topic' => $topic->identifier,
            ), 'default'),
            $topic->name);
}
