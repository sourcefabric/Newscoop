<?php
require_once('JSON.php');
$json = new Services_JSON();

require_once($GLOBALS['g_campsiteDir'].'/classes/Input.php');
require_once($GLOBALS['g_campsiteDir'].'/mma/StoredFile.php');

$file_title = Input::Get('file_title', 'string', '');

// check for uploaded file
$uploadFile = isset($_FILES['f_file_name']) ? $_FILES['f_file_name'] : false;

$data = new stdclass();
$data->Results = new stdclass();
if ($uploadFile) {
    $uploadSuccess = true;
    if ($uploadFile['error'] > 0) {
        $uploadSuccess = false;
        $data->Results->file_error = $uploadFile['error'];
    }

    $destDir = $GLOBALS['g_campsiteDir'].'/admin-files/filearchive/tmp';
    if ($uploadSuccess && (!file_exists($destDir) || !is_writable($destDir))) {
        $uploadSuccess = false;
	$data->Results->camp_error = camp_get_error_message(CAMP_ERROR_WRITE_DIR, $destDir);
    }

    $source = $uploadFile['tmp_name'];
    $destination = $destDir .'/' . $uploadFile['name'];
    
    if ($uploadSuccess && (!move_uploaded_file($source, $destination))) {
        $uploadSuccess = false;
	$data->Results->camp_error = camp_get_error_message(CAMP_ERROR_CREATE_FILE, $destination);
        if (file_exists($destination)) {
	    @unlink($destination);
	}
    }

    if ($uploadSuccess) {
	$data->Results->file_desc = $file_title;
	$data->Results->file_type = $uploadFile['type'];
	$data->Results->file_name = $uploadFile['name'];
	$data->Results->file_size = $uploadFile['size'];
	$data->Results->file_error = $uploadFile['error'];
	$data->Results->upload_success = true;

	$metaData = camp_get_metadata($destination);
	$data->Results->file_mdata = $metaData;
    }
} else {
    $data->Results->camp_error = CAMP_ERROR_CREATE_FILE;
}

echo($json->encode($data));
?>