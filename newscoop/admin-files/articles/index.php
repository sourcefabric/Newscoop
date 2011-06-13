<?php
require_once($GLOBALS['g_campsiteDir']. "/$ADMIN_DIR/articles/article_common.php");
require_once($GLOBALS['g_campsiteDir']. '/classes/DbObjectArray.php');
require_once($GLOBALS['g_campsiteDir']. '/classes/ArticlePublish.php');
require_once($GLOBALS['g_campsiteDir']. '/classes/ArticleImage.php');
require_once($GLOBALS['g_campsiteDir']. '/classes/ArticleTopic.php');
require_once($GLOBALS['g_campsiteDir']. '/classes/SimplePager.php');

require_once LIBS_DIR . '/ArticleList/ArticleList.php';

camp_load_translation_strings("api");

$f_publication_id = Input::Get('f_publication_id', 'int', 0);
$f_issue_number = Input::Get('f_issue_number', 'int', 0);
$f_section_number = Input::Get('f_section_number', 'int', 0);
$f_language_id = Input::Get('f_language_id', 'int', 0);
if (isset($_SESSION['f_language_selected'])) {
	$f_old_language_selected = (int)$_SESSION['f_language_selected'];
} else {
	$f_old_language_selected = 0;
}
$f_language_selected = (int)camp_session_get('f_language_selected', 0);
$offsetVarName = "f_article_offset_".$f_publication_id."_".$f_issue_number."_".$f_language_id."_".$f_section_number;
$f_article_offset = camp_session_get($offsetVarName, 0);
$ArticlesPerPage = 15;

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), $_SERVER['REQUEST_URI']);
	exit;
}

if ($f_old_language_selected != $f_language_selected) {
	camp_session_set('f_article_offset', 0);
	$f_article_offset = 0;
}

if ($f_article_offset < 0) {
	$f_article_offset = 0;
}

$sectionObj = new Section($f_publication_id, $f_issue_number, $f_language_id, $f_section_number);
if (!$sectionObj->exists()) {
	camp_html_display_error(getGS('Section does not exist.'));
	exit;
}

$publicationObj = new Publication($f_publication_id);
if (!$publicationObj->exists()) {
	camp_html_display_error(getGS('Publication does not exist.'));
	exit;
}

$issueObj = new Issue($f_publication_id, $f_language_id, $f_issue_number);
if (!$issueObj->exists()) {
	camp_html_display_error(getGS('Issue does not exist.'));
	exit;
}

$allArticleLanguages = $issueObj->getLanguages();
if (!in_array($f_language_selected, DbObjectArray::GetColumn($allArticleLanguages, 'Id'))) {
	$f_language_selected = 0;
}

$sqlOptions = array("LIMIT" => array("START" => $f_article_offset,
									 "MAX_ROWS" => $ArticlesPerPage));
if ($f_language_selected) {
	// Only show a specific language.
	$totalArticles = Article::GetArticles($f_publication_id,
										  $f_issue_number,
										  $f_section_number,
										  $f_language_selected,
										  null,
										  true);
	$allArticles = Article::GetArticles($f_publication_id,
										$f_issue_number,
										$f_section_number,
										$f_language_selected,
										$sqlOptions);
	$numUniqueArticles = $totalArticles;
	$numUniqueArticlesDisplayed = count($allArticles);
} else {
	// Show articles in all languages.
	$totalArticles = Article::GetArticles($f_publication_id,
										  $f_issue_number,
										  $f_section_number,
										  null,
										  null,
										  true);
	$allArticles = Article::GetArticlesGrouped($f_publication_id,
											   $f_issue_number,
											   $f_section_number,
											   null,
											   $f_language_id,
											   $sqlOptions);
	$numUniqueArticles = Article::GetArticlesGrouped($f_publication_id,
													 $f_issue_number,
													 $f_section_number,
													 null,
													 null,
													 null,
													 true);
	$numUniqueArticlesDisplayed = count(array_unique(DbObjectArray::GetColumn($allArticles, 'Number')));
}
$numArticlesThisPage = count($allArticles);

$previousArticleNumber = 0;

$pagerUrl = "index.php?f_publication_id=".$f_publication_id
	."&f_issue_number=".$f_issue_number
	."&f_section_number=".$f_section_number
	."&f_language_id=".$f_language_id
	."&f_language_selected=".$f_language_selected."&";
$pager = new SimplePager($numUniqueArticles, $ArticlesPerPage, $offsetVarName, $pagerUrl);

$topArray = array('Pub' => $publicationObj, 'Issue' => $issueObj,
				  'Section' => $sectionObj);
camp_html_content_top(getGS('Article List') . ': ' . $sectionObj->getName(), $topArray);
?>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" class="action_buttons" style="padding-top: 5px;">
<TR>
	<TD><A HREF="/<?php echo $ADMIN; ?>/sections/?Pub=<?php p($f_publication_id); ?>&Issue=<?php p($f_issue_number); ?>&Language=<?php p($f_language_id); ?>"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/left_arrow.png" BORDER="0"></A></TD>
	<TD><A HREF="/<?php echo $ADMIN; ?>/sections/?Pub=<?php p($f_publication_id); ?>&Issue=<?php p($f_issue_number); ?>&Language=<?php p($f_language_id); ?>"><B><?php  putGS("Section List"); ?></B></A></TD>
	<?php if ($g_user->hasPermission('AddArticle')) { ?>
    <TD style="padding-left: 20px;"><A HREF="/<?php echo $ADMIN; ?>/articles/add.php?f_publication_id=<?php p($f_publication_id); ?>&f_issue_number=<?php p($f_issue_number); ?>&f_section_number=<?php p($f_section_number); ?>&f_language_id=<?php p($f_language_id); ?>" ><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/add.png" BORDER="0"></A></TD>
    <TD><A HREF="/<?php echo $ADMIN; ?>/articles/add.php?f_publication_id=<?php p($f_publication_id); ?>&f_issue_number=<?php p($f_issue_number); ?>&f_section_number=<?php p($f_section_number); ?>&f_language_id=<?php p($f_language_id); ?>" ><B><?php  putGS("Add new article"); ?></B></A></TD>
	<?php  } ?>
</tr>
</TABLE>
<?php
    $articlelist = new ArticleList();

    $articlelist->setPublication($f_publication_id);
    $articlelist->setIssue($f_issue_number);
    $articlelist->setSection($f_section_number);
    $articlelist->setLanguage($f_language_id);

    $articlelist->setColVis(TRUE);
    if ($g_user->hasPermission('Publish')) {
    	$articlelist->setOrder(TRUE);
    }
    $articlelist->setSearch(TRUE);


    $articlelist->renderActions();
    $articlelist->render();
?>

<?php camp_html_copyright_notice(); ?>
</body>
</html>
