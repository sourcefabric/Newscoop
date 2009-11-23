<?php

$crumbs = array();
$crumbs[] = array(getGS("File Archive"), "/$ADMIN/filearchive/");
$crumbs[] = array(getGS("Add new file"), "");
echo camp_html_breadcrumbs($crumbs);

?>

<!-- CSS definitions -->
<link type="text/css" rel="stylesheet" href="/javascript/yui/build/container/assets/container.css"/>
<link rel="stylesheet" type="text/css" href="/admin/filearchive/yui-assets/styles.css"/>

<!-- Campsite Javascript library -->
<script type="text/javascript" src="/javascript/campsite.js"></script>

<!-- YUI dependencies -->
<script type="text/javascript" src="/javascript/yui/build/yahoo/yahoo-min.js"></script>
<script type="text/javascript" src="/javascript/yui/build/event/event-min.js"></script>
<script type="text/javascript" src="/javascript/yui/build/connection/connection-min.js"></script>
<script type="text/javascript" src="/javascript/yui/build/yahoo-dom-event/yahoo-dom-event.js"></script>
<script type="text/javascript" src="/javascript/yui/build/animation/animation-min.js"></script>
<script type="text/javascript" src="/javascript/yui/build/container/container-min.js"></script>

<?php camp_html_display_msgs(); ?>
<div id="camp-message"></div>
<p>
<div id="form_upload">
  <form name="file_upload" id="file_upload" method="POST" action="#" enctype="multipart/form-data" onsubmit="return checkAddForm(this);">
  <fieldset id="pushbuttonsfrommarkup" class="yui-skin-sam">
  <table border="0" cellspacing="0" cellpadding="6" class="table_input">
  <tr>
    <td colspan="2">
      <b><?php putGS('Add new file'); ?></b>
      <hr noshade size="1" color="black">
    </td>
  </tr>
  <tr>
    <td align="right"><?php putGS('Name'); ?>:</td>
    <td align="left">
      <input type="text" id="f_file_title" name="f_file_title" value="<?php echo Image::GetMaxId(); ?>" size="32" class="input_text"/>
    </td>
  </tr>
  <tr>
    <td align="right" ><?php putGS('File'); ?>:</td>
    <td align="left">
      <input type="file" id="f_file_name" name="f_file_name" size="32" class="input_file"/>
    </td>
  </tr>
  <tr>
    <td colspan="2" align="center">
      <input type="button" name="uploadButton" id="uploadButton" value="<?php putGS('Upload'); ?>" class="button" />
    </td>
  </tr>
  </table>
  </fieldset>
  </form>
</div>

<div id="form_mdata">
  <p>
  <table border="0" cellspacing="0" cellpadding="6" class="table_input">
  <tr>
    <td colspan="2">
      <b><?php putGS('File info'); ?></b>
      <hr noshade size="1" color="black">
    </td>
  </tr>
  <tr>
    <td colspan="2">
      <table cellspacing="0" cellpadding="1">
      <tr>
        <td align="right"><strong><?php putGS('Title'); ?>:&nbsp;</strong> </td>
        <td align="left"><div id="file-title"></div></td>
      </tr>
      <tr>
        <td align="right"><strong><?php putGS('Format'); ?>:&nbsp;</strong> </td>
        <td align="left"><div id="file-type"></div></td>
      </tr>
      <tr>
        <td align="right"><strong><?php putGS('Size'); ?>:&nbsp;</strong> </td>
        <td align="left"><div id="file-size"></div></td>
      </tr>
      </table>
    </td>
  </tr>
  <form name="file_mdata" method="POST" action="#" enctype="multipart/form-data" onsubmit="return checkAddForm(this);">
  <fieldset id="pushbuttonsfrommarkup" class="yui-skin-sam">
  <tr>
    <td colspan="2">
      <b><?php putGS('File metadata'); ?></b>
      <hr noshade size="1" color="black">
    </td>
  </tr>
  <tr>
    <td>
      <table id="mdata_fields">

      </table>
    </td>
  </tr>
  <tr>
    <td colspan="2" align="center">
      <input type="button" name="editButton" id="editButton" value="<?php putGS('Save'); ?>" class="button" />
    </td>
  </tr>
  </table>
</div>



<script>
document.forms.file_upload.f_file_name.focus();
</script>

<!-- YUI code //-->
<script>
YAHOO.namespace("camp.container");

var mesg = document.getElementById('camp-message');
var fdesc = document.getElementById('file-title');
var ftype = document.getElementById('file-type');
var fsize = document.getElementById('file-size');

YAHOO.util.Event.onDOMReady(function () {
    YAHOO.camp.container.form_upload = new YAHOO.widget.Module("form_upload");
    YAHOO.camp.container.form_upload.render();
    YAHOO.camp.container.form_mdata = new YAHOO.widget.Module("form_mdata", { visible: false });
    YAHOO.camp.container.form_mdata.render();
});

var sUrl = '/admin/filearchive/yui-assets/upload.php';

function init() {
    var onUploadButtonClick = function(e){
        YAHOO.util.Connect.setForm('file_upload', true);

	var uploadHandler = {
	    upload: function(o) {
	        var json = o.responseText.substring(o.responseText.indexOf('{'), o.responseText.lastIndexOf('}') + 1);
		var data = eval('(' + json + ')');

		mesg.style.display = 'inline';
		if (data.Results.upload_success == undefined) {
		    mesg.style.color = 'red';
		    if (data.Results.file_error != undefined) {
		        mesg.innerHTML = '<?php putGS("Error"); ?>' + ': '
			    + data.Results.file_error;
		    } else if (data.Results.camp_error != undefined) {
		        mesg.innerHTML = '<?php putGS("Error"); ?>' + ': '
			    + data.Results.camp_error;
		    }
		    YAHOO.camp.container.wait.hide();
		    return false;
		}

		// now we are pretty sure upload went ok
		mesg.style.color = 'green';
		mesg.innerHTML = '<?php putGS("File"); ?>' + ' '
		    + data.Results.file_name + ' '
		    + '<?php putGS("was uploaded successfully"); ?>';
		fdesc.innerHTML = data.Results.file_desc;
		ftype.innerHTML = data.Results.file_type;
		fsize.innerHTML = formatBytes(data.Results.file_size);

		for (x in data.Results.file_mdata) {
		    if (typeof data.Results.file_mdata[x] == 'object') {
		        continue;
		    }
		    addInputTextField('mdata_fields', x, data.Results.file_mdata[x]);
		}

		YAHOO.camp.container.wait.hide();
		YAHOO.camp.container.form_upload.hide();
		YAHOO.camp.container.form_mdata.show();
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

	YAHOO.camp.container.wait.setHeader("<?php putGS('Uploading, please wait...'); ?>");
	YAHOO.camp.container.wait.setBody("<img src=\"http://us.i1.yimg.com/us.yimg.com/i/us/per/gr/gp/rel_interstitial_loading.gif\"/>");
	YAHOO.camp.container.wait.render(document.body);

	var yFileTitle = document.getElementById('f_file_title').value;

	var postData = "file_title=" + encodeURIComponent(yFileTitle);

	// Show the saving panel
	YAHOO.camp.container.wait.show();

	var request = YAHOO.util.Connect.asyncRequest('POST', sUrl, uploadHandler, postData);
	setTimeout(function() { YAHOO.util.Connect.abort(request, callback) }, 30000);
    };

    var onEditButtonClick = function(e){
        YAHOO.util.Connect.setForm('file_mdata');
    }

    YAHOO.util.Event.on('uploadButton', 'click', onUploadButtonClick);
    YAHOO.util.Event.on('editButton', 'click', onEditButtonClick);
}

YAHOO.util.Event.on(window, 'load', init);
</script>
<?php camp_html_copyright_notice(); ?>