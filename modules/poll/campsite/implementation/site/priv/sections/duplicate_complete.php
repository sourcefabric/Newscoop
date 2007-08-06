<?php

require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/sections/section_common.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Article.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Log.php');

if (!$g_user->hasPermission("ManageSection")) {
    camp_html_display_error(getGS("You do not have the right to add sections."));
    exit;
}
if (!$g_user->hasPermission("AddArticle")) {
    camp_html_display_error(getGS("You do not have the right to add articles."));
    exit;
}

$f_src_publication_id = Input::Get('f_src_publication_id', 'int', 0);
$f_src_issue_number = Input::Get('f_src_issue_number', 'int', 0);
$f_src_section_number = Input::Get('f_src_section_number', 'int', 0);
$f_language_id = Input::Get('f_language_id', 'int', 0);
$f_dest_publication_id = Input::Get('f_dest_publication_id', 'int', 0);
$f_dest_issue_number = Input::Get('f_dest_issue_number', 'int', 0);
$f_dest_section_number = Input::Get('f_dest_section_number', 'int', 0);

if (!Input::IsValid()) {
       camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()));
    exit;
}

$srcPublicationObj =& new Publication($f_src_publication_id);
if (!$srcPublicationObj->exists()) {
    camp_html_display_error(getGS('Publication does not exist.'));
    exit;
}

$srcIssueObj =& new Issue($f_src_publication_id, $f_language_id, $f_src_issue_number);
if (!$srcIssueObj->exists()) {
    camp_html_display_error(getGS('Issue does not exist.'));
    exit;
}

$srcSectionObj =& new Section($f_src_publication_id, $f_src_issue_number, $f_language_id, $f_src_section_number);
if (!$srcSectionObj->exists()) {
    camp_html_display_error(getGS('Section does not exist.'));
    exit;
}

$dstPublicationObj =& new Publication($f_dest_publication_id);
$dstIssueObj =& new Issue($f_dest_publication_id, $f_language_id, $f_dest_issue_number);
$dstSectionObj =& new Section($f_dest_publication_id, $f_dest_issue_number, $f_language_id, $f_dest_section_number);

$topArray = array('Pub' => $srcPublicationObj, 'Issue' => $srcIssueObj, 'Section' => $srcSectionObj);
camp_html_content_top(getGS('Duplicating section'), $topArray);

?>
<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box">
<TR>
    <TD COLSPAN="2">
        <B> <?php  putGS("Duplicating section"); ?> </B>
        <HR NOSHADE SIZE="1" COLOR="BLACK">
    </TD>
</TR>
<TR>
    <TD COLSPAN="2">
        <BLOCKQUOTE>
       <?php  putGS('Section $1 has been duplicated to $2. $3 of $4', '<B>'.$srcSectionObj->getName().'</B>', '<B>'.$dstSectionObj->getIssueNumber().'</B>', '<B>'.$dstIssueObj->getName().' ('.$dstIssueObj->getLanguageName().')</B>', '<B>'.$dstPublicationObj->getName().'</B>'); ?>
        </BLOCKQUOTE>
    </TD>
</TR>
<TR>
    <TD COLSPAN="2">
        <DIV ALIGN="CENTER">
            <table>
            <tr>
                <td>
                   <b><a href="<?php echo "/$ADMIN/sections/edit.php?Pub=$f_dest_publication_id&Issue=$f_dest_issue_number&Section=$f_dest_section_number&Language=$f_language_id"; ?>"><?php putGS("Go to new section"); ?></a></b>
                </td>
                <td style="padding-left: 50px;">
                   <b><a href="<?php echo "/$ADMIN/sections/edit.php?Pub=$f_src_publication_id&Issue=$f_src_issue_number&Section=$f_src_section_number&Language=$f_language_id"; ?>"><?php putGS("Go to source section"); ?></a></b>
                </td>
            </tr>
            </table>
        </DIV>
    </TD>
</TR>
</TABLE>
<P>

<?php camp_html_copyright_notice(); ?>