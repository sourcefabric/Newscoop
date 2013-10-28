<?php
/**
 * @package Newscoop
 * @subpackage Soundcloud plugin
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

$translator = \Zend_Registry::get('container')->getService('translator');

$trackTypes = array(
    'original' => $translator->trans('Original', array(), 'plugin_soundcloud'),
    'remix' => $translator->trans('Remix', array(), 'plugin_soundcloud'),
    'live' => $translator->trans('Live', array(), 'plugin_soundcloud'),
    'recording' => $translator->trans('Recording', array(), 'plugin_soundcloud'),
    'spoken' => $translator->trans('Spoken', array(), 'plugin_soundcloud'),
    'podcast' => $translator->trans('Podcast', array(), 'plugin_soundcloud'),
    'demo' => $translator->trans('Demo', array(), 'plugin_soundcloud'),
    'in progress' => $translator->trans('Work in progress', array(), 'plugin_soundcloud'),
    'stem' => $translator->trans('Stem', array(), 'plugin_soundcloud'),
    'loop' => $translator->trans('Loop', array(), 'plugin_soundcloud'),
    'sound effect' => $translator->trans('Sound Effect', array(), 'plugin_soundcloud'),
    'sample' => $translator->trans('One Shot Sample', array(), 'plugin_soundcloud'),
    'other' => $translator->trans('Other'),
);

$licenseTypes = array(
    'no-rights-reserved' => $translator->trans('The work is in the public domain', array(), 'plugin_soundcloud'),
    'all-rights-reserved' => $translator->trans('All rights are reserved', array(), 'plugin_soundcloud'),
    'cc-by' => 'Creative Commons Attribution',
    'cc-by-nc' => 'Creative Commons Attribution Noncommercial',
    'cc-by-nd' => 'Creative Commons Attribution No Derivative Works',
    'cc-by-sa' => 'Creative Commons Attribution Share Alike',
    'cc-by-nc-nd' => 'Creative Commons Attribution Noncommercial Non Derivate Works',
    'cc-by-nc-sa' => 'Creative Commons Attribution Noncommercial Share Alike',
);

$keyTypes = array(
    'A', 'Ab', 'B', 'Bb', 'C', 'C#', 'D', 'Eb', 'E', 'F', 'F#', 'G', 'Ab', 'Am',
    'Abm', 'Bm', 'Bbm', 'Cm', 'C#m', 'Dm', 'Ebm', 'Em', 'Fm', 'F#m', 'Gm', 'Abm',
);
