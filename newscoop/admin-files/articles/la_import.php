<?php
require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/articles/article_common.php");
require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/javascript_common.php");
require_once($GLOBALS['g_campsiteDir']."/classes/ArticleType.php");

global $Campsite;

$translator = \Zend_Registry::get('container')->getService('translator');

if ( isset($_POST['f_save']) && !SecurityToken::isValid()) {
    camp_html_display_error($translator->trans('Invalid security token!'));
    exit;
}

if (!$g_user->hasPermission('ManageIssue') || !$g_user->hasPermission('AddArticle')) {
    camp_html_display_error($translator->trans("You do not have the right to import XML archives.", array(), 'articles'));
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

// Build the back link
$backlink = $Campsite['WEBSITE_URL'] . DIR_SEP . 'admin' . DIR_SEP . 'articles' . DIR_SEP . basename(__FILE__);

if ($f_save) {
    if (isset($_FILES["f_input_file"])) {
        switch($_FILES["f_input_file"]['error']) {
            case 0: // UPLOAD_ERR_OK
                break;
            case 1: // UPLOAD_ERR_INI_SIZE
            case 2: // UPLOAD_ERR_FORM_SIZE
                camp_html_display_error($translator->trans("The file exceeds the allowed max file size.", array(), 'articles'), null, true);
                break;
            case 3: // UPLOAD_ERR_PARTIAL
                camp_html_display_error($translator->trans("The uploaded file was only partially uploaded. This is common when the maximum time to upload a file is low in contrast with the file size you are trying to input. The maximum input time is specified in php.ini", array(), 'articles'), null, true);
                break;
            case 4: // UPLOAD_ERR_NO_FILE
                camp_html_display_error($translator->trans("You must select a file to upload.", array(), 'articles'), null, true);
                break;
            case 6: // UPLOAD_ERR_NO_TMP_DIR
            case 7: // UPLOAD_ERR_CANT_WRITE
                camp_html_display_error($translator->trans("There was a problem uploading the file.", array(), 'articles'), null, true);
                break;
        }
    } else {
        camp_html_display_error($translator->trans("The file exceeds the allowed max file size.", array(), 'articles'), null, true);
    }
}

if (!Input::IsValid()) {
    camp_html_display_error($translator->trans('Invalid input: $1', array('$1' => Input::GetErrorString())), $backlink);
    exit;
}

$articleTypes = ArticleType::GetArticleTypes();
$allPublications = Publication::GetPublications();
$allLanguages = Language::GetLanguages(null, null, null, array(), array(), true);

$isValidXMLFile = false;
if ($f_save && !empty($_FILES['f_input_file'])) {
    if (file_exists($_FILES['f_input_file']['tmp_name'])) {
        if (!($buffer = @file_get_contents($_FILES['f_input_file']['tmp_name']))) {
	    camp_html_display_error($translator->trans("File could not be read.", array(), 'articles'), $backlink);
	    exit;
	}

    try {
        $xml = new SimpleXMLElement($buffer);
        if (!is_object($xml)) {
            throw new Exception();
        }
	} catch (Exception $e) {
	    camp_html_display_error($translator->trans("File is not a valid XML file.", array(), 'articles'), $backlink);
	    exit;
    }

	if (!isset($xml->article->name)) {
	    camp_html_add_msg($translator->trans("Bad format in XML file.", array(), 'articles'), $backlink);
	}

	$isValidXMLFile = true;
	@unlink($_FILES['f_input_file']['tmp_name']);
    } else {
        camp_html_display_error($translator->trans("File does not exist.", array(), 'articles'), $backlink);
        exit;
    }
} elseif ($f_save) {
    camp_html_add_msg($translator->trans("File could not be uploaded.", array(), 'articles'), $backlink);
}


if ($isValidXMLFile) {
    if ($f_publication_id > 0) {
        $publicationObj = new Publication($f_publication_id);
        if (!$publicationObj->exists()) {
            camp_html_display_error($translator->trans('Publication does not exist.'), $backlink);
            exit;
        }
    	if ($f_issue_number > 0) {
    	    $issueObj = new Issue($f_publication_id, $f_article_language_id, $f_issue_number);
    	    if (!$issueObj->exists()) {
    	        camp_html_display_error($translator->trans('Issue does not exist.'), $backlink);
                exit;
    	    }

    	    if ($f_section_number > 0) {
    	        $sectionObj = new Section($f_publication_id, $f_issue_number, $f_article_language_id, $f_section_number);
                if (!$sectionObj->exists()) {
                    camp_html_display_error($translator->trans('Section does not exist.'), $backlink);
                    exit;
                }
    	    }
    	}
    }

    // Loads article data from XML file into database
    $xmlArticle = array();
    $articleFields = array();
    $errorMessages = array();
    $articleCount = 0;
    foreach ($xml->article as $article) {
        $existingArticles = Article::GetByName((string) $article->name,
					       $f_publication_id,
					       $f_issue_number,
					       $f_section_number,
					       $f_article_language_id, true);
	// There is already an article with same name and language
	if (count($existingArticles) > 0) {
	    $existingArticle = array_pop($existingArticles);
	    // Is overwrite articles false? then skip and process next article
	    if ($f_overwrite_articles == 'N') {
	        $errorMessages[][] = $translator->trans('Article <i>$1</i> already exists, and was not overwritten.<br />',array('$1' => $article->name), 'articles');
	        continue;
	    }
	}

	if (isset($existingArticle) && $existingArticle->exists()) {
	    $articleObj = $existingArticle;
	} else {
	    $articleObj = new Article($f_article_language_id);
	    $articleName = (string) $article->name;
	    $articleObj->create($f_article_type, $articleName, $f_publication_id, $f_issue_number, $f_section_number);
	}

	// Checks whether article was successfully created
	if (!$articleObj->exists()) {
	    camp_html_display_error($translator->trans('Article could not be created.', array(), 'articles'), $backlink);
	    exit;
	}
	$articleFields['name'] = true;

	// Number of articles successfully created in database
	$articleCount++;
	$errorMessages[$articleCount][] = '<p><strong>'
	    . htmlspecialchars((string) $article->name) . '</strong></p>';
	$xmlArticle = get_object_vars($article);

	$articleTypeObj = $articleObj->getArticleData();
	$dbColumns = $articleTypeObj->getUserDefinedColumns(false, true);
	$articleTypeFields = array();
	foreach ($dbColumns as $dbColumn) {
	    $fieldName = $dbColumn->getPrintName();
	    $field = strtolower($fieldName);
	    if (!isset($article->$field)) {
	        $errorMessages[$articleCount][] = 'The article type field "<i>'
		    .$fieldName
		    .'</i>" does not match any field from XML input file.<br />';
		continue;
	    }

	    $articleFields[$field] = true;

	    $articleTypeObj->setProperty($dbColumn->getName(), (string) $article->$field);
	}

	// Updates the article creator and author
    $articleObj->setCreatorId($g_user->getUserId());
    $isAuthorFromCreator = FALSE;
	if (isset($article->author) && !empty($article->author)) {
	    $authorName = (string) $article->author;
	} else {
	    $authorName = (string) $g_user->getRealName();
	    $isAuthorFromCreator = TRUE;
	}
    $authorObj = new Author($authorName);
    if (!$authorObj->exists()) {
        $authorData = Author::ReadName($authorName);
        if ($isAuthorFromCreator) {
            $authorData['email'] = $g_user->getEmail();
        }
        $authorObj->create($authorData);
    }
    if ($authorObj->exists()) {
        $articleObj->setAuthor($authorObj);
        $articleFields['author'] = true;
    }

	// Updates the article
	if (isset($article->keywords) && !empty($article->keywords)) {
	    $articleObj->setKeywords((string) $article->keywords);
	}
	$articleFields['keywords'] = true;

	foreach($xmlArticle as $articleFieldName => $articleFieldValue) {
	    if (!array_key_exists($articleFieldName, $articleFields)) {
	        $errorMessages[$articleCount][] = '"' . $articleFieldName
		    .'" field in XML file '
		    . 'was not loaded into database as there is not any '
		    . 'article type field matching it.<br />';
	    }
	}
    }

    camp_html_add_msg($translator->trans("$1 articles successfully imported.", array('$1' => $articleCount), 'articles'), "ok");
}


// Gets all issues
$allIssues = array();
if ($f_publication_id > 0) {
    $allIssues = Issue::GetIssues($f_publication_id, $f_article_language_id, null, null, null, false, array("LIMIT" => 300, "ORDER BY" => array("Number" => "DESC")), true);
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
    $allSections = Section::GetSections($f_publication_id, $f_issue_number, $f_article_language_id, null, null, array("ORDER BY" => array("Number" => "DESC")), true);
    // Automatically selects the section if there is only one
    if (count($allSections) == 1) {
        $tmpSection = camp_array_peek($allSections);
        $f_section_number = $tmpSection->getSectionNumber();
    }
}

$crumbs = array();
$crumbs[] = array($translator->trans("Actions"), "");
$crumbs[] = array($translator->trans("Import XML", array(), 'articles'), "");
echo camp_html_breadcrumbs($crumbs);

?>

<?php camp_html_display_msgs(); ?>

<p>
<form name="import_archive" enctype="multipart/form-data" method="POST" action="/<?php echo $ADMIN; ?>/articles/la_import.php" onsubmit="return <?php camp_html_fvalidate(); ?>;">
<?php echo SecurityToken::FormParameter(); ?>

<table border="0" cellspacing="0" cellpadding="0" class="box_table">
<tr>
  <td colspan="2">
    <b><?php echo $translator->trans("Import XML", array(), 'articles'); ?></b>
    <hr noshade size="1" color="black">
  </td>
</tr>
<tr>
  <td valign="top">
    <table>
    <tr>
      <td align="right"><?php echo $translator->trans("Article Type", array(), 'articles'); ?>:</td>
      <td>
        <select name="f_article_type" id="f_article_type" class="input_select" alt="select" emsg="<?php echo $translator->trans('You must select an article type.', array(), 'articles'); ?>">
        <option value=""><?php echo $translator->trans('---Select article type---', array(), 'articles'); ?></option>
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
      <td align="right"><?php echo $translator->trans("Language"); ?>:</td>
      <td>
        <select name="f_article_language_id" id="f_article_language_id" class="input_select" alt="select" emsg="<?php echo $translator->trans('You must select an article language.', array(), 'articles'); ?>" onchange="if (this.options[this.selectedIndex].value != <?php p($f_article_language_id); ?>) {this.form.submit();}">
        <option value=""><?php echo $translator->trans('---Select language---'); ?></option>
        <?php
        foreach ($allLanguages as $language) {
            camp_html_select_option($language->getLanguageId(), $f_article_language_id, $language->getNativeName());
        }
        ?>
        </select>
      </td>
    </tr>
    <tr>
      <td align="right"><?php echo $translator->trans("Publication"); ?>:</td>
      <td>
        <?php if ($f_article_language_id > 0 && count($allPublications) > 1) { ?>
        <select name="f_publication_id" id="f_publication_id" class="input_select" alt="select" emsg="<?php echo $translator->trans('You must select a publication.', array(), 'articles') ?>" onchange="if (this.options[this.selectedIndex].value != <?php p($f_publication_id); ?>) {this.form.submit();}">
        <option value=""><?php echo $translator->trans('---Select publication---'); ?></option>
        <?php
        foreach ($allPublications as $publication) {
            camp_html_select_option($publication->getPublicationId(), $f_publication_id, $publication->getName());
        }
        ?>
        </select>
        <?php } else { ?>
        <select class="input_select" disabled><option><?php echo $translator->trans('No publications'); ?></option></select>
        <?php } ?>
      </td>
    </tr>
    <tr>
      <td align="right"><?php echo $translator->trans("Issue"); ?>:</td>
      <td>
        <?php if (($f_publication_id > 0) && (count($allIssues) >= 1)) { ?>
        <select name="f_issue_number" id="f_issue_number" class="input_select" onchange="if (this.options[this.selectedIndex].value != <?php p($f_issue_number); ?>) { this.form.submit(); }">
        <option value="0"><?php echo $translator->trans('---Select issue---'); ?></option>
        <?php
            foreach ($allIssues as $issue) {
                camp_html_select_option($issue->getIssueNumber(), $f_issue_number, $issue->getName());
            }
        ?>
        </select>
        <?php } else { ?>
        <select class="input_select" disabled><option><?php echo $translator->trans('No issues'); ?></option></select>
        <?php } ?>
        &nbsp;
        (<?php echo $translator->trans('Optional', array(), 'articles'); ?>)
      </td>
    </tr>
    <tr>
      <td align="right"><?php echo $translator->trans("Section"); ?>:</td>
      <td>
        <?php if (($f_issue_number > 0) && (count($allSections) >= 1)) { ?>
        <select name="f_section_number" id="f_section_number" class="input_select">
        <option value=""><?php echo $translator->trans('---Select section---'); ?></option>
        <?php
            foreach ($allSections as $section) {
                camp_html_select_option($section->getSectionNumber(), $f_section_number, $section->getName());
            }
        ?>
        </select>
        <?php } else { ?>
        <select class="input_select" disabled><option><?php echo $translator->trans('No sections'); ?></option></select>
        <?php } ?>
        &nbsp;
        (<?php echo $translator->trans('Optional', array(), 'articles'); ?>)
      </td>
    </tr>
    <tr>
      <td align="right"><?php echo $translator->trans("Overwrite articles with matching names", array(), 'articles'); ?>?:</td>
      <td>
        <input type="radio" name="f_overwrite_articles" value="Y" <?php if ($f_overwrite_articles == 'Y') p("checked"); ?> /> <?php echo $translator->trans("Yes"); ?>
        <input type="radio" name="f_overwrite_articles" value="N" <?php if ($f_overwrite_articles == 'N' || $f_overwrite_articles == '') p("checked"); ?> /> <?php echo $translator->trans("No"); ?>
      </td>
    </tr>
    <tr>
      <td align="right"><?php echo $translator->trans("Input File", array(), 'articles'); ?>:</td>
      <td>
        <input type="file" name="f_input_file" id="f_input_file" size="40" class="input_text" alt="file|xml|0" emsg="<?php echo $translator->trans('You must select a XML input file.', array(), 'articles'); ?>" />
      </td>
    </tr>
    </table>
  </td>
</tr>
<tr>
  <td colspan="2" align="center">
    <hr noshade size="1" color="black">
    <input type="submit" name="f_save" value="<?php echo $translator->trans('Save'); ?>" class="button" />
  </td>
</tr>
</table><br />

<?php if ( isset($errorMessages) && sizeof($errorMessages) > 0) { ?>
<table border="0" cellspacing="0" cellpadding="0" class="box_table">
<tr>
  <td>
    <b><?php echo $translator->trans("Error List", array(), 'articles'); ?></b>
    <hr noshade size="1" color="black">
  </td>
</tr>
<tr>
  <td>
    <?php
    foreach ($errorMessages as $key => $errors) {
        for ($i = 0; $i < sizeof($errors); $i++) {
	    print($errors[$i]);
	}
    }
    ?>
  </td>
</tr>
</table>
<?php } ?>

<?php camp_html_copyright_notice(); ?>
