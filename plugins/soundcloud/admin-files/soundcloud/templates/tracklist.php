<input id="paging-offset" type="hidden" name="offset" value="<?= $offset ?>" />
<? $counter = 0 ?>
<table cellspacing="0" cellpadding="0" class="datatable" id="gridx" style="width: 100%; margin: 0pt;">
  <tbody>
  <? foreach ($trackList as $index => $t): ?>
    <tr  class="<?= $index % 2 ? 'odd' : 'even' ?>">
      <td>
          <div class="soundcloud-list-item">
            <div class="buttons">
            <a id="<?= $t['id'] ?>" href="" class="track-play ui-state-default icon-button"><span class="ui-icon"></span><? putGS('Play') ?></a>
            <? if ($g_user->hasPermission('plugin_soundcloud_update')): ?>
                <a id="<?= $t['id'] ?>" href="" class="track-edit ui-state-default icon-button"><span class="ui-icon ui-icon-pencil"></span><? putGS('Edit') ?></a>
                <a id="<?= $t['id'] ?>" href="/<?= $ADMIN ?>/soundcloud/controller.php?action=setlist&track=<?= $t['id'] ?>" class="track-set ui-state-default icon-button"><span class="ui-icon"></span><? putGS('Add to set') ?></a>
               <? endif ?>
            <? if ($g_user->hasPermission('plugin_soundcloud_delete')): ?>
                <a id="<?= $t['id'] ?>"class="track-delete ui-state-default icon-button no-text" href=""><span class="ui-icon ui-icon-minusthick"></span></a>
            <? endif ?>
            <? if ($attachement && empty($attached[(string)$t['id']])): ?>
                <a id="<?= $t['id'] ?>" class="track-attach ui-state-default icon-button no-text" href=""><span class="ui-icon ui-icon-plusthick"></span></a>
            <? endif ?>
            </div>
            <div class="controls">
            <div class="metadata">
                <h3><a id="title-<?= $t['id'] ?>" target="soundcloud" href="<?= $t['permalink_url'] ?><?= $t['sharing']=='public'?'':'/'.$t['secret_token'] ?>" class="text-link soundcloud-title"><?= $t['title'] ?></a></h3>
                <?= $t['description'] ?>
                <div class="info"><?= getGS('Tags') . ': ' .  $t['tag_list'] ?></div>
                <span class="editable"><?= $t['created_at'] ?>
                #<?= $t['id']?> |
                p:<?= $t['playback_count'] ?> |
                f:<?= $t['favoritings_count'] ?> |
                d:<?= $t['download_count'] ?> |
                c:<?= $t['comment_count'] ?> |
                <? $sec = $t['duration'] / 1000 ?>
                <? printf('%d', $sec / 60 % 60) ?>:<? printf('%d', $sec % 60) ?>
                <? if($t['sharing'] != 'public'): ?>
                    | <img alt="<?= getGS('Private') ?>" src="<?= $Campsite['SUBDIR'] ?>/plugins/soundcloud/css/images/locked_big.png">
                <? endif ?>
                </span>
            </div>
        </div>
        <div id="player-<?= $t['id'] ?>" style="display:none">
        <object height="81" width="100%"><param name="movie" value="http://player.soundcloud.com/player.swf?url=<?= urlencode($t['secret_uri']) ?>&amp;show_comments=true&amp;auto_play=true&amp;color=08597d"></param>
        <param name="allowscriptaccess" value="always"></param>
        <embed allowscriptaccess="always" height="81" src="http://player.soundcloud.com/player.swf?url=<?= urlencode($t['secret_uri']) ?>&amp;show_comments=true&amp;auto_play=true&amp;color=08597d" type="application/x-shockwave-flash" width="100%"></embed>
        </object>
        </div>
        </div>
      </td>
    </tr>
    <? $counter++ ?>
  <? endforeach ?>
  </tbody>
</table>
<? if ($counter > 0): ?>
<div class="fg-toolbar ui-toolbar ui-widget-header ui-corner-bl ui-corner-br ui-helper-clearfix">
  <div class="dataTables_info" id="gridx_info"><? putGS('Showing $1 to $2', $counter == 0?$offset:$offset+1, $offset+$counter) ?></div>
  <div class="dataTables_paginate fg-buttonset ui-buttonset fg-buttonset-multi ui-buttonset-multi paging_two_button" id="gridx_paginate">
  <a id="search-prev" class="fg-button ui-button ui-state-default ui-corner-left <?= $offset == 0 ? 'ui-state-disabled' : '' ?>" title="<? putGS('Previous') ?>" id="gridx_previous"><span class="ui-icon ui-icon-circle-arrow-w"></span></a>
  <a id="search-next"class="fg-button ui-button ui-state-default ui-corner-right" title="<? putGS('Next') ?>" id="gridx_next"><span class="ui-icon ui-icon-circle-arrow-e"></span></a></div>
</div>
<? else: ?>
    <? putGS('No tracks found') ?>
<? endif ?>
