<?php
require_once($GLOBALS['g_campsiteDir'].'/classes/Article.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Image.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Issue.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Section.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Language.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Publication.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Log.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Input.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/ImageSearch.php');

$translator = \Zend_Registry::get('container')->getService('translator');

if (!SecurityToken::isValid()) {
    camp_html_display_error($translator->trans('Invalid security token!'));
    exit;
}

$f_language_selected = Input::Get('f_language_selected', 'int', 0);
$f_publication_id = Input::Get('f_publication_id', 'int', 0);
$f_issue_number = Input::Get('f_issue_number', 'int', 0);
$f_section_number = Input::Get('f_section_number', 'int', 0);
$f_article_number = Input::Get('f_article_number', 'int', 0);
$f_orig_image_template_id = Input::Get('f_orig_image_template_id', 'int', 0);
$f_image_id = Input::Get('f_image_id', 'int', 0);

$f_language_id = Input::Get('f_language_id', 'int', 0, true);
$f_image_template_id = Input::Get('f_image_template_id', 'int', null, true);
$f_image_description = trim(Input::Get('f_image_description', 'string', null, true));
$f_image_photographer = trim(Input::Get('f_image_photographer', 'string', null, true));
$f_image_place = trim(Input::Get('f_image_place', 'string', null, true));
$f_image_date = Input::Get('f_image_date', 'string', null, true);
$f_image_status = Input::Get('f_image_status', 'int', 0);

$backLink = "/$ADMIN/articles/images/edit.php?"
		. "f_publication_id=" . $f_publication_id
		. "&f_issue_number=" . $f_issue_number
		. "&f_section_number=" . $f_section_number
		. "&f_article_number=" . $f_article_number
		. "&f_image_id=" . $f_image_id
		. "&f_language_id=" . $f_language_id
		. "&f_language_selected=" . $f_language_selected
		. "&f_image_template_id=" . $f_orig_image_template_id;

if (!Input::IsValid()) {
	camp_html_display_error($translator->trans('Invalid input: $1', array('$1' => Input::GetErrorString())), null, true);
	exit;
}

$articleObj = new Article($f_language_selected, $f_article_number);

if (!$g_user->hasPermission('ChangeImage') && !$g_user->hasPermission('AttachImageToArticle')) {
	camp_html_add_msg($translator->trans("You do not have the right to change image information.", array(), 'article_images'));
	camp_html_goto_page(camp_html_article_url($articleObj, $f_language_id, 'edit.php'));
}

$imageObj = new Image($f_image_id);

if (!is_null($f_image_description) && $g_user->hasPermission('ChangeImage')) {
	$attributes = array();
	$attributes['Description'] = $f_image_description;
	$attributes['Photographer'] = $f_image_photographer;
	$attributes['Place'] = $f_image_place;
	$attributes['Date'] = $f_image_date;
    $attributes['Status'] = $f_image_status ? 'approved' : 'unapproved';
	$imageObj->update($attributes);
}

if ($g_user->hasPermission('AttachImageToArticle')) {
	if (is_numeric($f_image_template_id) && ($f_image_template_id > 0)) {
		$articleImageObj = new ArticleImage($f_article_number, $f_image_id);
		$updated = $articleImageObj->setTemplateId($f_image_template_id);
		if ($updated == false) {
			camp_html_add_msg($translator->trans("Image number $1 already exists", array('$1' => $f_image_template_id), 'article_images'));
			camp_html_goto_page($backLink);
		}
	}
}

camp_html_add_msg($translator->trans("Image '$1' updated.", array('$1' => $imageObj->getDescription()), 'article_images'), "ok");

?>

<script type="text/javascript">
	window.location.href='<?php echo "/$ADMIN/articles/edit.php?f_language_id=$f_language_id&f_article_number=$f_article_number&f_publication_id=$f_publication_id&f_issue_number=$f_issue_number&f_section_number=$f_section_number&f_language_selected=$f_language_selected"; ?>'
</script>
