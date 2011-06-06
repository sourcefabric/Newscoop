<?php
require_once($GLOBALS['g_campsiteDir']. "/$ADMIN_DIR/sections/section_common.php");
require_once($GLOBALS['g_campsiteDir']. '/classes/Template.php');
require_once($GLOBALS['g_campsiteDir']. '/classes/Article.php');

if (!$g_user->hasPermission('DeleteSection')) {
	camp_html_display_error(getGS('You do not have the right to delete sections.'));
	exit;
}
$f_publication_id = Input::Get('Pub', 'int', 0);
$f_issue_number = Input::Get('Issue', 'int', 0);
$f_language_id = Input::Get('Language', 'int', 0);
$f_section_number = Input::Get('Section', 'int', 0);

$numArticles = count(Article::GetArticles($f_publication_id, $f_issue_number, $f_section_number, $f_language_id));
$numSubscriptions = count(Subscription::GetSubscriptions($f_publication_id));
$publicationObj = new Publication($f_publication_id);
$issueObj = new Issue($f_publication_id, $f_language_id, $f_issue_number);
$sectionObj = new Section($f_publication_id, $f_issue_number, $f_language_id, $f_section_number);

$sectionTranslations = Section::GetSections($f_publication_id, $f_issue_number, null, null, $sectionObj->getName(), null);
$topArray = array('Pub' => $publicationObj, 'Issue' => $issueObj, 'Section' => $sectionObj);
camp_html_content_top(getGS('Delete section'), $topArray);

?>
<P>
<FORM METHOD="POST" ACTION="/<?php echo $ADMIN; ?>/sections/do_del.php">
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box">
<?php echo SecurityToken::FormParameter(); ?>
<TR>
	<TD COLSPAN="2">
		<B> <?php  putGS("Delete section"); ?> </B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD COLSPAN="2" align="center"><?php putGS('There are $1 articles in this section.', '<b>'.$numArticles.'</b>'); ?></TD>
</TR>
<TR>
	<TD COLSPAN="2" align="center">
                <INPUT TYPE="radio" NAME="f_delete_all_section_translations" class="input_checkbox" value="N" checked> <?php putGS('Delete only this section ($1)', $sectionObj->getLanguageName()); ?>
                <br/>
                <INPUT TYPE="radio" NAME="f_delete_all_section_translations" class="input_checkbox" value="Y"> <?php putGS('Delete all translations of this section'); ?>
                <br />
                <?php
                foreach ($sectionTranslations as $key => $sectionTranslation) {
                    echo $sectionTranslation->getLanguageName() . '<br />';
                }
                ?>
        </TD>
</TR>
<TR>
	<TD COLSPAN="2" align="center">
                <INPUT TYPE="radio" NAME="f_delete_all_articles_translations" class="input_checkbox" value="N" checked> <?php putGS('Delete all articles written in $1 language from this section', $sectionObj->getLanguageName()); ?>
                <br/>
                <INPUT TYPE="radio" NAME="f_delete_all_articles_translations" class="input_checkbox" value="Y"> <?php putGS('Delete all articles and all of their translations'); ?>
        </TD>
</TR>
<TR>
	<TD COLSPAN="2" align="center">
		<?php  putGS('There are $1 subscriptions which will be affected.','<B>'.$numSubscriptions.'</B>'); ?>
		<br>
		<INPUT TYPE="checkbox" checked NAME="f_delete_subscriptions" class="input_checkbox"> <?php  putGS("Delete section from all subscriptions."); ?>

	</TD>
</TR>
<TR>
	<TD COLSPAN="2" align="center"><?php  putGS('Are you sure you want to delete the section $1?','<B>'.htmlspecialchars($sectionObj->getName()).'</B>'); ?></TD>
</TR>
<TR>
	<TD COLSPAN="2" align="center">
	<INPUT TYPE="HIDDEN" NAME="f_publication_id" VALUE="<?php p($f_publication_id); ?>">
	<INPUT TYPE="HIDDEN" NAME="f_issue_number" VALUE="<?php p($f_issue_number); ?>">
	<INPUT TYPE="HIDDEN" NAME="f_section_number" VALUE="<?php p($f_section_number); ?>">
	<INPUT TYPE="HIDDEN" NAME="f_language_id" VALUE="<?php p($f_language_id); ?>">
	<INPUT TYPE="submit" class="button" NAME="Yes" VALUE="<?php  putGS('Yes'); ?>">
	&nbsp;&nbsp;&nbsp;&nbsp;
	<INPUT TYPE="button" class="button" NAME="No" VALUE="<?php  putGS('No'); ?>" ONCLICK="location.href='/<?php p($ADMIN);?>/sections/?Pub=<?php p($f_publication_id); ?>&Issue=<?php p($f_issue_number); ?>&Language=<?php  p($f_language_id); ?>'">
	</TD>
</TR>
</TABLE>
</FORM>
<P>

<?php camp_html_copyright_notice(); ?>
