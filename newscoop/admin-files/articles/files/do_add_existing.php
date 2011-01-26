<?php
camp_load_translation_strings("article_files");
require_once($GLOBALS['g_campsiteDir'].'/classes/Article.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Attachment.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/ArticleAttachment.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Translation.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Input.php');

if (!SecurityToken::isValid()) {
    camp_html_add_msg(getGS('Invalid security token!'));
?>
<script type="text/javascript">
window.close();
window.opener.location.reload();
</script>
<?php
	exit;
}

$f_language_selected = Input::Get('f_language_selected', 'int', 0);
$f_article_number = Input::Get('f_article_number', 'int', 0);
$articleObj = new Article($f_language_selected, $f_article_number);
if (!$articleObj->exists()) {
	camp_html_display_error(getGS("Article does not exist."), null, true);
	exit;
}

foreach ((array) $_POST['item'] as $attachmentId) {
    ArticleAttachment::AddFileToArticle((int) $attachmentId, $articleObj->getArticleNumber());
}
$logtext = getGS('$1 file/s attached to article', sizeof($_POST['item']));
Log::ArticleMessage($articleObj, $logtext, null, 38, TRUE);

?>
<script type="text/javascript">
try {
    parent.$.fancybox.reload = true;
    parent.$.fancybox.message = '<?php putGS('Files attached.'); ?>';
    parent.$.fancybox.close();
} catch (e) {}
</script>

<?php exit; ?>

