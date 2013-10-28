<?php
/**
 * @package Newscoop
 * @subpackage SoundCloud plugin
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

$translator = \Zend_Registry::get('container')->getService('translator');

echo camp_html_breadcrumbs(array(
    array($translator->trans('Plugins'), $Campsite['WEBSITE_URL'] . '/admin/plugins/manage.php'),
    array('SoundCloud', ''),
    array($translator->trans('Track manager', array(), 'plugin_soundcloud'), ''),
));

$attachement = false;
include 'master.php';
