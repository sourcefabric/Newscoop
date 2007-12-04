<?php

// This is an example of a single file module for Phorum. Almost all
// programming courses start out with building a program that displays
// "Hello, world!" on screen. The Phorum developers have followed 
// the tradition and created "Hello, world!" for the Phorum module
// system.

if(!defined("PHORUM")) return;

/* phorum module info
title: Single file "Hello, world!" module
desc: This is an example of a single file module. The module will display "Hello, world!" after displaying the page header.
hook: after_header|phorum_mod_helloworld_after_header
*/

function phorum_mod_helloworld_after_header () {
    print "Hello, world!";
}
