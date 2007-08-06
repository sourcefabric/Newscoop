<?php
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/sections/section_common.php");
require_once($_SERVER['DOCUMENT_ROOT']. '/classes/Template.php');
require_once($_SERVER['DOCUMENT_ROOT']. '/classes/Article.php');

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
$publicationObj =& new Publication($f_publication_id);
$issueObj =& new Issue($f_publication_id, $f_language_id, $f_issue_number);
$sectionObj =& new Section($f_publication_id, $f_issue_number, $f_language_id, $f_section_number);

$topArray = array('Pub' => $publicationObj, 'Issue' => $issueObj, 'Section' => $sectionObj);
camp_html_content_top(getGS('Delete section'), $topArray);

?>
<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box">
<TR>
    <TD COLSPAN="2">
        <B> <?php  putGS("Delete section"); ?> </B>
        <HR NOSHADE SIZE="1" COLOR="BLACK">
    </TD>
</TR>
    <FORM METHOD="POST" ACTION="do_del.php">
<TR>
    <TD COLSPAN="2" align="center"><?php putGS('There are $1 articles in this section.', '<b>'.$numArticles.'</b>'); ?></TD>
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
    </FORM>
    </TD>
</TR>
</TABLE>
<P>

<?php camp_html_copyright_notice(); ?>