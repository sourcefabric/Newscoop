<?php
$inEditMode = ($f_edit_mode == 'edit');
$inViewMode = ($f_edit_mode == 'view');

if ($articleObj->userCanModify($g_user)) {
    $switchModeUrl = camp_html_article_url($articleObj, $f_language_id, 'edit.php')
        . '&f_edit_mode=' . ( ($inEditMode) ? 'view' : 'edit');
}

// Get URL to the frontend version of the article
if (isset($publicationObj) && $publicationObj->getUrlTypeId() == 2 && $articleObj->isPublished()) {
    $liveLinkURL = ShortURL::GetURL($publicationObj->getPublicationId(),
        $articleObj->getLanguageId(), null, null, $articleObj->getArticleNumber());
    $doLiveLink = TRUE;
    if (PEAR::isError($liveLinkURL)) {
        $doLiveLink = FALSE;
    }
}
?>
  <!-- BEGIN Article Title and Saving buttons bar //-->                            
  <div class="toolbar clearfix">
  <?php if ($inEditMode) { ?>
    <input class="top-input" name="f_article_title" id="f_article_title" type="text"
      value="<?php print htmlspecialchars($articleObj->getTitle()); ?>" onkeyup="buttonEnable('save_f_article_title');" <?php print $spellcheck ?> />
  <?php } else { ?>
    <span class="article-title"><?php print wordwrap(htmlspecialchars($articleObj->getTitle()), 80, '<br />'); ?></span>
  <?php } ?>
    <span class="comments"><?php p(count($comments)); ?></span>
    <div class="save-button-bar">
      <input type="button" onclick="makeRequest('all');" class="save-button" value="<?php putGS('Save All'); ?>" id="save" name="save" />
      <input type="submit" class="save-button" value="<?php putGS('Save and Close'); ?>" id="save_and_close" name="save_and_close" />
    </div>
    <div class="top-button-bar">
      <input type="button" name="edit" value="<?php putGS('Edit'); ?>" <?php if ($inEditMode) {?> disabled="disabled" class="default-button disabled"<?php } else { ?> onclick="location.href='<?php p($switchModeUrl); ?>';" class="default-button"<?php } ?> />
      <input type="button" name="edit" value="<?php putGS('View'); ?>" <?php if ($inViewMode) {?> disabled="disabled" class="default-button disabled"<?php } else { ?> onclick="location.href='<?php p($switchModeUrl); ?>';" class="default-button"<?php } ?> />
      <?php if ($doLiveLink) { ?>
	  <a class="ui-state-default icon-button" target="_blank" href="<?php echo $liveLinkURL; ?>"><span class="ui-icon ui-icon-extlink"></span><?php putGS('Go to live article'); ?></a>
	  <?php } ?>
    </div>
  </div>
  <!-- END Article Title and Saving buttons bar //-->
</div>

<div class="wrapper">
  <!-- BEGIN Info/Messaging bar //-->
  <div class="info-bar">
	<span class="info-text" id="info-text"></span>
  </div>
  <!-- END Infor/Messaging bar //-->

  <!-- START Side bar //-->
  <div class="sidebar">
  <?php if ($articleObj->getWorkflowStatus() != 'N') { ?>
      <!-- BEGIN Scheduled Publishing table -->
      <?php require('edit_main_box.php'); ?>
      <!-- END Scheduled Publishing table -->
      <?php } ?>

      <!-- BEGIN Geo-locations table -->
      <?php require('edit_locations_box.php'); ?>
      <!-- END Geo-locations table -->

      <!-- BEGIN Topics table -->
      <?php require('edit_topics_box.php'); ?>
      <!-- END Topics table -->

      <!-- BEGIN Switches table -->
      <?php require('edit_switches_box.php'); ?>
      <!-- END Switches table -->

      <!-- BEGIN Info table -->
      <?php require('edit_info_box.php'); ?>
      <!-- END Info table -->

      <!-- BEGIN Media table -->
      <?php require('edit_media_box.php'); ?>
      <!-- END Images table -->

      <?php if (SystemPref::Get("UseCampcasterAudioclips") == 'Y') { ?>
      <!-- BEGIN Audioclips table -->
      <?php // require('edit_audioclips_box.php'); ?>
      <!-- END Audioclips table -->
      <?php } ?>

      <?php // CampPlugin::PluginAdminHooks(__FILE__); ?>

  </div>
  <script type="text/javascript">
  $(document).ready(function() {
    $('.sidebar .articlebox').each(function() {
        var box = $(this);
        var title = box.attr('title');

        // main classes
        box.addClass('ui-widget-content small-block block-shadow');

        // wrap content
        $('> *', box).wrapAll('<div class="padded clearfix" />');

        // wrap header
        var header = $('<div class="collapsible" />').prependTo(box);
        $('<h3><span class="ui-icon"></span><a href="#" tabindex="-1">'+title+'</a></h3>')
            .addClass('head ui-accordion-header ui-helper-reset ui-state-default ui-widget')
            .appendTo(header);
    });

    // init tabs
    $('.tabs .padded').tabs();
  });
  </script>
  <!-- END Side bar //-->

  <!-- START Main form //-->
  <div class="main-content-wrapper">
    <div class="ui-widget-content big-block block-shadow padded-strong">
      <fieldset class="plain">
      <!-- BEGIN Authors //-->
      
      <!-- END Authors //-->

      <!-- BEGIN Dates //-->
      <ul>
        <li>
          <label>Date</label>
          <?php if ($articleObj->isPublished()) { ?>
          <div class="text-container left-floated date-published"><strong><?php putGS('Published'); ?>:</strong> <?php print htmlspecialchars($articleObj->getCreationDate()); ?>
            <?php if ($inEditMode) { ?><a id="f_trigger_c" class="calendar-button" href="#">&nbsp;</a><?php } ?></div>
          <?php } ?>
          <div class="text-container left-floated date-created"><?php putGS('Created'); ?>: <?php print htmlspecialchars($articleObj->getCreationDate()); ?>
            <?php if ($inEditMode) { ?><a id="f_trigger_c" class="calendar-button" href="#">&nbsp;</a><?php } ?></div>
          <div class="text-container left-floated date-changed"><span class="date-last-modified" id="date-last-modified"></span></div>
        </li>
      </ul>
      <!-- BEGIN Dates //-->
      </fieldset>
      <fieldset class="plain">
        <ul>
        <?php
        foreach ($dbColumns as $dbColumn) {
            // Single line text fields
            if ($dbColumn->getType() == ArticleTypeField::TYPE_TEXT) {
        ?>
          <li>
            <label><?php echo htmlspecialchars($dbColumn->getDisplayName()); ?></label>
            <?php
            if ($inEditMode) {
                $fCustomFields[] = $dbColumn->getName();
            ?>
            <input name="<?php echo $dbColumn->getName(); ?>"
              id="<?php echo $dbColumn->getName(); ?>"
              type="text"
              size="45"
              value="<?php print htmlspecialchars($articleData->getProperty($dbColumn->getName())); ?>"
              class="input_text"
              autocomplete="off"
              style="width:532px;"
              <?php print $spellcheck ?> />
            <?php } else {
                print htmlspecialchars($articleData->getProperty($dbColumn->getName()));
            }
            ?>
          </li>
        <?php
            } elseif ($dbColumn->getType() == ArticleTypeField::TYPE_DATE) {
                // Date fields
                if ($articleData->getProperty($dbColumn->getName()) == '0000-00-00') {
                    $articleData->setProperty($dbColumn->getName(), 'CURDATE()', TRUE, TRUE);
                }
        ?>
          <li>
            <label><?php echo htmlspecialchars($dbColumn->getDisplayName()); ?></label>
            <?php
            if ($inEditMode) {
                $fCustomFields[] = $dbColumn->getName();
            ?>
            <input name="<?php echo $dbColumn->getName(); ?>"
              id="<?php echo $dbColumn->getName(); ?>"
              type="text"
              value="<?php echo htmlspecialchars($articleData->getProperty($dbColumn->getName())); ?>"
              class="input_text datepicker"
              size="11"
              maxlength="10"
            <?php } else { ?>
            <span style="padding-left: 4px; padding-right: 4px; padding-top: 1px; padding-bottom: 1px; border: 1px solid #888; margin-right: 5px; background-color: #EEEEEE;"><?php echo htmlspecialchars($articleData->getProperty($dbColumn->getName())); ?></span>
            <?php
            }
            ?>
            <?php putGS('YYYY-MM-DD'); ?>
          </li>
        <?php
            } elseif ($dbColumn->getType() == ArticleTypeField::TYPE_BODY) {
                // Multiline text fields
                // Transform Campsite-specific tags into editor-friendly tags.
                $unparsedText = $articleData->getProperty($dbColumn->getName());
                $text = parseTextBody($unparsedText, $f_article_number);
        ?>
          <li>
            <label><?php echo htmlspecialchars($dbColumn->getDisplayName()); ?></label>
            <div class="tinyMCEHolder">
            <?php
            if ($inEditMode) {
                $textAreaId = $dbColumn->getName() . '_' . $f_article_number;
                $fCustomTextareas[] = $textAreaId;
            ?>
              <textarea name="<?php print($textAreaId); ?>"
                id="<?php print($textAreaId); ?>" class="tinymce"
                rows="20" cols="70"><?php print $text; ?></textarea>
            <?php } else { ?>
              <?php p($text); ?>
            <?php } ?>
          </li>
        <?php
            } elseif ($dbColumn->getType() == ArticleTypeField::TYPE_TOPIC) {
                $articleTypeField = new ArticleTypeField($articleObj->getType(),
                                                         substr($dbColumn->getName(), 1));
                $rootTopicId = $articleTypeField->getTopicTypeRootElement();
                $rootTopic = new Topic($rootTopicId);
                $subtopics = Topic::GetTree($rootTopicId);
                $articleTopicId = $articleData->getProperty($dbColumn->getName());
        ?>
          <li>
            <label><?php echo htmlspecialchars($dbColumn->getDisplayName()); ?></label>
                <?php if (count($subtopics) == 0) { ?>
                <?php putGS('No subtopics available'); ?>
                <?php } else { ?>
                    <select class="input_select" name="<?php echo $dbColumn->getName(); ?>" id="<?php echo $dbColumn->getName(); ?>" <?php if ($f_edit_mode != "edit") { ?>disabled<?php } ?> onchange="buttonEnable('save_<?php p($dbColumn->getName()); ?>');">
                    <option value="0"></option>
                    <?php
                    $TOL_Language = camp_session_get('TOL_Language', 'en');
                    $currentLanguage = new Language($TOL_Language);
                    $currentLanguageId = $currentLanguage->getLanguageId();
                    foreach ($subtopics as $topicPath) {
                        $printTopic = array();
                        foreach ($topicPath as $topicId => $topic) {
                            $translations = $topic->getTranslations();
                            if (array_key_exists($currentLanguageId, $translations)) {
                                $currentTopic = $translations[$currentLanguageId];
                            } elseif ( ($currentLanguageId != 1) && array_key_exists(1, $translations)) {
                                $currentTopic = $translations[1];
                            } else {
                                $currentTopic = end($translations);
                            }
                            $printTopic[] = $currentTopic;
                        }
                        camp_html_select_option($topicId, $articleTopicId, implode(" / ", $printTopic));
                    }
                    ?>
                    </select>
        <?php
                }
            } elseif ($dbColumn->getType() == ArticleTypeField::TYPE_NUMERIC) {
        ?>
          <li>
            <label><?php echo htmlspecialchars($dbColumn->getDisplayName()); ?></label>
            <input type="text" class="input_text" size="20" maxlength="20" <?php print $spellcheck ?>
              name="<?php echo $dbColumn->getName(); ?>"
              id="<?php echo $dbColumn->getName(); ?>"
              value="<?php echo htmlspecialchars($articleData->getProperty($dbColumn->getName())); ?>"
              <?php if ($inViewMode) { ?>disabled<?php } ?> />
          </li>
        <?php
            }
        }
        ?>
      </ul>
      </fieldset>
    </div>

    <!-- BEGIN Comments //-->
    <div class="ui-widget-content big-block block-shadow">
      <div class="collapsible">
        <h3 class="head ui-accordion-header ui-helper-reset ui-state-default ui-widget">
        <span class="ui-icon"></span>
        <a href="#" tabindex="-1"><?php putGS('Comments'); ?></a></h3>
      </div>
      <div class="padded-strong">
      <?php if ($inEditMode && $showCommentControls) { ?>
        <fieldset class="frame">
	       <input type="radio" name="f_comment_status" class="input_radio" id="f_comment_status" checked onchange=""><label for="comments_enabled" class="inline-style left-floated" style="padding-right:15px;"><?php putGS('Enabled'); ?></label>
           <input type="radio" name="f_comment_status" class="input_radio" id="f_comment_status" onchange=""><label for="comments_disabled" class="inline-style left-floated" style="padding-right:15px;"><?php putGS('Disabled'); ?></label>
           <input type="radio" name="f_comment_status" class="input_radio" id="f_comment_status" onchange=""><label for="comments_disabled" class="inline-style left-floated"><?php putGS('Locked'); ?></label>
        </fieldset>
      <?php } ?>
      <?php
      if ($showComments && $f_show_comments) {
          include('comments/show_comments.php');
      }
      ?>
      </div>
    </div>
    <?php if ($inEditMode && $showComments && $f_show_comments) { ?>
    <div class="ui-widget-content big-block block-shadow padded-strong">
      <?php include('comments/add_comment_form.php'); ?>
    </div>
    <?php } ?>
    <!-- END Comments //-->
  </div>
  <!-- END Main form //-->
