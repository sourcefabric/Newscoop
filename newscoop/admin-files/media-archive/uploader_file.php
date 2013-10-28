<?php
/**
 * @package Newscoop
 *
 * @author Mihai Nistor <mihai.nistor@sourcefabric.org>
 * @copyright 2010 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.sourcefabric.org
 */

require_once($GLOBALS['g_campsiteDir']. "/classes/Plupload.php");
require_once($GLOBALS['g_campsiteDir'].'/classes/Attachment.php');

$translator = \Zend_Registry::get('container')->getService('translator');

if (!$g_user->hasPermission('AddFile')) {
	camp_html_display_error($translator->trans("You do not have the right to add files.", array(), 'media_archive'));
	exit;
}

$attachmentObj = new Attachment();
$attachmentObj->makeDirectories();
// Plupload
$files = Plupload::OnMultiFileUpload($attachmentObj->getStorageLocation());
?>
