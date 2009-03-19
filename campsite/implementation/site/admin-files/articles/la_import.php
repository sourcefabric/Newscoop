<?php
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/articles/article_common.php");
require_once($_SERVER['DOCUMENT_ROOT']. "/classes/ArticleType.php");

global $Campsite;

if (!$g_user->hasPermission('AddArticle')) {
	camp_html_display_error(getGS("You do not have the right to add articles."));
	exit;
}

$articleTypes = ArticleType::GetArticleTypes();
$publications = Publication::GetPublications();
$languages = Language::GetLanguages();
//var_dump($languages); exit;

$crumbs = array();
$crumbs[] = array(getGS("Actions"), "");
$crumbs[] = array(getGS("Import legacy archive"), "");
echo camp_html_breadcrumbs($crumbs);

?>

<p>
<form name="import_archive" method="GET" action="la_import.php" onsubmit="return <?php camp_html_fvalidate(); ?>;">
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
        <input type="file" name="f_input_file" size="40" class="input_text" alt="blank" emsg="<?php putGS('You must complete the $1 field.', getGS('Name')); ?>" value="<?php echo htmlspecialchars($f_article_name); ?>" />
      </td>
    </tr>
    <tr>
      <td align="right"><?php putGS("Article Type"); ?>:</td>
      <td>
        <select name="f_user_type">
        <option value="">---</option>
        <?php
        foreach ($articleTypes as $article_type) {
            $articleType = new ArticleType($article_type);
            camp_html_select_option($articleType->getTypeName(), '', $articleType->getTypeName());
        }
        ?>
        </select>
      </td>
    </tr>
    <tr>
      <td align="right"><?php putGS("Publication"); ?>:</td>
      <td>
        <select name="f_publication" onchange="this.form.submit();">
        <option value="">---</option>
        <?php
        foreach ($publications as $publication) {
            camp_html_select_option($publication->getPublicationId(), '', $publication->getName());
        }
        ?>
        </select>
      </td>
    </tr>
    <tr>
      <td align="right"><?php putGS("Language"); ?>:</td>
      <td>
        <select name="f_language">
        <option value="">---</option>
        <?php
        foreach ($languages as $language) {
            camp_html_select_option($language->getLanguageId(), '', $language->getName());
        }
        ?>
        </select>
      </td>
    </tr>
    <tr>
      <td align="right"><?php putGS("Issue"); ?>:</td>
      <td>
        <select name="f_issue" onchange="this.form.submit();">
        <option value="">---</option>
        <?php
  //foreach ($issues as $issue) {
  //        camp_html_select_option($issue->getIssueNumber(), '', $issue->getName());
  //    }
        ?>
        </select>
      </td>
    </tr>
    <tr>
      <td align="right"><?php putGS("Section"); ?>:</td>
      <td>
        <select name="f_section">
        <option value="">---</option>
        <?php
  //foreach ($sections as $section) {
  //        camp_html_select_option($section->getSectionNumber(), '', $section->getName());
  //    }
        ?>
        </select>
      </td>
    </tr>
    <tr>
      <td align="right"><?php putGS("Overwrite existing articles"); ?>?:</td>
      <td>
        <input type="radio" name="f_overwrite_articles" value="Y" <?php if (SystemPref::Get("SiteOnline") == 'Y') p("checked"); ?> /> <?php putGS("Yes"); ?>
        <input type="radio" name="f_overwrite_articles" value="N" <?php if (SystemPref::Get("SiteOnline") == 'N') p("checked"); ?> /> <?php putGS("No"); ?>
      </td>
    </tr>
    </table>
  </td>
</tr>
<tr>
  <td colspan="2" align="center">
    <hr noshade size="1" color="black">
    <input type="submit" name="save" value="<?php putGS('Save'); ?>" class="button" onclick="document.forms.add_article.action='do_add.php';" />
  </td>
</tr>
</table>

<?php camp_html_copyright_notice(); ?>