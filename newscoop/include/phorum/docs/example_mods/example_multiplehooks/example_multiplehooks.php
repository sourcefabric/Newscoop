<?php

// This module demonstrates the use of multiple hooks in a single module.

if(!defined("PHORUM")) return;

function phorum_mod_example_multiplehooks_after_header () {
    print "Hello";
}

function phorum_mod_example_multiplehooks_before_footer () {
    print "World!";
}

?>
