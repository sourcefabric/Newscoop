			<table width="100%" style="border: 1px solid #EEEEEE;">
			<tr>
				<td>
					<table width="100%" bgcolor="#EEEEEE" cellpadding="3" cellspacing="0">
					<tr>
						<td align="left">
                            <strong><?php putGS("Comments"); ?></strong>
						</td>
					</tr>
					</table>
				</td>
			</tr>
            <tr>
				<td align="left" width="100%" style="padding-left: 8px;">
                <?php
                if (SystemPref::Get("UseDBReplication") == 'Y') {
                    if ($connectedToOnlineServer) {
                ?>
                    <span class="success_message">
                <?php
                        putGS("Online Server: On");
                    } elseif (isset($connectedToOnlineServer)
                              &&$connectedToOnlineServer == false) {
                ?>
                    <span class="failure_message">
                <?php
                        putGS("Online Server: Off");
                    }
                ?>
                    </span><br />
                <?php
                }
                ?>
                    <?php putGS("Total:"); ?> <?php p(count($comments)); ?>
                    <br />
                    <?php if ($f_show_comments) { ?>
                    <a href="<?php echo camp_html_article_url($articleObj, $f_language_selected, "edit.php", "", "&f_show_comments=0"); ?>"><?php putGS("Hide Comments"); ?></a>
                    <?php } else { ?>
				    <a href="<?php echo camp_html_article_url($articleObj, $f_language_selected, "edit.php", "", "&f_show_comments=1"); ?>"><?php putGS("Show Comments"); ?></a>
				    <?php } ?>
                </td>
            </tr>
            </table>
