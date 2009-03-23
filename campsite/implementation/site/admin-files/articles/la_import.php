<?php
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/articles/article_common.php");
require_once($_SERVER['DOCUMENT_ROOT']. "/classes/ArticleType.php");

global $Campsite;

if (!$g_user->hasPermission('ImportLegacyArchive')) {
    camp_html_display_error(getGS("You do not have the right to import legacy archves."));
    exit;
}

// Whether form was submitted
$f_save = Input::Get('f_save', 'string', '', true);

// The article location dropdowns cause this page to reload,
// so we need to preserve the state with each refresh.
$f_article_type = Input::Get('f_article_type', 'string', '', true);
$f_article_language_id = Input::Get('f_article_language_id', 'int', 0, true);

// For choosing the article location.
$f_publication_id = Input::Get('f_publication_id', 'int', 0, true);
$f_issue_number = Input::Get('f_issue_number', 'int', 0, true);
$f_section_number = Input::Get('f_section_number', 'int', 0, true);

// Whether articles must be overwritten
$f_overwrite_articles = Input::Get('f_overwrite_articles', 'string', '', true);

if ($f_save) {
    if (isset($_FILES["f_input_file"])) {
        switch($_FILES["f_input_file"]['error']) {
	case 0: // UPLOAD_ERR_OK
	    break;
	case 1: // UPLOAD_ERR_INI_SIZE
	case 2: // UPLOAD_ERR_FORM_SIZE
	    camp_html_display_error(getGS("The file exceeds the allowed max file size."), null, true);
	    break;
	case 3: // UPLOAD_ERR_PARTIAL
	    camp_html_display_error(getGS("The uploaded file was only partially uploaded. This is common when the maximum time to upload a file is low in contrast with the file size you are trying to input. The maximum input time is specified in 'php.ini'"), null, true);
	    break;
	case 4: // UPLOAD_ERR_NO_FILE
	    camp_html_display_error(getGS("You must select a file to upload."), null, true);
	    break;
	case 6: // UPLOAD_ERR_NO_TMP_DIR
	case 7: // UPLOAD_ERR_CANT_WRITE
	    camp_html_display_error(getGS("There was a problem uploading the file."), null, true);
	    break;
	}
    } else {
        camp_html_display_error(getGS("The file exceeds the allowed max file size."), null, true);
    }
 }

if (!Input::IsValid()) {
    camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), $_SERVER['REQUEST_URI']);
    exit;
}

$articleTypes = ArticleType::GetArticleTypes();
$allPublications = Publication::GetPublications();
$allLanguages = Language::GetLanguages();

//if (!empty($_FILES['f_input_file'])) {
//    var_dump($_FILES['f_input_file']);
//}

// Gets all issues
$allIssues = array();
if ($f_publication_id > 0) {
    $allIssues = Issue::GetIssues($f_publication_id, $f_article_language_id, null, null, null, array("LIMIT" => 300, "ORDER BY" => array("Number" => "DESC")));
    // Automatically selects the issue if there is only one
    if (count($allIssues) == 1) {
        $tmpIssue = camp_array_peek($allIssues);
	$f_issue_number = $tmpIssue->getIssueNumber();
    }
}

// Gets all the sections
$allSections = array();
if ($f_issue_number > 0) {
    $destIssue = new Issue($f_publication_id);
    $allSections = Section::GetSections($f_publication_id, $f_issue_number, $f_article_language_id, null, null, array("ORDER BY" => array("Number" => "DESC")));
    // Automatically selects the section if there is only one
    if (count($allSections) == 1) {
        $tmpSection = camp_array_peek($allSections);
        $f_section_number = $tmpSection->getSectionNumber();
    }
}

$crumbs = array();
$crumbs[] = array(getGS("Actions"), "");
$crumbs[] = array(getGS("Import legacy archive"), "");
echo camp_html_breadcrumbs($crumbs);

?>

<?php camp_html_display_msgs(); ?>

<p>
<form name="import_archive" enctype="multipart/form-data" method="POST" action="la_import.php" onsubmit="return <?php camp_html_fvalidate(); ?>;">
<table border="0" cellspacing="0" cellpadding="6" class="table_input">
<tr>
  <td colspan="2">
    <b><?php  putGS("Import legacy archive"); ?></b>
    <hr noshade size="1" color="black">
  </td>
</tr>
<tr>
  <td valign="top">
    <table>
    <tr>
      <td align="right"><?php putGS("Input File"); ?>:</td>
      <td>
        <input type="file" name="f_input_file" id="f_input_file" size="40" class="input_text" emsg="<?php putGS('You must select an XML input file.'); ?>" />
      </td>
    </tr>
    <tr>
      <td align="right"><?php putGS("Article Type"); ?>:</td>
      <td>
        <select name="f_article_type" id="f_article_type" class="input_select">
        <option value=""><?php putGS('---Select article type---'); ?></option>
        <?php
        foreach ($articleTypes as $article_type) {
            $articleType = new ArticleType($article_type);
            camp_html_select_option($articleType->getTypeName(), $f_article_type, $articleType->getTypeName());
        }
        ?>
        </select>
      </td>
    </tr>
    <tr>
      <td align="right"><?php putGS("Language"); ?>:</td>
      <td>
        <select name="f_article_language_id" id="f_article_language_id" class="input_select" onchange="if (this.options[this.selectedIndex].value != <?php p($f_article_language_id); ?>) {this.form.submit();}">
        <option value=""><?php putGS('---Select language---'); ?></option>
        <?php
        foreach ($allLanguages as $language) {
            camp_html_select_option($language->getLanguageId(), $f_article_language_id, $language->getName());
        }
        ?>
        </select>
      </td>
    </tr>
    <tr>
      <td align="right"><?php putGS("Publication"); ?>:</td>
      <td>
        <?php if ($f_article_language_id > 0 && count($allPublications) > 1) { ?>
        <select name="f_publication_id" id="f_publication_id" class="input_select" onchange="if (this.options[this.selectedIndex].value != <?php p($f_publication_id); ?>) {this.form.submit();}">
        <option value=""><?php putGS('---Select publication---'); ?></option>
        <?php
        foreach ($allPublications as $publication) {
            camp_html_select_option($publication->getPublicationId(), $f_publication_id, $publication->getName());
        }
        ?>
        </select>
        <?php } else { ?>
        <select class="input_select" disabled><option><?php putGS('No publications'); ?></option></select>
        <?php } ?>
      </td>
    </tr>
    <tr>
      <td align="right"><?php putGS("Issue"); ?>:</td>
      <td>
        <?php if (($f_publication_id > 0) && (count($allIssues) >= 1)) { ?>
        <select name="f_issue_number" id="f_issue_number" class="input_select" onchange="if (this.options[this.selectedIndex].value != <?php p($f_issue_number); ?>) { this.form.submit(); }">
        <option value="0"><?php putGS('---Select issue---'); ?></option>
        <?php
            foreach ($allIssues as $issue) {
                camp_html_select_option($issue->getIssueNumber(), $f_issue_number, $issue->getName());
            }
        ?>
        </select>
        <?php } else { ?>
        <select class="input_select" disabled><option><?php putGS('No issues'); ?></option></select>
        <?php } ?>
        &nbsp;
        (<?php putGS('Optional'); ?>)
      </td>
    </tr>
    <tr>
      <td align="right"><?php putGS("Section"); ?>:</td>
      <td>
        <?php if (($f_issue_number > 0) && (count($allSections) >= 1)) { ?>
        <select name="f_section_number" id="f_section_number" class="input_select">
        <option value=""><?php putGS('---Select section---'); ?></option>
        <?php
            foreach ($allSections as $section) {
                camp_html_select_option($section->getSectionNumber(), $f_section_number, $section->getName());
            }
        ?>
        </select>
        <?php } else { ?>
        <select class="input_select" disabled><option><?php putGS('No sections'); ?></option></select>
        <?php } ?>
        &nbsp;
        (<?php putGS('Optional'); ?>)
      </td>
    </tr>
    <tr>
      <td align="right"><?php putGS("Overwrite existing articles"); ?>?:</td>
      <td>
        <input type="radio" name="f_overwrite_articles" value="Y" <?php if ($f_overwrite_articles == 'Y') p("checked"); ?> /> <?php putGS("Yes"); ?>
        <input type="radio" name="f_overwrite_articles" value="N" <?php if ($f_overwrite_articles == 'N' || $f_overwrite_articles == '') p("checked"); ?> /> <?php putGS("No"); ?>
      </td>
    </tr>
    </table>
  </td>
</tr>
<tr>
  <td colspan="2" align="center">
    <hr noshade size="1" color="black">
    <input type="submit" name="f_save" value="<?php putGS('Save'); ?>" class="button" onclick="document.forms.add_article.action='do_add.php';" />
  </td>
</tr>
</table>

<?php camp_html_copyright_notice(); ?>