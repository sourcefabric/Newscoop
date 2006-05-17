<?php
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/articles/article_common.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Log.php');

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

if (!$User->hasPermission("AddArticle")) {
	$errorStr = getGS('You do not have the right to change this article.  You may only edit your own articles and once submitted an article can only be changed by authorized users.');
	camp_html_display_error($errorStr);
	exit;
}

// Optional input.
$f_language_id = Input::Get('f_language_id', 'int', 0, true);
$f_publication_id = Input::Get('f_publication_id', 'int', 0, true);
$f_issue_number = Input::Get('f_issue_number', 'int', 0, true);
$f_section_number = Input::Get('f_section_number', 'int', 0, true);

// Required input.
$f_article_code = Input::Get('f_article_code', 'string', 0);
$f_translation_title = trim(Input::Get('f_translation_title'));
$f_translation_language = Input::Get('f_translation_language');
list($articleNumber, $languageId) = split("_", $f_article_code);
$BackLink = "/$ADMIN/articles/translate.php?f_language_id=$f_language_id"
		. "&f_publication_id=$f_publication_id&f_issue_number=$f_issue_number"
		. "&f_section_number=$f_section_number&f_article_code=$f_article_code"
		. "&f_translation_title=$f_translation_title&f_translation_language=$f_translation_language";

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()));
	exit;
}

$articleObj =& new Article($languageId, $articleNumber);
if (!$articleObj->exists()) {
	camp_html_display_error(getGS('Article does not exist.'));
	exit;
}

$translationLanguageObj =& new Language($f_translation_language);
if (!$translationLanguageObj->exists()) {
	camp_html_display_error(getGS('Language does not exist.'));
	exit;
}

$translationArticle =& new Article($f_translation_language, $articleNumber);
if ($translationArticle->exists()) {
	$BackLink = camp_html_article_url($articleObj, $f_language_id, "edit.php");
	camp_html_display_error(getGS("The article has already been translated into $1.", $translationLanguageObj->getNativeName()), $BackLink);
	exit;
}

$f_publication_id = $articleObj->getPublicationId();

// Only create the translated issue and section if the article has been
// categorized.
if ($f_publication_id > 0) {
	$publicationObj =& new Publication($f_publication_id);
	if (!$publicationObj->exists()) {
		camp_html_display_error(getGS('Publication does not exist.'), $BackLink);
		exit;
	}

	$f_issue_number = $articleObj->getIssueNumber();
	$issueObj =& new Issue($f_publication_id, $f_language_id, $f_issue_number);
	if (!$issueObj->exists()) {
		camp_html_display_error(getGS('No such issue.'), $BackLink);
		exit;
	}

	$translationIssueObj =& new Issue($f_publication_id, $f_translation_language, $f_issue_number);
	if (!$translationIssueObj->exists()) {
		if (!$User->hasPermission("ManageIssue")) {
			camp_html_display_error(getGS('An issue must be created for the selected language but you do not have the right to create an issue.'), $BackLink);
			exit;
		}
		foreach ($issueObj->getData() as $field=>$fieldValue) {
			if ($field != 'IdLanguage') {
				$translationIssueObj->setProperty($field, $fieldValue, false);
			}
		}
		$f_issue_name = Input::Get('f_issue_name', 'string', '');
		if ($f_issue_name != '') {
			$translationIssueObj->setName($f_issue_name);
		}
		$f_issue_urlname = Input::Get('f_issue_urlname', 'string', $issueObj->getUrlName());
		if ($f_issue_urlname == "") {
			camp_html_display_error(getGS('You must complete the $1 field.','"'.getGS('New issue URL name').'"'), $BackLink);
			exit;
		}
		if (!camp_is_valid_url_name($f_issue_urlname)) {
			camp_html_display_error(getGS('The $1 field may only contain letters, digits and underscore (_) character.', '"' . getGS('New issue URL name') . '"'), $BackLink);
			exit;
		}
		$translationIssueObj->create($f_issue_urlname);
		if (!$translationIssueObj->exists()) {
			camp_html_display_error(getGS('Unable to create the issue for translation $1.', $translationLanguageObj->getName()), $BackLink);
			exit;
		}
	}

	$f_section_number = $articleObj->getSectionNumber();
	$sectionObj =& new Section($f_publication_id, $f_issue_number, $f_language_id, $f_section_number);
	if (!$sectionObj->exists()) {
		camp_html_display_error(getGS('No such section.'), $BackLink);
		exit;
	}

	$translationSectionObj =& new Section($f_publication_id, $f_issue_number, $f_translation_language,
										  $f_section_number);
	if (!$translationSectionObj->exists()) {
		if (!$User->hasPermission("ManageSection")) {
			camp_html_display_error(getGS('A section must be created for the selected language but you do not have the right to create a section.'), $BackLink);
			exit;
		}
		foreach ($sectionObj->getData() as $field=>$fieldValue) {
			if ($field != 'IdLanguage') {
				$translationSectionObj->setProperty($field, $fieldValue, false);
			}
		}
		$f_section_name = Input::Get('f_section_name', 'string', $sectionObj->getName());
		$f_section_urlname = Input::Get('f_section_urlname', 'string', $sectionObj->getUrlName());
		if ($f_section_urlname == "") {
			camp_html_display_error(getGS('You must complete the $1 field.','"'.getGS('New section URL name').'"'), $BackLink);
			exit;
		}
		if (!camp_is_valid_url_name($f_section_urlname)) {
			camp_html_display_error(getGS('The $1 field may only contain letters, digits and underscore (_) character.', '"' . getGS('New section URL name') . '"'), $BackLink);
			exit;
		}
		$translationSectionObj->create($f_section_name, $f_section_urlname);
		if (!$translationSectionObj->exists()) {
			camp_html_display_error(getGS('Unable to create the section for translation $1.', $translationLanguageObj->getName()), $BackLink);
			exit;
		}
	}
}

$articleCopy = $articleObj->createTranslation($f_translation_language, $User->getUserId(), $f_translation_title);

header('Location: '.camp_html_article_url($articleCopy, $f_language_id, 'edit.php'));
exit;
?>