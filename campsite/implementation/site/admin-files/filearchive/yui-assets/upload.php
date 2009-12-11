<?php
require_once('JSON.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Input.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/XR_CcClient.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Archive_File.php');
require_once($GLOBALS['g_campsiteDir'].'/mma/StoredFile.php');

$json = new Services_JSON();
$data = new stdclass();
$data->Results = new stdclass();
$data->Results->success = true;

// Check if form was posted. $_POST and $_FILES are empty usually when
// post_max_size in php.ini is not set properly.
if (empty($_POST) && empty($_FILES)) {
    $data->Results->success = false;
    // TODO: more informative err message?
    $data->Results->camp_error = getGS('Form could not be posted.');
}

$file_title = Input::Get('file_title', 'string', '');

// Check for file upload error
if ($data->Results->success
        && $_FILES['f_file_name']['error'] != UPLOAD_ERR_OK) {
    $data->Results->success = false;
    $data->Results->camp_error = camp_get_file_upload_error($_FILES['f_file_name']['error']);
}

// If true, file was uploaded successfully, then CS proceeds to store it
// in the file archive.
if ($data->Results->success) {
    $uploadFile = $_FILES['f_file_name'];

    list($fileGroup, $fileFormat) = explode('/', $uploadFile['type']);
    $fileClassName = 'Archive_'.ucwords($fileGroup).'File';

    require_once($GLOBALS['g_campsiteDir']."/classes/$fileClassName.php");
    $fileObj = new $fileClassName();
    $filePath = $fileObj->onFileUpload($uploadFile);

    if (PEAR::isError($filePath)) {
        $data->Results->success = false;
	$data->Results->camp_error = getGS($filePath->getMessage());
	eval($fileClassName."::DeleteTemporaryFile('$filePath');");
	// php >= 5.3.0
	//$fileClassName::DeleteTemporaryFile($uploadFile['tmp_name']);
    }

    if ($data->Results->success) {
        $sessId = camp_session_get(CS_FILEARCHIVE_SESSION_VAR_NAME, '');
	$metaDataArray = array();
	$mask = $fileObj->getMask();
	$metaData = camp_get_metadata($filePath);

	foreach($mask['pages'] as $key => $val) {
	  foreach($mask['pages'][$key] as $k => $v) {
	        $element = $v['element'];
                $metaTagValue = (isset($metaData[$element])) ? $metaData[$element] : null;
		if ($element == 'dcterms:extent') {
		    $metaTagValue = (string) round((float) $metaTagValue, 6);
		}
                if (!is_null($metaTagValue) && $metaTagValue != '') {
		    $metaDataArray[$v['element']] = $metaTagValue;
                }
	    }
	}

	eval('$fileGunid='.$fileClassName."::Store('$sessId','$filePath',\$metaDataArray,'$fileGroup');");
	//$fileGunid = $fileClassName::Store($sessId, $filePath, $metaData);
	if (PEAR::isError($fileGunid)) {
	    $data->Results->success = false;
	    $data->Results->camp_error = getGS('There was an error while saving the file: $1', $fileGunid->getMessage());
	    eval($fileClassName."::DeleteTemporaryFile('$filePath');");
	    //$fileClassName::DeleteTemporaryFile($filePath);
	} else {
	    eval($fileClassName."::OnFileStore('$filePath');");
	    //$fileClassName::OnFileStore($filePath);

	    $data->Results->file_desc = $file_title;
	    $data->Results->file_type = $uploadFile['type'];
	    $data->Results->file_name = $uploadFile['name'];
	    $data->Results->file_size = $uploadFile['size'];
	    $data->Results->file_error = $uploadFile['error'];
	    $data->Results->file_mdata = $metaData;
	}
    }
}

echo($json->encode($data));
?>