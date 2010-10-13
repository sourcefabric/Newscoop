<?php
camp_load_translation_strings("plugin_poll");
global $articleObj, $f_article_number, $f_edit_mode; 
?>

<tr><td>
  <TABLE width="100%" style="border: 1px solid #EEEEEE;">
    <TR>
    	<TD>
    		<TABLE width="100%" bgcolor="#EEEEEE" cellpadding="3" cellspacing="0">
    		<TR>
    			<TD align="left">
    			<STRONG><?php putGS("Polls"); ?></STRONG>
    			</TD>
    			<?php if (($f_edit_mode == "edit") && $g_user->hasPermission('plugin_poll')) {  ?>
    			<TD align="right">
    				<IMG src="<?php p($Campsite["ADMIN_IMAGE_BASE_URL"]);?>/add.png" border="0">
    				<A href="javascript: void(0);" onclick="window.open('<?php p("/$ADMIN/poll/assign_popup.php?f_poll_item=article&amp;f_language_id={$articleObj->getLanguageId()}&amp;f_article_nr=$f_article_number"); ?>', 'assign_poll', 'scrollbars=yes, resizable=yes, menubar=no, toolbar=no, width=800, height=600, top=200, left=100');"><?php putGS("Attach"); ?></A>
    			</TD>
    			<?php } ?>
    		</TR>
    		</TABLE>
    	</TD>
    </TR>
    <?php
    foreach (PollArticle::getAssignments(null, $articleObj->getLanguageId(), $articleObj->getArticleNumber()) as $pollArticle) {
        $poll = $pollArticle->getPoll($articleObj->getLanguageId());
        ?>
        <TR>
        	<TD align="left" valign="top"><?php p($poll->getName().' ('.$poll->getLanguageName().')') ?></TD>
        </TR>
    <?php } ?>
  </TABLE>
</td></tr>
