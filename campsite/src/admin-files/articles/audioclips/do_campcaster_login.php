<?php
camp_load_translation_strings('home');
require_once($GLOBALS['g_campsiteDir'].'/classes/XR_CcClient.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/SystemPref.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Article.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Input.php');
camp_load_translation_strings("article_audioclips");
camp_load_translation_strings("api");

if (SystemPref::Get("UseCampcasterAudioclips") != 'Y') {
    camp_html_display_error(getGS("Campcaster integration is disabled"), null, true);
    exit;
}

$f_cc_username = Input::Get('f_cc_username');
$f_cc_password = Input::Get('f_cc_password');
$BackLink = Input::Get('f_backlink', 'string', null, true);

if (!Input::isValid()) {
	camp_html_goto_page("/$ADMIN/articles/audioclips/campcaster_login.php?error_code=userpass");
}

$ccLogin = camp_campcaster_login($f_cc_username, $f_cc_password);
if (PEAR::isError($ccLogin)) {
    $errorMessage = $ccLogin->getMessage();
    if ($ccLogin->getCode() == '802') {
        camp_html_add_msg(getGS("Incorrect password or your user is not a valid Campcaster user"));
    } elseif (!empty($errorMessage)) {
        camp_html_add_msg(getGS("There was an error logging in to the Campcaster server")
        .':<br>'.$errorMessage);
    } else {
        camp_html_add_msg(getGS("There was an error logging in to the Campcaster server"));
    }
} else {
    $BackLink = trim($BackLink);
    if (empty($BackLink)) {
        camp_html_add_msg(getGS("Campcaster session started successfully"), "Ok");
    } else {
        camp_html_goto_page("$BackLink");
    }
}
?>
<script>
window.opener.document.forms.article_edit.submit();
window.close();
</script>
