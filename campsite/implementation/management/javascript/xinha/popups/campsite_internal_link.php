<?php
/**
 * Notes about this file:
 * - If the text in the editor is already hyperlinked, then this file is called with the
 *   arguments in the URL, like "filename.php?IdPublication=1&IdLanguage=2&..."
 * - Everytime a menu item is changed, the file is re-fetched with the same arguments
 *	 set in the POST.
 * 
 */
require_once($_SERVER['DOCUMENT_ROOT'].'/db_connect.php');
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/CampsiteInterface.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Language.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Publication.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Issue.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Section.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Article.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/common.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Input.php');
load_common_include_files("$ADMIN_DIR");

$maxSelectLength = 60;
$languageId = Input::get('IdLanguage', 'int', 0, true);
$publicationId = Input::get('IdPublication', 'int', 0, true);
$sectionId = Input::get('NrSection', 'int', 0, true);
$issueId = Input::get('NrIssue', 'int', 0, true);
$articleId = Input::get('NrArticle', 'int', 0, true);
$target = Input::get('target', 'string', '', true);

$languages =& Language::GetLanguages();
$publications = Publication::GetPublications();
if (($languageId != 0) && ($publicationId != 0)) {
	$issues = Issue::GetIssues($publicationId, $languageId);
}
if (($languageId != 0) && ($publicationId != 0) && ($issueId != 0)) {
	$sections = Section::GetSections($publicationId, $issueId, $languageId);
}
if (($languageId != 0) && ($publicationId != 0) && ($issueId != 0) && ($sectionId != 0)) {
	$articles = Article::GetArticles($publicationId, $issueId, $sectionId, $languageId);
}
?>
<html>
<head>
<title>Insert Internal Link</title>
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

function Init() {
	__dlg_translate('Campsite');
	// This function gets the arguments passed to the window and sizes the window.
	__dlg_init();
	
	// Make sure that the proper translation appears in the drop down
	document.getElementById("f_target").selectedIndex = 1;
	document.getElementById("f_target").selectedIndex = 0;
	
	var param = window.dialogArguments;
	var target_select = document.getElementById("f_target");
	if (param) {
		targetValue = param["f_target"];
		if ((param["f_target"] != "") && (param["f_target"] != "_blank") 
			&& (param["f_target"] != "_self") && (param["f_target"] != "_top")) {
			targetValue = "_other";
			target_select.selectedIndex = 4;
			otherTextBox = document.getElementById("f_other_target");
			otherTextBox.value = param["f_target"];
			otherTextBox.style.visibility = "visible";
		}
		comboSelectValue(target_select, targetValue);
	}
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
	targetElement = document.getElementById("f_target");
	target = targetElement ? targetElement.value : '';
	
	// User must at least specify language and publication.
	if ((languageId <= 0) || (publicationId <= 0)) {
		alert("You must specify the language and the publication.");
		return false;		
	}
	
	// Pass data back to the calling window.
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
	if (target != '') {
		if (target == "_other") {
		    param["f_target"] = document.getElementById("f_other_target").value;
		}
		else {
			param["f_target"] = target;
		}
	}
	else {
		param["f_target"] = "";
	}
	param["f_title"] = "";
	__dlg_close(param);
	return false;
};

function onCancel() {
	__dlg_close(null);
	return false;
};

function onTargetChanged(selectElement) {
	var f = document.getElementById("f_other_target");
	if (selectElement.value == "_other") {
		f.style.visibility = "visible";
		f.select();
		f.focus();
	} 
	else {
		f.style.visibility = "hidden";
	}
};
</script>
</head>
<body onload="Init()">
<div class="title">Insert Internal Link</div>
<?php //print_r($_REQUEST); ?>
<form action="<?php $_SERVER['PHP_SELF'] ?>" method="POST">
<table border="0" style="width: 100%;" id="main_table">
<tr>
	<td class="label"><?php putGS("Language"); ?>:</td>
    <td>
    	<?php
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
	<td class="label"><?php putGS("Publication"); ?>:</td>
    <td>
    	<?php
    	$options = array();
    	$options[0] = "?";
    	foreach ($publications as $publication) {
    		$options[$publication->getPublicationId()] = substr($publication->getName(), 0, $maxSelectLength);
    	}
    	$extras = 'id="IdPublication" onchange="this.form.submit()"';
    	if ($languageId == 0){
    		$extras .= ' disabled';
    	}
    	CampsiteInterface::CreateSelect("IdPublication", $options, $publicationId, $extras, true);
    	?>
	</td>
</tr>
<tr>
	<td class="label"><?php putGS("Issue"); ?>:</td>
	<td>
    	<?php
    	$options = array();
    	$options[0] = "?";
    	if (($languageId != 0) && ($publicationId != 0)) {
	    	foreach ($issues as $issue) {
	    		$options[$issue->getIssueNumber()] = substr($issue->getName(), 0, $maxSelectLength);
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
	<td class="label"><?php putGS("Section"); ?>:</td>
	<td>
    	<?php
    	$options = array();
    	$options[0] = "?";
    	if (($languageId != 0) && ($publicationId != 0) && ($issueId != 0)) {
	    	foreach ($sections as $section) {
	    		$options[$section->getSectionNumber()] = substr($section->getName(), 0, $maxSelectLength);
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
	<td class="label"><?php putGS("Article"); ?>:</td>
	<td>
    	<?php
    	$options = array();
    	$options[0] = "?";
    	if (($languageId != 0) && ($publicationId != 0) && ($issueId != 0) && ($sectionId != 0)) {
	    	foreach ($articles as $article) {
	    		$options[$article->getArticleNumber()] = substr($article->getTitle(), 0, $maxSelectLength);
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
<tr>
	<td class="label">Target:</td>
	<td>
		<table cellpadding="0" cellspacing="0">
		<tr>
			<td>
				<select id="f_target" onchange="onTargetChanged(this);">
		      	<option value="">None (use implicit)</option>
		      	<option value="_blank">New window (_blank)</option>
		      	<option value="_self">Same frame (_self)</option>
		      	<option value="_top">Top frame (_top)</option>
		      	<option value="_other">Other</option>
		    	</select>
		    </td>
		    <td>
    			<input type="text" name="f_other_target" id="f_other_target" size="10" style="visibility: hidden" />
    		</td>
    	</tr>
    	</table>
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
