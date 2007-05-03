<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */

/**
 * Campsite set_section function plugin
 *
 * Type:     function
 * Name:     set_section
 * Purpose:  
 *
 * @param array
 *     $p_params[name] The Name of the section to be set
 *     $p_params[number] The Number of the section to be set
 * @param object
 *     $p_smarty The Smarty object
 */
function smarty_function_set_section($p_params, &$p_smarty)
{
    global $g_ado_db;

    // gets the context variable
    $campsite = $p_smarty->get_template_vars('campsite');

    $attrValue = 0;
    if (isset($p_params['number']) && !empty($p_params['number'])) {
        $attrValue = intval($p_params['number']);
    } elseif (isset($p_params['name']) && !empty($p_params['name'])) {
        $queryStr = "SELECT Number FROM Sections "
            . "WHERE IdPublication = ".$campsite->publication->identifier
            . " AND NrIssue = ".$campsite->issue->number
            . " AND Name = '".$g_ado_db->escape($p_params['name'])."'";
        $row = $g_ado_db->GetRow($queryStr);
        if ($row['Number'] > 0) {
            $attrValue = $row['Number'];
        }
    }

    if (!$attrValue) {
        return false;
    }
    if ($campsite->section->defined
            && $campsite->section->number == $attrValue) {
        return;
    }

    $section = new MetaSection($campsite->publication->identifier,
                               $campsite->issue->number,
                               $campsite->language->number, $attrValue);
    if ($section->defined == 'defined') {
        $campsite->section = $section;
    }

} // fn smarty_function_set_section

?>