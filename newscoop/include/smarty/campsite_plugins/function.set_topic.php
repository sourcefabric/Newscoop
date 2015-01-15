<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */

/**
 * Campsite set_topic function plugin
 *
 * Type:     function
 * Name:     set_topic
 * Purpose:
 *
 * @param array
 *     $p_params[name] The Name of the topic to be set
 *     $p_params[identifier] The Identifier of the topic to be set
 * @param object
 *     $p_smarty The Smarty object
 */
function smarty_function_set_topic($p_params, &$p_smarty)
{
    // gets the context variable
    $campsite = $p_smarty->getTemplateVars('gimme');

    if (isset($p_params['identifier'])) {
        $attrName = 'identifier';
        $attrValue = $p_params['identifier'];
        $topicId = intval($p_params['identifier']);
    } elseif (isset($p_params['name'])) {
        $attrName = 'name';
        $attrValue = $p_params['name'];
        $topicService = \Zend_Registry::get('container')->getService('newscoop_newscoop.topic_service');
        $topic = $topicService->getTopicByFullName($p_params['name']);
        if (!is_null($topic) && $topic) {
            $topicId = $topic->getId();
        } else {
            $campsite->topic->trigger_invalid_value_error($attrName, $attrValue, $p_smarty);

            return false;
        }
    } else {
        $property = array_shift(array_keys($p_params));
        CampTemplate::singleton()->trigger_error("invalid parameter '$property' in set_topic");

        return false;
    }

    if ($campsite->topic->defined
            && $campsite->topic->identifier == $topicId) {
        return;
    }

    $topicObj = new MetaTopic($topicId);
    if ($topicObj->defined) {
        $campsite->topic = $topicObj;
    } else {
        $campsite->topic->trigger_invalid_value_error($attrName, $attrValue, $p_smarty);
    }
} // fn smarty_function_set_topic
