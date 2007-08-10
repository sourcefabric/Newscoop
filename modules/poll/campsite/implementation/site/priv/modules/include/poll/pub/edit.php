<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/phpwrapper/settings.ini.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/phpwrapper/functions.php';
require_once $_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/modules/include/poll/poll_linker.class.php";

?>
<table style="border-top: 1px solid black; padding-left: 10px; padding-right: 10px; padding-top: 7px; padding-bottom: 6px; margin-top: 10px;" width="100%">
    <tr>
        <td>
            <font size="+1"><b><?php putGS("Assign Poll"); ?></b></font>
        </td>
    </tr>
	<TR>
		<TD align="middle">
	    <?php 
	    $moduleHandler =& new poll_linker();
	    echo $moduleHandler->selectPoll('publication', $publicationObj->getLanguageId(), $publicationObj->getPublicationId());
	    unset($moduleHandler);
	    ?>
	 	</TD>
	</TR>
</table>