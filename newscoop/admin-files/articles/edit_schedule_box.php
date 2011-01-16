<div class="ui-widget-content small-block" style="margin-top:8px; margin-bottom:0;">
  <div class="collapsible">
    <h3 class="head ui-accordion-header ui-helper-reset ui-state-default ui-widget">
    <span class="ui-icon"></span>
      <a href="#" tabindex="-1"><?php putGS('Publish Schedule'); ?></a></h3>
  </div>
  <div class="padded">
  <?php if ($inEditMode && $g_user->hasPermission('Publish')) { ?>
  <a class="iframe ui-state-default icon-button right-floated" href="<?php echo camp_html_article_url($articleObj, $f_language_id, "autopublish.php"); ?>"><span class="ui-icon ui-icon-plusthick"></span><?php putGS('Add Event'); ?></a>
  <?php } ?>
    <div class="clear"></div>
    <?php if (sizeof($articleEvents) > 0) { ?>
    <ul class="block-list">
    <?php foreach ($articleEvents as $event) { ?>
      <li><?php p(htmlspecialchars($event->getActionTime())); ?>
        <ul class="simple-list">
        <?php
        $publishAction = $event->getPublishAction();
        if (!empty($publishAction)) {
            echo '<li>';
            if ($publishAction == 'P') {
                putGS('Publish');
            }
            if ($publishAction == 'U') {
                putGS('Unpublish');
            }
            echo '</li>';
        }
        $frontPageAction = $event->getFrontPageAction();
        if (!empty($frontPageAction)) {
            echo '<li>';
            if ($frontPageAction == 'S') {
                putGS('Show on front page');
            }
            if ($frontPageAction == 'R') {
                putGS('Remove from front page');
            }
            echo '</li>';
        }
        $sectionPageAction = $event->getSectionPageAction();
        if (!empty($sectionPageAction)) {
            echo '<li>';
            if ($sectionPageAction == 'S') {
                putGS('Show on section page');
            }
            if ($sectionPageAction == 'R') {
                putGS('Remove from section page');
            }
            echo '</li>';
        }
        ?>
        </ul>
        <?php if ($inEditMode && $g_user->hasPermission('Publish')) { ?>
        <a href="<?php p(camp_html_article_url($articleObj, $f_language_id, 'autopublish_del.php', '', '&f_event_id='.$event->getArticlePublishId(), true)); ?>"
          onclick="return confirm('<?php putGS("Are you sure you want to remove the event scheduled on $1?", camp_javascriptspecialchars($event->getActionTime())); ?>');"
          class="corner-button"><span class="ui-icon ui-icon-closethick"></span></a>
        <?php } ?>
      </li>
      <?php } ?>
    </ul>
  <?php } ?>
  </div>
</div>
