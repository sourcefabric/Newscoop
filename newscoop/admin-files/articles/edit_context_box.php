<?php if (empty($userIsBlogger)) { 
$translator = \Zend_Registry::get('container')->getService('translator');
?>
<div class="articlebox" title="<?php echo $translator->trans('Related Articles', array(), 'articles'); ?>"><div>
<div id="context_box">
    <div id="contextBoxArticlesList" style="display:block; padding-bottom:8px;">

    </div>

    <?php if ($inEditMode && $g_user->hasPermission('ChangeArticle')) : ?>
        <a class="iframe ui-state-default icon-button right-floated"
        href="<?php echo camp_html_article_url($articleObj, $f_language_id, "context_box/popup.php"); ?>">
        <span class="ui-icon ui-icon-pencil"></span><?php echo $translator->trans('Edit'); ?></a>
    <?php endif; ?>
</div>
</div></div>
<?php } ?>
