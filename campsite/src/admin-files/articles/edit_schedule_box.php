			<TABLE width="100%" style="border: 1px solid #EEEEEE;">
			<TR>
				<TD>
					<TABLE width="100%" bgcolor="#EEEEEE" cellpadding="3" cellspacing="0">
					<TR>
						<TD align="left">
						<STRONG><?php putGS("Publish Schedule"); ?></STRONG>
						</TD>
						<?php if (($f_edit_mode == "edit") && $g_user->hasPermission('Publish')) {  ?>
						<TD align="right">
							<TABLE cellpadding="2" cellspacing="0"><tr><td><img src="<?php p($Campsite["ADMIN_IMAGE_BASE_URL"]);?>/add.png" border="0"></td>
							<TD><A href="javascript: void(0);" onclick="window.open('<?php echo camp_html_article_url($articleObj, $f_language_id, "autopublish.php"); ?>', 'autopublish_window', 'scrollbars=yes, resizable=yes, menubar=no, toolbar=no, width=450, height=500, top=200, left=200');"><?php putGS("Add Event"); ?></A></TD></TR></TABLE>
						</TD>
						<?php } ?>
					</TR>
					</TABLE>
				</TD>
			</TR>
			<?php foreach ($articleEvents as $event) { ?>
			<TR>
				<TD style="padding-left: 8px;">
					<TABLE cellpadding="0" cellspacing="2">
					<TR>
						<TD valign="middle" style="padding-top: 3px;">
							<?php p(htmlspecialchars($event->getActionTime())); ?>
						</TD>
						<TD style="padding-left: 3px;" valign="middle">
						<?php if (($f_edit_mode == "edit") && $g_user->hasPermission('Publish')) { ?>
						<A href="<?php p(camp_html_article_url($articleObj, $f_language_id, "autopublish_del.php", '', '&f_event_id='.$event->getArticlePublishId(), true)); ?>" onclick="return confirm('<?php putGS("Are you sure you want to remove the event scheduled on $1?", camp_javascriptspecialchars($event->getActionTime())); ?>');"><IMG src="<?php p($Campsite["ADMIN_IMAGE_BASE_URL"]);?>/unlink.png" border="0"></A>
						<?php } ?>
						</TD>
					</TR>
					<?php
					$publishAction = $event->getPublishAction();
					if (!empty($publishAction)) {
						echo "<TR><TD colspan=2 style='padding-left: 7px;'>";
						if ($publishAction == "P") {
							putGS("Publish");
						}
						if ($publishAction == "U") {
							putGS("Unpublish");
						}
						echo "</TD></TR>";
					}
					$frontPageAction = $event->getFrontPageAction();
					if (!empty($frontPageAction)) {
						echo "<TR><TD colspan=2 style='padding-left: 7px;'>";
						if ($frontPageAction == "S") {
							putGS("Show on front page");
						}
						if ($frontPageAction == "R") {
							putGS("Remove from front page");
						}
						echo "</TD></TR>";
					}
					$sectionPageAction = $event->getSectionPageAction();
					if (!empty($sectionPageAction)) {
						echo "<TR><TD colspan=2 style='padding-left: 7px;'>";
						if ($sectionPageAction == "S") {
							putGS("Show on section page");
						}
						if ($sectionPageAction == "R") {
							putGS("Remove from section page");
						}
						echo "</TD></TR>";
					}
					?>
					</TABLE>
				</TD>
			</TR>
			<?php } ?>
			</TABLE>
