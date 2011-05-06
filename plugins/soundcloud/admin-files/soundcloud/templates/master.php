<link type="text/css" href="<?= $Campsite['SUBDIR'] ?>/plugins/soundcloud/css/soundcloud.css" rel="stylesheet" />
<script src="<?= $Campsite['SUBDIR'] ?>/plugins/soundcloud/javascript/functions.js" type="text/javascript"></script>

<script type="text/javascript">
var localizer = localizer || {};
localizer.uploading = '<? putGS('Uploading... please wait') ?>';
localizer.processing = '<? putGS('Processing...') ?>';
localizer.attention = '<? putGS('Attention!') ?>';
localizer.deleteQuestion = '<? putGS('Are you sure you want to delete the track:') ?>';
localizer.ok = '<? putGS('Ok') ?>';
localizer.cancel = '<? putGS('Cancel') ?>';
localizer.setlist = '<? putGS('Set list') ?>';
<? if ($js): ?>
    <?= $js ?>
<? endif ?>
$(document).ready(function(){
    setEvents();
    $('.tabs').tabs()
    .tabs('select', '<?= $g_user->hasPermission('plugin_soundcloud_upload') ? '#tabs-1' : '#tabs-2' ?>');
<? if ($showMessage): ?>
    showMessage('<?= $showMessage['title'] ?>','<?= $showMessage['message'] ?>'
      ,'<?= $showMessage['type'] ?>',<?= $showMessage['fixed'] ?>);
<? endif ?>
});
</script>

<div class="ui-widget-content small-block block-shadow soundcloud soundcloud-attach">
  <div class="padded clearfix inner-tabs">
    <div class="tabs">
      <ul>
        <li <?= !$g_user->hasPermission('plugin_soundcloud_upload') ? 'style="display:none"' : '' ?>>
            <a href="#tabs-1"><? putGS('Upload') ?></a></li>
        <li><a href="#tabs-2"><? putGS('Tracks') ?></a></li>
        <li id="edit-tab" style="display:none"><a href="#tabs-3"><? putGS('Edit') ?></a></li>
      </ul>
      <div id="tabs-1"><? include 'upload.php' ?></div>
      <div id="tabs-2"><? include 'tracks.php' ?></div>
      <div id="tabs-3">
          <form id="edit-form" name="edit-form" method="post" action="controller.php" enctype="multipart/form-data">
          </form>
      </div>
    </div>
  </div>
</div>
