<?php if (empty($userIsBlogger)) { ?>
<div class="articlebox" title="<?php putGS('Multi date event'); ?>"><div>
<div id="multidate_box">
    <div id="multiDateEventList" style="display:block; padding-bottom:8px;">

    </div>

    <?php if ($inEditMode && $g_user->hasPermission('ChangeArticle')) : ?>
        <a class="iframe ui-state-default icon-button right-floated"
        href="<?php echo camp_html_article_url($articleObj, $f_language_id, "multidate/popup.php", "", "&multidatefield=".$multiDatesField); ?>">
        <span class="ui-icon ui-icon-pencil"></span><?php putGS('Edit'); ?></a>
    <?php endif; ?>
</div>
</div></div>
<?php } ?>
