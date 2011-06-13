<? $counter = 0 ?>
<input id="set-track" type="hidden" value="<?= $track ?>">
<table cellspacing="0" cellpadding="0" class="datatable" id="gridx" style="width: 100%; margin: 0pt;">
  <tbody>
  <? foreach ($setList as $index => $t): ?>
    <tr class="<?= $index % 2 ? 'odd' : 'even' ?>">
      <td>
          <div class="soundcloud-list-item">
            <div class="controls">
            <div class="buttons">
            <? $tracks = array();
               foreach ($t['tracks'] as $value):
                   $tracks[] = $value['id'];
               endforeach ?>
            <? if ($g_user->hasPermission('plugin_soundcloud_update')): ?>
                <? $trackInSet = in_array($track, $tracks) ?>
                <a id="<?= $t['id'] ?>" style="<?= !$trackInSet ? '' : 'display:none;' ?>" class="addtoset ui-state-default icon-button no-text" href=""><span class="ui-icon ui-icon-plusthick"></span></a>
                <a id="<?= $t['id'] ?>" style="<?= $trackInSet ? '' : 'display:none;' ?>" class="removefromset ui-state-default icon-button no-text" href=""><span class="ui-icon ui-icon-minusthick"></span></a>
            <? endif ?>
            </div>
            <div class="metadata">
                <h3><a id="title-<?= $t['id'] ?>" target="soundcloud" href="<?= $t['permalink_url'] ?><?= $t['sharing']=='public'?'':'/'.$t['secret_token'] ?>" class="text-link soundcloud-title"><?= $t['title'] ?></a>
                <? if($t['sharing'] != 'public'): ?>
                    <img alt="<?= getGS('Private') ?>" src="<?= $Campsite['SUBDIR'] ?>/plugins/soundcloud/css/images/locked_big.png">
                <? endif ?>
                </h3>
            </div>
        </div>
        </div>
      </td>
    </tr>
    <? $counter++ ?>
  <? endforeach ?>
  </tbody>
</table>
<? if ($counter == 0): ?>
    <? putGS('No sets found') ?>
<? endif ?>
