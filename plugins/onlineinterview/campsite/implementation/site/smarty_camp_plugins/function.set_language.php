<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */

/**
 * Campsite set_language function plugin
 *
 * Type:     function
 * Name:     set_language
 * Purpose:
 *
 * @param array
 *     $p_params The English name of the language to be set
 * @param object
 *     $p_smarty The Smarty object
 */
function smarty_function_set_language($p_params, &$p_smarty)
{
    // gets the context variable
    $campsite = $p_smarty->get_template_vars('campsite');

    if (isset($p_params['name'])) {
    	$languageName = $p_params['name'];
    } else {
    	$property = array_shift(array_keys($p_params));
    	$campsite->language->trigger_invalid_property_error($property, $p_smarty);
        return false;
    }

    if ($campsite->language->defined
            && $campsite->language->english_name == $languageName) {
        return;
    }

    $languages = Language::GetLanguages(null, null, $languageName);
    if (empty($languages)) {
    	$campsite->language->trigger_invalid_value_error('name', $languageName, $p_smarty);
    	return false;
    }
    $campsite->language = new MetaLanguage($languages[0]->getLanguageId());
} // fn smarty_function_set_language

?>