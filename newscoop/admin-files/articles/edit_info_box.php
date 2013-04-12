<?php use Newscoop\Webcode\Manager; ?>

<div class="ui-widget-content small-block block-shadow">
  <div class="collapsible">
    <h3 class="head ui-accordion-header ui-helper-reset ui-state-default ui-widget">
    <span class="ui-icon"></span>
    <a href="#" tabindex="-1"><?php putGS('Info'); ?></a></h3>
  </div>
  <div class="padded clearfix">
    <dl class="inline-list">
      <dt><?php putGS('Reads'); ?></dt>
      <dd>
        <?php
        if ($articleObj->isPublished()) {
            $requestObject = new RequestObject($articleObj->getProperty('object_id'));
            echo $requestObject->exists() ? $requestObject->getRequestCount() : '0';
        } else {
            putGS('N/A');
        }
        ?>
      </dd>
      <dt><?php putGS('Type'); ?></dt>
      <dd><?php print htmlspecialchars($articleType->getDisplayName()); ?></dd>
      <dt><?php putGS('Number'); ?></dt>
      <dd><?php p($articleObj->getArticleNumber()); ?></dd>
      <dt><?php putGS('Created by'); ?></dt>
      <dd><?php p(htmlspecialchars($articleCreator->getRealName())); ?></dd>
      <dt><?php putGS('Webcode'); ?></dt>
      <dd><?php echo '+', $articleObj->getWebcode(); ?></dd>
      <dt><?php putGS('Rating'); ?></dt>
      <dd><?php echo $articleObj->getRating(); ?></dd>
      <!-- render here results of admin.article.edit.info events classes -->
    </dl>
  </div>
</div>
