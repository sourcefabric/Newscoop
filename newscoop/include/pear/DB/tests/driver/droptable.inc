<?php

/**
 * Contains the function for dropping tables
 *
 * PHP versions 4 and 5
 *
 * LICENSE: This source file is subject to version 3.0 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_0.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category   Database
 * @package    DB
 * @author     Daniel Convissor <danielc@php.net>
 * @copyright  1997-2007 The PHP Group
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    $Id: droptable.inc,v 1.3 2007/07/06 05:19:21 aharvey Exp $
 * @link       http://pear.php.net/package/DB
 */

/**
 * Drops the requested table
 *
 * @param object $dbh    the PEAR DB object currently in use
 * @param string $table  the name of the table to be dropped
 *
 * @return int  DB_OK on success.  DB_Error object on failure.
 */
function drop_table($dbh, $table) {
    switch ($dbh->phptype) {
        case 'fbsql':
            $res = $dbh->query("DROP TABLE $table CASCADE");
            break;
        default:
            $res = $dbh->query("DROP TABLE $table");
    }
    return $res;
}
