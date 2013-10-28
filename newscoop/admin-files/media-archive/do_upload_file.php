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

$translator = \Zend_Registry::get('container')->getService('translator');

if (!SecurityToken::isValid()) {
    camp_html_display_error($translator->trans('Invalid security token!'));
    exit;
}

if (!$g_user->hasPermission('AddFile')) {
    camp_html_display_error($translator->trans("You do not have the right to add files.", array(), 'media_archive'));
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
    camp_html_add_msg($translator->trans('$1 files uploaded.', array('$1' => $nrOfFiles), 'media_archive'), "ok");
    camp_html_goto_page("/$ADMIN/media-archive/multiedit_file.php");
} else {
    camp_html_add_msg($f_path . DIR_SEP . basename($newFilePath));
    camp_html_goto_page($backLink);
}

?>
