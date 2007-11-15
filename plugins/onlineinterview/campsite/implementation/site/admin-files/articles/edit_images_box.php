			<TABLE width="100%" style="border: 1px solid #EEEEEE;">
			<TR>
				<TD>
					<TABLE width="100%" bgcolor="#EEEEEE" cellpadding="3" cellspacing="0">
					<TR>
						<TD align="left">
						<STRONG><?php putGS("Images"); ?></STRONG>
						</TD>
						<?php if (($f_edit_mode == "edit") && $g_user->hasPermission('AttachImageToArticle')) {  ?>
						<TD align="right">
							<IMG src="<?php p($Campsite["ADMIN_IMAGE_BASE_URL"]);?>/add.png" border="0" />
							<A href="javascript: void(0);" onclick="window.open('<?php echo camp_html_article_url($articleObj, $f_language_id, "images/popup.php"); ?>', 'attach_image', 'scrollbars=yes, resizable=yes, menubar=no, toolbar=no, width=750, height=600, top=200, left=100');"><?php putGS("Attach"); ?></A>
						</TD>
						<?php } ?>
					</TR>
					</TABLE>
				</TD>
			</TR>
			<?php
			foreach ($articleImages as $tmpArticleImage) {
				$image = $tmpArticleImage->getImage();
				$imageEditUrl = "/$ADMIN/articles/images/edit.php?f_publication_id=$f_publication_id&f_issue_number=$f_issue_number&f_section_number=$f_section_number&f_article_number=$f_article_number&f_image_id=".$image->getImageId()."&f_language_id=$f_language_id&f_language_selected=$f_language_selected&f_image_template_id=".$tmpArticleImage->getTemplateId();
				$detachUrl = "/$ADMIN/articles/images/do_unlink.php?f_publication_id=$f_publication_id&f_issue_number=$f_issue_number&f_section_number=$f_section_number&f_article_number=$f_article_number&f_image_id=".$image->getImageId()."&f_language_selected=$f_language_selected&f_language_id=$f_language_id&f_image_template_id=".$tmpArticleImage->getTemplateId();
				$imageSize = getimagesize($image->getImageStorageLocation());
			?>
			<TR>
				<TD align="center" width="100%">
					<TABLE>
					<TR>
						<TD align="center" valign="middle">
							<?php echo $tmpArticleImage->getTemplateId(); ?>.
						</TD>
						<TD align="center" valign="middle">
							<?php if ($f_edit_mode == "edit") { ?><A href="<?php p($imageEditUrl); ?>"><?php } ?><IMG src="<?php p($image->getThumbnailUrl()); ?>" border="0"><?php if ($f_edit_mode == "edit") { ?></A><?php } ?>
						</TD>
						<?php if (($f_edit_mode == "edit") && $g_user->hasPermission('AttachImageToArticle')) { ?>
						<TD>
							<A href="<?php p($detachUrl); ?>" onclick="return confirm('<?php putGS("Are you sure you want to remove the image \\'$1\\' from the article?", camp_javascriptspecialchars($image->getDescription())); ?>');"><IMG src="<?php p($Campsite["ADMIN_IMAGE_BASE_URL"]);?>/unlink.png" border="0"></A>
						</TD>
						<?php } ?>
					</TR>
					<TR>
						<TD></TD>
						<TD align="center"><?php p($imageSize[0]."x".$imageSize[1]); ?></TD>
						<TD></TD>
					</TR>
					</TABLE>
				</TD>
			</TR>
			<?php } ?>
			</TABLE>
