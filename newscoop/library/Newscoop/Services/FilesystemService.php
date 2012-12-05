<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

/**
 * Filesystem service
 */
class FilesystemService
{
    public static function isReadable($fileName, $message = true)
    {
        if (!is_readable($fileName)) {
            if ($message) {
                echo "\nThis script requires access to the file $p_fileName.\n";
                echo "Please run this script as a user with appropriate privileges.\n";
                echo "Most often this user is 'root'.\n\n";
            }
            return false;
        }
        return true;
    }
}
