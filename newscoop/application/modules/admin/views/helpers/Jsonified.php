<?php
/**
 * Render actions view helper
 */
class Admin_View_Helper_Jsonified extends Zend_View_Helper_Abstract
{
    /**
     * Render actions
     *
     * @param array $p_inputs
     * @param array $p_methods default empty array
     * @return string
     */
    public function jsonified(array $p_input, array $p_methods = array())
    {
        if(count($p_methods))
        {
            $regex = '/"('.implode($p_methods,'|').')":"([\w\-\.]+)"/i';
            $replace = '"$1":$2';
            return preg_replace($regex, $replace, json_encode($p_input));
        }
        else
            return json_encode($p_input);
    }
}
