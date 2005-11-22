<?php  
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/common.php');
load_common_include_files("article_images");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Article.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Image.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Issue.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Section.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Language.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Publication.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Log.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Input.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/ImageSearch.php');
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/camp_html.php");

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

$f_publcation_id = Input::Get('f_publication_id', 'int', 0);
$f_issue_number = Input::Get('f_issue_number', 'int', 0);
$f_section_number = Input::Get('f_section_number', 'int', 0);
$f_language_id = Input::Get('f_language_id', 'int', 0);
$f_language_selected = Input::Get('f_language_selected', 'int', 0);
$f_article_number = Input::Get('f_article_number', 'int', 0);
$f_image_id = Input::Get('f_image_id', 'int', 0);
$f_image_template_id = Input::Get('f_image_template_id', 'int', 0, true);
$f_image_description = trim(Input::Get('f_image_description', 'string', '', true));
$f_image_photographer = trim(Input::Get('f_image_photographer', 'string', '', true));
$f_image_place = trim(Input::Get('f_image_place', 'string', '', true));
$f_image_date = Input::Get('f_image_date', 'string', '', true);

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()));
	exit;	
}

$articleObj =& new Article($f_language_selected, $f_article_number);
$publicationObj =& new Publication($f_publication_id);
$issueObj =& new Issue($f_publication_id, $f_language_id, $f_issue_number);
$sectionObj =& new Section($f_publcation_id, $f_issue_number, $f_language_id, $f_section_number);

// This file can only be accessed if the user has the right to change articles
// or the user created this article and it hasnt been published yet.
if (!$articleObj->userCanModify($User)) {	
	camp_html_display_error(getGS('You do not have the right to change the article.'));
	exit;		
}

$imageObj =& new Image($f_image_id);
$attributes = array();
$attributes['Description'] = $f_image_description;
$attributes['Photographer'] = $f_image_photographer;
$attributes['Place'] = $f_image_place;
$attributes['Date'] = $f_image_date;
$view = Input::Get('view', 'string', 'thumbnail', true);
$imageObj->update($attributes);
if (is_numeric($f_image_template_id) && ($f_image_template_id > 0)) {
	ArticleImage::SetTemplateId($f_article_number, $f_image_id, $f_image_template_id);
}

$logtext = getGS('Changed image properties of $1',$attributes['Description']); 
Log::Message($logtext, $User->getUserName(), 43);

$ref = camp_html_article_url($articleObj, $f_language_selected, 'edit.php');

// Go back to article image list.
header("Location: $ref");

?>