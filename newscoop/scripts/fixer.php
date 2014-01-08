#!/usr/bin/env php
<?php
/**
 * @package Newscoop
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2014 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */
$newscoopDir = realpath(__DIR__ . '/../');
// set chmods for directories
exec('chmod -R 777 '.$newscoopDir.'/cache/');
exec('chmod -R 777 '.$newscoopDir.'/log/');
exec('chmod -R 777 '.$newscoopDir.'/conf/');
exec('chmod -R 777 '.$newscoopDir.'/library/Proxy/');
exec('chmod -R 777 '.$newscoopDir.'/themes/');
exec('chmod -R 777 '.$newscoopDir.'/plugins/');
exec('chmod -R 777 '.$newscoopDir.'/public/files/');
exec('chmod -R 777 '.$newscoopDir.'/images/');
