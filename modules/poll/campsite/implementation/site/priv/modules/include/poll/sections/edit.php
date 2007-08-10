<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/phpwrapper/settings.ini.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/phpwrapper/functions.php';
require_once $_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/modules/include/poll/poll_linker.class.php";

?>

<TR>
	<TD COLSPAN="2" style="padding-top:20px;">
		<B><?php  putGS("Assign Poll"); ?></B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD colspan="2" align="middle">
    <?php 
    $moduleHandler =& new poll_linker();
    echo $moduleHandler->selectPoll('section', $sectionObj->getLanguageId(), $sectionObj->getPublicationId(), $sectionObj->getIssueNumber(), $sectionObj->getSectionNumber());
    unset($moduleHandler);
    ?>
 	</TD>
</TR>