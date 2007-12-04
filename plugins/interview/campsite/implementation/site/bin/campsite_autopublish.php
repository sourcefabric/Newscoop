<?php

$www_dir = $GLOBALS['argv'][1];
$www_dir = $www_dir . '/html';
$_SERVER['DOCUMENT_ROOT'] = $www_dir;
require_once($www_dir.'/db_connect.php');
require_once($www_dir.'/classes/ArticlePublish.php');
require_once($www_dir.'/classes/IssuePublish.php');
IssuePublish::DoPendingActions();
ArticlePublish::DoPendingActions();

?>