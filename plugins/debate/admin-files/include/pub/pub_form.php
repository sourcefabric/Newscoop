<?php
$translator = \Zend_Registry::get('container')->getService('translator');
if (is_object($publicationObj)) :
?>

<table BORDER="0" CELLSPACING="0" CELLPADDING="3" style="padding-top: 0.5em; padding-left: 10px; padding-right: 10px;" width="100%">
    <tr><td colspan="2"><HR NOSHADE SIZE="1" COLOR="BLACK"></td></tr>
    <tr width="100%">
        <td>
            <font size="+1"><b><?php echo $translator->trans("Debates", array(), 'plugin_debate'); ?></b></font>
        </td>

    <?php if ($g_user->hasPermission('plugin_debate_admin')) {  ?>

    	<TD align="right">
    		<IMG src="<?php p($Campsite["ADMIN_IMAGE_BASE_URL"]);?>/configure.png" border="0">
    		<A href="javascript: void(0);" onclick="window.open('<?php p("/$ADMIN/debate/assign_popup.php?f_debate_item=publication&amp;f_publication_id=" . $publicationObj->getPublicationId() . "&f_language_id={$publicationObj->getLanguageId()}"); ?>', 'assign_debate', 'scrollbars=yes, resizable=yes, menubar=no, toolbar=no, width=800, height=600, top=200, left=100');"><?php echo $translator->trans("Edit"); ?></A>
    	</TD>
    	<?php } ?>
    </TR>

    <TR>
    	<TD colspan="2" align="left" valign="top" width="100%">
    	<div style="overflow: auto; max-height: 50px">
        <?php
        foreach (DebatePublication::getAssignments(null, $publicationObj->getPublicationId()) as $debatePublication) {
            $debate = $debatePublication->getDebate($publicationObj->getLanguageId());
            p($debate->getName());
    		p("&nbsp;({$debate->getLanguageName()})<br>");
    	}
    	?>
    	</div>
        </TD>
    </TR>
</TABLE>
<?php endif; ?>