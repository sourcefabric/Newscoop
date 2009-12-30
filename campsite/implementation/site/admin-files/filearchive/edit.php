<?php

require_once($GLOBALS['g_campsiteDir'].'/classes/Input.php');
require_once($GLOBALS['g_campsiteDir']."/classes/XR_CcClient.php");
require_once($GLOBALS['g_campsiteDir'].'/classes/Archive_File.php');

$fileGunId = Input::Get('gunid', 'string', '');

if (empty($fileGunId)) {
  // return error
}

// TODO: check this out
$xrc =& XR_CcClient::Factory($mdefs, true);
$resp = $xrc->ping($sessid);
if (PEAR::isError($resp)) {
    switch ($resp->getCode()) {
        case '805':
            camp_html_goto_page('campcaster_login.php');
            break;
        case '804':
        default:
            camp_html_add_msg(getGS("Unable to reach the storage server."));
            break;
    }
}

$file = Archive_File::Get($fileGunId);
if ($file == false || $file->exists() == false) {
    echo 'File does not exist';
    exit;
}

$mask = $file->getMask();
$fileTypeTitle = ucwords($file->getFileType());

$crumbs = array();
$crumbs[] = array(getGS("File Archive"), "/$ADMIN/filearchive/");
$crumbs[] = array(getGS("Edit $fileTypeTitle file"), "");
echo camp_html_breadcrumbs($crumbs);

?>
<link type="text/css" rel="stylesheet" href="/javascript/yui/build/container/assets/container.css"/>
<link rel="stylesheet" type="text/css" href="/admin/filearchive/yui-assets/styles.css" />

<script type="text/javascript" src="/javascript/yui/build/yahoo-dom-event/yahoo-dom-event.js"></script>
<script type="text/javascript" src="/javascript/yui/build/connection/connection-min.js"></script>
<script type="text/javascript" src="/javascript/yui/build/element/element-min.js"></script>
<script type="text/javascript" src="/javascript/yui/build/tabview/tabview-min.js"></script>
<script type="text/javascript" src="/javascript/yui/build/container/container-min.js"></script>

<div id="camp-message"></div>
<p>
<form id="file_edit" name="file_edit" method="post" action="#">
<input type="hidden" name="file_gunid" value="<?php echo $fileGunId; ?>" />
<input type="hidden" name="file_type" value="<?php echo $file->getFileType(); ?>" />
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
	  $element_encode = str_replace(':','_',$v['element']);
	  $element_form_name = 'f_' . $key . '_' . $element_encode;
	  $isDisabled = (isset($v['attributes']['disabled'])) ? $v['attributes']['disabled'] : 'off';
  ?>
      <tr>
        <td align="right"><?php echo htmlspecialchars($file->getMetatagLabel($element)); ?>:</td>
        <td>
        <?php
        if ($isDisabled == 'on') {
	    $tagValue = htmlspecialchars($file->getMetatagValue($element));
	    if ($element == 'ls:mtime') {
	        echo '<div id="f_mtime">'.$tagValue . "</div>\n";
	    } elseif ($element == 'ls:filesize') {
	        echo camp_format_bytes($tagValue) . "\n";
	    } else {
	        echo $tagValue ."\n";
	    }
        ?>
          <input type="hidden" id="<?php echo $element_form_name; ?>" name="<?php echo $element_form_name; ?>" value="<?php echo $tagValue; ?>" />
        <?php
        } else {
        ?>
          <input type="text" class="input_text" id="<?php echo $element_form_name; ?>" name="<?php echo $element_form_name; ?>" size="50" value="<?php echo $file->getMetatagValue($element); ?>" />
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
          <input type="button" name="editButton<?php echo $key; ?>" id="editButton<?php echo $key; ?>" value="<?php putGS('Save'); ?>" class="button" />
          <input type="button" name="cancelButton" id="cancelButton" value="<?php putGS('Cancel'); ?>" class="button" onclick="location.href='/admin/filearchive/'" />
        </td>
      </tr>
      </table>
      </div>
  <?php
  }
  ?>
</div>
</form>
<?php
//
$jsArrayPages = array();
$jsArrayFields = array();
foreach($mask['pages'] as $key => $val) {
    foreach($mask['pages'][$key] as $k => $v) {
        $element = $v['element'];
	$element_encode = str_replace(':','_',$v['element']);
	$jsArrayFields[] = "'".addslashes('f_'.$key.'_'.$element_encode)."'";
    }
    $jsArrayPages[] = "'".addslashes($key)."'";
}
$jsArrayPagesStr = implode(',', $jsArrayPages);
$jsArrayFieldsStr = implode(',', $jsArrayFields);
?>
<!-- YUI code //-->
<script type="text/javascript">
YAHOO.namespace("camp.container");

var tabView = new YAHOO.widget.TabView('file_md');

var mesg = document.getElementById('camp-message');

var sUrl = '/admin/filearchive/yui-assets/do_edit.php';

function init() {
    var formPages = [<?php print($jsArrayPagesStr); ?>];

    var onEditButtonClick = function(e){
        YAHOO.util.Connect.setForm('file_edit');

	var editHandler = {
	    success: function(o) {
	        var json = o.responseText.substring(o.responseText.indexOf('{'), o.responseText.lastIndexOf('}') + 1);
		var data = eval('(' + json + ')');

		mesg.style.display = 'inline';
		if (data.Results.success == false) {
		    mesg.style.color = 'red';
		    if (data.Results.camp_error != undefined) {
		        mesg.innerHTML = '<?php putGS("Error"); ?>' + ': '
			    + data.Results.camp_error;
		    }
		    YAHOO.camp.container.wait.hide();
		    return false;
		}

		// now we are pretty sure editing went ok
		mesg.style.color = 'green';
		mesg.innerHTML = '<?php putGS("File data was edited successfully"); ?>';

		elmMtime = document.getElementById('f_mtime');
		elmMtime.style.fontWeight = 'bold';
		elmMtime.innerHTML = data.Results.mtime;
		YAHOO.camp.container.wait.hide();
	    },
	    failure: function(o) {
	        if(o.status == 0 || o.status == -1) {
		    mesg.style.display = 'inline';
		    mesg.style.color = 'red';
		    mesg.innerHTML = '<?php putGS("Error: Campsite was unable to edit the file."); ?>';
		    YAHOO.example.container.wait.hide();
		}
	    }
	};

	// Initialize the temporary Panel to display while waiting
	// for file upload
	YAHOO.camp.container.wait = 
            new YAHOO.widget.Panel("wait",
                                   { width:"240px",
				     fixedcenter:true,
				     close:false,
				     draggable:false,
				     zindex: 4,
				     modal:true,
				     visible:false
				   }
				  );

	YAHOO.camp.container.wait.setHeader("<?php putGS('Saving, please wait...'); ?>");
	YAHOO.camp.container.wait.setBody("<img src=\"/css/rel_interstitial_loading.gif\" />");
	YAHOO.camp.container.wait.render(document.body);

	//
	var formFields = [<?php print($jsArrayFieldsStr); ?>];

	//
	var postData = '';
	for (i = 0; i < formFields.length; i++) {
	    postData += '&' + formFields[i] + '=' + encodeURIComponent(document.getElementById(formFields[i]).value);
	}

	// Show the saving panel
	YAHOO.camp.container.wait.show();

	var request = YAHOO.util.Connect.asyncRequest('POST', sUrl, editHandler, postData);
	setTimeout(function() { YAHOO.util.Connect.abort(request, editHandler) }, 30000);
    };

    //var onEditButtonClick = function(e){
    //    YAHOO.util.Connect.setForm('file_mdata');
    //}

    var editButtonName = '';
    for (i = 0; i < formPages.length; i++) {
        editButtonName = 'editButton' + formPages[i];
        YAHOO.util.Event.on(editButtonName, 'click', onEditButtonClick);
    }
}

YAHOO.util.Event.on(window, 'load', init);
</script>
<?php camp_html_copyright_notice(); ?>