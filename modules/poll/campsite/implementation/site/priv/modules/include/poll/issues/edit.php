<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/phpwrapper/settings.ini.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/phpwrapper/functions.php';
require_once $_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/modules/include/poll/poll_linker.class.php";

?>


<TR>
	<TD COLSPAN="2" style="padding-top: 20px;">
		<B><?php  putGS("Assign poll"); ?></B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD colspan="2" align="center">
    <?php 
    $moduleHandler =& new poll_linker();
    echo $moduleHandler->selectPoll('issue', $issueObj->getLanguageId(), $issueObj->getPublicationId(), $issueObj->getIssueNumber());
    unset($moduleHandler);
    ?>
 	</TD>
</TR>