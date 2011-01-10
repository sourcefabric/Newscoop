<!-- BEGIN the article control bar -->
<div class="ui-widget-content small-block block-shadow highlight-block padded">
<form name="article_actions" action="do_article_action.php" method="POST">
<?php echo SecurityToken::FormParameter(); ?>
<input type="hidden" name="f_publication_id" id="f_publication_id" value="<?php p($f_publication_id); ?>" />
<input type="hidden" name="f_issue_number" id="f_issue_number" value="<?php p($f_issue_number); ?>" />
<input type="hidden" name="f_section_number" id="f_section_number" value="<?php p($f_section_number); ?>" />
<input type="hidden" name="f_language_id" id="f_language_id" value="<?php p($f_language_id); ?>" />
<input type="hidden" name="f_language_selected" id="f_language_selected" value="<?php p($f_language_selected); ?>" />
<input type="hidden" name="f_article_number" id="f_article_number" value="<?php p($f_article_number); ?>" />
<script type="text/javascript">
function action_selected(dropdownElement) {
    // Get the index of the "delete" option.
    deleteOptionIndex = -1;
    for (var index = 0; index < dropdownElement.options.length; index++) {
        if (dropdownElement.options[index].value == "delete") {
            deleteOptionIndex = index;
        }
    }

    // if the user has selected the "delete" option
    if (dropdownElement.selectedIndex == deleteOptionIndex) {
        ok = confirm("<?php putGS("Are you sure you want to delete this article?"); ?>");
        if (!ok) {
            dropdownElement.options[0].selected = true;
            return;
        }
    }

    // do the action if it isnt the first or second option
    if ((dropdownElement.selectedIndex != 0) &&  (dropdownElement.selectedIndex != 1)) {
        dropdownElement.form.submit();
    }
}
</script>
  <fieldset class="plain">
    <!-- BEGIN Language -->
  <?php
  if (count($articleLanguages) > 1) {
      $languageUrl = "edit.php?f_publication_id=$f_publication_id"
          . "&f_issue_number=$f_issue_number"
          . "&f_section_number=$f_section_number"
          . "&f_article_number=$f_article_number"
          . "&f_language_id=$f_language_id"
          . '&f_language_selected=';
  ?>
    <select name="f_language_selected" class="input_select right-floated" onchange="dest='<?php p($languageUrl); ?>'+this.options[this.selectedIndex].value; location.href=dest;">
    <?php
    foreach ($articleLanguages as $articleLanguage) {
        camp_html_select_option($articleLanguage->getLanguageId(), $f_language_selected, htmlspecialchars($articleLanguage->getNativeName()));
    }
    ?>
    </select>
  <?php
  } else {
      $articleLanguage = camp_array_peek($articleLanguages);
      echo '<strong>'.htmlspecialchars($articleLanguage->getNativeName()).'</strong>';
  }
  ?>
    <label for="f_action_language" class="inline-style right-floated" style="width:80px;"><?php putGS('Language'); ?></label>
    <!-- END Language -->

    <!-- BEGIN Actions -->
    <select name="f_action" class="input_select left-floated" onchange="action_selected(this);">
      <option value=""><?php putGS("Actions"); ?>...</option>
      <option value=""></option>
      <?php if ($articleObj->userCanModify($g_user) && $articleObj->isLocked()) { ?>
      <option value="unlock">Unlock</option>
      <?php } ?>

      <?php if ($g_user->hasPermission('DeleteArticle')) { ?>
      <option value="delete">Delete</option>
      <?php } ?>

      <?php if ($g_user->hasPermission('AddArticle')) { ?>
      <option value="copy">Duplicate</option>
      <?php } ?>

      <?php if ($g_user->hasPermission('TranslateArticle')) { ?>
      <option value="translate">Translate</option>
      <?php } ?>

      <?php if ($g_user->hasPermission('MoveArticle')) { ?>
      <option value="move">Move</option>
      <?php } ?>
    </select>
    <!-- END Actions -->

    <!-- BEGIN Workflow -->
    <?php if ($g_user->hasPermission('Publish')) { ?>
    <select name="f_action_workflow" class="input_select left-floated" onchange="this.form.submit();">
    <?php
    if (isset($issueObj) && $issueObj->isPublished()) {
        camp_html_select_option('Y', $articleObj->getWorkflowStatus(), getGS('Status') . ': ' . getGS('Published'));
    } else {
        camp_html_select_option('M', $articleObj->getWorkflowStatus(), getGS('Status') . ': ' . getGS('Publish with issue'));
    }
    camp_html_select_option('S', $articleObj->getWorkflowStatus(), getGS('Status') . ': ' . getGS('Submitted'));
    camp_html_select_option('N', $articleObj->getWorkflowStatus(), getGS('Status') . ': ' . getGS('New'));
    ?>
    </select>
    <?php } elseif ($articleObj->userCanModify($g_user) && ($articleObj->getWorkflowStatus() != 'Y')) { ?>
    <select name="f_action_workflow" class="input_select left-floated" onchange="this.form.submit();">
    <?php
    camp_html_select_option('S', $articleObj->getWorkflowStatus(), getGS('Status') . ': ' . getGS('Submitted'));
    camp_html_select_option('N', $articleObj->getWorkflowStatus(), getGS('Status') . ': ' . getGS('New'));
    ?>
    </select>
    <?php } else {
        switch ($articleObj->getWorkflowStatus()) {
            case 'Y':
                echo getGS('Status') . ': ' . getGS('Published');
                break;
            case 'M':
                echo getGS('Status') . ': ' . getGS('Publish with issue');
                break;
            case 'S':
                echo getGS('Status') . ': ' . getGS('Submitted');
                break;
            case 'N':
                echo getGS('Status') . ': ' . getGS('New');
                break;
        }
    }
    ?>
    <!-- END Workflow -->
  </fieldset>
  <?php require_once('edit_schedule_box.php'); ?>
</form>
</div>
