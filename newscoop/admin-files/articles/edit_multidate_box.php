<?php if (empty($userIsBlogger)) { ?>
<div class="articlebox" title="<?php putGS('Multi date events'); ?>"><div>
<div id="multidate_box">
<style type="text/css">
/*
<?php foreach($multiDatesFields as $one_multi_date_field) { $one_multi_date_field = substr($one_multi_date_field, 1); ?>
.multidate_item_field_<?php echo $one_multi_date_field; ?> {
    background-color:<?php $field_obj = new ArticleTypeField($articleObj->getType(), $one_multi_date_field); echo $field_obj->getColor(); ?>;
}
<?php } ?>
*/
</style>
    <div id="multiDateEventList" style="display:block; padding-bottom:8px;">

    </div>

    <?php if ($inEditMode && $g_user->hasPermission('ChangeArticle')) : ?>
        <a class="iframe ui-state-default icon-button right-floated"
        href="<?php echo camp_html_article_url($articleObj, $f_language_id, "multidate/popup.php", "", ""); ?>">
        <span class="ui-icon ui-icon-pencil"></span><?php putGS('Edit'); ?></a>
    <?php endif; ?>
</div>
</div></div>
<?php } ?>
