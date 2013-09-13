<?php
$translator = \Zend_Registry::get('container')->getService('translator');
?>
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
        alert('<?php echo $translator->trans('Make sure to enter the Moderator Address below', array(), 'pub'); ?>');
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
                <font size="+1"><b><?php echo $translator->trans("General attributes", array(), 'pub'); ?></b></font>
            </td>
        </tr>

        <?php if (isset($publicationObj)) { ?>
        <TR>
        	<TD align="right">
        		<?php echo $translator->trans("Number"); ?>:
        	</TD>
        	<td>
        		<?php p($publicationObj->getPublicationId()); ?>
        	</td>
        </TR>
        <?php } ?>

        <TR>
        	<TD ALIGN="RIGHT"><?php  echo $translator->trans("Name"); ?>:</TD>
        	<TD>
        	<INPUT TYPE="TEXT" class="input_text" NAME="f_name" VALUE="<?php  if (isset($publicationObj)) { p(htmlspecialchars($publicationObj->getName())); } ?>" SIZE="32" alt="blank" emsg="<?php echo $translator->trans('You must fill in the $1 field.',array('$1' => $translator->trans('Name'))); ?>">
        	</TD>
        </TR>

        <TR>
        	<TD ALIGN="RIGHT"><?php  echo $translator->trans("Default Site Alias", array(), 'pub'); ?>:</TD>
        	<TD>
                <?php if (isset($publicationObj)) { ?>
        		<SELECT NAME="f_default_alias" class="input_select">
        		<?php
        		foreach ($aliases as $alias) {
        			camp_html_select_option($alias->getId(), $publicationObj->getDefaultAliasId(), $alias->getName());
        		}
        		?>
        		</SELECT>&nbsp;
        		<a href="/<?php p($ADMIN); ?>/pub/aliases.php?Pub=<?php echo $f_publication_id ?>"><?php echo $translator->trans("Edit aliases", array(), 'pub'); ?></a>
        		<?php } else { ?>
                <?php
                $defaultAlias = substr($_SERVER['HTTP_HOST'], 0, strpos($_SERVER['HTTP_HOST'], ':'));
                $defaultAlias = (!$defaultAlias) ? $_SERVER['HTTP_HOST'] : $defaultAlias;
                ?>
                <INPUT TYPE="TEXT" class="input_text" NAME="f_default_alias" VALUE="<?php p(urlencode($defaultAlias)); ?>" SIZE="32" alt="blank" emsg="<?php echo $translator->trans('You must fill in the $1 field.', array('$1' => $translator->trans('Site'))); ?>">
        		<?php } ?>
        	</TD>
        </TR>
        <TR>
        	<TD ALIGN="RIGHT"><?php  echo $translator->trans("Default language"); ?>:</TD>
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
        <a href="/<?php echo $ADMIN; ?>/languages/"><?php echo $translator->trans("Edit languages", array(), 'pub'); ?></a>
        	</TD>
        </TR>
        <TR>
        	<TD ALIGN="RIGHT"><?php  echo $translator->trans("URL Type", array(), 'pub'); ?>:</TD>
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
            <TD ALIGN="RIGHT"><?php  echo $translator->trans("Search engine optimization", array(), 'pub'); ?>:</TD>
            <TD>
                <?php $seo = isset($publicationObj) ? $publicationObj->getSeo() : array(); ?>
                <input type="checkbox" NAME="f_seo[name]" class="input_checkbox" <?php if (!empty($seo['name'])) { ?>checked<?php } ?>>
                <?php  echo $translator->trans("Article title", array(), 'pub'); ?><br>
                <input type="checkbox" NAME="f_seo[keywords]" class="input_checkbox" <?php if (!empty($seo['keywords'])) { ?>checked<?php } ?>>
                <?php  echo $translator->trans("Article keywords", array(), 'pub'); ?><br>
                <input type="checkbox" NAME="f_seo[topics]" class="input_checkbox" <?php if (!empty($seo['topics'])) { ?>checked<?php } ?>>
                <?php  echo $translator->trans("Article topics", array(), 'pub'); ?>
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
                <font size="+1"><b><?php echo $translator->trans("Comments"); ?></b></font>
            </td>
        </tr>

        <TR>
        	<TD ALIGN="left" colspan="2" style="padding-left: 20px;">

        	   <table>
        	   <tr>
        	       <td>
               	       <?php  echo $translator->trans("Comments enabled?", array(), 'pub'); ?>:
               	   </td>
               	   <td>
               	       <input type="checkbox" NAME="f_comments_enabled" class="input_checkbox" <?php if ($commentsEnabled) { ?>checked<?php } ?> onchange="onCommentsActivated(this);">
                	</TD>
                </TR>
                <TR>
                	<TD ALIGN="left" style="padding-left: 20px;"><?php  echo $translator->trans("Article comments default to enabled?", array(), 'pub'); ?>:</td>
                	<td>
                    <input type="checkbox" NAME="f_comments_article_default" class="input_checkbox" id="comment_default" <?php if (!$commentsEnabled) {?> disabled<?php } ?> <?php if (isset($publicationObj) && $publicationObj->commentsArticleDefaultEnabled()) { ?>checked<?php } ?>>
                	</TD>
                </TR>
                <TR>
                	<TD ALIGN="left" style="padding-left: 20px;"><?php  echo $translator->trans("Subscriber comments moderated?", array(), 'pub'); ?>:</td>
                	<td>
                        <input type="checkbox" NAME="f_comments_subscribers_moderated" id="subscriber_moderated" class="input_checkbox" <?php if (!$commentsEnabled) {?> disabled<?php } ?> <?php if (isset($publicationObj) && $publicationObj->commentsSubscribersModerated()) { ?>checked<?php } ?> onchange="onCommentsModerated(this);">
                	</TD>
                </TR>
                <TR>
                	<TD ALIGN="left" style="padding-left: 20px;"><?php  echo $translator->trans("Public allowed to comment?", array(), 'pub'); ?>:</td>
                	<td>
                    <input type="checkbox" NAME="f_comments_public_enabled" id="public_enabled" class="input_checkbox" <?php if (!$commentsEnabled) {?> disabled<?php } ?> <?php if (isset($publicationObj) && $publicationObj->publicComments()) { ?>checked<?php } ?>>
                	</TD>
                </TR>
                <TR>
                	<TD ALIGN="left" style="padding-left: 40px;"><?php  echo $translator->trans("Public comments moderated?", array(), 'pub'); ?>:</td>
                	<td>
                    <input type="checkbox" NAME="f_comments_public_moderated" id="public_moderated" class="input_checkbox" <?php if (!$commentsEnabled) {?> disabled<?php } ?> <?php if (isset($publicationObj) && $publicationObj->commentsPublicModerated()) { ?>checked<?php } ?> onchange="onCommentsModerated(this);">
                	</TD>
                </TR>
                <TR>
                	<TD ALIGN="left" style="padding-left: 20px;"><?php  echo $translator->trans("Use CAPTCHA to prevent spam?", array(), 'pub'); ?>:</td>
                	<td>
                    <input type="checkbox" NAME="f_comments_captcha_enabled" id="captcha_enabled" class="input_checkbox" <?php if (!$commentsEnabled) {?> disabled<?php } ?> <?php if (isset($publicationObj) && $publicationObj->isCaptchaEnabled()) { ?>checked<?php } ?>>
                	</TD>
                </TR>
                <!--<TR>
                	<TD ALIGN="left" style="padding-left: 20px;"><?php  echo $translator->trans("Enable spam blocking?", array(), 'pub'); ?>:</td>
                	<td>
                    <input type="checkbox" NAME="f_comments_spam_blocking_enabled" id="spam_blocking_enabled" class="input_checkbox" <?php if (!$commentsEnabled) {?> disabled<?php } ?> <?php if (isset($publicationObj) && $publicationObj->isSpamBlockingEnabled()) { ?>checked<?php } ?>>
                	</TD>
                </TR>-->
                <TR>
                	<TD ALIGN="left" style="padding-left: 20px;"><?php  echo $translator->trans("Moderator Address", array(), 'pub'); ?>:</td>
                	<td>
                    <input type="text" class="input_text" NAME="f_comments_moderator_to" id="moderator_to" value="<?php echo isset($publicationObj)? $publicationObj->getCommentsModeratorTo(): ''; ?>">
                	</TD>
                </TR>
                <TR>
                	<TD ALIGN="left" style="padding-left: 20px;"><?php  echo $translator->trans("From Address", array(), 'pub'); ?>:</td>
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
                <font size="+1"><b><?php echo $translator->trans("Subscription defaults"); ?></b></font>
            </td>
        </tr>

        <TR>
        	<TD ALIGN="RIGHT"><?php  echo $translator->trans("Time Unit", array(), 'pub'); ?>:</TD>
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
        	<td colspan="2" align="left"><b><?php echo $translator->trans('Paid subscriptions', array(), 'pub'); ?></b></td>
        </tr>
        <TR>
        	<TD ALIGN="RIGHT"><?php  echo $translator->trans("Currency"); ?>:</TD>
        	<TD>
        	<INPUT TYPE="TEXT" class="input_text" NAME="f_currency" VALUE="<?php if (isset($publicationObj)) { p(htmlspecialchars($publicationObj->getCurrency())); } ?>" SIZE="10" MAXLENGTH="10">
        	</TD>
        </TR>
        <tr>
        	<td colspan="2" align="left"><?php  echo $translator->trans("Time unit cost per one section", array(), 'pub'); ?>:</td>
        </tr>
        <TR>
        	<TD ALIGN="RIGHT">- <?php echo $translator->trans('one language', array(), 'pub'); ?>:</TD>
        	<TD>
        	<INPUT TYPE="TEXT" class="input_text" NAME="f_unit_cost" VALUE="<?php  if (isset($publicationObj)) { p($publicationObj->getUnitCost()); } ?>" SIZE="10" MAXLENGTH="10">
        	</TD>
        </TR>
        <TR>
        	<TD ALIGN="RIGHT">- <?php echo $translator->trans('all languages', array(), 'pub'); ?>:</TD>
        	<TD>
        	<INPUT TYPE="TEXT" class="input_text" NAME="f_unit_cost_all_lang" VALUE="<?php if (isset($publicationObj)) { p($publicationObj->getUnitCostAllLang()); } ?>" SIZE="10" MAXLENGTH="10">
        	</TD>
        </TR>
        <TR>
        	<TD ALIGN="RIGHT"><?php  echo $translator->trans("Default time period", array(), 'pub'); ?>:</TD>
        	<TD>
        	<INPUT TYPE="TEXT" class="input_text" NAME="f_paid" VALUE="<?php if (isset($publicationObj)) { p($publicationObj->getPaidTime()); } ?>" SIZE="10" MAXLENGTH="10"> <?php echo $translator->trans('time units', array(), 'pub'); ?>
        	</TD>
        </TR>
        <tr>
        	<td colspan="2" align="left" style="padding-top: 1em;"><b><?php echo $translator->trans('Trial subscriptions', array(), 'pub'); ?></b></td>
        </tr>
        <TR>
        	<TD ALIGN="RIGHT"><?php  echo $translator->trans("Default time period", array(), 'pub'); ?>:</TD>
        	<TD>
        	<INPUT TYPE="TEXT" class="input_text" NAME="f_trial" VALUE="<?php if (isset($publicationObj)) { p($publicationObj->getTrialTime()); } ?>" SIZE="10" MAXLENGTH="10"> <?php echo $translator->trans('time units', array(), 'pub'); ?>
        	</TD>
        </TR>

        <?php if (isset($publicationObj)) { ?>
        <tr>
            <td colspan="2" align="center" style="padding-top: 1em;">
                <a href="/<?php echo $ADMIN; ?>/pub/deftime.php?Pub=<?php echo $f_publication_id; ?>&Language=<?php p($publicationObj->getDefaultLanguageId()); ?>"><?php echo $translator->trans("Set subscription settings by country", array(), 'pub'); ?></a>
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
<?php if (isset($publicationObj)) { ?>
<tr>
	<td colspan="2">
        <!-- Old plugins hooks -->
        <?php CampPlugin::adminHook(__FILE__, array('publicationObj'=>$publicationObj)); ?>

        <!-- New plugins hooks -->
        <?php 
        echo \Zend_Registry::get('container')->getService('newscoop.plugins.service')
            ->renderPluginHooks('newscoop_admin.interface.publication.edit', null, array(
                'publication' => $publicationObj
            ));
        ?>
    </td>
</tr>
<?php } ?>
<TR>
	<TD COLSPAN="2" align="center" style="padding-left: 8px; padding-right: 8px;">
	   <table style="border-top: 1px solid black; padding-top: 7px; padding-bottom: 6px; margin-top: 10px;" width="100%">
	   <tr>
	       <td align="center">
        	<?php if (isset($publicationObj)) { ?>
        	<INPUT TYPE="HIDDEN" NAME="f_publication_id" VALUE="<?php  p($f_publication_id); ?>">
        	<?php } ?>
        	<INPUT TYPE="submit" class="button" NAME="Save" VALUE="<?php  echo $translator->trans('Save'); ?>">
        	</TD>
       </tr>
       </table>
   </td>
</TR>
</table>
