			<TABLE width="100%" style="border: 1px solid #EEEEEE;">
			<TR>
				<TD>
					<TABLE width="100%" bgcolor="#EEEEEE" cellpadding="3" cellspacing="0">
					<TR>
						<TD align="left">
						<STRONG><?php putGS("Files"); ?></STRONG>
						</TD>
						<?php if (($f_edit_mode == "edit") && $g_user->hasPermission('AddFile')) {  ?>
						<TD align="right">
							<IMG src="<?php p($Campsite["ADMIN_IMAGE_BASE_URL"]);?>/add.png" border="0">
							<A href="javascript: void(0);" onclick="window.open('<?php echo camp_html_article_url($articleObj, $f_language_id, "files/popup.php"); ?>', 'attach_file', 'scrollbars=yes, resizable=yes, menubar=no, toolbar=no, width=500, height=400, top=200, left=100');"><?php putGS("Attach"); ?></A>
						</TD>
						<?php } ?>
					</TR>
					</TABLE>
				</TD>
			</TR>
			<?php
//			foreach ($articleFiles as $file) {
			?>
			<TR>
				<TD align="center" width="100%">
				</TD>
			</TR>
			<?php //} ?>
			</TABLE>
