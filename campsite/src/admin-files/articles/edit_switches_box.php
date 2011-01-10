<div class="ui-widget-content small-block block-shadow">
  <div class="collapsible">
    <h3 class="head ui-accordion-header ui-helper-reset ui-state-default ui-widget">
    <span class="ui-icon"></span>
    <a href="#" tabindex="-1"><?php putGS('Switches'); ?></a></h3>
  </div>
  <div class="padded">
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
        <input type="button" class="default-button right-floated clear-margin next-to-field" value="<?php putGS('Save'); ?>" />
      </li>
    </ul>
  </div>
</div>
