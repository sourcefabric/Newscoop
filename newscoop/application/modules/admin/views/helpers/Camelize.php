<?php

class Admin_View_Helper_Camelize extends Zend_View_Helper_Abstract
{
    /**
     * Camelcase string
     * @param string $string the string to camelize
     * @param bool $lazy wether to make it lazyCamelCase or not
     */
    public function camelize( $string, $spacify = true, $lazy = false )
    {
        if( $spacify ) {
            $repl = "str_replace('_',' ',strtoupper('\\1'))";
        }
        else {
            $repl = "strtoupper('\\2')";
        }
        $ret = preg_replace("`(?<=[a-z0-9])(_([a-z0-9]))`e", $repl, $string );
        if( $lazy ) {
            return lcfirst( $ret );
        }
        else {
            return ucfirst( $ret );
        }
    }
}