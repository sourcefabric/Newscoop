			<TABLE width="100%" style="border: 1px solid #EEEEEE;">
			<TR>
				<TD>
					<TABLE width="100%" bgcolor="#EEEEEE" cellpadding="3" cellspacing="0">
					<TR>
						<TD align="left">
						<STRONG>Locations</STRONG>
						</TD>
						<TD align="right">
							<TABLE cellpadding="2" cellspacing="0"><TR>
							<TR><TD><IMG SRC="<?php p($Campsite["ADMIN_IMAGE_BASE_URL"]);?>/add.png" border="0"></TD>
							<TD><A href="javascript: void(0);" onclick="window.open('<?php echo camp_html_article_url($articleObj, $f_language_id, "locations/popup.php"); ?>', 'map_edit_window', 'scrollbars=yes, resizable=yes, menubar=no, toolbar=no, width=1050, height=600, top=200, left=200');">Edit</A></TD></TR></TABLE>
						</TD>
					</TR>
					<TR>
                        <TD align="center" colspan="2"><A href="#" onclick="window.open('<?php echo camp_html_article_url($articleObj, $f_language_id, "locations/preview.php"); ?>', 'map_preview_window', 'scrollbars=yes, resizable=yes, menubar=no, toolbar=no, width=1050, height=600, top=200, left=200');">preview</A>&nbsp;<A href="#" onClick="return false;">x</A></TD>
					</TR>
					</TABLE>
				</TD>
			</TR>
			</TABLE>
