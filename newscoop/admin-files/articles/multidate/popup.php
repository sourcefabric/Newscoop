<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="Expires" content="now" />
<title><?php putGS("Multi date event"); ?></title>
<script type="text/javascript">
function popup_close() {
	alert('popup close');
	try {
        if (parent.$.fancybox.reload) {
            parent.$.fancybox.message = '<?php putGS('Locations updated.'); ?>';
        }
        parent.$.fancybox.close();
    }
    catch (e) {window.close();}
}

function popup_save() {
	alert('popup save');
    //callServer(['ArticleList', 'doAction'], aoData, fnSaveCallback);
}
</script>


<?php
$f_multidate_box = 1;
require_once($GLOBALS['g_campsiteDir']. "/$ADMIN_DIR/html_head.php");
require_once($GLOBALS['g_campsiteDir'].'/classes/SystemPref.php');


$f_publication_id = Input::Get('f_publication_id', 'int', 0);
$f_issue_number = Input::Get('f_issue_number', 'int', 0);
$f_section_number = Input::Get('f_section_number', 'int', 0);
$f_language_id = Input::Get('f_language_id', 'int', 1);
if (isset($_SESSION['f_language_selected'])) {
	$f_old_language_selected = (int)$_SESSION['f_language_selected'];
} else {
	$f_old_language_selected = 0;
}
$f_language_selected = (int)camp_session_get('f_language_selected', 0);
?>
</head>
<body onLoad="return false;" style="background: none repeat scroll 0 0 #FFFFFF;">




<div class="content">
<div id="multidate-box">
<div class="toolbar">
<div class="save-button-bar"><input type="submit" name="cancel"
	value="<?php echo putGS('Close'); ?>" class="default-button" onclick="popup_close();"
	id="context_button_close"> <input type="submit" name="save"
	value="<?php echo putGS('Save'); ?>" class="save-button-small" onclick="popup_save();"
	id="context_button_save"></div>
<h2><?php echo putGS('Multi date event'); ?></h2>
</div>
<div class="context-content" style="position:relative">

</div>
</div>
</div>
</body>
</html>




