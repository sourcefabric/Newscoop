            <TABLE width="100%" style="border: 1px solid #EEEEEE;">
            <FORM action="do_sortlist.php" method="POST" name="audioclip_sortlist_form" id="audioclip_sortlist_form" onsubmit="populateHiddenVars('audioclip_sortlist');">
            <INPUT type="hidden" name="f_sortlist_order" id="f_sortlist_order" size="60" />
            <INPUT type="hidden" name="f_sortlist_name" value="audioclip_sortlist" />
            <INPUT type="hidden" name="f_language_selected" value="<?php p($f_language_selected); ?>" />
            <INPUT type="hidden" name="f_language_id" value="<?php p($f_language_id); ?>" />
            <INPUT type="hidden" name="f_article_number" value="<?php p($f_article_number); ?>" />
            <TR>
                <TD>
                    <TABLE width="100%" bgcolor="#EEEEEE" cellpadding="3" cellspacing="0">
                    <TR>
                        <TD align="left">
                        <STRONG><?php putGS("Audioclips"); ?></STRONG>
                        </TD>
                        <?php if (($f_edit_mode == "edit") && $g_user->hasPermission('AttachAudioclipToArticle')) {  ?>
                        <TD align="right">
                            <A href="javascript: void(0);" onclick="window.open('<?php echo camp_html_article_url($articleObj, $f_language_id, "audioclips/popup.php"); ?>', 'attach_audioclip', 'scrollbars=yes, resizable=yes, menubar=no, toolbar=no, width=750, height=600, top=200, left=100');"><IMG src="<?php p($Campsite["ADMIN_IMAGE_BASE_URL"]);?>/add.png" border="0" title="Attach" /></A>&nbsp;
                            <INPUT type="image" src="<?php p($Campsite["ADMIN_IMAGE_BASE_URL"]); ?>/save.png" name="save" title="Save Order" <?php if (!$articleAudioclips) { p('disabled'); } ?> />
                        </TD>
                        <?php } ?>
                    </TR>
                    </TABLE>
                </TD>
            </TR>
            <SCRIPT type="text/javascript" src="<?php echo $Campsite["WEBSITE_URL"]; ?>/javascript/domTT/domLib.js"></SCRIPT>
            <SCRIPT type="text/javascript" src="<?php echo $Campsite["WEBSITE_URL"]; ?>/javascript/domTT/domTT.js"></SCRIPT>
            <SCRIPT type="text/javascript" src="<?php echo $Campsite["WEBSITE_URL"]; ?>/javascript/scriptaculous/prototype.js"></SCRIPT>
            <SCRIPT type="text/javascript" src="<?php echo $Campsite["WEBSITE_URL"]; ?>/javascript/scriptaculous/scriptaculous.js"></SCRIPT>
            <SCRIPT type="text/javascript">
            <!--
                function populateHiddenVars(sortableList)
                {
                    document.getElementById('f_sortlist_order').value = Sortable.serialize(sortableList);
                    return true;
                }

                var domTT_styleClass = 'domTTOverlib';
            //-->
            </SCRIPT>
            <TR>
                <TD>
            <DIV id="audioclip_sortlist">
            <?php
            foreach($articleAudioclips as $articleAudioclip) {
                $toolTipCaption = '';
                $toolTipContent = '';
                $aClipMetaTags = $articleAudioclip->getAvailableMetaTags();
                foreach ($aClipMetaTags as $metaTag) {
                    list($nameSpace, $localPart) = explode(':', strtolower($metaTag));
                    if ($localPart == 'title') {
                        $toolTipCaption = '<strong>'.$metatagLabel[$metaTag] . ': ' . $articleAudioclip->getMetatagValue($localPart) . '</strong><br />';
                    } else {
                        $toolTipContent .= $metatagLabel[$metaTag] . ': ' . $articleAudioclip->getMetatagValue($localPart) . '<br />';
                    }
                }
                if (($f_edit_mode == "edit")
                    && $g_user->hasPermission('AttachAudioclipToArticle')) {
                    $aClipEditUrl = "/$ADMIN/articles/audioclips/edit.php?f_publication_id=$f_publication_id&f_issue_number=$f_issue_number&f_section_number=$f_section_number&f_article_number=$f_article_number&f_action=edit&f_audioclip_id=".$articleAudioclip->getGunId()."&f_language_id=$f_language_id&f_language_selected=$f_language_selected";
                    $aClipDeleteUrl = "/$ADMIN/articles/audioclips/do_del.php?f_publication_id=$f_publication_id&f_issue_number=$f_issue_number&f_section_number=$f_section_number&f_article_number=$f_article_number&f_audioclip_id=".$articleAudioclip->getGunId()."&f_language_selected=$f_language_selected&f_language_id=$f_language_id";
                    $audioclipEditLink = '<a href="javascript: void(0);" onclick="window.open(\''.$aClipEditUrl.'\', \'attach_audioclip\', \'scrollbars=yes, resizable=yes, menubar=no, toolbar=no, width=750, height=600, top=200, left=100\');" onmouseover="domTT_activate(this, event, \'caption\', \''.addslashes($toolTipCaption).'\', \'content\', \''.addslashes($toolTipContent).'\', \'trail\', true, \'delay\', 0);">'.wordwrap($articleAudioclip->getMetatagValue('title'), '25', '<br />', true).'</a>';
                    $audioclipDeleteLink = '<a href="'.$aClipDeleteUrl.'" title="'.getGS("Delete").'" onclick="return confirm(\''.getGS("Are you sure you want to remove the audio file \'$1\' from the article?", camp_javascriptspecialchars($articleAudioclip->getMetatagValue('title'))).'\');"><img src="'.$Campsite['ADMIN_IMAGE_BASE_URL'].'/unlink.png" border="0" /></a>';
                    $audioclipLink = $audioclipEditLink . ' ' . $audioclipDeleteLink;
                } else {
                    $audioclipLink = '<a href="#" onmouseover="domTT_activate(this, event, \'caption\', \''.addslashes($toolTipCaption).'\', \'content\', \''.addslashes($toolTipContent).'\', \'trail\', true, \'delay\', 0);">'.wordwrap($articleAudioclip->getMetatagValue('title'), '25', '<br />', true).'</a>';
                }
            ?>
                <DIV id="div_<?php p($articleAudioclip->getGunId()); ?>">
                <?php putGS("Title"); ?>: <?php p($audioclipLink); ?>
                <BR />
                <?php putGS("Creator"); ?>: <?php p($articleAudioclip->getMetatagValue('creator')); ?>
                <BR />
                <?php putGS("Length"); ?>: <?php p(camp_time_format($articleAudioclip->getMetatagValue('extent'))); ?>
                </DIV>
            <?php
            } // foreach($articleAudioclips as $articleAudioclip) {
            ?>
            </DIV>
            </FORM>
                </TD>
            </TR>
            </TABLE>
            <SCRIPT type="text/javascript">
            // <![CDATA[
                Sortable.create('audioclip_sortlist', {tag:'div'});
            // ]]>
            </SCRIPT>
