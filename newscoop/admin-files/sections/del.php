<?php
require_once($GLOBALS['g_campsiteDir']. "/$ADMIN_DIR/sections/section_common.php");
require_once($GLOBALS['g_campsiteDir']. '/classes/Article.php');

$translator = \Zend_Registry::get('container')->getService('translator');

if (!$g_user->hasPermission('DeleteSection')) {
	camp_html_display_error($translator->trans('You do not have the right to delete sections.', array(), 'sections'));
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
camp_html_content_top($translator->trans('Delete section', array(), 'sections'), $topArray);

?>
<P>
<FORM METHOD="POST" ACTION="/<?php echo $ADMIN; ?>/sections/do_del.php">
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box">
<?php echo SecurityToken::FormParameter(); ?>
<TR>
	<TD COLSPAN="2" align="center"><?php echo $translator->trans('There are $1 articles in this section.', array('$1' => '<b>'.$numArticles.'</b>'), 'sections'); ?></TD>
</TR>
<TR>
	<TD COLSPAN="2" align="center">
                <INPUT TYPE="radio" NAME="f_delete_all_section_translations" class="input_checkbox" value="N" checked> <?php echo $translator->trans('Delete only this section ($1)', array('$1' => $sectionObj->getLanguageName()), 'sections'); ?>
                <br/>
                <INPUT TYPE="radio" NAME="f_delete_all_section_translations" class="input_checkbox" value="Y"> <?php echo $translator->trans('Delete all translations of this section', array(), 'sections'); ?>
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
                <INPUT TYPE="radio" NAME="f_delete_all_articles_translations" class="input_checkbox" value="N" checked> <?php echo $translator->trans('Delete all articles written in $1 language from this section', array('$1' => $sectionObj->getLanguageName()), 'sections'); ?>
                <br/>
                <INPUT TYPE="radio" NAME="f_delete_all_articles_translations" class="input_checkbox" value="Y"> <?php echo $translator->trans('Delete all articles and all of their translations', array(), 'sections'); ?>
        </TD>
</TR>
<TR>
	<TD COLSPAN="2" align="center">
		<?php  echo $translator->trans('There are $1 subscriptions which will be affected.', array('$1' => '<B>'.$numSubscriptions.'</B>'), 'sections'); ?>
		<br>
		<INPUT TYPE="checkbox" checked NAME="f_delete_subscriptions" class="input_checkbox"> <?php  echo $translator->trans("Delete section from all subscriptions.", array(), 'sections'); ?>

	</TD>
</TR>
<TR>
	<TD COLSPAN="2" align="center"><?php  echo $translator->trans('Are you sure you want to delete the section $1?', array('$1' => '<B>'.htmlspecialchars($sectionObj->getName()).'</B>'), 'sections'); ?></TD>
</TR>
<TR>
	<TD COLSPAN="2" align="center">
	<INPUT TYPE="HIDDEN" NAME="f_publication_id" VALUE="<?php p($f_publication_id); ?>">
	<INPUT TYPE="HIDDEN" NAME="f_issue_number" VALUE="<?php p($f_issue_number); ?>">
	<INPUT TYPE="HIDDEN" NAME="f_section_number" VALUE="<?php p($f_section_number); ?>">
	<INPUT TYPE="HIDDEN" NAME="f_language_id" VALUE="<?php p($f_language_id); ?>">
	<INPUT TYPE="submit" class="button" NAME="Yes" VALUE="<?php  echo $translator->trans('Yes'); ?>">
	&nbsp;&nbsp;&nbsp;&nbsp;
	<INPUT TYPE="button" class="button" NAME="No" VALUE="<?php  echo $translator->trans('No'); ?>" ONCLICK="location.href='/<?php p($ADMIN);?>/sections/?Pub=<?php p($f_publication_id); ?>&Issue=<?php p($f_issue_number); ?>&Language=<?php  p($f_language_id); ?>'">
	</TD>
</TR>
</TABLE>
</FORM>
<P>

<?php camp_html_copyright_notice(); ?>
