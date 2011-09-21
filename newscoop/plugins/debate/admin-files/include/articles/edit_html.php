<?php
camp_load_translation_strings("plugin_debate");
global $articleObj, $f_article_number, $f_edit_mode;
?>

<div class="articlebox" title="<?php putGS('Debates'); ?>">

<?php if (($f_edit_mode == "edit") && $g_user->hasPermission('plugin_debate')) {  ?>
<a class="iframe ui-state-default icon-button right-floated" href="<?php p("/$ADMIN/debate/assign_popup.php?f_debate_item=article&amp;f_language_id={$articleObj->getLanguageId()}&amp;f_article_nr=$f_article_number"); ?>"><span class="ui-icon ui-icon-plusthick"></span><?php putGS('Attach'); ?></a>
<div class="clear"></div>
<?php } ?>

<ul class="block-list">
<?php foreach (DebateArticle::getAssignments(null, $articleObj->getLanguageId(), $articleObj->getArticleNumber()) as $debateArticle) {
    $debate = $debateArticle->getDebate($articleObj->getLanguageId());
?>
<li><?php echo $debate->getName(), ' (', $debate->getLanguageName(), ')'; ?></li>
<?php } ?>
</ul>

</div>
