<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */

/**
 * Wrap a string in a html-element, if the string doesnt start with this element.
 *
 * Type:     function
 * Name:     wrap_in_element
 * Purpose:
 *
 * @param array
 *     $p_params[string]     The text to wrap
 *     $p_params[element]    The element to use when wrapping
 *     $p_params[attributes] Optional attributes for the element
 * @param object
 *     $p_smarty The Smarty object
 */
function smarty_function_wrap_in_element(array $p_params, &$p_smarty)
{
    $elementAttributes = '';
    $wrappableString = $p_params['string'];
    $wrapElement = $p_params['element'];
    if (array_key_exists('attributes', $p_params)) {
        $elementAttributes = ' '.$p_params['attributes'];
    }
    $pattern = '/^\<'.$wrapElement.'\>/';

    if (preg_match($pattern, $wrappableString)) {
        return $wrappableString;
    } else {
        return sprintf('<%s%s>%s</%s>', $wrapElement, $elementAttributes, $wrappableString, $wrapElement);
    }
} // fn smarty_function_wrap_in_element
