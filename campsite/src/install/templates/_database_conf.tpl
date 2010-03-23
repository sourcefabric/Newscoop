<?php
/**
 * @package Campsite
 *
 * @author Holman Romero <holman.romero@gmail.com>
 * @copyright 2009 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @version $Revision$
 * @link http://www.campware.org
 */

global $Campsite;

/**
 * This is only to keep backward compatibility.
 * further we will implement the use of CampConfig class
 * in the administrator.
 */
$Campsite['DATABASE_NAME'] = '{{ $DATABASE_NAME }}';
$Campsite['DATABASE_SERVER_ADDRESS'] = '{{ $DATABASE_SERVER_ADDRESS }}';
$Campsite['DATABASE_SERVER_PORT'] = '{{ $DATABASE_SERVER_PORT }}';
$Campsite['DATABASE_USER'] = '{{ $DATABASE_USER }}';
$Campsite['DATABASE_PASSWORD'] = '{{ $DATABASE_PASSWORD }}';

/** Database settings **/
$Campsite['db']['type'] = 'mysql';
$Campsite['db']['host'] = $Campsite['DATABASE_SERVER_ADDRESS'];
$Campsite['db']['port'] = $Campsite['DATABASE_SERVER_PORT'];
$Campsite['db']['name'] = $Campsite['DATABASE_NAME'];
$Campsite['db']['user'] = $Campsite['DATABASE_USER'];
$Campsite['db']['pass'] = $Campsite['DATABASE_PASSWORD'];

?>
