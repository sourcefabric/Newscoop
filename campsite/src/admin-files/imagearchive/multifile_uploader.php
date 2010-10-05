<?php
/**
 * @package Campsite
 *
 * @author Holman Romero <holman.romero@sourcefabric.org>
 * @copyright 2010 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.sourcefabric.org
 */
 
require_once($GLOBALS['g_campsiteDir']. "/classes/Plupload.php");

if (!$g_user->hasPermission('AddImage')) {
	camp_html_display_error(getGS("You do not have the right to add images."));
	exit;
}

// Plupload
$files = Plupload::OnMultiFileUpload($Campsite['IMAGE_DIRECTORY']);
?>
