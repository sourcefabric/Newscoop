<?php
    /*
    * Smarty plugin
    * -------------------------------------------------------------
    * File:     outputfilter.replace_quotes.php
    * Type:     outputfilter
    * Name:     replace_quotes
    * Purpose:  Converts " chars to « or » 
    * -------------------------------------------------------------
    */
    function smarty_outputfilter_replace_quotes($output, &$smarty)
    {
        return preg_replace('/(.+) \"(.+)\" /', '$1 «$2» ', $output);
    }

?>
