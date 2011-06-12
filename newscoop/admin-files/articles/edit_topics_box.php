<div class="ui-widget-content small-block block-shadow">
  <div class="collapsible">
    <h3 class="head ui-accordion-header ui-helper-reset ui-state-default ui-widget">
    <span class="ui-icon"></span>
    <a href="#" tabindex="-1"><?php putGS('Keywords &amp; Topics'); ?></a></h3>
  </div>
  <div class="padded">
  <?php if ($inEditMode && $g_user->hasPermission('ChangeArticle')) { ?>
  <form id="article-keywords" action="/<?php echo $ADMIN; ?>/articles/post.php" method="POST">
      <fieldset class="frame top-field">
        <label for="Keywords" class="block-label"><?php putGS('Keywords'); ?></label>
        <input type="text" name="Keywords" id="Keywords" size="45"
          class="input_text" value="<?php echo $articleObj->getKeywords(); ?>" onkeyup="" autocomplete="off" style="width:75%;" /><input
          type="submit" class="default-button right-floated clear-margin next-to-field" value="<?php putGS('Save'); ?>" />
      </fieldset>
    </form>
    <script type="text/javascript">
    $(document).ready(function() {
        $('form#article-keywords').submit(function() {
            if (!$(this).hasClass('changed')) {
                return false;
            }

            ajax_forms++;
            var keywords = $('input#Keywords', $(this)).val();
            callServer(['Article', 'setKeywords'], [
                <?php echo $f_language_selected; ?>,
                <?php echo $articleObj->getArticleNumber(); ?>,
                keywords], function(json) {
                    flashMessage('<?php putGS('Keywords saved.'); ?>');
                    ajax_forms--;
                });

            $(this).removeClass('changed');
            return false;
        }).change(function() {
            $(this).addClass('changed');
        });
    });
    </script>
  <?php } ?>
    <div class="frame">
    <?php if ($inEditMode && $g_user->hasPermission('AttachTopicToArticle')) { ?>
      <a class="iframe ui-state-default icon-button right-floated"
      href="<?php echo camp_html_article_url($articleObj, $f_language_id, "topics/popup.php"); ?>"><span
        class="ui-icon ui-icon-pencil"></span><?php putGS('Edit'); ?></a>
    <?php } ?>
      <label class="left-floated block-label"><?php putGS('Topics'); ?></label>
      <div class="clear"></div>
    <?php if (sizeof($articleTopics) > 0) { ?>
      <ul class="block-list">
    <?php
    foreach ($articleTopics as $tmpArticleTopic) {
        $detachUrl = "/$ADMIN/articles/topics/do_del.php?f_article_number=$f_article_number&f_topic_id=".$tmpArticleTopic->getTopicId()."&f_language_selected=$f_language_selected&f_language_id=$f_language_id&".SecurityToken::URLParameter();
        $path = $tmpArticleTopic->getPath();
        $pathStr = '';
        foreach ($path as $element) {
            $name = $element->getName($f_language_selected);
            if (empty($name)) {
                // For backwards compatibility -
                // get the english translation if the translation
                // doesnt exist for the article's language.
                $name = $element->getName(1);
                if (empty($name)) {
                    $name = '-----';
                }
            }
            $pathStr .= ' / '. htmlspecialchars($name);
        }

        // Get the topic name for the 'detach topic' dialog box, below.
        $tmpTopicName = $tmpArticleTopic->getName($f_language_selected);
        // For backwards compatibility.
        if (empty($tmpTopicName)) {
            $tmpTopicName = $tmpArticleTopic->getName(1);
        }
    ?>
        <li><?php p(wordwrap($pathStr, 45, '<br />&nbsp;&nbsp;')); ?>
        <?php if ($inEditMode && $g_user->hasPermission('AttachTopicToArticle')) { ?>
          <a class="corner-button" href="<?php p($detachUrl); ?>"
            onclick="return checkChanged() && confirm('<?php putGS("Are you sure you want to remove the topic \\'$1\\' from the article?", camp_javascriptspecialchars($tmpTopicName)); ?>');"><span class="ui-icon ui-icon-closethick"></span></a></li>
        <?php } ?>
    <?php } ?>
      </ul>
    <?php } ?>
    </div>
  </div>
</div>
