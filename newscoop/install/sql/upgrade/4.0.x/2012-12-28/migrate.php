<?php
/**
 * @package Newscoop
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

global $application;

$application->bootstrap('container');
$em = Zend_Registry::get('container')->getService('em');

try {
    $em->getConnection()->exec('ALTER TABLE  `Articles` ADD  `indexed` TIMESTAMP NULL DEFAULT NULL');
} catch (Exception $e) {
    // ignore if column exists
    if ($e->getCode() !== '42S21') {
        throw $e;
    }
}
