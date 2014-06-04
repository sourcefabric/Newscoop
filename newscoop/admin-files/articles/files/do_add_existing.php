<?php
require_once($GLOBALS['g_campsiteDir'].'/classes/Article.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Attachment.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/ArticleAttachment.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Translation.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Input.php');

$translator = \Zend_Registry::get('container')->getService('translator');

if (!SecurityToken::isValid()) {
    camp_html_add_msg($translator->trans('Invalid security token!'));
?>
<script type="text/javascript">
parent.$.fancybox.reload = true;
parent.$.fancybox.close();
</script>
<?php
	exit;
}

$f_language_selected = Input::Get('f_language_selected', 'int', 0);
$f_article_number = Input::Get('f_article_number', 'int', 0);
$articleObj = new Article($f_language_selected, $f_article_number);
if (!$articleObj->exists()) {
	camp_html_display_error($translator->trans("Article does not exist."), null, true);
	exit;
}

foreach ((array) $_POST['item'] as $attachmentId) {
    ArticleAttachment::AddFileToArticle((int) $attachmentId, $articleObj->getArticleNumber());
}
$cacheService = \Zend_Registry::get('container')->getService('newscoop.cache');
$cacheService->clearNamespace('attachments');
$logtext = $translator->trans('$1 file/s attached to article', array('$1' => sizeof($_POST['item'])), 'article_files');
Log::ArticleMessage($articleObj, $logtext, null, 38, TRUE);

?>
<script type="text/javascript">
try {
    parent.$.fancybox.reload = true;
    parent.$.fancybox.message = '<?php echo $translator->trans('Files attached.', array(), 'article_files') ?>';
    parent.$.fancybox.close();
} catch (e) {}
</script>

<?php exit; ?>

