<?php
require_once($_SERVER['DOCUMENT_ROOT']."/classes/common.php");
load_common_include_files("$ADMIN_DIR/articles/images");
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/articles/article_common.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/ArticleImage.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Image.php");

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}
if (!$User->hasPermission("AddImage")) {
	camp_html_display_error(getGS("You do not have the right to add images" ));
	exit;
}
$maxId = Image::GetMaxId();
$Pub = Input::Get('Pub', 'int', 0);
$Issue = Input::Get('Issue', 'int', 0);
$Section = Input::Get('Section', 'int', 0);
$Language = Input::Get('Language', 'int', 0);
$sLanguage = Input::Get('sLanguage', 'int', 0);
$Article = Input::Get('Article', 'int', 0);

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), $_SERVER['REQUEST_URI']);
	exit;	
}

$articleObj =& new Article($Pub, $Issue, $Section, $sLanguage, $Article);
$publicationObj =& new Publication($Pub);
$issueObj =& new Issue($Pub, $Language, $Issue);
$sectionObj =& new Section($Pub, $Issue, $Language, $Section);

$ImageTemplateId = ArticleImage::GetUnusedTemplateId($Article);

query ("SELECT LEFT(NOW(), 10)", 'q_now');
fetchRowNum($q_now);

// Add extra breadcrumb for image list.
$extraCrumbs = array(getGS("Images")=>"/$ADMIN/articles/images/?Pub=$Pub&Issue=$Issue&Language=$Language&Section=$Section&Article=$Article&sLanguage=$sLanguage");
$topArray = array('Pub' => $publicationObj, 'Issue' => $issueObj, 
				  'Section' => $sectionObj, 'Article'=>$articleObj);
camp_html_content_top(getGS("Add new image"), $topArray, true, true, $extraCrumbs);

?>
<script>
function checkAddForm(form) {
	retval = ((form.cURL.value != '') || (form.cImage.value != ''));
	retval = retval && validateForm(form, 0, 0, 0, 1, 8);
	return retval;
} // fn checkAddForm
</script>

<P>
<FORM NAME="dialog" METHOD="POST" ACTION="/<?php echo $ADMIN; ?>/articles/images/do_add.php" ENCTYPE="multipart/form-data" onsubmit="return checkAddForm(this);">
<CENTER>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" ALIGN="CENTER" class="table_input">
<TR>
	<TD COLSPAN="2">
		<B><?php  putGS("Add new image"); ?></B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Number"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" NAME="cNumber" VALUE="<?php p($ImageTemplateId); ?>" SIZE="5" MAXLENGTH="5" class="input_text" alt="number|0" emsg="<?php putGS('Please enter a number for the image.'); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Description"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" NAME="cDescription" VALUE="Image <?php  p($maxId); ?>" SIZE="32" MAXLENGTH="128" class="input_text" alt="blank" emsg="<?php putGS("Please enter a description for the image."); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Photographer"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" NAME="cPhotographer" SIZE="32" MAXLENGTH="64" VALUE="<?php echo $User->getName(); ?>" class="input_text">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Place"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" NAME="cPlace" SIZE="32" MAXLENGTH="64" class="input_text">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Date"); ?>:</TD>
	<TD>
		<INPUT TYPE="TEXT" NAME="cDate" VALUE="<?php  pgetNumVar($q_now,0); ?>" class="input_text" SIZE="11" MAXLENGTH="10"> <?php  putGS('YYYY-MM-DD'); ?>
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php putGS("URL"); ?>:</TD>
	<TD>
		<INPUT TYPE="TEXT" NAME="cURL" VALUE="" class="input_text" SIZE="32"> 
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php putGS("Image"); ?>:</TD>
	<TD>
		<INPUT TYPE="FILE" NAME="cImage" SIZE="32" MAXLENGTH="64" class="input_file" alt="file|jpg,jpeg,jpe,gif,png,tif,tiff|bok" emsg="<?php putGS("You must select an image file to upload."); ?>">
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
	<DIV ALIGN="CENTER">
    <INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<?php  p($Pub); ?>">
    <INPUT TYPE="HIDDEN" NAME="Issue" VALUE="<?php  p($Issue); ?>">
    <INPUT TYPE="HIDDEN" NAME="Section" VALUE="<?php  p($Section); ?>">
    <INPUT TYPE="HIDDEN" NAME="Article" VALUE="<?php  p($Article); ?>">
    <INPUT TYPE="HIDDEN" NAME="Language" VALUE="<?php  p($Language); ?>">
    <INPUT TYPE="HIDDEN" NAME="sLanguage" VALUE="<?php  p($sLanguage); ?>">
	<INPUT TYPE="submit" NAME="Save" VALUE="<?php  putGS('Save changes'); ?>" class="button">
	<INPUT TYPE="button" NAME="Cancel" VALUE="<?php  putGS('Cancel'); ?>"  class="button" ONCLICK="location.href='/<?php echo $ADMIN; ?>/articles/images/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>&Section=<?php  p($Section); ?>'">
	</DIV>
	</TD>
</TR>
</TABLE></CENTER>
</FORM>
<P>

<?php camp_html_copyright_notice(); ?>