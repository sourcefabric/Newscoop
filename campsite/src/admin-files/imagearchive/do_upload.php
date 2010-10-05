<?php
/**
 * @package Campsite
 */

if (!SecurityToken::isValid()) {
    camp_html_display_error(getGS('Invalid security token!'));
    exit;
}

if (!$g_user->hasPermission('AddImage')) {
	camp_html_display_error(getGS("You do not have the right to add images."));
	exit;
}

$f_image_url = Input::Get('f_image_url', 'string', '', true);
$nrOfFiles = isset($_POST['uploader_count']) ? $_POST['uploader_count'] : 0;

if (empty($f_image_url) && empty($nrOfFiles)) {
	camp_html_add_msg(getGS("You must select an image file to upload."));
	camp_html_goto_page("/$ADMIN/imagearchive/add.php");
}

// process image url
if (!empty($f_image_url)) {
    $attributes = array(
        'Description' => '',
        'Photographer' => '',
        'Place' => '',
        'Date' => '',
    );

	if (camp_is_valid_url($f_image_url)) {
		$result = Image::OnAddRemoteImage($f_image_url, $attributes, $g_user->getUserId());
	} else {
		camp_html_add_msg(getGS("The URL you entered is invalid: '$1'", htmlspecialchars($f_image_url)));
	}
}

// process uploaded images
for ($i = 0; $i < $nrOfFiles; $i++) {
    $tmpnameIdx = 'uploader_' . $i . '_tmpname';
    $nameIdx = 'uploader_' . $i . '_name';
    $statusIdx = 'uploader_' . $i . '_status';
    if ($_POST[$statusIdx] == 'done') {
        $result = Image::ProcessFile($_POST[$tmpnameIdx], $_POST[$nameIdx], $g_user->getUserId());
    }
}

if ($result != NULL) {
    camp_html_add_msg(getGS('"$1" files uploaded.', $nrOfFiles), "ok");
    camp_html_goto_page("/$ADMIN/imagearchive/multiedit.php");
} else {
    camp_html_add_msg($f_path . DIR_SEP . basename($newFilePath));
    camp_html_goto_page($backLink);
}

?>
