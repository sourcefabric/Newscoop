<?php
/**
 * Notes about this file:
 * - If the text in the editor is already hyperlinked, then this file is called with the
 *   arguments in the URL, like "filename.php?IdPublication=1&IdLanguage=2&..."
 * - Everytime a menu item is changed, the file is re-fetched with the same arguments
 *	 set in the POST.
 * 
 */
require_once($_SERVER['DOCUMENT_ROOT']."/classes/config.php");
require_once($_SERVER['DOCUMENT_ROOT']."/priv/CampsiteInterface.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Language.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Publication.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Issue.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Section.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Article.php");

$maxSelectLength = 80;
?>
<html>
<head>
<title>Insert Campsite Internal Link</title>
<LINK rel="stylesheet" type="text/css" href="<?php echo $Campsite["website_url"] ?>/stylesheet.css">
<style type="text/css">
html, body {
  background: ButtonFace;
  color: ButtonText;
  font: 11px Tahoma,Verdana,sans-serif;
  margin: 0px;
  padding: 0px;
}

body { padding: 5px; }

table {
  font: 11px Tahoma,Verdana,sans-serif;
}

select, input, button { 
	font: 11px Tahoma,Verdana,sans-serif; 
}

button { 
	width: 70px; 
}

table .label { 
	text-align: right; 
	width: 8em; 
}

.title { 
	background: #ddf; 
	color: #000; 
	font-weight: bold; 
	font-size: 120%; 
	padding: 3px 10px; 
	margin-bottom: 10px;
	border-bottom: 1px solid black; 
	letter-spacing: 2px;
}

#buttons {
	margin-top: 1em; 
	border-top: 1px solid #999;
	padding: 2px; 
	text-align: right;
}
</style>

<script type="text/javascript" src="popup.js"></script>
<script type="text/javascript">
I18N = window.opener.HTMLArea.I18N.dialogs;

function i18n(str) {
	return (I18N[str] || str);
};

function Init() {
	__dlg_translate(I18N);
	// This function gets the arguments passed to the window and sizes the window.
	__dlg_init();
	//var param = window.dialogArguments;

	window.resizeTo(400, 190);
};

function onOK() {
	languageId = document.getElementById("IdLanguage").value;
	publicationElement = document.getElementById("IdPublication");
	publicationId = publicationElement ? publicationElement.value : 0;
	issueElement = document.getElementById("NrIssue");
	issueId = issueElement ? issueElement.value : 0;
	sectionElement = document.getElementById("NrSection");
	sectionId = sectionElement ? sectionElement.value : 0;
	articleElement = document.getElementById("NrArticle");
	articleId = articleElement? articleElement.value : 0;
	if ((languageId <= 0) || (publicationId <= 0)) {
		alert("You must specify the language and the publication.");
		// They must at least specify language and publication.
		//__dlg_close(null);
		return false;		
	}
	// pass data back to the calling window
	var param = new Object();
	param["f_href"] = "campsite_internal_link?IdPublication="+publicationId
					  +"&IdLanguage="+languageId;
	if (issueId > 0) {
		param["f_href"] += "&NrIssue=" + issueId;
	}
	if (sectionId > 0) {
		param["f_href"] += "&NrSection=" + sectionId;
	}
	if (articleId > 0) {
		param["f_href"] += "&NrArticle=" + articleId;
	}
	param["f_title"] = "";
	param["f_target"] = "";
	__dlg_close(param);
	return false;
};

function onCancel() {
	__dlg_close(null);
	return false;
};
</script>
</head>
<body onload="Init()">
<div class="title">Insert Campsite Internal Link</div>
<?php //print_r($_REQUEST); ?>
<form action="<?php $_SERVER['PHP_SELF'] ?>" method="POST">
<table border="0" style="width: 100%;" id="main_table">
<tr>
	<td class="label">Language:</td>
    <td>
    	<?php
    	$languages =& Language::getAllLanguages();
    	$languageId = isset($_REQUEST["IdLanguage"]) ? $_REQUEST["IdLanguage"] : 0;
    	$extras = 'id="IdLanguage" onchange="this.form.submit();"';
    	$options = array();
    	$options[0] = "?";
    	foreach ($languages as $language) {
    		$options[$language->getLanguageId()] = substr($language->getName(), 0, $maxSelectLength);
    	}
    	CampsiteInterface::CreateSelect("IdLanguage", $options, $languageId, $extras, true);
    	?>
    </td>
  </tr>
<tr>
	<td class="label">Publication:</td>
    <td>
    	<?php
    	$publications = Publication::getAllPublications();
    	$options = array();
    	$options[0] = "?";
    	foreach ($publications as $publication) {
    		$options[$publication->getPublicationId()] = substr($publication->getName(), 0, $maxSelectLength);
    	}
    	$publicationId = isset($_REQUEST["IdPublication"]) ? $_REQUEST["IdPublication"] : 0;
    	$extras = 'id="IdPublication" onchange="this.form.submit()"';
    	if ($languageId == 0){
    		$extras .= ' disabled';
    	}
    	CampsiteInterface::CreateSelect("IdPublication", $options, $publicationId, $extras, true);
    	?>
	</td>
</tr>
<tr>
	<td class="label">Issue:</td>
	<td>
    	<?php
    	$issueId = isset($_REQUEST["NrIssue"]) ? $_REQUEST["NrIssue"] : 0;
    	$options = array();
    	$options[0] = "?";
    	if (($languageId != 0) && ($publicationId != 0)) {
	    	$issues = Issue::getIssuesInPublication($publicationId, $languageId);
	    	foreach ($issues as $issue) {
	    		$options[$issue->getIssueId()] = substr($issue->getName(), 0, $maxSelectLength);
	    	}
	    	$extras = 'id="NrIssue" onchange="this.form.submit()"';
    	}
    	else {
    		$extras = ' disabled';
    	}
    	CampsiteInterface::CreateSelect("NrIssue", $options, $issueId, $extras, true);
    	?>
	</td>
</tr>
<tr>
	<td class="label">Section:</td>
	<td>
    	<?php
    	$sectionId = isset($_REQUEST["NrSection"]) ? $_REQUEST["NrSection"] : 0;
    	$options = array();
    	$options[0] = "?";
    	if (($languageId != 0) && ($publicationId != 0) && ($issueId != 0)) {
	    	$sections = Section::getSectionsInIssue($publicationId, $issueId, $languageId);
	    	foreach ($sections as $section) {
	    		$options[$section->getSectionId()] = substr($section->getName(), 0, $maxSelectLength);
	    	}
	    	$extras = 'id="NrSection" onchange="this.form.submit()"';
    	}
    	else {
    		$extras = ' disabled';
    	}
    	CampsiteInterface::CreateSelect("NrSection", $options, $sectionId, $extras, true);
    	?>
	</td>
</tr>
<tr>
	<td class="label">Article:</td>
	<td>
    	<?php
    	$articleId = isset($_REQUEST["NrArticle"]) ? $_REQUEST["NrArticle"] : 0;
    	$options = array();
    	$options[0] = "?";
    	if (($languageId != 0) && ($publicationId != 0) && ($issueId != 0) && ($sectionId != 0)) {
	    	$articles = Article::getArticlesInSection($publicationId, $issueId, $sectionId, $languageId);
	    	foreach ($articles as $article) {
	    		$options[$article->getArticleId()] = substr($article->getTitle(), 0, $maxSelectLength);
	    	}
	    	$extras = 'id="NrArticle"';
    	}
    	else {
    		$extras = ' disabled';
    	}
    	CampsiteInterface::CreateSelect("NrArticle", $options, $articleId, $extras, true);
    	?>
	</td>
</tr>
</table>

<div id="buttons">
  <button type="button" name="ok" onclick="return onOK();">OK</button>
  <button type="button" name="cancel" onclick="return onCancel();">Cancel</button>
</div>
</form>
</body>
</html>
