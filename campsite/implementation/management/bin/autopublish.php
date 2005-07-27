<?php

exec('./campsite_config --www_dir', $www_dir);
$www_dir = array_pop($www_dir) . '/campsite/html';
echo $www_dir;
$g_documentRoot = $www_dir;
require_once($www_dir.'/db_connect.php');
require_once($www_dir.'/classes/ArticlePublish.php');
require_once($www_dir.'/classes/IssuePublish.php');
IssuePublish::DoPendingActions();
ArticlePublish::DoPendingActions();

?>