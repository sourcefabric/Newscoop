<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/phpwrapper/settings.ini.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/phpwrapper/functions.php';
require_once $_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/modules/include/poll/poll_linker.class.php";

$moduleHandler =& new poll_linker();
$moduleHandler->linkPoll(array(), 'issue', $issueObj->getLanguageId(), $issueObj->getPublicationId(), $issueObj->getIssueNumber());
unset($moduleHandler);
?>