<?php
/**
 * @package Campsite
 */

require_once($GLOBALS['g_campsiteDir']. "/$ADMIN_DIR/templates/template_common.php");

if (!SecurityToken::isValid()) {
    camp_html_display_error(getGS('Invalid security token!'));
    exit;
}

if (!$g_user->hasPermission('ManageTempl')) {
    camp_html_display_error(getGS("You do not have the right to modify templates."));
    exit;
}

$f_path = Input::Get('f_path', 'string', '');
$f_charset = Input::Get('f_charset', 'string', '');


$baseUpload = $Campsite['TEMPLATE_DIRECTORY'] . $f_path;
$backLink = "/$ADMIN/templates/upload_templ.php?Path=" . urlencode($f_path);

$nrOfFiles = isset($_POST['uploader_count']) ? $_POST['uploader_count'] : 0;
for ($i = 0; $i < $nrOfFiles; $i++) {
    $tmpnameIdx = 'uploader_' . $i . '_tmpname';
    $nameIdx = 'uploader_' . $i . '_name';
    $statusIdx = 'uploader_' . $i . '_status';
    if ($_POST[$statusIdx] == 'done') {
        $tmpFilePath = CS_TMP_TPL_DIR . DIR_SEP . $_POST[$tmpnameIdx];
        $newFilePath = $baseUpload . DIR_SEP . $_POST[$nameIdx];
        $result = Template::ProcessFile($tmpFilePath, $newFilePath, $f_charset);
    }
}

if ($result) {
    camp_html_add_msg(getGS('"$1" files uploaded.', $nrOfFiles), "ok");
    camp_html_goto_page("/$ADMIN/templates/?Path=" . urlencode($f_path));
} else {
    camp_html_add_msg($f_path . DIR_SEP . basename($newFilePath));
    camp_html_goto_page($backLink);
}

?>