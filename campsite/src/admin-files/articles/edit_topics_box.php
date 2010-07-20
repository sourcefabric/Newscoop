			<TABLE width="100%" style="border: 1px solid #EEEEEE;">
			<TR>
				<TD>
					<TABLE width="100%" bgcolor="#EEEEEE" cellpadding="3" cellspacing="0">
					<TR>
						<TD align="left">
						<STRONG><?php putGS("Topics"); ?></STRONG>
						</TD>
						<?php if (($f_edit_mode == "edit") && $g_user->hasPermission('AttachTopicToArticle')) {  ?>
						<TD align="right">
							<IMG src="<?php p($Campsite["ADMIN_IMAGE_BASE_URL"]);?>/add.png" border="0">
							<A href="javascript: void(0);" onclick="window.open('<?php echo camp_html_article_url($articleObj, $f_language_id, "topics/popup.php"); ?>', 'attach_topic', 'scrollbars=yes, resizable=yes, menubar=no, toolbar=no, width=300, height=400, top=200, left=200');"><?php putGS("Attach"); ?></A>
						</TD>
						<?php } ?>
					</TR>
					</TABLE>
				</TD>
			</TR>
			<?PHP
			foreach ($articleTopics as $tmpArticleTopic) {
				$detachUrl = "/$ADMIN/articles/topics/do_del.php?f_article_number=$f_article_number&f_topic_id=".$tmpArticleTopic->getTopicId()."&f_language_selected=$f_language_selected&f_language_id=$f_language_id&".SecurityToken::URLParameter();
			?>
			<TR>
				<TD align="center" width="100%" style="border-top: 1px solid #EEEEEE;">
					<TABLE>
					<TR>
						<TD align="center" valign="middle">
							<?php
							$path = $tmpArticleTopic->getPath();
							$pathStr = "";
							foreach ($path as $element) {
								$name = $element->getName($f_language_selected);
								if (empty($name)) {
									// For backwards compatibility -
									// get the english translation if the translation
									// doesnt exist for the article's language.
									$name = $element->getName(1);
									if (empty($name)) {
										$name = "-----";
									}
								}
								$pathStr .= " / ". htmlspecialchars($name);
							}

							// Get the topic name for the 'detach topic' dialog box, below.
							$tmpTopicName = $tmpArticleTopic->getName($f_language_selected);
							// For backwards compatibility.
							if (empty($tmpTopicName)) {
								$tmpTopicName = $tmpArticleTopic->getName(1);
							}
							?>
							<?php p(wordwrap($pathStr, 25, "<br>&nbsp;&nbsp;")); ?>
						</TD>
						<?php if (($f_edit_mode == "edit") && $g_user->hasPermission('AttachTopicToArticle')) { ?>
						<TD>
							<A href="<?php p($detachUrl); ?>" onclick="return confirm('<?php putGS("Are you sure you want to remove the topic \\'$1\\' from the article?", camp_javascriptspecialchars($tmpTopicName)); ?>');"><IMG src="<?php p($Campsite["ADMIN_IMAGE_BASE_URL"]);?>/unlink.png" border="0"></A>
						</TD>
						<?php } ?>
					</TR>
					</TABLE>
				</TD>
			</TR>
			<?php } ?>
			</TABLE>
