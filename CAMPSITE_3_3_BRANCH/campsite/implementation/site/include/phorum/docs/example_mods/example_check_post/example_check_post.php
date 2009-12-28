<?php

// This module demonstrates how a hook function for the check_post
// hook could be written.

if(!defined("PHORUM")) return;

function phorum_mod_example_check_post_check_post ($args) {
   list ($message, $error) = $args; 
   if (!empty($error)) return $args;

   if (stristr($message["body"], "bar") !== false) {
       return array($message, "The body may not contain 'bar'");
   }
    
   return $args;
}

?>
