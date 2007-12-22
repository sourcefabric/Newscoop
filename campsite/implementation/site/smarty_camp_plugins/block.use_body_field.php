<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */


/**
 *
 * Type:     block
 * Name:     use_body_field
 * Purpose:  Provides a...
 *
 * @param string
 *     $p_params
 * @param string
 *     $p_smarty
 * @param string
 *     $p_content
 *
 * @return
 *
 */
function smarty_block_use_body_field($p_params, $p_content, &$p_smarty, &$p_repeat)
{
    require_once $p_smarty->_get_plugin_filepath('shared','escape_special_chars');

    // gets the context variable
    $campsite = $p_smarty->get_template_vars('campsite');

    if (!isset($p_content)) {
        if (!isset($p_params['article_type'])) {
            CampTemplate::singleton()->trigger_error('missing parameter article_type in statement use_body_field');
            return $p_content;
        }
        if (!isset($p_params['field_name'])) {
            CampTemplate::singleton()->trigger_error('missing parameter field_name in statement use_body_field');
            return $p_content;
        }
        $articleType = new ArticleType($p_params['article_type']);
        if (!$articleType->exists()) {
            CampTemplate::singleton()->trigger_error('invalid value ' . $p_params['article_type'] . ' of parameter article_type in statement use_body_field');
            return $p_content;
        }
        $articleTypeField = new ArticleTypeField($p_params['article_type'], $p_params['field_name']);
        if (!$articleTypeField->exists()) {
            CampTemplate::singleton()->trigger_error('invalid value ' . $p_params['field_name'] . ' of parameter field_name in statement use_body_field');
            return $p_content;
        }
        $campsite->body_field_article_type = $p_params['article_type'];
        $campsite->body_field_name = $p_params['field_name'];
    } else {
        $campsite->body_field_article_type = null;
        $campsite->body_field_name = null;
    }

    return $p_content;
} // fn smarty_block_use_body_field

?>