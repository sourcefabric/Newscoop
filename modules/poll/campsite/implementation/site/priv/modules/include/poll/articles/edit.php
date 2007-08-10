<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/phpwrapper/settings.ini.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/phpwrapper/functions.php';
require_once $_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/modules/include/poll/poll_linker.class.php";

?>
<TR><TD>
	<!-- BEGIN COMMENTS table -->
	<TABLE width="100%" style="border: 1px solid #EEEEEE;">
	<TR>
		<TD>
			<TABLE width="100%" bgcolor="#EEEEEE" cellpadding="3" cellspacing="0">
			<TR>
				<TD align="left">
				<b><?php putGS("Assign poll"); ?></b>
				</td>
		    </TR>
		    
		    <TR><TD align="center">
            <?php 
            $moduleHandler =& new poll_linker();
            echo $moduleHandler->selectPoll('article', $articleObj->getLanguageId(), null, null, null, $articleObj->getArticleNumber());
            unset($moduleHandler);
            ?>
            </TD></TR>
            </TABLE>
        </TD>
    </TR>
    </TABLE>
</TD></TR>