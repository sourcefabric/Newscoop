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
    // gets the context variable
    $campsite = $p_smarty->get_template_vars('campsite');

    if (isset($p_params['number'])) {
    	$attrName = 'number';
        $attrValue = $p_params['number'];
        $sectionNumber = intval($p_params['number']);
    } elseif (isset($p_params['name'])) {
    	$sections = Section::GetSections($campsite->publication->identifier,
    									 $campsite->issue->number,
    									 $campsite->language->number,
    									 null,
    									 $p_params['name']);
        if (isset($sections[0])) {
        	$attrName = 'name';
        	$attrValue = $p_params['name'];
            $sectionNumber = intval($sections[0]->getSectionNumber());
        } else {
	    	$campsite->section->trigger_invalid_value_error($attrName, $attrValue, $p_smarty);
        	return false;
        }
    } else {
    	$property = array_shift(array_keys($p_params));
    	$campsite->section->trigger_invalid_property_error($property, $p_smarty);
        return false;
    }

    if ($campsite->section->defined
            && $campsite->section->number == $sectionNumber) {
        return;
    }

    $sectionObj = new MetaSection($campsite->publication->identifier,
								  $campsite->issue->number,
								  $campsite->language->number,
								  $sectionNumber);
    if ($sectionObj->defined) {
        $campsite->section = $sectionObj;
    }
} // fn smarty_function_set_section

?>