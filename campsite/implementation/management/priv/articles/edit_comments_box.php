			<TABLE width="100%" style="border: 1px solid #EEEEEE;">
			<TR>
				<TD>
					<TABLE width="100%" bgcolor="#EEEEEE" cellpadding="3" cellspacing="0">
					<TR>
						<TD align="left">
						<STRONG><?php putGS("Comments"); ?></STRONG>
						</TD>
					</TR>
					</TABLE>
				</TD>
			</TR>
            <TR>
				<TD align="left" width="100%" style="padding-left: 8px;">
				<?php
				if (is_array($comments)) {
					putGS("Total:"); ?> <?php p(count($comments));
				?>
				    <BR />
				    <?php if ($f_show_comments) { ?>
				    <A href="<?php echo camp_html_article_url($articleObj, $f_language_selected, "edit.php", "", "&f_show_comments=0"); ?>"><?php putGS("Hide Comments"); ?></A>
				    <?php } else { ?>
				    <A href="<?php echo camp_html_article_url($articleObj, $f_language_selected, "edit.php", "", "&f_show_comments=1"); ?>"><?php putGS("Show Comments"); ?></A>
				    <?php }
				} else {
					putGS("Comments Disabled");
				}
				?>
                </TD>
            </TR>
            </TABLE>
