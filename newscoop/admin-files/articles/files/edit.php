<?php
camp_load_translation_strings("article_files");
require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/articles/article_common.php");
require_once($GLOBALS['g_campsiteDir'].'/classes/Attachment.php');

$f_publication_id = Input::Get('f_publication_id', 'int', 0, true);
$f_issue_number = Input::Get('f_issue_number', 'int', 0, true);
$f_section_number = Input::Get('f_section_number', 'int', 0, true);
$f_language_id = Input::Get('f_language_id', 'int', 0, true);

$f_language_selected = Input::Get('f_language_selected', 'int', 0);
$f_article_number = Input::Get('f_article_number', 'int', 0);
$f_attachment_id = Input::Get('f_attachment_id', 'int', 0);

if (!Input::IsValid()) {
    camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), $_SERVER['REQUEST_URI'], true);
    exit;
}

if ($f_publication_id > 0) {
    $publicationObj = new Publication($f_publication_id);
    $issueObj = new Issue($f_publication_id, $f_language_id, $f_issue_number);
    $sectionObj = new Section($f_publication_id, $f_issue_number, $f_language_id, $f_section_number);
}
$articleObj = new Article($f_language_selected, $f_article_number);
$attachmentObj = new Attachment($f_attachment_id);

if (!$articleObj->exists()) {
    camp_html_display_error(getGS("Article does not exist."), null, true);
    exit;
}

$isDisabled = '';
$isReadOnly = '';
if (!$g_user->hasPermission('ChangeFile')) {
    $isDisabled = 'disabled';
    $isReadOnly = 'readonly';
    $title = getGS('File information');
} else {
    $title = getGS('Change file information');
}
// Add extra breadcrumb for image list.
if ($f_publication_id > 0) {
    $extraCrumbs = array(getGS("Attachments") => "");
    $topArray = array('Pub' => $publicationObj, 'Issue' => $issueObj,
                      'Section' => $sectionObj, 'Article'=>$articleObj);
    camp_html_content_top($title, $topArray, true, true, $extraCrumbs);
} else {
    $crumbs = array();
    $crumbs[] = array(getGS("Actions"), "");
    $crumbs[] = array(getGS("Edit article"), camp_html_article_url($articleObj, $f_language_id, "edit.php"));
    $crumbs[] = array(getGS("Attachments"), "");
    $crumbs[] = array($title, "");
    echo camp_html_breadcrumbs($crumbs);
}
?>
<table cellpadding="1" cellspacing="0" class="action_buttons" style="padding-top: 10px;">
<tr>
    <td><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/left_arrow.png" BORDER="0"></td>
    <td><a href="<?php echo camp_html_article_url($articleObj, $f_language_id, "edit.php"); ?>"><b><?php putGS("Back to Edit Article"); ?></b></a></td>
</table>
<P>
<?php if (strstr($attachmentObj->getMimeType(), "image/") &&
                (strstr($_SERVER['HTTP_ACCEPT'], $attachmentObj->getMimeType()) ||
                (strstr($_SERVER['HTTP_ACCEPT'], "*/*")))) { ?>
<div class="indent">
<IMG SRC="<?php echo $attachmentObj->getAttachmentUrl(); ?>" BORDER="0" ALT="<?php echo htmlspecialchars($attachmentObj->getDescription($f_language_selected)); ?>">
</div>
<P>
<?php } ?>
<FORM NAME="dialog" METHOD="POST" ACTION="/<?php echo $ADMIN; ?>/articles/files/do_edit.php" >
<?php echo SecurityToken::FormParameter(); ?>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" class="box_table">
<TR>
    <TD COLSPAN="2">
        <B><?php  p($title); ?></B>
        <HR NOSHADE SIZE="1" COLOR="BLACK">
    </TD>
</TR>
<TR>
    <TD ALIGN="RIGHT"><?php putGS('File Name'); ?>:</TD>
    <TD><?php echo htmlspecialchars($attachmentObj->getFileName()); ?> &nbsp; <A
        HREF="<?php p($attachmentObj->getAttachmentUrl()); ?>"><IMG
        TITLE="<?php putGS('Download'); ?>" BORDER="0" ALIGN="absmiddle" SRC="<?php p($Campsite["ADMIN_IMAGE_BASE_URL"]);?>/download.png" /></A></TD>
</TR>
<TR>
    <TD ALIGN="RIGHT"><?php  putGS('Description'); ?>:</TD>
    <TD>
    <INPUT TYPE="TEXT" NAME="f_description" VALUE="<?php echo htmlspecialchars($attachmentObj->getDescription($f_language_selected)); ?>" class="input_text" SIZE="32" <?php p($isReadOnly); ?>>
    </TD>
</TR>
<TR>
    <TD ALIGN="RIGHT"><?php putGS('File Size'); ?>:</TD>
    <TD><?php p(camp_format_bytes($attachmentObj->getSizeInBytes())); ?></TD>
</TR>
<TR>
    <TD ALIGN="left" colspan="2" style="padding-left: 15px;"><?php  putGS("Should this file only be available for this translation of the article, or for all translations?"); ?></TD>
</TR>
<TR>
    <TD colspan="2" class="indent"  style="padding-left: 30px;">
    <INPUT type="radio" name="f_language_specific" value="yes" <?php if ($attachmentObj->getLanguageId()) { ?> checked<?php } ?> <?php p($isDisabled); ?>><?php putGS("Only this translation"); ?><br>
    <INPUT type="radio" name="f_language_specific" value="no" <?php if (!$attachmentObj->getLanguageId()) { ?> checked<?php } ?>  <?php p($isDisabled); ?>><?php putGS("All translations"); ?>
    </TD>
</TR>
<TR>
    <TD ALIGN="left" colspan="2"  style="padding-left: 15px;"><?php  putGS("Do you want this file to open in the user's browser, or to automatically download?"); ?></TD>
</TR>
<TR>
    <TD colspan="2" style="padding-left: 30px;">
    <INPUT type="radio" name="f_content_disposition" value="" <?php if (!$attachmentObj->getContentDisposition()) { ?> checked<?php } ?> <?php p($isDisabled); ?>><?php putGS("Open in the browser"); ?><br>
    <INPUT type="radio" name="f_content_disposition" value="attachment" <?php if ($attachmentObj->getContentDisposition()) { ?> checked<?php } ?> <?php p($isDisabled); ?>><?php putGS("Automatically download"); ?>
    </TD>
</TR>
<?php if ($g_user->hasPermission('ChangeFile')) { ?>
<TR>
    <TD COLSPAN="2">
    <DIV ALIGN="CENTER">
    <INPUT TYPE="HIDDEN" NAME="f_publication_id" VALUE="<?php  p($f_publication_id); ?>">
    <INPUT TYPE="HIDDEN" NAME="f_issue_number" VALUE="<?php  p($f_issue_number); ?>">
    <INPUT TYPE="HIDDEN" NAME="f_section_number" VALUE="<?php  p($f_section_number); ?>">
    <INPUT TYPE="HIDDEN" NAME="f_article_number" VALUE="<?php  p($f_article_number); ?>">
    <INPUT TYPE="HIDDEN" NAME="f_language_id" VALUE="<?php  p($f_language_id); ?>">
    <INPUT TYPE="HIDDEN" NAME="f_language_selected" VALUE="<?php  p($f_language_selected); ?>">
    <INPUT TYPE="HIDDEN" NAME="f_attachment_id" VALUE="<?php  p($f_attachment_id); ?>">
    <INPUT TYPE="submit" NAME="Save" VALUE="<?php  putGS('Save'); ?>" class="button">
    </DIV>
    </TD>
</TR>
<?php } ?>
</TABLE>
</FORM>
<P>
<?php

camp_html_copyright_notice(); ?>
