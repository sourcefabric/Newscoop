<?php

// This file will deliver the attachment. It is supposed to work like this:
// http://site/attachment/xxxxxxxxx.ext

require_once($_SERVER['DOCUMENT_ROOT'].'/configuration.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/db_connect.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Attachment.php');

$requestURI = $_SERVER['REQUEST_URI'];
$attachment = substr($requestURI, strlen('/attachment/'));

// Remove any GET parameters
if (($questionMark = strpos($attachment, '?')) !== false) {
	$attachment = substr($attachment, 0, $questionMark);
}

// Remove all attempts to get at other parts of the file system
$attachment = str_replace('/../', '/', $attachment);

$extension = '';
if (($extensionStart = strrpos($attachment, '.')) !== false) {
	$extension = strtolower(substr($attachment, $extensionStart + 1));
}
$attachmentId = (int)substr($attachment, 0, $extensionStart);

$queryStr = "SELECT * FROM Attachments WHERE id = $attachmentId";
$attachmentObj =& new Attachment($attachmentId);
if (!$attachmentObj->exists()) {
	header('HTTP/1.0 404 Not Found');
	exit;
}


header('Content-Type: ' . $attachmentObj->getMimeType());
header('Content-Disposition: ' . $attachmentObj->getContentDisposition()
		. "; filename=" . $attachmentObj->getFileName());
header('Content-Length: ' . $attachmentObj->getSizeInBytes());

$filePath = $attachmentObj->getStorageLocation();
if (file_exists($filePath)) {
	readfile($filePath);
} else {
	header('HTTP/1.0 404 Not Found');
	exit;
}

?>
