<div class="articlebox" title="<?php putGS('Switches'); ?>"><div>
<form id="article-switches" action="/<?php echo $ADMIN; ?>/articles/post.php" method="POST">

    <ul class="check-list padded">
      <li><input type="checkbox" name="f_on_front_page" id="f_on_front_page"
        class="input_checkbox" <?php if ($articleObj->onFrontPage()) { ?> checked<?php } ?> <?php if ($inViewMode || !$publishRights ) { ?>disabled<?php } ?> />
        <label for="f_on_front_page"><?php putGS('Show article on front page'); ?></label>
      </li>
      <li><input type="checkbox" name="f_on_section_page" id="f_on_section_page"
        class="input_checkbox" <?php if ($articleObj->onSectionPage()) { ?> checked<?php } ?> <?php if ($inViewMode || !$publishRights) { ?>disabled<?php } ?> />
        <label for="f_on_section_page"><?php putGS('Show article on section page'); ?></label>
      </li>
      <li><input type="checkbox" name="f_is_public" id="f_is_public"
        class="input_checkbox" <?php if ($articleObj->isPublic()) { ?> checked<?php } ?> <?php if ($inViewMode || !$publishRights) { ?>disabled<?php } ?> /> <label for="f_is_public"><?php putGS('Visible to non-subscribers'); ?></label> </li>
    <?php
    foreach ($dbColumns as $dbColumn) {
        // Custom switches
        if ($dbColumn->getType() == ArticleTypeField::TYPE_SWITCH) {
            $checked = $articleData->getFieldValue($dbColumn->getPrintName()) ? 'checked' : '';
    ?>
      <li>
        <input type="checkbox" name="<?php echo $dbColumn->getName(); ?>" id="<?php echo $dbColumn->getName(); ?>"
          class="input_checkbox db" value="on" <?php if ($inViewMode) { ?>disabled<?php } ?> <?php echo $checked; ?> />
        <label for="<?php echo $dbColumn->getName(); ?>"><?php echo htmlspecialchars($dbColumn->getDisplayName()); ?></label>
      </li>
    <?php
        }
    }
    if ($inEditMode) {
    ?>
      <li>
        <input type="submit" class="default-button right-floated clear-margin next-to-field" value="<?php putGS('Save'); ?>" />
      </li>
    <?php } ?>
    </ul>
    </form>

    <script type="text/javascript">
    $(document).ready(function() {
        $('form#article-switches').submit(function() {
            var form = $(this);
            var vals = {
                'setOnFrontPage': $('input[name=f_on_front_page]', form),
                'setOnSectionPage': $('input[name=f_on_section_page]', form),
                'setIsPublic': $('input[name=f_is_public]', form)
            };

            // check if saved
            if (!form.hasClass('changed')) {
                return false;
            }

            // set dynamic
            $('input.db', form).each(function() {
                var val = 'off';
                if ($(this).attr('checked')) {
                    val = 'on';
                }

                ajax_forms++;
                callServer(['ArticleData', 'setProperty'], [
                    '<?php echo $articleObj->getType(); ?>',
                    <?php echo $articleObj->getArticleNumber(); ?>,
                    <?php echo $f_language_selected; ?>,
                    $(this).attr('name'), val], function(json) {
                	   flashMessage('<?php putGS('Switches saved.'); ?>');
                	    ajax_forms--;
                    });
            });

            // set static
            for (method in vals) {
                if (vals[method].attr('disabled'))
                    continue;
                ajax_forms++;
                callServer(['Article', method], [
                    <?php echo $f_language_selected; ?>,
                    <?php echo $articleObj->getArticleNumber(); ?>,
                    Number(vals[method].attr('checked'))], function(json) {
                        flashMessage('<?php putGS('Switches saved.'); ?>');
                        ajax_forms--;
                    });
            }

            form.removeClass('changed');
            return false;
        }).change(function() {
            $(this).addClass('changed');
        });
    });
    </script>
</div></div>
