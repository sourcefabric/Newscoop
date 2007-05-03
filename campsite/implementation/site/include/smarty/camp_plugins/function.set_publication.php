<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */

/**
 * Campsite set_publication function plugin
 *
 * Type:     function
 * Name:     set_publication
 * Purpose:  
 *
 * @param array
 *     $p_params[name] The Name of the publication to be set
 *     $p_params[identifier] The Identifier of the publication to be set
 * @param object
 *     $p_smarty The Smarty object
 */
function smarty_function_set_publication($p_params, &$p_smarty)
{
    global $g_ado_db;

    $attrValue = 0;
    if (isset($p_params['identifier']) && !empty($p_params['identifier'])) {
        $attrValue = intval($p_params['identifier']);
    } elseif (isset($p_params['name']) && !empty($p_params['name'])) {
        $queryStr = "SELECT Id FROM Publications "
            . "WHERE Name = '".$g_ado_db->escape($p_params['name'])."'";
        $row = $g_ado_db->GetRow($queryStr);
        if ($row['Id'] > 0) {
            $attrValue = $row['Id'];
        }
    }

    if (!$attrValue) {
        return false;
    }

    // gets the context variable
    $campsite = $p_smarty->get_template_vars('campsite');
    if ($campsite->publication->defined
            && $campsite->publication->identifier == $attrValue) {
        return;
    }

    $publication = new MetaPublication($attrValue);
    if ($publication->defined == 'defined') {
        $campsite->publication = $publication;
    }

} // fn smarty_function_set_publication

?>