<?php
$translator = \Zend_Registry::get('container')->getService('translator');
global $articleObj, $f_article_number, $f_edit_mode, $g_user;
?>

<div class="articlebox" title="<?php echo $translator->trans('Polls', array(), 'plugin_poll'); ?>">

<?php if (($f_edit_mode == "edit") && $g_user->hasPermission('plugin_poll')) {  ?>
<a class="iframe ui-state-default icon-button right-floated" href="<?php p("/$ADMIN/poll/assign_popup.php?f_poll_item=article&amp;f_language_id={$articleObj->getLanguageId()}&amp;f_article_nr=$f_article_number"); ?>"><span class="ui-icon ui-icon-plusthick"></span><?php echo $translator->trans('Attach'); ?></a>
<div class="clear"></div>
<?php } ?>

<ul class="block-list">
<?php foreach (PollArticle::getAssignments(null, $articleObj->getLanguageId(), $articleObj->getArticleNumber()) as $pollArticle) {
    $poll = $pollArticle->getPoll($articleObj->getLanguageId());
?>
<li><?php echo htmlspecialchars($poll->getName()), ' (', htmlspecialchars($poll->getLanguageName()), ')'; ?></li>
<?php } ?>
</ul>

</div>
