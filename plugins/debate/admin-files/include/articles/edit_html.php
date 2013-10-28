<?php
$translator = \Zend_Registry::get('container')->getService('translator');
?>

<div class="articlebox" title="<?php echo $translator->trans('Debates', array(), 'plugin_debate'); ?>">

<?php if (($f_edit_mode == "edit") && $g_user->hasPermission('plugin_debate_admin')) {  ?>
<a class="iframe ui-state-default icon-button right-floated" href="<?php p("/$ADMIN/debate/assign_popup.php?f_debate_item=article&amp;f_language_id={$articleObj->getLanguageId()}&amp;f_article_nr=".$articleObj->getArticleNumber()); ?>"><span class="ui-icon ui-icon-plusthick"></span><?php echo $translator->trans('Attach'); ?></a>
<div class="clear"></div>
<?php } ?>

<ul class="block-list">
<?php foreach (DebateArticle::getAssignments(null, $articleObj->getLanguageId(), $articleObj->getArticleNumber()) as $debateArticle) {
    $debate = $debateArticle->getDebate($articleObj->getLanguageId());
?>
<li><?php echo htmlspecialchars($debate->getName()), ' (', htmlspecialchars($debate->getLanguageName()), ')'; ?></li>
<?php } ?>
</ul>

</div>
