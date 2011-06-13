<script type="text/javascript">
function onCommentsActivated(p_checkbox)
{
    if (p_checkbox.checked) {
        document.getElementById('comment_default').disabled = false;
        document.getElementById('public_enabled').disabled = false;
        document.getElementById('public_moderated').disabled = false;
        document.getElementById('subscriber_moderated').disabled = false;
        document.getElementById('captcha_enabled').disabled = false;
        document.getElementById('spam_blocking_enabled').disabled = false;
    } else {
        document.getElementById('comment_default').disabled = true;
        document.getElementById('public_enabled').disabled = true;
        document.getElementById('public_moderated').disabled = true;
        document.getElementById('subscriber_moderated').disabled = true;
        document.getElementById('captcha_enabled').disabled = true;
        document.getElementById('spam_blocking_enabled').disabled = true;
    }
}

function onCommentsModerated(p_checkbox)
{
    moderator = document.getElementById('moderator_to').value;
    if (p_checkbox.checked && moderator == '') {
        alert('<?php putGS('Make sure to enter the "Moderator Address" below'); ?>');
    }
}
</script>
<TABLE BORDER="0" CLASS="box_table" cellpadding="0" cellspacing="0">
<tr>
    <td>
        <!-- Begin left column -->
        <table BORDER="0" CELLSPACING="0" CELLPADDING="3" style="padding-left: 10px; padding-right: 10px;
		<?php
			if( Saas::singleton()->hasPermission("ManagePublicationSubscriptions")) {
				echo 'border-right: 1px solid black;';
			}
		?>
		">
        <tr>
            <td colspan="2">
                <font size="+1"><b><?php putGS("General attributes"); ?></b></font>
            </td>
        </tr>

        <?php if (isset($publicationObj)) { ?>
        <TR>
        	<TD align="right">
        		<?php putGS("Number"); ?>:
        	</TD>
        	<td>
        		<?php p($publicationObj->getPublicationId()); ?>
        	</td>
        </TR>
        <?php } ?>

        <TR>
        	<TD ALIGN="RIGHT"><?php  putGS("Name"); ?>:</TD>
        	<TD>
        	<INPUT TYPE="TEXT" class="input_text" NAME="f_name" VALUE="<?php  if (isset($publicationObj)) { p(htmlspecialchars($publicationObj->getName())); } ?>" SIZE="32" alt="blank" emsg="<?php putGS('You must fill in the $1 field.',getGS('Name')); ?>">
        	</TD>
        </TR>

        <TR>
        	<TD ALIGN="RIGHT"><?php  putGS("Default Site Alias"); ?>:</TD>
        	<TD>
                <?php if (isset($publicationObj)) { ?>
        		<SELECT NAME="f_default_alias" class="input_select">
        		<?php
        		foreach ($aliases as $alias) {
        			camp_html_select_option($alias->getId(), $publicationObj->getDefaultAliasId(), $alias->getName());
        		}
        		?>
        		</SELECT>&nbsp;
        		<a href="/<?php p($ADMIN); ?>/pub/aliases.php?Pub=<?php echo $f_publication_id ?>"><?php putGS("Edit aliases"); ?></a>
        		<?php } else { ?>
                <INPUT TYPE="TEXT" class="input_text" NAME="f_default_alias" VALUE="<?php p(urlencode($_SERVER['HTTP_HOST'])); ?>" SIZE="32" alt="blank" emsg="<?php putGS('You must fill in the $1 field.',getGS('Site')); ?>">
        		<?php } ?>
        	</TD>
        </TR>
        <TR>
        	<TD ALIGN="RIGHT"><?php  putGS("Default language"); ?>:</TD>
        	<TD>
        	<SELECT NAME="f_language" class="input_select">
        	<?php
        	$selectedLanguage = '';
        	if (isset($publicationObj)) {
        	    $selectedLanguage = $publicationObj->getDefaultLanguageId();
        	}
        	foreach ($languages as $language) {
        		camp_html_select_option($language->getLanguageId(), $selectedLanguage, $language->getNativeName());
        	}
        	?>
        	</SELECT>&nbsp;
        <a href="/<?php echo $ADMIN; ?>/languages/"><?php putGS("Edit languages"); ?></a>
        	</TD>
        </TR>
        <TR>
        	<TD ALIGN="RIGHT"><?php  putGS("URL Type"); ?>:</TD>
        	<TD>
        	<SELECT NAME="f_url_type" class="input_select"
        	onchange="
        	   if (this.value == '2') {
        	       $('#seo').show();
        	   } else {
        	       $('#seo').hide();
        	   }">
        	<?php
        	   $selectedUrlType = '2';
        	   if (isset($publicationObj)) {
        	       $selectedUrlType = $publicationObj->getUrlTypeId();
        	   }
        		foreach ($urlTypes as $urlType) {
        			camp_html_select_option($urlType->getId(), $selectedUrlType, $urlType->getName());
        		}
        	?>
        	</SELECT>
        	</TD>
        </TR>
        <TR id="seo" <?php if(isset($publicationObj) && $publicationObj->getUrlTypeId() != '2') echo 'style="display:none;"'?>>
            <TD ALIGN="RIGHT"><?php  putGS("Search engine optimization"); ?>:</TD>
            <TD>
                <?php $seo = isset($publicationObj) ? $publicationObj->getSeo() : array(); ?>
                <input type="checkbox" NAME="f_seo[name]" class="input_checkbox" <?php if (!empty($seo['name'])) { ?>checked<?php } ?>>
                <?php  putGS("Article title"); ?><br>
                <input type="checkbox" NAME="f_seo[keywords]" class="input_checkbox" <?php if (!empty($seo['keywords'])) { ?>checked<?php } ?>>
                <?php  putGS("Article keywords"); ?><br>
                <input type="checkbox" NAME="f_seo[topics]" class="input_checkbox" <?php if (!empty($seo['topics'])) { ?>checked<?php } ?>>
                <?php  putGS("Article topics"); ?>
            </TD>
        </TR>

        <tr><td colspan="2"><HR NOSHADE SIZE="1" COLOR="BLACK"></td></tr>

        <?php
        $commentsEnabled = false;
        if (isset($publicationObj) && $publicationObj->commentsEnabled()) {
            $commentsEnabled = true;
        }
        ?>
        <tr>
            <td colspan="2">
                <font size="+1"><b><?php putGS("Comments"); ?></b></font>
            </td>
        </tr>

        <TR>
        	<TD ALIGN="left" colspan="2" style="padding-left: 20px;">

        	   <table>
        	   <tr>
        	       <td>
               	       <?php  putGS("Comments enabled?"); ?>:
               	   </td>
               	   <td>
               	       <input type="checkbox" NAME="f_comments_enabled" class="input_checkbox" <?php if ($commentsEnabled) { ?>checked<?php } ?> onchange="onCommentsActivated(this);">
                	</TD>
                </TR>
                <TR>
                	<TD ALIGN="left" style="padding-left: 20px;"><?php  putGS("Article comments default to enabled?"); ?>:</td>
                	<td>
                    <input type="checkbox" NAME="f_comments_article_default" class="input_checkbox" id="comment_default" <?php if (!$commentsEnabled) {?> disabled<?php } ?> <?php if (isset($publicationObj) && $publicationObj->commentsArticleDefaultEnabled()) { ?>checked<?php } ?>>
                	</TD>
                </TR>
                <TR>
                	<TD ALIGN="left" style="padding-left: 20px;"><?php  putGS("Subscriber comments moderated?"); ?>:</td>
                	<td>
                        <input type="checkbox" NAME="f_comments_subscribers_moderated" id="subscriber_moderated" class="input_checkbox" <?php if (!$commentsEnabled) {?> disabled<?php } ?> <?php if (isset($publicationObj) && $publicationObj->commentsSubscribersModerated()) { ?>checked<?php } ?> onchange="onCommentsModerated(this);">
                	</TD>
                </TR>
                <TR>
                	<TD ALIGN="left" style="padding-left: 20px;"><?php  putGS("Public allowed to comment?"); ?>:</td>
                	<td>
                    <input type="checkbox" NAME="f_comments_public_enabled" id="public_enabled" class="input_checkbox" <?php if (!$commentsEnabled) {?> disabled<?php } ?> <?php if (isset($publicationObj) && $publicationObj->publicComments()) { ?>checked<?php } ?>>
                	</TD>
                </TR>
                <TR>
                	<TD ALIGN="left" style="padding-left: 40px;"><?php  putGS("Public comments moderated?"); ?>:</td>
                	<td>
                    <input type="checkbox" NAME="f_comments_public_moderated" id="public_moderated" class="input_checkbox" <?php if (!$commentsEnabled) {?> disabled<?php } ?> <?php if (isset($publicationObj) && $publicationObj->commentsPublicModerated()) { ?>checked<?php } ?> onchange="onCommentsModerated(this);">
                	</TD>
                </TR>
                <TR>
                	<TD ALIGN="left" style="padding-left: 20px;"><?php  putGS("Use CAPTCHA to prevent spam?"); ?>:</td>
                	<td>
                    <input type="checkbox" NAME="f_comments_captcha_enabled" id="captcha_enabled" class="input_checkbox" <?php if (!$commentsEnabled) {?> disabled<?php } ?> <?php if (isset($publicationObj) && $publicationObj->isCaptchaEnabled()) { ?>checked<?php } ?>>
                	</TD>
                </TR>
                <!--<TR>
                	<TD ALIGN="left" style="padding-left: 20px;"><?php  putGS("Enable spam blocking?"); ?>:</td>
                	<td>
                    <input type="checkbox" NAME="f_comments_spam_blocking_enabled" id="spam_blocking_enabled" class="input_checkbox" <?php if (!$commentsEnabled) {?> disabled<?php } ?> <?php if (isset($publicationObj) && $publicationObj->isSpamBlockingEnabled()) { ?>checked<?php } ?>>
                	</TD>
                </TR>-->
                <TR>
                	<TD ALIGN="left" style="padding-left: 20px;"><?php  putGS("Moderator Address"); ?>:</td>
                	<td>
                    <input type="text" class="input_text" NAME="f_comments_moderator_to" id="moderator_to" value="<?php echo isset($publicationObj)? $publicationObj->getCommentsModeratorTo(): ''; ?>">
                	</TD>
                </TR>
                <TR>
                	<TD ALIGN="left" style="padding-left: 20px;"><?php  putGS("From Address"); ?>:</td>
                	<td>
                    <input type="text" class="input_text" NAME="f_comments_moderator_from" id="moderator_from" value="<?php echo isset($publicationObj)? $publicationObj->getCommentsModeratorFrom(): ''; ?>">
                	</TD>
                </TR>
                </table>
            </td>
        </tr>
        </table>
        <!-- END left column -->
    </td>
	<?php
		if( Saas::singleton()->hasPermission("ManagePublicationSubscriptions")) {
	?>
    <!-- BEGIN right column -->
    <td style="" valign="top">
        <table BORDER="0" CELLSPACING="0" CELLPADDING="3" style="padding-top: 0.5em; padding-left: 10px; padding-right: 10px;">
        <tr>
            <td colspan="2">
                <font size="+1"><b><?php putGS("Subscription defaults"); ?></b></font>
            </td>
        </tr>

        <TR>
        	<TD ALIGN="RIGHT"><?php  putGS("Time Unit"); ?>:</TD>
        	<TD>
            <SELECT NAME="f_time_unit" class="input_select">
        	<?php
        	$selectedTimeUnit = '';
        	if (isset($publicationObj)) {
        	    $selectedTimeUnit = $publicationObj->getTimeUnit();
        	}
        	foreach ($timeUnits as $timeUnit) {
        		camp_html_select_option($timeUnit->getUnit(), $selectedTimeUnit, $timeUnit->getName());
        	}
        	?>
            </SELECT>
        	</TD>
        </TR>
        <tr>
        	<td colspan="2" align="left"><b><?php putGS('Paid subscriptions'); ?></b></td>
        </tr>
        <TR>
        	<TD ALIGN="RIGHT"><?php  putGS("Currency"); ?>:</TD>
        	<TD>
        	<INPUT TYPE="TEXT" class="input_text" NAME="f_currency" VALUE="<?php if (isset($publicationObj)) { p(htmlspecialchars($publicationObj->getCurrency())); } ?>" SIZE="10" MAXLENGTH="10">
        	</TD>
        </TR>
        <tr>
        	<td colspan="2" align="left"><?php  putGS("Time unit cost per one section"); ?>:</td>
        </tr>
        <TR>
        	<TD ALIGN="RIGHT">- <?php putGS('one language'); ?>:</TD>
        	<TD>
        	<INPUT TYPE="TEXT" class="input_text" NAME="f_unit_cost" VALUE="<?php  if (isset($publicationObj)) { p($publicationObj->getUnitCost()); } ?>" SIZE="10" MAXLENGTH="10">
        	</TD>
        </TR>
        <TR>
        	<TD ALIGN="RIGHT">- <?php putGS('all languages'); ?>:</TD>
        	<TD>
        	<INPUT TYPE="TEXT" class="input_text" NAME="f_unit_cost_all_lang" VALUE="<?php if (isset($publicationObj)) { p($publicationObj->getUnitCostAllLang()); } ?>" SIZE="10" MAXLENGTH="10">
        	</TD>
        </TR>
        <TR>
        	<TD ALIGN="RIGHT"><?php  putGS("Default time period"); ?>:</TD>
        	<TD>
        	<INPUT TYPE="TEXT" class="input_text" NAME="f_paid" VALUE="<?php if (isset($publicationObj)) { p($publicationObj->getPaidTime()); } ?>" SIZE="10" MAXLENGTH="10"> <?php putGS('time units'); ?>
        	</TD>
        </TR>
        <tr>
        	<td colspan="2" align="left" style="padding-top: 1em;"><b><?php putGS('Trial subscriptions'); ?></b></td>
        </tr>
        <TR>
        	<TD ALIGN="RIGHT"><?php  putGS("Default time period"); ?>:</TD>
        	<TD>
        	<INPUT TYPE="TEXT" class="input_text" NAME="f_trial" VALUE="<?php if (isset($publicationObj)) { p($publicationObj->getTrialTime()); } ?>" SIZE="10" MAXLENGTH="10"> <?php putGS('time units'); ?>
        	</TD>
        </TR>

        <?php if (isset($publicationObj)) { ?>
        <tr>
            <td colspan="2" align="center" style="padding-top: 1em;">
                <a href="/<?php echo $ADMIN; ?>/pub/deftime.php?Pub=<?php echo $f_publication_id; ?>&Language=<?php p($publicationObj->getDefaultLanguageId()); ?>"><?php putGS("Set subscription settings by country"); ?></a>
            </td>
        </tr>
        <?php } ?>
        </TABLE>
    </td>
    <!-- END right column -->
    <?php
		}
    ?>
</tr>

<?php CampPlugin::PluginAdminHooks(__FILE__); ?>

<TR>
	<TD COLSPAN="2" align="center" style="padding-left: 8px; padding-right: 8px;">
	   <table style="border-top: 1px solid black; padding-top: 7px; padding-bottom: 6px; margin-top: 10px;" width="100%">
	   <tr>
	       <td align="center">
        	<?php if (isset($publicationObj)) { ?>
        	<INPUT TYPE="HIDDEN" NAME="f_publication_id" VALUE="<?php  p($f_publication_id); ?>">
        	<?php } ?>
        	<INPUT TYPE="submit" class="button" NAME="Save" VALUE="<?php  putGS('Save'); ?>">
        	</TD>
       </tr>
       </table>
   </td>
</TR>
</table>
