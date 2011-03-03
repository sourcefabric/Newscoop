<?php
/**
 * @package Newscoop
 *
 * @author Mihai Nistor <mihai.nistor@sourcefabric.org>
 * @copyright 2010 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.sourcefabric.org
 */
require_once($GLOBALS['g_campsiteDir'].'/classes/Attachment.php');

camp_load_translation_strings("media_archive");

if (!SecurityToken::isValid()) {
    camp_html_display_error(getGS('Invalid security token!'));
    exit;
}

if (!$g_user->hasPermission('AddFile')) {
    camp_html_display_error(getGS("You do not have the right to add files."));
    exit;
}

$nrOfFiles = isset($_POST['uploader_count']) ? $_POST['uploader_count'] : 0;

// process uploaded files
for ($i = 0; $i < $nrOfFiles; $i++) {
    $tmpnameIdx = 'uploader_' . $i . '_tmpname';
    $nameIdx = 'uploader_' . $i . '_name';
    $statusIdx = 'uploader_' . $i . '_status';
    if ($_POST[$statusIdx] == 'done') {
        $result = Attachment::ProcessFile($_POST[$tmpnameIdx], $_POST[$nameIdx], $g_user->getUserId());
    }
}

if ($result != NULL) {
    camp_html_add_msg(getGS('"$1" files uploaded.', $nrOfFiles), "ok");
    camp_html_goto_page("/$ADMIN/media-archive/multiedit_file.php");
} else {
    camp_html_add_msg($f_path . DIR_SEP . basename($newFilePath));
    camp_html_goto_page($backLink);
}

?>
