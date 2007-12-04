--TEST--
DB_driver::fetchmode object
--INI--
error_reporting = 2047
--SKIPIF--
<?php chdir(dirname(__FILE__)); require_once './skipif.inc'; ?>
--FILE--
<?php
require_once './mktable.inc';
require_once '../fetchmode_object.inc';
?>
--EXPECT--
--- fetch with param DB_FETCHMODE_OBJECT ---
stdclass -> a b cc d
stdclass -> a b cc d
--- fetch with default fetchmode DB_FETCHMODE_OBJECT ---
stdclass -> a b cc d
stdclass -> a b cc d
--- fetch with default fetchmode DB_FETCHMODE_OBJECT and class DB_row ---
db_row -> a b cc d
db_row -> a b cc d
--- fetch with default fetchmode DB_FETCHMODE_OBJECT with no class then DB_row ---
stdclass -> a b cc d
db_row -> a b cc d
