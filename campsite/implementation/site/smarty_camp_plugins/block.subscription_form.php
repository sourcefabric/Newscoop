<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */

$g_documentRoot = $_SERVER['DOCUMENT_ROOT'];


/**
 * Campsite subscription_form block plugin
 *
 * Type:     block
 * Name:     subscription_form
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
function smarty_block_subscription_form($p_params, $p_content, &$p_smarty, &$p_repeat)
{
    if (isset($p_content)) {
        return null;
    }

    require_once $p_smarty->_get_plugin_filepath('shared','escape_special_chars');

    // gets the context variable
    $campsite = $p_smarty->get_template_vars('campsite');
    $html = '';

    if (isset($p_params['template'])) {
        $template = new MetaTemplate($p_params['template']);
        if (!$template->defined()) {
            $template = null;
        }
    }
    $templateId = is_null($template) ? $campsite->template->identifier : $template->identifier;

    if (!isset($p_params['submit_button'])) {
        $p_params['submit_button'] = 'Submit';
    }

    $subsType = strtolower(CampRequest::GetVar('SubsType'));
    if ($subsType != 'trial' && $subsType != 'paid') {
        return null;
    }
    if ($subsType == 'paid') {
        echo "\n<link href=\"/javascript/campsite.js\" rel=\"stylesheet\" type=\"text/css\">\n";
    }

    $publication = $campsite->publication;
    $timeUnits = $subsType == 'trial' ? $publication->subscription_trial_time : $publication->subscription_paid_time;
    $sectionsNumber = Section::GetNumUniqueSections($publication->identifier, false);

    $html = "<form name=\"subscription_form\" action=\"\" method=\"post\">\n"
    ."<input type=\"hidden\" name=\"tpl\" value=\"$templateId\" />\n"
    ."<input type=\"hidden\" name=\"SubsType\" value=\"$subsType\" />\n"
    ."<input type=\"hidden\" name=\"tx_subs\" value=\"$timeUnits\" />\n"
    ."<input type=\"hidden\" name=\"nos\" value=\"$sectionsNumber\" />\n"
    ."<input type=\"hidden\" name=\"unitcost\" value=\""
    .$publication->subscription_unit_cost."\" />\n"
    ."<input type=\"hidden\" name=\"unitcostalllang\" value=\""
    .$publication->subscription_unit_cost_all_lang."\" />\n";

    $html .= $p_content;

    if ($subsType == 'paid' && isset($p_params['total']) != '') {
        $html .= $p_params['total']." <input type=\"text\" name=\"suma\" size=\"10\" "
        ."READONLY /> ".$currency;
    }

    $html .= "<input type=\"submit\" name=\"submit_comment\" "
    ."id=\"articleCommentSubmit\" value=\""
    .smarty_function_escape_special_chars($p_params['submit_button'])
    ."\" />\n";
    if (isset($p_params['preview_button']) && !empty($p_params['preview_button'])) {
        $html .= "<input type=\"submit\" name=\"previewComment\" "
        ."id=\"articleCommentPreview\" value=\""
        .smarty_function_escape_special_chars($p_params['preview_button'])
        ."\" />\n";
    }
    $html .= "</form>\n";

    return $html;
} // fn smarty_block_subscription_form

?>