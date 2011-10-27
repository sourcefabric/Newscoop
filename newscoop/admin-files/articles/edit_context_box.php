<?php if (empty($userIsBlogger)) { ?>
<div class="articlebox" title="<?php putGS('Related Articles'); ?>"><div>
<div id="context_box">
    <div id="contextBoxArticlesList" style="display:block; padding-bottom:8px;">

    </div>

    <?php if ($inEditMode && $g_user->hasPermission('ChangeArticle')) : ?>
        <a class="iframe ui-state-default icon-button right-floated"
        href="<?php echo camp_html_article_url($articleObj, $f_language_id, "context_box/popup.php"); ?>">
        <span class="ui-icon ui-icon-pencil"></span><?php putGS('Edit'); ?></a>
    <?php endif; ?>
</div>
</div></div>
<?php } ?>
