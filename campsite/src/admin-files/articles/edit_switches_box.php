<div class="articlebox" title="<?php putGS('Switches'); ?>">
    <form id="article-switches" action="post.php" method="POST">
        <input type="hidden" name="f_language_selected" value="<?php echo $f_language_selected; ?>" />
        <input type="hidden" name="f_article_number" value="<?php echo $articleObj->getArticleNumber(); ?>" />
        <input type="hidden" name="f_save" value="switch" />

    <ul class="check-list padded">
      <li><input type="checkbox" name="f_on_front_page" id="f_on_front_page"
        class="input_checkbox" <?php if ($articleObj->onFrontPage()) { ?> checked<?php } ?> <?php if ($inViewMode) { ?>disabled<?php } ?> />
        <label for="f_on_front_page"><?php putGS('Show article on front page'); ?></label>
      </li>
      <li><input type="checkbox" name="f_on_section_page" id="f_on_section_page"
        class="input_checkbox" <?php if ($articleObj->onSectionPage()) { ?> checked<?php } ?> <?php if ($inViewMode) { ?>disabled<?php } ?> />
        <label for="f_on_section_page"><?php putGS('Show article on section page'); ?></label>
      </li>
      <li><input type="checkbox" name="f_is_public" id="f_is_public"
        class="input_checkbox" <?php if ($articleObj->isPublic()) { ?> checked<?php } ?> <?php if ($inViewMode) { ?>disabled<?php } ?> />
        <label for="f_is_public"><?php putGS('Visible to non-subscribers'); ?></label>
      </li>
    <?php
    foreach ($dbColumns as $dbColumn) {
        // Custom switches
        if ($dbColumn->getType() == ArticleTypeField::TYPE_SWITCH) {
            $checked = $articleData->getFieldValue($dbColumn->getPrintName()) ? 'checked' : '';
    ?>
      <li>
        <input type="checkbox" name="<?php echo $dbColumn->getName(); ?>" id="<?php echo $dbColumn->getName(); ?>"
          class="input_checkbox" <?php if ($inViewMode) { ?>disabled<?php } ?> <?php echo $checked; ?> />
        <label><?php echo htmlspecialchars($dbColumn->getDisplayName()); ?></label>
      </li>
    <?php
        }
    }
    ?>
      <li>
        <input type="submit" class="default-button right-floated clear-margin next-to-field" value="<?php putGS('Save'); ?>" onclick="saveSwitches(this); return false;" />
      </li>
    </ul>
    <script type="text/javascript">
    function saveSwitches(button)
    {
        var form = $(button).closest('form');
        var vals = {
            'setOnFrontPage': $('input[name=f_on_front_page]:checked', form).val(),
            'setOnSectionPage': $('input[name=f_on_section_page]:checked', form).val(),
            'setIsPublic': $('input[name=f_is_public]:checked', form).val()
        };

        for (method in vals) {
            if (vals[method] == 'on') {
                vals[method] = 1;
            } else {
                vals[method] = 0;
            }

            callServer(['Article', method], [
                <?php echo $f_language_selected; ?>,
                <?php echo $articleObj->getArticleNumber(); ?>,
                vals[method]], function(json) {
                    flashMessage('<?php putGS('Saved'); ?>');
                });
        }

        return false;
    }
    </script>
    </form>
</div>
