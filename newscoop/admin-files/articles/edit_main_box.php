<?php
$translator = \Zend_Registry::get('container')->getService('translator');
// set language url
$languageUrl = implode('&', array(
    "edit.php?f_publication_id=$f_publication_id",
    "f_issue_number=$f_issue_number",
    "f_section_number=$f_section_number",
    "f_article_number=$f_article_number",
    "f_language_id={{language}}",
    "f_language_selected={{language}}",
));

?>

<!-- BEGIN the article control bar -->
<div class="ui-widget-content small-block block-shadow highlight-block padded">
<form name="article_actions" action="/<?php echo $ADMIN; ?>/articles/do_article_action.php" method="POST">
<?php echo SecurityToken::FormParameter(); ?>
<input type="hidden" name="f_publication_id" id="f_publication_id" value="<?php p($f_publication_id); ?>" />
<input type="hidden" name="f_issue_number" id="f_issue_number" value="<?php p($f_issue_number); ?>" />
<input type="hidden" name="f_section_number" id="f_section_number" value="<?php p($f_section_number); ?>" />
<input type="hidden" name="f_language_id" id="f_language_id" value="<?php p($f_language_id); ?>" />
<input type="hidden" name="f_language_selected" id="f_language_selected" value="<?php p($f_language_selected); ?>" />
<input type="hidden" name="f_article_number" id="f_article_number" value="<?php p($f_article_number); ?>" />
<script type="text/javascript">
function action_selected(dropdownElement)
{
    if (!checkChanged()) {
        return false;
    }

    // Get the index of the "delete" option.
    deleteOptionIndex = -1;
    for (var index = 0; index < dropdownElement.options.length; index++) {
        if (dropdownElement.options[index].value == "delete") {
            deleteOptionIndex = index;
        }
    }

    // if the user has selected the "delete" option
    if (dropdownElement.selectedIndex == deleteOptionIndex) {
        ok = confirm("<?php echo $translator->trans("Are you sure you want to delete this article?", array(), 'articles'); ?>");
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

function change_language(select)
{
    if (!checkChanged()) {
        return false;
    }
    
    var languageUrl = '<?php p($languageUrl); ?>';
    
    var dest = languageUrl.replace(/\{\{language\}\}/gi, select.options[select.selectedIndex].value);
    window.location.href = dest;
}
</script>

  <fieldset class="plain">
    <!-- BEGIN Language -->
    <?php if (sizeof($articleLanguages) > 1) { ?>
    <select name="f_language_selected" class="input_select right-floated" onchange="change_language(this);">
    <?php
    foreach ($articleLanguages as $articleLanguage) {
        camp_html_select_option($articleLanguage->getLanguageId(), $f_language_selected, htmlspecialchars($articleLanguage->getNativeName()));
    }
    ?>
    </select>
  <?php
  } else {
      $articleLanguage = camp_array_peek($articleLanguages);
      echo '<strong class="right-floated" id="article_language">'.htmlspecialchars($articleLanguage->getNativeName()).'</strong>';
  }
  ?>
    <label for="f_action_language" class="inline-style right-floated" style="width:80px;"><?php echo $translator->trans('Language'); ?></label>
    <!-- END Language -->

    <?php if (empty($userIsBlogger)) { ?>
    <!-- BEGIN Actions -->
    <select name="f_action" class="input_select" onchange="action_selected(this);" style="margin-bottom:2px;">
      <option value=""><?php echo $translator->trans("Actions"); ?>...</option>
      <option value=""></option>
      <?php if ($articleObj->userCanModify($g_user) && $articleObj->isLocked()) { ?>
      <option value="unlock"><?php echo $translator->trans('Unlock'); ?></option>
      <?php } ?>

      <?php if (!$locked && $g_user->hasPermission('DeleteArticle')) { ?>
      <option value="delete"><?php echo $translator->trans('Delete'); ?></option>
      <?php } ?>

      <?php if ($g_user->hasPermission('AddArticle')) { ?>
      <option value="copy"><?php echo $translator->trans('Duplicate'); ?></option>
      <?php } ?>

      <?php if ($g_user->hasPermission('TranslateArticle')) { ?>
      <option value="translate"><?php echo $translator->trans('Translate'); ?></option>
      <?php } ?>

      <?php if (!$locked && $g_user->hasPermission('MoveArticle')) { ?>
      <option value="move"><?php echo $translator->trans('Move'); ?></option>
      <?php } ?>
    </select>
    <!-- END Actions -->
    <?php } ?>

    <!-- BEGIN Workflow -->
    <?php if ($g_user->hasPermission('Publish')) { ?>
    <select name="f_action_workflow" class="input_select" id="f_action_workflow"
      onchange="return checkChanged() && this.form.submit();" <?php if ($locked) { ?>disabled="disabled"<?php } ?>>
    <?php
    if (!isset($issueObj)) {
        camp_html_select_option('Y', $articleObj->getWorkflowStatus(), $translator->trans('Publish'));
    } elseif ($issueObj->isPublished()) {
        camp_html_select_option('Y', $articleObj->getWorkflowStatus(), $translator->trans('Status') . ': ' . $translator->trans('Published'));
    } else {
        camp_html_select_option('M', $articleObj->getWorkflowStatus(), $translator->trans('Status') . ': ' . $translator->trans('Publish with issue'));
    }
    camp_html_select_option('S', $articleObj->getWorkflowStatus(), $translator->trans('Status') . ': ' . $translator->trans('Submitted'));
    camp_html_select_option('N', $articleObj->getWorkflowStatus(), $translator->trans('Status') . ': ' . $translator->trans('New'));
    ?>
    </select>
    <?php } elseif ($articleObj->userCanModify($g_user) && ($articleObj->getWorkflowStatus() != 'Y')) { ?>
    <select name="f_action_workflow" class="input_select" id="f_action_workflow"
      onchange="return checkChanged() && this.form.submit();" <?php if ($locked) { ?>disabled="disabled"<?php } ?>>
    <?php
    camp_html_select_option('S', $articleObj->getWorkflowStatus(), $translator->trans('Status') . ': ' . $translator->trans('Submitted'));
    camp_html_select_option('N', $articleObj->getWorkflowStatus(), $translator->trans('Status') . ': ' . $translator->trans('New'));
    ?>
    </select>
    <?php } else {
        switch ($articleObj->getWorkflowStatus()) {
            case 'Y':
                echo $translator->trans('Status') . ': ' . $translator->trans('Published');
                break;
            case 'M':
                echo $translator->trans('Status') . ': ' . $translator->trans('Publish with issue');
                break;
            case 'S':
                echo $translator->trans('Status') . ': ' . $translator->trans('Submitted');
                break;
            case 'N':
                echo $translator->trans('Status') . ': ' . $translator->trans('New');
                break;
        }
    }
    ?>
    <!-- END Workflow -->
  </fieldset>
  <?php
  if( $articleObj->getWorkflowStatus() != 'N' ) {
      require_once('edit_schedule_box.php');
  }
  ?>
</form>
</div>
