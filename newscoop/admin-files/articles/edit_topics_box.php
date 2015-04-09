<?php $translator = \Zend_Registry::get('container')->getService('translator'); ?>
<div class="ui-widget-content small-block block-shadow">
  <div class="collapsible">
    <h3 class="head ui-accordion-header ui-helper-reset ui-state-default ui-widget">
    <span class="ui-icon"></span>
    <a href="#" tabindex="-1"><?php echo $translator->trans('Keywords &amp; Topics', array(), 'articles'); ?></a></h3>
  </div>
  <div class="padded">
  <?php if ($inEditMode && $g_user->hasPermission('ChangeArticle')) { ?>
  <form id="article-keywords" action="/<?php echo $ADMIN; ?>/articles/post.php" method="POST">
      <fieldset class="frame top-field">
        <label for="Keywords" class="block-label"><?php echo $translator->trans('Keywords'); ?></label>
        <input type="text" name="Keywords" id="Keywords" size="45"
          class="input_text" value="<?php echo $articleObj->getKeywords(); ?>" onkeyup="" autocomplete="off" style="width:75%;" /><input
          type="submit" class="default-button right-floated clear-margin next-to-field" value="<?php echo $translator->trans('Save'); ?>" />
      </fieldset>
    </form>
    <script type="text/javascript">
    $(document).ready(function () {
        $('form#article-keywords').submit(function () {
            if (!$(this).hasClass('changed')) {
                return false;
            }

            var keywords = $('input#Keywords', $(this)).val();
            callServer(['Article', 'setKeywords'], [
                <?php echo $f_language_selected; ?>,
                <?php echo $articleObj->getArticleNumber(); ?>,
                keywords], function (json) {
                    flashMessage('<?php echo $translator->trans('Keywords saved.', array(), 'articles'); ?>');
                });

            $(this).removeClass('changed');

            return false;
        }).change(function () {
            $(this).addClass('changed');
        });

        $('a#detachTopic').click(function (e) {
          var alertMsg = $(this).attr('data-msg');
          var result = confirm(alertMsg);
          var that = $(this);

          if (checkChanged() && result) {
            callServer('ping', [], function (json) {
                $.ajax({
                    type: "POST",
                    url: that.attr('href'),
                    data: {
                      'articleNumber': that.attr('data-article-number'),
                      'topicId': that.attr('data-topicId'),
                      'language': that.attr('data-language')
                    },
                    dataType: "json",
                    success: function (msg) {
                      if (msg.status) {
                        flashMessage(msg.message);
                        that.parent().remove();
                      } else {
                        flashMessage(msg.message, 'error');
                      }
                    },
                    error: function (xhr, textStatus, errorThrown) {
                        flashMessage(textStatus, 'error');
                    }
                });
            });
          }

          e.preventDefault();
        });
    });
    </script>
  <?php }
    $em = \Zend_Registry::get('container')->getService('em');
    $router = \Zend_Registry::get('container')->getService('router');
    $language = $em->getReference("Newscoop\Entity\Language", $f_language_id);
  ?>
    <div class="frame" id="topic_box_frame">
    <?php if ($inEditMode && $g_user->hasPermission('AttachTopicToArticle')) { ?>
      <a class="iframe ui-state-default icon-button right-floated"
      href="<?php echo $router->generate('newscoop_newscoop_topics_index_compact', array(
          'compactView' => 'compact',
          'articleNumber' => $articleObj->getArticleNumber(),
          'language' => $language->getCode()
      )); ?>"><span
        class="ui-icon ui-icon-pencil"></span><?php echo $translator->trans('Edit'); ?></a>
    <?php } ?>
      <label class="left-floated block-label"><?php echo $translator->trans('Topics'); ?></label>
      <div class="clear"></div>
    <?php if (count($articleTopics) > 0) { ?>
      <ul class="block-list">
    <?php
    $repo = $em->getRepository("Newscoop\NewscoopBundle\Entity\Topic");
    foreach ($articleTopics as $tmpArticleTopic) {
        $tmpArticleTopic = $tmpArticleTopic->getTopic();
        $pathStr = $repo->getReadablePath($tmpArticleTopic, $language->getCode());
        // Get the topic name for the 'detach topic' dialog box, below.
        $tempArray = array_values(explode(" / ", $pathStr));
        $tmpTopicName = end($tempArray);
    ?>
        <li><?php p(wordwrap($pathStr, 45, '<br />&nbsp;&nbsp;')); ?>
        <?php if ($inEditMode && $g_user->hasPermission('AttachTopicToArticle')) { ?>
          <a class="corner-button" id="detachTopic" data-language="<?php echo $f_language_selected ?>" data-topicId="<?php echo $tmpArticleTopic->getTopicId() ?>" data-article-number="<?php echo $f_article_number ?>" href="/admin/topics/detach"
            data-msg="<?php echo $translator->trans("Are you sure you want to remove the topic $1 from the article?", array('$1' => camp_javascriptspecialchars($tmpTopicName)), 'articles'); ?>"><span class="ui-icon ui-icon-closethick"></span></a></li>
        <?php } ?>
    <?php } ?>
      </ul>
    <?php } ?>
    </div>
  </div>
</div>
