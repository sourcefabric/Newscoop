<?php $translator = \Zend_Registry::get('container')->getService('translator'); ?>
<div class="ui-widget-content small-block" style="margin-top:8px; margin-bottom:0;">
  <div class="collapsible">
    <h3 class="head ui-accordion-header ui-helper-reset ui-state-default ui-widget">
    <span class="ui-icon"></span>
      <a href="#" tabindex="-1"><?php echo $translator->trans('Publish Schedule', array(), 'articles'); ?></a></h3>
  </div>
  <div class="padded">
  <?php if ($inEditMode && $g_user->hasPermission('Publish')) { ?>
  <a class="iframe ui-state-default icon-button right-floated" href="<?php echo camp_html_article_url($articleObj, $f_language_id, "autopublish.php"); ?>"><span class="ui-icon ui-icon-plusthick"></span><?php echo $translator->trans('Add Event'); ?></a>
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
                echo $translator->trans('Publish');
            }
            if ($publishAction == 'U') {
                echo $translator->trans('Unpublish');
            }
            echo '</li>';
        }
        $frontPageAction = $event->getFrontPageAction();
        if (!empty($frontPageAction)) {
            echo '<li>';
            if ($frontPageAction == 'S') {
                echo $translator->trans('Show on front page');
            }
            if ($frontPageAction == 'R') {
                echo $translator->trans('Remove from front page');
            }
            echo '</li>';
        }
        $sectionPageAction = $event->getSectionPageAction();
        if (!empty($sectionPageAction)) {
            echo '<li>';
            if ($sectionPageAction == 'S') {
                echo $translator->trans('Show on section page');
            }
            if ($sectionPageAction == 'R') {
                echo $translator->trans('Remove from section page');
            }
            echo '</li>';
        }
        ?>
        </ul>
        <?php if ($inEditMode && $g_user->hasPermission('Publish')) { ?>
        <a href="<?php p(camp_html_article_url($articleObj, $f_language_id, 'autopublish_del.php', '', '&f_event_id='.$event->getArticlePublishId(), true)); ?>"
          onclick="return confirm('<?php echo $translator->trans("Are you sure you want to remove the event scheduled on $1?", array('$1' => camp_javascriptspecialchars($event->getActionTime())), 'articles'); ?>');"
          class="corner-button"><span class="ui-icon ui-icon-closethick"></span></a>
        <?php } ?>
      </li>
      <?php } ?>
    </ul>
  <?php } ?>
  </div>
</div>
