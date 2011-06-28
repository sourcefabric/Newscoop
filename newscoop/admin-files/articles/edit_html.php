<?php
/**
 * @package Newscoop
 */

// If the article is locked say so to the user
if ($articleObj->userCanModify($g_user) && $locked && !$inViewMode) {
?>
<div class="wrapper">
  <div class="main-content-wrapper">
    <div class="ui-widget-content big-block block-shadow padded-strong" style="text-align:center;">
      <h3 class="alert"><?php putGS('Article is locked'); ?></h3>
      <fieldset class="plain">
        <ul>
          <li>
          <?php
          $timeDiff = camp_time_diff_str($articleObj->getLockTime());
          if ($timeDiff['hours'] > 0) {
              putGS('The article has been locked by $1 ($2) $3 hour(s) and $4 minute(s) ago.',
                  '<b>'.htmlspecialchars($lockUserObj->getRealName()),
                  htmlspecialchars($lockUserObj->getUserName()).'</b>',
                  $timeDiff['hours'], $timeDiff['minutes']);
          } else {
              putGS('The article has been locked by $1 ($2) $3 minute(s) ago.',
                  '<b>'.htmlspecialchars($lockUserObj->getRealName()),
                  htmlspecialchars($lockUserObj->getUserName()).'</b>',
                  $timeDiff['minutes']);
          }
          ?>
          </li>
          <li>
            <input type="button" name="Yes" value="<?php putGS('Unlock'); ?>" class="button" onclick="location.href='<?php echo camp_html_article_url($articleObj, $f_language_id, "do_unlock.php", '', null, true); ?>'" />
            <input type="button" name="Yes" value="<?php putGS('View'); ?>" class="button" onclick="location.href='<?php echo camp_html_article_url($articleObj, $f_language_id, "edit.php", "", "&f_edit_mode=view"); ?>'" />
            <input type="button" name="No" value="<?php putGS('Cancel'); ?>" class="button" onclick="location.href='/<?php echo $ADMIN; ?>/articles/?f_publication_id=<?php p($f_publication_id); ?>&f_issue_number=<?php p($f_issue_number); ?>&f_language_id=<?php p($f_language_id); ?>&f_section_number=<?php p($f_section_number); ?>'" />
          </li>
        </ul>
      </fieldset>
    </div>
  </div>
</div>
<?php
    camp_html_copyright_notice();
    return;
}

// Get proper URL to switch between modes
if ($articleObj->userCanModify($g_user)) {
    $switchModeUrl = camp_html_article_url($articleObj, $f_language_id, 'edit.php')
        . '&f_edit_mode=' . ( ($inEditMode) ? 'view' : 'edit');
}

// Display either the "Go to live article" or "Preview" button
// depending on article status
$doPreviewLink = '';
if (isset($publicationObj) && $articleObj->isPublished()) {
    if ($publicationObj->getUrlTypeId() == 2) {
        $previewLinkURL = ShortURL::GetURL($publicationObj->getPublicationId(),
            $articleObj->getLanguageId(), null, null, $articleObj->getArticleNumber());
        $doPreviewLink = 'live';

        $seoFields = $publicationObj->getSeo();
        $articleEndLink = $articleObj->getSEOURLEnd($seoFields, $articleObj->getLanguageId());
        if(strlen($articleEndLink) > 0) {
            $previewLinkURL .= $articleEndLink;
        }

        if (PEAR::isError($previewLinkURL)) {
            $doLiveLink = '';
        }
    }
} else {
    if (isset($publicationObj) && (0 < $f_publication_id) && (0 < $f_issue_number) && (0 < $f_section_number))
    {
        $doPreviewLink = 'preview';
        $previewLinkURL = "/$ADMIN/articles/preview.php?f_publication_id=$f_publication_id"
            . "&f_issue_number=$f_issue_number&f_section_number=$f_section_number"
            . "&f_article_number=$f_article_number&f_language_id=$f_language_id&f_language_selected=$f_language_selected";
    }
}
?>
  <!-- BEGIN Article Title and Saving buttons bar //-->
  <div class="toolbar clearfix">
  <?php if ($inEditMode) { ?>
    <input class="top-input" name="f_article_title" id="f_article_title" type="text"
      value="<?php print htmlspecialchars($articleObj->getTitle()); ?>" <?php print $spellcheck ?> />
  <?php } elseif ($locked) {
  ?>
    <span class="article-title-locked"><?php print wordwrap(htmlspecialchars($articleObj->getTitle()), 80, '<br />'); ?></span>
  <?php } else { ?>
    <span class="article-title"><?php print wordwrap(htmlspecialchars($articleObj->getTitle()), 80, '<br />'); ?></span>
  <?php } ?>

    <span class="comments"><?php p(count(isset($comments) ? $comments : array())); ?></span>
    <div class="save-button-bar">
      <input type="submit" class="save-button" value="<?php putGS('Save All'); ?>" id="save" name="save" <?php if (!$inEditMode) { ?> disabled style="opacity: 0.3"<?php } ?> />
      <input type="submit" class="save-button" value="<?php $inEditMode ? putGS('Save and Close') : putGS('Close'); ?>" id="save_and_close" name="save_and_close" />
    </div>
    <div class="top-button-bar">
      <input type="button" name="edit" value="<?php putGS('Edit'); ?>" <?php if ($inEditMode || ! $articleObj->userCanModify($g_user)) {?> disabled="disabled" class="default-button disabled"<?php } else { ?> onclick="location.href='<?php p($switchModeUrl); ?>';" class="default-button"<?php } ?> />
      <input type="button" name="edit" value="<?php putGS('View'); ?>" <?php if ($inViewMode) {?> disabled="disabled" class="default-button disabled"<?php } else { ?> onclick="location.href='<?php p($switchModeUrl); ?>';" class="default-button"<?php } ?> />
      <?php if ($doPreviewLink == 'live') { ?>
	  <a class="ui-state-default icon-button" target="_blank" href="<?php echo $previewLinkURL; ?>"><span class="ui-icon ui-icon-extlink"></span><?php putGS('Go to live article'); ?></a>
	  <?php } elseif ($doPreviewLink == 'preview') { ?>
	  <a class="ui-state-default icon-button" href="#" onclick="window.open('<?php echo $previewLinkURL; ?>', 'fpreview', 'resizable=yes, menubar=no, toolbar=no, width=780, height=660'); return false;"><span class="ui-icon ui-icon-extlink"></span><?php putGS('Preview'); ?></a>
	  <?php } ?>
    </div>
    <div class="j-countable" id="f_article_count">&nbsp;</div>
  </div>
  <!-- END Article Title and Saving buttons bar //-->

<div class="wrapper">
  <!-- BEGIN Info/Messaging bar //-->
  <div class="info-bar">
	<span class="info-text" id="info-text"></span>
  </div>
  <!-- END Infor/Messaging bar //-->

  <!-- START Main form //-->
  <div class="main-content-wrapper">
  <form id="article-main" action="/<?php echo $ADMIN; ?>/articles/post.php" method="POST">
        <?php echo SecurityToken::formParameter(); ?>
        <?php
            $hiddens = array(
                'f_publication_id',
                'f_issue_number',
                'f_section_number',
                'f_language_id',
                'f_language_selected',
                'f_article_number',
                'f_article_title'
                );
            foreach ($hiddens as $name) {
                if (!isset($$name)) {
                    $$name = '';
                }

                echo '<input type="hidden" name="', $name;
                echo '" value="', $$name, '" />', "\n";
            }
        ?>

    <div class="ui-widget-content big-block block-shadow padded-strong">
      <fieldset class="plain">
      <!-- BEGIN Authors //-->
      <?php include_once('edit_html_authors.php'); ?>
      <!-- END Authors //-->

      <!-- BEGIN Dates //-->
      <ul>
        <li>
          <label><?php putGS('Date'); ?></label>
          <?php if ($articleObj->isPublished()) { ?>
          <div class="text-container left-floated date-published"><b><?php putGS('Published'); ?>:</b> <span class="f_publish_date"><?php print htmlspecialchars($articleObj->getPublishDate()); ?></span>
            <?php if ($inEditMode) { ?><input type="hidden" name="f_publish_date" value="<?php echo $articleObj->getPublishDate(); ?>" class="datetime" /><?php } ?></div>
          <?php } ?>
          <div class="text-container left-floated date-created"><?php putGS('Created'); ?>: <span class="f_creation_date"><?php print htmlspecialchars($articleObj->getCreationDate()); ?></span>
            <?php if ($inEditMode) { ?><input type="hidden" name="f_creation_date" value="<?php echo $articleObj->getCreationDate(); ?>" class="datetime" /><?php } ?></div>
          <div class="text-container left-floated date-changed"><span class="date-last-modified" id="date-last-modified"></span></div>
        </li>
      </ul>
      <?php if ($inEditMode) { ?>
      <script type="text/javascript">
      $(function() {
          // update displayed datetime
          $('input:hidden.datetime').change(function() {
              $('span.' + $(this).attr('name')).text($(this).val());
          }).next().css('vertical-align', 'middle')
          .css('margin-top', '-3px')
          .css('cursor', 'pointer');
      });
      </script>
      <?php } ?>
      <!-- END Dates //-->
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
              autocomplete="off"
              style="width:70%;"
              <?php if($dbColumn->getMaxSize()!=0 && $dbColumn->getMaxSize()!=''): ?>
              	maxlength="<?php echo $dbColumn->getMaxSize(); ?>"
              	class="input_text countableft"
             <?php else: ?>
              class="input_text"
             <?php endif; ?>
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
              maxlength="10" />
            <?php } else { ?>
            <span style="padding-left: 4px; padding-right: 4px; padding-top: 1px; padding-bottom: 1px; border: 1px solid #888; margin-right: 5px; background-color: #EEEEEE;"><?php echo htmlspecialchars($articleData->getProperty($dbColumn->getName())); ?></span>
            <?php
            }
            ?>
            &nbsp;<?php putGS('YYYY-MM-DD'); ?>
          </li>
        <?php
            } elseif ($dbColumn->getType() == ArticleTypeField::TYPE_BODY) {
                // Multiline text fields
                // Transform Campsite-specific tags into editor-friendly tags.
                $unparsedText = $articleData->getProperty($dbColumn->getName());
                $text = parseTextBody($unparsedText, $f_article_number);
                $editorSize = str_replace('editor_size=', '', $dbColumn->m_data['field_type_param']);
                if (!is_numeric($editorSize)) {
					require_once($GLOBALS['g_campsiteDir'].'/classes/ArticleTypeField.php');
					$editorSize = ArticleTypeField::BODY_ROWS_MEDIUM;
				}
        ?>
          <li>
            <label><?php echo htmlspecialchars($dbColumn->getDisplayName()); ?></label>
            <div class="tinyMCEHolder" style="overflow: auto; min-width: 670px; <?php echo $inEditMode ? '' : 'width: 74%'; ?>">
            <?php
            if ($inEditMode) {
                $textAreaId = $dbColumn->getName() . '_' . $f_article_number;
                $fCustomTextareas[] = $textAreaId;
            ?>
              <textarea name="<?php print($textAreaId); ?>"
                id="<?php print($textAreaId); ?>" class="tinymce"
                style="height: <?php print($editorSize); ?>px;" cols="70"><?php print $text; ?></textarea>
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
                    <select class="input_select" name="<?php echo $dbColumn->getName(); ?>" id="<?php echo $dbColumn->getName(); ?>" <?php if ($f_edit_mode != "edit") { ?>disabled<?php } ?>>
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

    <?php if ($inEditMode && $showCommentControls) { ?>
    <li>
        <label><?php putGS('Comment settings'); ?></label>
        <input type="radio" name="f_comment_status" value="enabled" class="input_radio" id="f_comment_status_enabled" <?php if ($articleObj->commentsEnabled() && !$articleObj->commentsLocked()) { ?> checked<?php } ?> />
        <label for="f_comment_status_enabled" class="inline-style left-floated" style="padding-right:15px;"><?php putGS('Enabled'); ?></label>
        <input type="radio" name="f_comment_status" value="disabled" class="input_radio" id="f_comment_status_disabled" <?php if (!$articleObj->commentsEnabled()) { ?> checked<?php } ?> />
        <label for="f_comment_status_disabled" class="inline-style left-floated" style="padding-right:15px;"><?php putGS('Disabled'); ?></label>
        <input type="radio" name="f_comment_status" value="locked" class="input_radio" id="f_comment_status_locked" <?php if ($articleObj->commentsEnabled() && $articleObj->commentsLocked()) { ?> checked<?php } ?>  />
        <label for="f_comment_status_locked" class="inline-style left-floated"><?php putGS('Locked'); ?></label>
    </li>
    <?php } ?>
      </ul>
      </fieldset>
    </div>
  </form><!-- /form#article -->

    <?php if ($showCommentControls) { ?>
    <div id="comments-list" class="ui-widget-content big-block block-shadow">
      <div class="collapsible">
        <h3 class="head ui-accordion-header ui-helper-reset ui-state-default ui-widget">
        <span class="ui-icon"></span>
        <a href="#" tabindex="-1"><?php putGS('Comments'); ?></a></h3>
      </div>
      <div class="padded-strong">
        <?php include('comments/show_comments.php'); ?>
      </div>
    </div>
    <?php } ?>

    <?php if ($inEditMode && $showCommentControls) { ?>
    <div id="comments-form" class="ui-widget-content big-block block-shadow padded-strong">
      <?php include('comments/add_comment_form.php'); ?>
    </div>
    <?php } ?>
    <!-- END Comments //-->
  </div>
  <!-- END Main form //-->

  <!-- START Side bar //-->
  <div class="sidebar">
      <!-- BEGIN Scheduled Publishing table -->
      <?php require('edit_main_box.php'); ?>
      <!-- END Scheduled Publishing table -->

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

      <?php CampPlugin::PluginAdminHooks(__FILE__); ?>

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
    $('.sidebar .tabs').each(function() {
        $(this).tabs();
        $(this).closest('.padded').addClass('inner-tabs');
    });
  });
  </script>
  <!-- END Side bar //-->

</div>
<?php camp_html_copyright_notice(); ?>
