<?php
    /*
    * Smarty plugin
    * -------------------------------------------------------------
    * File:     modifier.replace_quotes.php
    * Type:     modifier
    * Name:     replace_quotes
    * Purpose:  Converts " chars to « or » 
    * -------------------------------------------------------------
    */
    function smarty_modifier_replace_quotes($string)
    {
        return preg_replace('/([\s\']+)\"(.*?)\"([\s\']+)/', '$1&#171;$2&#187;$3', $string);
    }

?>
