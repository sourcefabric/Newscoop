<?php
/**
 * @package Newscoop
 * @subpackage Soundcloud plugin
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

echo camp_html_breadcrumbs(array(
    array(getGS('Plugins'), $Campsite['WEBSITE_URL'] . '/admin/plugins/manage.php'),
    array('Soundcloud', ''),
    array(getGS('Track manager'), ''),
));

$attachement = false;
include 'master.php';
?>