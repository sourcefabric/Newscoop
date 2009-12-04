<?php
require_once('JSON.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Input.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/XR_CcClient.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Archive_File.php');
require_once($GLOBALS['g_campsiteDir'].'/mma/StoredFile.php');

$file_title = Input::Get('file_title', 'string', '');

// check for uploaded file
$uploadFile = isset($_FILES['f_file_name']) ? $_FILES['f_file_name'] : false;

$json = new Services_JSON();
$data = new stdclass();
$data->Results = new stdclass();
if ($uploadFile) {
    $uploadSuccess = true;
    if ($uploadFile['error'] > 0) {
        $uploadSuccess = false;
        $data->Results->file_error = $uploadFile['error'];
    }

    list($fileGroup, $fileFormat) = explode('/', $uploadFile['type']);
    $fileClassName = 'Archive_'.ucwords($fileGroup).'File';

    require_once($GLOBALS['g_campsiteDir']."/classes/$fileClassName.php");
    $fileObj = new $fileClassName();
    $filePath = $fileObj->onFileUpload($uploadFile);

    if (PEAR::isError($filePath)) {
        $uploadSuccess = false;
	$data->Results->camp_error = camp_get_error_message($filePath->getMessage(), $filePath->getMessage(), null, true);
	eval($fileClassName."::DeleteTemporaryFile('$filePath');");
	// php >= 5.3.0
	//$fileClassName::DeleteTemporaryFile($uploadFile['tmp_name']);
    }

    if ($uploadSuccess) {
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
	    $data->Results->camp_error = camp_get_error_message(getGS('There was an error while saving the file: $1', $fileGunid->getMessage()), null, true);
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
	    $data->Results->upload_success = true;
	}
    }
} else {
    $data->Results->camp_error = CAMP_ERROR_CREATE_FILE;
}
echo($json->encode($data));
?>