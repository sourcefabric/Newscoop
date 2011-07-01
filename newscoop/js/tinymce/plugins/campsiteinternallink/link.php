<?php
/**
 * Notes about this file:
 * - If the text in the editor is already hyperlinked, then this file is called with the
 *   arguments in the URL, like "filename.php?IdPublication=1&IdLanguage=2&..."
 * - Everytime a menu item is changed, the file is re-fetched with the same arguments
 *       set in the POST.
 *
 */
$GLOBALS['g_campsiteDir'] = dirname(dirname(dirname(dirname(dirname(__FILE__)))));

// run zend
require_once $GLOBALS['g_campsiteDir'] . '/public/index.php';

require_once($GLOBALS['g_campsiteDir'].'/classes/User.php');

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    realpath(dirname(__FILE__) . '/../include'),
    get_include_path(),
)));
if (!is_file('Zend/Application.php')) {
	// include libzend if we dont have zend_application
	set_include_path(implode(PATH_SEPARATOR, array(
		'/usr/share/php/libzend-framework-php',
		get_include_path(),
	)));
}
require_once 'Zend/Application.php';

include_once("Zend/Auth.php");
include_once("Zend/Auth/Storage/Session.php");

// setup the correct namespace for the zend auth session
Zend_Auth::getInstance()->setStorage(new Zend_Auth_Storage_Session( 'Zend_Auth_Admin' ) );

$userId = Zend_Auth::getInstance()->getIdentity();
$userTmp = new User($userId);
if (!$userTmp->exists() || !$userTmp->isAdmin()) {
	header("Location: /$ADMIN/login.php");
	exit(0);
}
unset($userTmp);

header('Content-Type: text/html; charset=UTF-8');

require_once($GLOBALS['g_campsiteDir'].'/db_connect.php');
require_once($GLOBALS['g_campsiteDir'].'/conf/configuration.php');
require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/camp_html.php");
require_once($GLOBALS['g_campsiteDir'].'/classes/Language.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Publication.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Issue.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Section.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Article.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Input.php');

$maxSelectLength = 60;
$languageId = Input::get('IdLanguage', 'int', 0, true);
$publicationId = Input::get('IdPublication', 'int', 0, true);
$sectionId = Input::get('NrSection', 'int', 0, true);
$issueId = Input::get('NrIssue', 'int', 0, true);
$articleId = Input::get('NrArticle', 'int', 0, true);
$target = Input::get('targetlist', 'string', '', true);
$languages = Language::GetLanguages(null, null, null, array(), array(), true);
$publications = Publication::GetPublications();
if (($languageId != 0) && ($publicationId != 0)) {
        $issues = Issue::GetIssues($publicationId, $languageId, null, null, null, false, null, true);
}
if (($languageId != 0) && ($publicationId != 0) && ($issueId != 0)) {
        $sections = Section::GetSections($publicationId, $issueId, $languageId, null, null, null, true);
}
if (($languageId != 0) && ($publicationId != 0) && ($issueId != 0) && ($sectionId != 0)) {
        $articles = Article::GetArticles($publicationId, $issueId, $sectionId, $languageId);
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <title>{#campsiteinternallink.title}</title>

  <script type="text/javascript" src="../../tiny_mce_popup.js"></script>
  <script type="text/javascript" src="../../utils/mctabs.js"></script>
  <script type="text/javascript" src="../../utils/form_utils.js"></script>
  <script type="text/javascript" src="../../utils/validate.js"></script>
  <script type="text/javascript" src="js/campsiteinternallink.js"></script>
  <link href="css/campsiteinternallink.css" rel="stylesheet" type="text/css" />
  <base target="_self" />
</head>
<body id="campsiteinternallink" style="display: none">
  <form onsubmit="insertAction();return false;" action="#">
  <div class="tabs">
    <ul>
      <li id="general_tab" class="current"><span><a href="javascript:mcTabs.displayTab('general_tab','general_panel');" onmousedown="return false;">{#campsiteinternallink.general_tab}</a></span></li>
    </ul>
  </div>
  <div class="panel_wrapper">
    <div id="general_panel" class="panel current">
      <fieldset>
        <legend>{#campsiteinternallink.general_props}</legend>

        <table border="0" cellpadding="4" cellspacing="0">
        <tr>
          <td nowrap="nowrap"><label id="hreflabel" for="href">{#campsiteinternallink.language}</label></td>
          <td>
            <table border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td>
                <input type="hidden" name="language" id="language" />
                <select name="IdLanguage" id="IdLanguage" onchange="this.form.submit();">
                  <option value="0">?</option>
                  <?php
                  foreach ($languages as $language) {
                      $languageName = substr($language->getName(), 0, $maxSelectLength);
                      camp_html_select_option($language->getLanguageId(), $languageId, $languageName);
                  }
                  ?>
                </select>
              </td>
              <td id="hrefbrowsercontainer">&nbsp;</td>
            </tr>
            </table>
          </td>
        </tr>
        <tr id="pickpublicationfrom">
          <td class="column1"><label for="pickpublication">{#campsiteinternallink.publication}</label></td>
          <td colspan="2" id="pickpublicationcontainer">
            <input type="hidden" name="publication" id="publication" />
            <select name="IdPublication" id="IdPublication" onchange="this.form.submit();" <?php if ($languageId == 0){ ?>disabled<?php } ?>>
              <option value="0">?</option>
              <?php
              foreach ($publications as $publication) {
                  $publicationName = substr($publication->getName(), 0, $maxSelectLength);
                  camp_html_select_option($publication->getPublicationId(), $publicationId, $publicationName);
              }
              ?>
            </select>
          </td>
        </tr>
        <tr>
          <td class="column1"><label for="pickissue">{#campsiteinternallink.issue}</label></td>
          <td colspan="2" id="pickissuecontainer">
            <select name="NrIssue" id="NrIssue" onchange="this.form.submit();" <?php if (($languageId == 0) || ($publicationId == 0)) { ?>disabled<?php } ?>>
              <option value="0">?</option>
              <?php
              if (($languageId != 0) && ($publicationId != 0)) {
                  foreach ($issues as $issue) {
                      $issueName = substr($issue->getName(), 0, $maxSelectLength);
                      camp_html_select_option($issue->getIssueNumber(), $issueId, $issueName);
                  }
              }
              ?>
            </select>
          </td>
        </tr>
        <tr>
          <td><label id="picksectionfrom" for="picksection">{#campsiteinternallink.section}</label></td>
          <td id="picksectioncontainer">
            <select name="NrSection" id="NrSection" onchange="this.form.submit();" <?php if (($languageId == 0) || ($publicationId == 0) || ($issueId == 0)) { ?>disabled<?php } ?>>
              <option value="0">?</option>
              <?php
              if (($languageId != 0) && ($publicationId != 0) && ($issueId != 0)) {
                  foreach ($sections as $section) {
                      $sectionName = substr($section->getName(), 0, $maxSelectLength);
                      camp_html_select_option($section->getSectionNumber(), $sectionId, $sectionName);
                  }
              }
              ?>
            </select>
          </td>
        </tr>
        <tr>
          <td nowrap="nowrap"><label id="pickarticlefrom" for="article">{#campsiteinternallink.article}</label></td>
          <td id="pickarticlecontainer">
            <select name="NrArticle" id="NrArticle" onchange="this.form.submit();" <?php if (($languageId == 0) || ($publicationId == 0) || ($issueId == 0) || ($sectionId == 0)) { ?>disabled<?php } ?>>
              <option value="0">?</option>
              <?php
              if (($languageId != 0) && ($publicationId != 0) && ($issueId != 0) && ($sectionId != 0)) {
                  foreach ($articles as $article) {
                      $articleName = substr($article->getTitle(), 0, $maxSelectLength);
                      camp_html_select_option($article->getArticleNumber(), $articleId, $articleName);
                  }
              }
              ?>
            </select>
          </td>
        </tr>
        <tr>
          <input type="hidden" name="target" id="target" />
          <td class="column1"><label id="targetlistlabel" for="targetlist">{#campsiteinternallink.target}</label></td>
          <td id="targetlistcontainer">&nbsp;</td>
        </tr>
      </table>
      </fieldset>
    </div>
  </div>
  <div class="mceActionPanel">
    <div style="float: left">
      <input type="submit" id="insert" name="insert" value="{#insert}" />
    </div>
    <div style="float: right">
      <input type="button" id="cancel" name="cancel" value="{#cancel}" onclick="tinyMCEPopup.close();" />
    </div>
  </div>
  </form>
</body>
</html>
