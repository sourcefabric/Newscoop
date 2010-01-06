<?php
require_once('JSON.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Input.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/XR_CcClient.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Archive_File.php');
require_once($GLOBALS['g_campsiteDir'].'/mma/StoredFile.php');

$file_gunid = Input::Get('file_gunid', 'string', '');
$form_data = $_POST;

$json = new Services_JSON();
$data = new stdclass();
$data->Results = new stdclass();

$data->Results->success = true;

if (!Input::IsValid()) {
    $data->Results->success = false;
    $data->Results->camp_error = getGS('Invalid input: $1', Input::GetErrorString());
}

// TODO: check for permission
if ($data->Results->success
        && !$g_user->hasPermission('AttachAudioclipToArticle')) {
    $data->Results->success = false;
    $data->Results->camp_error = getGS('You do not have the right to change file information.');
}

if ($data->Results->success) {
    $file = Archive_File::Get($file_gunid);
    $res = $file->editMetadata($form_data);
    if (PEAR::isError($res)) {
        $data->Results->success = false;
        $data->Results->camp_error = getGS('Failed to update file information.');
    } else {
        $data->Results->mtime = $file->getModifiedTime();
        $data->Results->success = true;
    }
}

// JSON encoded result
echo($json->encode($data));
?>