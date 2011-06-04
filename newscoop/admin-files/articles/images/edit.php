<?php
camp_load_translation_strings("article_images");
require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/articles/article_common.php");
require_once($GLOBALS['g_campsiteDir'].'/classes/Image.php');

$uri = $_SERVER['REQUEST_URI'];

$f_publication_id = Input::Get('f_publication_id', 'int', 0, true);
$f_issue_number = Input::Get('f_issue_number', 'int', 0, true);
$f_section_number = Input::Get('f_section_number', 'int', 0, true);
$f_language_id = Input::Get('f_language_id', 'int', 0, true);

$f_language_selected = Input::Get('f_language_selected', 'int', 0);
$f_article_number = Input::Get('f_article_number', 'int', 0);
$f_image_id = Input::Get('f_image_id', 'int', 0);
$f_image_template_id = Input::Get('f_image_template_id', 'int', 0, true);

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
$imageObj = new Image($f_image_id);

if (!$g_user->hasPermission('ChangeImage')) {
	$title = getGS('Image information');
} else {
	$title = getGS('Change image information');
}

// Add extra breadcrumb for image list.
if ($f_publication_id > 0) {
	$extraCrumbs = array(getGS("Images") => "");
	$topArray = array('Pub' => $publicationObj, 'Issue' => $issueObj,
					  'Section' => $sectionObj, 'Article'=>$articleObj);
	camp_html_content_top($title, $topArray, true, true, $extraCrumbs);
} else {
	$crumbs = array();
	$crumbs[] = array(getGS("Actions"), "");
	$crumbs[] = array(getGS("Edit article"), camp_html_article_url($articleObj, $f_language_id, "edit.php"));
	$crumbs[] = array(getGS("Images"), "");
	$crumbs[] = array($title, "");
	echo camp_html_breadcrumbs($crumbs);
}
?>
<table cellpadding="1" cellspacing="0" class="action_buttons" style="padding-top: 10px;">
<tr>
	<td><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/left_arrow.png" BORDER="0"></td>
	<td><a href="<?php echo camp_html_article_url($articleObj, $f_language_id, "edit.php"); ?>"><b><?php putGS("Back to Edit Article"); ?></b></a></td>
</table>

<?php camp_html_display_msgs("0.25em", "0.25em"); ?>

<P>
<div class="indent">
<IMG SRC="<?php echo $imageObj->getImageUrl(); ?>" BORDER="0" ALT="<?php echo htmlspecialchars($imageObj->getDescription()); ?>">
</div>
<p>
<FORM NAME="dialog" METHOD="POST" ACTION="/<?php echo $ADMIN; ?>/articles/images/do_edit.php" >
<?php echo SecurityToken::FormParameter(); ?>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" class="box_table">
<TR>
	<TD COLSPAN="2">
		<B><?php  p($title); ?></B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS('Number'); ?>:</TD>
	<TD>
		<?php if ($g_user->hasPermission('AttachImageToArticle')) { ?>
		<INPUT TYPE="TEXT" NAME="f_image_template_id" VALUE="<?php echo $f_image_template_id; ?>" class="input_text" SIZE="32" MAXLENGTH="10">
		<?php } else {
			echo $f_image_template_id;
		} ?>
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS('Description'); ?>:</TD>
	<TD>
		<?php if ($g_user->hasPermission('ChangeImage')) { ?>
		<INPUT TYPE="TEXT" NAME="f_image_description" VALUE="<?php echo htmlspecialchars($imageObj->getDescription()); ?>" class="input_text" SIZE="32">
		<?php } else {
			echo htmlspecialchars($imageObj->getDescription());
		} ?>
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS('Photographer'); ?>:</TD>
	<TD>
		<?php if ($g_user->hasPermission('ChangeImage')) { ?>
		<INPUT TYPE="TEXT" NAME="f_image_photographer" VALUE="<?php echo htmlspecialchars($imageObj->getPhotographer());?>" class="input_text" SIZE="32">
		<?php } else {
			echo htmlspecialchars($imageObj->getPhotographer());
		} ?>
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS('Place'); ?>:</TD>
	<TD>
		<?php if ($g_user->hasPermission('ChangeImage')) { ?>
		<INPUT TYPE="TEXT" NAME="f_image_place" VALUE="<?php echo htmlspecialchars($imageObj->getPlace()); ?>" class="input_text" SIZE="32">
		<?php } else {
			echo htmlspecialchars($imageObj->getPlace());
		} ?>
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS('Date'); ?>:</TD>
	<TD>
		<?php if ($g_user->hasPermission('ChangeImage')) { ?>
		<input type="text" name="f_image_date" value="<?php echo htmlspecialchars($imageObj->getDate()); ?>" class="input_text date" size="11" maxlength="10" />
		<?php } else {
			echo htmlspecialchars($imageObj->getDate());
		} ?>
		<?php putGS('YYYY-MM-DD'); ?>
	</TD>
</TR>
<?php if ($g_user->hasPermission('ChangeImage') || $g_user->hasPermission('AttachImageToArticle')) { ?>
<TR>
	<TD COLSPAN="2">
	<DIV ALIGN="CENTER">
    <INPUT TYPE="HIDDEN" NAME="f_publication_id" VALUE="<?php  p($f_publication_id); ?>">
    <INPUT TYPE="HIDDEN" NAME="f_issue_number" VALUE="<?php  p($f_issue_number); ?>">
    <INPUT TYPE="HIDDEN" NAME="f_section_number" VALUE="<?php  p($f_section_number); ?>">
    <INPUT TYPE="HIDDEN" NAME="f_article_number" VALUE="<?php  p($f_article_number); ?>">
    <INPUT TYPE="HIDDEN" NAME="f_language_id" VALUE="<?php  p($f_language_id); ?>">
    <INPUT TYPE="HIDDEN" NAME="f_language_selected" VALUE="<?php  p($f_language_selected); ?>">
    <INPUT TYPE="HIDDEN" NAME="f_image_id" VALUE="<?php  p($f_image_id); ?>">
    <INPUT TYPE="HIDDEN" NAME="f_orig_image_template_id" VALUE="<?php p($f_image_template_id); ?>">
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
