<?php

if(!defined("PHORUM")) return;

function phorum_mod_example_language_after_header () {
    $PHORUM = $GLOBALS["PHORUM"];
    print $PHORUM["DATA"]["LANG"]["example_language"]["HelloWorld"];
}

?>
