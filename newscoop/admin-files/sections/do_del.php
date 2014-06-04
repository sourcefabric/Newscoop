<?php
require_once($GLOBALS['g_campsiteDir']. "/$ADMIN_DIR/sections/section_common.php");
require_once($GLOBALS['g_campsiteDir']. "/classes/Article.php");
require_once($GLOBALS['g_campsiteDir']. "/classes/Subscription.php");

$translator = \Zend_Registry::get('container')->getService('translator');

if (!SecurityToken::isValid()) {
    camp_html_display_error($translator->trans('Invalid security token!'));
    exit;
}

if (!$g_user->hasPermission('DeleteSection')) {
	camp_html_display_error($translator->trans('You do not have the right to delete sections.', array(), 'sections'));
	exit;
}
$f_publication_id = Input::Get('f_publication_id', 'int', 0);
$f_issue_number = Input::Get('f_issue_number', 'int', 0);
$f_language_id= Input::Get('f_language_id', 'int', 0);
$f_section_number = Input::Get('f_section_number', 'int', 0);
$f_delete_all_section_translations = Input::Get('f_delete_all_section_translations', 'string', '', true);
$f_delete_all_articles_translations = Input::Get('f_delete_all_articles_translations', 'string', '', true);
$f_delete_subscriptions = Input::Get('f_delete_subscriptions', 'string', '', true);

$publicationObj = new Publication($f_publication_id);
$issueObj = new Issue($f_publication_id, $f_language_id, $f_issue_number);
$sectionObj = new Section($f_publication_id, $f_issue_number, $f_language_id, $f_section_number);
$sectionName = $sectionObj->getName();

$articles = Article::GetArticles($f_publication_id, $f_issue_number, $f_section_number, $f_language_id);
$numArticles = count($articles);
$numSubscriptionsDeleted = 0;
$numArticlesDeleted = 0;
$f_delete_all_section_translations = ($f_delete_all_section_translations == 'Y') ? true : false;
$f_delete_all_articles_translations = ($f_delete_all_articles_translations == 'Y') ? true : false;
if ($f_delete_all_section_translations == false) {
    $numArticlesDeleted = $sectionObj->delete(true, $f_delete_all_articles_translations);
    $cacheService = \Zend_Registry::get('container')->getService('newscoop.cache');
    $cacheService->clearNamespace('section');
} else {
    $sectionTranslations = Section::GetSections($f_publication_id, $f_issue_number, null, null, $sectionObj->getName(), null);
    foreach ($sectionTranslations as $key => $sectionTranslation) {
        $numArticlesDeleted += $sectionTranslation->delete(true, $f_delete_all_articles_translations);
    }
}

if ($f_delete_subscriptions != "") {
    $numSubscriptionsDeleted = Subscription::DeleteSubscriptionsInSection($f_publication_id, $f_section_number);
}

$offsetVarName = 'SectOffs_'.$f_publication_id.'_'.$f_issue_number.'_'.$f_language_id;
$SectOffs = camp_session_get($offsetVarName, 0);
$totalSections = Section::GetTotalSections($f_publication_id, $f_issue_number, $f_language_id);
if ($SectOffs >= $totalSections) {
    camp_session_set($offsetVarName, 0);
}

$topArray = array('Pub' => $publicationObj, 'Issue' => $issueObj, 'Section' => $sectionObj);
camp_html_content_top($translator->trans('Delete section', array(), 'sections'), $topArray);
?>

<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box">
<TR>
	<TD COLSPAN="2">
		<B> <?php  echo $translator->trans("Deleting section", array(), 'sections'); ?> </B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
	   <BLOCKQUOTE>
        <LI><?php echo $translator->trans('The section $1 has been deleted.', array('$1' => '<B>'.htmlspecialchars($sectionName).'</B>'), 'sections'); ?></LI>
		<LI><?php echo $translator->trans('A total of $1 subscriptions were updated.', array('$1' => '<B>'.$numSubscriptionsDeleted.'</B>'), 'sections'); ?></LI>
		<LI><?php echo $translator->trans('A total of $1 articles were deleted.', array('$1' => '<B>'.$numArticlesDeleted.'</B>'), 'sections'); ?></LI>
        </BLOCKQUOTE>
    </TD>
</TR>
<TR>
	<TD COLSPAN="2" align="center">
        <INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php  echo $translator->trans('OK'); ?>" ONCLICK="location.href='/<?php p($ADMIN); ?>/sections/index.php?Pub=<?php  p($f_publication_id); ?>&Issue=<?php  p($f_issue_number); ?>&Language=<?php  p($f_language_id); ?>'">
	</TD>
</TR>
</TABLE>
<P>

<?php camp_html_copyright_notice(); ?>
