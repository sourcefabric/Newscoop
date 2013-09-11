<?php
$translator = \Zend_Registry::get('container')->getService('translator');
if (empty($userIsBlogger)) : ?>
<div class="articlebox" title="<?php echo $translator->trans('Featured Article Lists', array(), 'articles'); ?>">
  <div>
    <div id="playlist">
      <div id="playlistArticles" style="display:block; padding-bottom:8px;">
      <?php
          try {
              $playlists = Zend_Registry::get('container')->getService('em')
                  ->getRepository('Newscoop\Entity\Playlist')
                  ->getArticlePlaylists(Input::Get('f_article_number', 'int', 1));

              $playlistsData = array();
              foreach ($playlists as $playlist) {
                  $playlistsData[] = (object) array (
                      'name' => $playlist->getName(),
                      'id' => $playlist->getId(),
                  );
              }
          } catch(\Exception $e) {
              $playlistsData = array();
          }
      ?>

      <ul class="block-list" id="added-to-playlists">
      <?php if ($playlistsData) : ?>
        <?php foreach ($playlistsData as $playlist) : ?>
          <li playlist-id="<?php echo $playlist->id ?>">
            <?php echo $this->view->escape($playlist->name); ?>
          </li>
        <?php endforeach; ?>
      <?php endif; ?>
      </ul>
      </div>

      <?php if ($inEditMode && $GLOBALS['controller']->getHelper('acl')->isAllowed('playlist', 'manage')) : ?>
      <a class="iframe ui-state-default icon-button right-floated"
        popup-width="600"
        href="<?php echo camp_html_article_url($articleObj, $f_language_id, "playlist/popup.php"); ?>">
        <span class="ui-icon ui-icon-pencil"></span><?php echo $translator->trans('Edit'); ?></a>
      <?php endif; ?>
    </div>
  </div>
</div>
<?php endif; ?>
