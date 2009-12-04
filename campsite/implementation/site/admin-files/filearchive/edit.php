<?php

require_once($GLOBALS['g_campsiteDir'].'/classes/Input.php');
require_once($GLOBALS['g_campsiteDir']."/classes/XR_CcClient.php");
require_once($GLOBALS['g_campsiteDir'].'/classes/Archive_File.php');

$fileGunId = Input::Get('gunid', 'string', '');

if (empty($fileGunId)) {
  // return error
}

$file = Archive_File::Get($fileGunId);
$mask = $file->getMask();
$fileTypeTitle = ucwords($file->getFileType());

$crumbs = array();
$crumbs[] = array(getGS("File Archive"), "/$ADMIN/filearchive/");
$crumbs[] = array(getGS("Edit $fileTypeTitle file"), "");
echo camp_html_breadcrumbs($crumbs);

?>
<link rel="stylesheet" type="text/css" href="/admin/filearchive/yui-assets/styles.css" />

<script type="text/javascript" src="/javascript/yui/build/yahoo-dom-event/yahoo-dom-event.js"></script>
<script type="text/javascript" src="/javascript/yui/build/element/element-min.js"></script>
<script type="text/javascript" src="/javascript/yui/build/tabview/tabview-min.js"></script>
<p>
<form name="file_edit" method="post" action="#">
<div id="file_md" class="yui-navset">
  <ul class="yui-nav">
  <?php
  $cnt = 1;
  foreach($mask['pages'] as $key => $val) {
      $selected = ($cnt == 1) ? ' class="selected"' : '';
      echo '<li'.$selected.'><a href="#tab'.$cnt++.'"><em>'
	.$key.'</em></a></li>';
  }
  ?>
  </ul>
  <div class="yui-content">
  <?php
  $cnt = 1;
  foreach($mask['pages'] as $key => $val) {
      echo '<div id="tab'.$cnt.'">';
  ?>
      <table border="0" cellspacing="0" cellpadding="6" class="table_input">
  <?php
      foreach($mask['pages'][$key] as $k => $v) {
          $element = $v['element'];
	  $isDisabled = (isset($v['attributes']['disabled'])) ? $v['attributes']['disabled'] : 'off';
  ?>
      <tr>
        <td align="right"><?php echo htmlspecialchars($file->getMetatagLabel($element)); ?>:</td>
        <td>
        <?php
        if ($isDisabled == 'on') {
            echo htmlspecialchars($file->getMetatagValue($element));
        } else {
        ?>
          <input type="text" class="input_text" size="50" value="<?php echo htmlspecialchars($file->getMetatagValue($element)); ?>" />
        <?php
        }
        ?>
        </td>
      </tr>
  <?php
      }
  ?>
      <tr>
        <td colspan="2" align="center">
          <input type="button" name="editButton" id="editButton" value="<?php putGS('Save'); ?>" class="button" />
          <input type="button" name="cancelButton" id="cancelButton" value="<?php putGS('Cancel'); ?>" class="button" />
        </td>
      </tr>
      </table>
      </div>
  <?php
  }
  ?>
</div>
</form>
<script type="text/javascript">
  var tabView = new YAHOO.widget.TabView('file_md');
</script>
<?php camp_html_copyright_notice(); ?>