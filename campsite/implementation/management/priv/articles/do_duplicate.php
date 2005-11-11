<?php
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/articles/article_common.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Log.php");

// Check permissions
list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}


$f_publication_id = Input::Get('f_publication_id', 'int', 0);
$f_issue_number = Input::Get('f_issue_number', 'int', 0);
$f_section_number = Input::Get('f_section_number', 'int', 0);
$f_language_id = Input::Get('f_language_id', 'int', 0);
$f_article_number = Input::Get('f_article_number', 'int', 0);
//$sLanguage = Input::Get('sLanguage', 'int', 0);
$sLanguage = $f_language_id;
$f_destination_publication_id = Input::Get('f_destination_publication_id', 'int', 0);
$f_destination_issue_id = Input::Get('f_destination_issue_id', 'int', 0);
$f_destination_section_id = Input::Get('f_destination_section_id', 'int', 0);
$f_article_name = Input::Get('f_article_name');
//$BackLink = Input::Get('Back', 'string', "/$ADMIN/articles/index.php", true);

if (!$User->hasPermission("AddArticle")) {
	camp_html_display_error(getGS("You do not have the right to add articles."), $BackLink);
	exit;
}

if (!Input::IsValid()) {
	camp_html_display_error(getGS("Invalid input: $1", Input::GetErrorString()), $BackLink);
	exit;	
}

$articleObj =& new Article($sLanguage, $f_article_number);
$sectionObj =& new Section($f_publication_id, $f_issue_number, $f_language_id, $f_section_number);
$issueObj =& new Issue($f_publication_id, $f_language_id, $f_issue_number);
$publicationObj =& new Publication($f_publication_id);

$articleCopy = $articleObj->copy($f_destination_publication_id, $f_destination_issue_id, $f_destination_section_id, $User->getId());
$articleCopy->setTitle($f_article_name);

$logtext = getGS('Article $1 added to $2. $3 from $4. $5 of $6',
	$articleCopy->getName(), $sectionObj->getSectionNumber(),
	$sectionObj->getName(), $issueObj->getIssueNumber(),
	$issueObj->getName(), $publicationObj->getName() );
Log::Message($logtext, $User->getUserName(), 155);

$url = camp_html_article_url($articleCopy, $f_language_id, "edit.php");
header("Location: $url");
exit;
?>