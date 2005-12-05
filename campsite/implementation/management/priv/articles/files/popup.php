<?PHP
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/common.php');
load_common_include_files("article_files");
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/articles/article_common.php");

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

$f_publication_id = Input::Get('f_publication_id', 'int', 0);
$f_issue_number = Input::Get('f_issue_number', 'int', 0);
$f_section_number = Input::Get('f_section_number', 'int', 0);
$f_language_id = Input::Get('f_language_id', 'int', 0);
$f_language_selected = Input::Get('f_language_selected', 'int', 0);
$f_article_number = Input::Get('f_article_number', 'int', 0);

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), $_SERVER['REQUEST_URI']);
	exit;	
}

$publicationObj =& new Publication($f_publication_id);
$issueObj =& new Issue($f_publication_id, $f_language_id, $f_issue_number);
$sectionObj =& new Section($f_publication_id, $f_issue_number, $f_language_id, $f_section_number);
$articleObj =& new Article($f_language_selected, $f_article_number);
?>
<html>
<head>
    <META http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<META HTTP-EQUIV="Expires" CONTENT="now">
	<LINK rel="stylesheet" type="text/css" href="<?php echo $Campsite['WEBSITE_URL']; ?>/css/admin_stylesheet.css">
	<title><?php putGS("Attach File to Article"); ?></title>
</head>
<body>

<br>
<FORM NAME="dialog" METHOD="POST" ACTION="/<?php echo $ADMIN; ?>/articles/files/do_add.php" ENCTYPE="multipart/form-data" onsubmit="return validateForm(form, 0, 0, 0, 1, 8);">
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" class="table_input">
<TR>
	<TD COLSPAN="2">
		<B><?php  putGS("Attach File to Article"); ?></B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php putGS("File"); ?>:</TD>
	<TD>
		<INPUT TYPE="FILE" NAME="f_file" SIZE="32" MAXLENGTH="64" class="input_file" alt="file" emsg="<?php putGS("You must select a file to upload."); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Description"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" NAME="f_description" VALUE="" SIZE="32" MAXLENGTH="128" class="input_text" alt="blank" emsg="<?php putGS("Please enter a description for the file."); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="left" colspan="2" style="padding-left: 15px;"><?php  putGS("Should this file only be available for this translation of the article, or for all translations?"); ?></TD>
</TR>
<TR>
	<TD colspan="2" class="indent"  style="padding-left: 30px;">
	<INPUT type="radio" name="f_language_specific" value="yes"><?php putGS("Only this translation"); ?><br>
	<INPUT type="radio" name="f_language_specific" value="no" checked><?php putGS("All translations"); ?>
	</TD>
</TR>
<TR>
	<TD ALIGN="left" colspan="2"  style="padding-left: 15px;"><?php  putGS("Do you want this file to open in the user's browser, or to automatically download?"); ?></TD>
</TR>
<TR>
	<TD colspan="2" style="padding-left: 30px;">
	<INPUT type="radio" name="f_content_disposition" value=""><?php putGS("Open in the browser"); ?><br>
	<INPUT type="radio" name="f_content_disposition" value="attachment" checked><?php putGS("Automatically download"); ?>
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
	<DIV ALIGN="CENTER">
    <INPUT TYPE="HIDDEN" NAME="f_publication_id" VALUE="<?php  p($f_publication_id); ?>">
    <INPUT TYPE="HIDDEN" NAME="f_issue_number" VALUE="<?php  p($f_issue_number); ?>">
    <INPUT TYPE="HIDDEN" NAME="f_section_number" VALUE="<?php  p($f_section_number); ?>">
    <INPUT TYPE="HIDDEN" NAME="f_article_number" VALUE="<?php  p($f_article_number); ?>">
    <INPUT TYPE="HIDDEN" NAME="f_language_id" VALUE="<?php  p($f_language_id); ?>">
    <INPUT TYPE="HIDDEN" NAME="f_language_selected" VALUE="<?php  p($f_language_selected); ?>">
    <INPUT TYPE="HIDDEN" NAME="BackLink" VALUE="<?php  p($_SERVER['REQUEST_URI']); ?>">
	<INPUT TYPE="submit" NAME="Save" VALUE="<?php  putGS('Save'); ?>" class="button">
	&nbsp;&nbsp;
	<INPUT TYPE="button" NAME="Cancel" VALUE="<?php  putGS('Cancel'); ?>" class="button" onclick="window.close();">
	</DIV>
	</TD>
</TR>
</TABLE>
</FORM>
<P>	
</body>
</html>