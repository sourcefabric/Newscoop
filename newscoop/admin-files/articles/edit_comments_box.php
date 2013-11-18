<?php
$translator = \Zend_Registry::get('container')->getService('translator');
$preferencesService = \Zend_Registry::get('container')->getService('system_preferences_service');
?>
			<table width="100%" style="border: 1px solid #EEEEEE;">
			<tr>
				<td>
					<table width="100%" bgcolor="#EEEEEE" cellpadding="3" cellspacing="0">
					<tr>
						<td align="left">
                            <strong><?php echo $translator->trans("Comments", array(), 'articles'); ?></strong>
						</td>
					</tr>
					</table>
				</td>
			</tr>
            <tr>
				<td align="left" width="100%" style="padding-left: 8px;">
                <?php
                if ($preferencesService->UseDBReplication == 'Y') {
                    if ($connectedToOnlineServer) {
                ?>
                    <span class="success_message">
                <?php
                       echo $translator->trans("Online Server: On", array(), 'articles');
                    } elseif (isset($connectedToOnlineServer)
                              &&$connectedToOnlineServer == false) {
                ?>
                    <span class="failure_message">
                <?php
                        echo $translator->trans("Online Server: Off", array(), 'articles');
                    }
                ?>
                    </span><br />
                <?php
                }
                ?>
                    <?php echo $translator->trans("Total:", array(), 'articles'); ?> <?php p(count($comments)); ?>
                    <br />
                    <?php if ($f_show_comments) { ?>
                    <a href="<?php echo camp_html_article_url($articleObj, $f_language_selected, "edit.php", "", "&f_show_comments=0"); ?>"><?php echo $translator->trans("Hide Comments", array(), 'articles'); ?></a>
                    <?php } else { ?>
				    <a href="<?php echo camp_html_article_url($articleObj, $f_language_selected, "edit.php", "", "&f_show_comments=1"); ?>"><?php echo $translator->trans("Show Comments", array(), 'articles'); ?></a>
				    <?php } ?>
                </td>
            </tr>
            </table>
