<?php
camp_load_translation_strings('home');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/XR_CcClient.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/SystemPref.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Article.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Input.php');

$f_cc_username = Input::Get('f_cc_username');
$f_cc_password = Input::Get('f_cc_password');
$BackLink = Input::Get('f_backlink', 'string', null, true);

if (!Input::isValid()) {
	camp_html_goto_page("/$ADMIN/priv/articles/audioclips/campcaster_login.php?error_code=userpass");
}

function camp_campcaster_login($f_cc_username, $f_cc_password)
{
    global $mdefs;

    if (SystemPref::Get('CampcasterHostName') == ''
	    	|| SystemPref::Get('CampcasterHostPort') == ''
    	    || SystemPref::Get('CampcasterXRPCPath') == ''
        	|| SystemPref::Get('CampcasterXRPCFile') == '') {
       	return false;
	}
    $xrc =& XR_CcClient::Factory($mdefs);
    if (PEAR::isError($xrc)) {
    	return $xrc;
    }
    $r = $xrc->xr_login($f_cc_username, $f_cc_password);
    if (PEAR::isError($r)) {
    	return $r;
    }
    camp_session_set('cc_sessid', $r['sessid']);
    return true;
}

$ccLogin = camp_campcaster_login($f_user_name, $t_password);
if (PEAR::isError($ccLogin)) {
    camp_html_add_msg(getGS("There was an error logging in to the Campcaster server") . ": "
                      . $ccLogin->getMessage());
}

$BackLink = trim($BackLink);
if (empty($BackLink)) {
?>
<script>
window.opener.document.forms.article_edit.onsubmit();
window.opener.document.forms.article_edit.submit();
window.close();
</script>
<?php
} else {
	camp_html_goto_page("$BackLink");
}
?>