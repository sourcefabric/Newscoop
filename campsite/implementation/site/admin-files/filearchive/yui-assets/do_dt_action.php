<?php
require_once('JSON.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Input.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/XR_CcClient.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Archive_File.php');
require_once($GLOBALS['g_campsiteDir'].'/mma/StoredFile.php');


$task = Input::Get('task', 'string', '');
$fileGunids = $_POST;

$success = false;
$affectedFiles = 0;
$totalFiles = is_array($fileGunids) ? count($fileGunids) : 0;
switch($task) {
    case 'delete':
        foreach($fileGunids as $param => $fileGunid) {
            if ($param == 'task') continue;
            $file = Archive_File::Get($fileGunid);
            if ($file->delete()) {
                $success = true;
                $affectedFiles += 1;
            }
        }
        break;
}

$json = new Services_JSON();
$data = new stdclass();
$data->Results = new stdclass();

$data->Results->total_files = $affectedFiles;
$data->Results->success = $success;

// JSON encoded result
echo($json->encode($data));
?>