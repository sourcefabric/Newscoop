<?PHP
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/common.php');
load_common_include_files("article_images");
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/articles/article_common.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Image.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/ImageSearch.php');
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/imagearchive/include.inc.php");

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
$f_image_attach_mode = camp_session_get('f_image_attach_mode', 'existing');

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
	<title><?php putGS("Attach Image To Article"); ?></title>
</head>
<body>
<table style="margin-top: 10px; margin-left: 5px;" cellpadding="0" cellspacing="0">
<tr>
	<td style="padding: 3px; background-color: #EEE; border-top: 1px solid #8baed1; border-right: 1px solid #8baed1; border-left: 1px solid #8baed1; <?php if ($f_image_attach_mode != "new") { ?>border-bottom: 1px solid #8baed1;<?php } ?>"><a href="<?php echo camp_html_article_url($articleObj, $f_language_selected, "images/popup.php", "", "&f_image_attach_mode=new"); ?>"><img src="<?php p($Campsite['ADMIN_IMAGE_BASE_URL']); ?>/add.png" border="0"><b><?php putGS("Attach New Image"); ?></b></a></td>
	<td style="padding: 3px; background-color: #EEE; border-top: 1px solid #8baed1; border-right: 1px solid #8baed1; <?php if ($f_image_attach_mode != "existing") { ?>border-bottom: 1px solid #8baed1;<?php } ?>"><a href="<?php echo camp_html_article_url($articleObj, $f_language_selected, "images/popup.php", "", "&f_image_attach_mode=existing"); ?>"><img src="<?php p($Campsite['ADMIN_IMAGE_BASE_URL']); ?>/add.png" border="0"><b><?php putGS("Attach Existing Image"); ?></b></a></td>
</tr>
<tr>
	<td colspan="2" style="background-color: #EEE; padding-top: 5px; border-bottom: 1px solid #8baed1; border-right: 1px solid #8baed1; border-left: 1px solid #8baed1;">	
		<?php 
		if ($f_image_attach_mode == "existing") {
			include("search.php"); 
		} else {
			include("add.php");
		}?>
	</td>
</tr>
</table>

</body>
</html>