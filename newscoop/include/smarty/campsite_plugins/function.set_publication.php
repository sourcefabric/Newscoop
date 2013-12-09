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
    // gets the context variable
    $campsite = $p_smarty->getTemplateVars('gimme');

    if (isset($p_params['identifier'])) {
    	$attrName = 'identifier';
    	$attrValue = $p_params['identifier'];
        $publicationId = intval($p_params['identifier']);
    } elseif (isset($p_params['name'])) {
    	$attrName = 'name';
    	$attrValue = $p_params['name'];
    	$publications = Publication::GetPublications($p_params['name']);
        if (!empty($publications)) {
            $publicationId = $publications[0]->getPublicationId();
        } else {
	    	$campsite->publication->trigger_invalid_value_error($attrName, $attrValue, $p_smarty);
        	return false;
        }
    } elseif (isset($p_params['alias'])) {
        $aliasObj = new Alias($p_params['alias']);

        if (!$aliasObj->exists()) {
            $campsite->publication->trigger_invalid_value_error($attrName, $attrValue, $p_smarty);
            return false;
        }

        $publicationId = $aliasObj->getPublicationId();
    } else {
    	$property = array_shift(array_keys($p_params));
        CampTemplate::singleton()->trigger_error("invalid parameter '$property' in set_publication");
    	return false;
    }

    if ($campsite->publication->defined
            && $campsite->publication->identifier == $publicationId) {
        return;
    }

    $publicationObj = new MetaPublication($publicationId);
    if ($publicationObj->defined) {
        $campsite->publication = $publicationObj;
    } else {
    	$campsite->publication->trigger_invalid_value_error($attrName, $attrValue, $p_smarty);
    }
} // fn smarty_function_set_publication

?>
