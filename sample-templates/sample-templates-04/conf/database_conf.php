<?php
/**
 * @package Campsite
 *
 * @author Holman Romero <holman.romero@gmail.com>
 * @copyright 2009 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @version $Revision$
 * @link http://www.sourcefabric.org
 */

global $Campsite;

/**
 * This is only to keep backward compatibility.
 * further we will implement the use of CampConfig class
 * in the administrator.
 */
$Campsite['DATABASE_NAME'] = 'campsite34';
$Campsite['DATABASE_SERVER_ADDRESS'] = 'localhost';
$Campsite['DATABASE_SERVER_PORT'] = '3306';
$Campsite['DATABASE_USER'] = 'root';
$Campsite['DATABASE_PASSWORD'] = '';

/** Database settings **/
$Campsite['db']['type'] = 'mysql';
$Campsite['db']['host'] = $Campsite['DATABASE_SERVER_ADDRESS'];
$Campsite['db']['port'] = $Campsite['DATABASE_SERVER_PORT'];
$Campsite['db']['name'] = $Campsite['DATABASE_NAME'];
$Campsite['db']['user'] = $Campsite['DATABASE_USER'];
$Campsite['db']['pass'] = $Campsite['DATABASE_PASSWORD'];

?>