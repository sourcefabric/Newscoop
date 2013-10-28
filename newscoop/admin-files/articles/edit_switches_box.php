<?php
$translator = \Zend_Registry::get('container')->getService('translator');
if (empty($userIsBlogger)) { ?>
<div class="articlebox" title="<?php echo $translator->trans('Switches', array(), 'articles'); ?>"><div>
<form id="article-switches" action="/<?php echo $ADMIN; ?>/articles/post.php" method="POST">

    <ul class="check-list padded">
      <li><input type="checkbox" name="f_on_front_page" id="f_on_front_page"
        class="input_checkbox" <?php if ($articleObj->onFrontPage()) { ?> checked<?php } ?> <?php if ($inViewMode || !$publishRights ) { ?>disabled<?php } ?> />
        <label for="f_on_front_page"><?php echo $translator->trans('Show article on front page'); ?></label>
      </li>
      <li><input type="checkbox" name="f_on_section_page" id="f_on_section_page"
        class="input_checkbox" <?php if ($articleObj->onSectionPage()) { ?> checked<?php } ?> <?php if ($inViewMode || !$publishRights) { ?>disabled<?php } ?> />
        <label for="f_on_section_page"><?php echo $translator->trans('Show article on section page'); ?></label>
      </li>
      <li><input type="checkbox" name="f_rating_enabled" id="f_rating_enabled"
        class="input_checkbox" <?php if ($articleObj->ratingEnabled()) { ?> checked<?php } ?> <?php if ($inViewMode || !$publishRights) { ?>disabled<?php } ?> />
        <label for="f_rating_enabled"><?php echo $translator->trans('Enable Rating', array(), 'articles'); ?></label>
      </li>
      <li><input type="checkbox" name="f_is_public" id="f_is_public"
        class="input_checkbox" <?php if ($articleObj->isPublic()) { ?> checked<?php } ?> <?php if ($inViewMode || !$publishRights) { ?>disabled<?php } ?> /> <label for="f_is_public"><?php echo $translator->trans('Visible to non-subscribers', array(), 'articles'); ?></label> </li>
    <?php
    foreach ($dbColumns as $dbColumn) {
        // Custom switches
        if ($dbColumn->getType() == ArticleTypeField::TYPE_SWITCH) {
            $checked = $articleData->getFieldValue($dbColumn->getPrintName()) ? 'checked' : '';
    ?>
      <li>
        <input type="checkbox" name="<?php echo $dbColumn->getName(); ?>" id="<?php echo $dbColumn->getName(); ?>"
          class="input_checkbox db" value="on" <?php if ($inViewMode) { ?>disabled<?php } ?> <?php echo $checked; ?> />
        <label for="<?php echo $dbColumn->getName(); ?>"><?php echo htmlspecialchars($dbColumn->getDisplayName($articleObj->getLanguageId())); ?></label>
      </li>
    <?php
        }
    }
    if ($inEditMode) {
    ?>
      <li>
        <input type="submit" class="default-button right-floated clear-margin next-to-field" value="<?php echo $translator->trans('Save'); ?>" />
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
                'setIsPublic': $('input[name=f_is_public]', form),
                'setRatingEnabled': $('input[name=f_rating_enabled]', form)
            };

            // check if saved
            if (!form.hasClass('changed')) {
                return false;
            }

            // set dynamic
            $('input.db', form).each(function() {
                var val = 'off';
                if ($(this).is(':checked')) {
                    val = 'on';
                }

                callServer(['ArticleData', 'setProperty'], [
                    '<?php echo $articleObj->getType(); ?>',
                    <?php echo $articleObj->getArticleNumber(); ?>,
                    <?php echo $f_language_selected; ?>,
                    $(this).attr('name'), val], function(json) {
                	   flashMessage('<?php echo $translator->trans('Switches saved.', array(), 'articles'); ?>');
                    });
            });

            // set static
            for (method in vals) {
                if (vals[method].attr('disabled'))
                    continue;
                callServer(['Article', method], [
                    <?php echo $f_language_selected; ?>,
                    <?php echo $articleObj->getArticleNumber(); ?>,
                    Number(vals[method].is(':checked'))], function(json) {
                        flashMessage('<?php echo $translator->trans('Switches saved.', array(), 'articles'); ?>');
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
<?php } ?>
