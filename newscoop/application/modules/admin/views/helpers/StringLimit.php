<?php
/**
 * Render actions view helper
 */
class Admin_View_Helper_StringLimit extends Zend_View_Helper_Abstract
{
    /**
     * Render actions
     *
     * @param array $p_inputs
     * @param array $p_methods default empty array
     * @return arrat $
     */
    public function stringLimit($p_string, $p_limit = 140, $p_trailing = '...' )
    {
        $str = substr($p_string, 0, $p_limit);
        if (strlen($p_string) > $p_limit) {
            $str .= $p_trailing;
        }
        return $str;
    }
}
