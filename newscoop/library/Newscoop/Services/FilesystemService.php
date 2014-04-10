<?php
/**
 * @package Newscoop
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2014 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

use Doctrine\ORM\EntityManager;

/**
 * Filesystem service
 */
class FilesystemService
{

    /** @var Doctrine\ORM\EntityManager */
    protected $em;

    /**
     * Constructor
     *
     * @param Doctrine\ORM\EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Check if file is isReadable
     *
     * @param  string  $fileName
     * @param  boolean $message  Show message
     *
     * @return boolean
     */
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

    /**
     * Makes a file name safe to use.
     *
     * @param  string  $fileName
     *
     * @return string
     */
    public function sanitizeFileName($fileName)
    {
        $fileName = htmlentities($fileName, ENT_QUOTES, 'UTF-8');
        $fileName = preg_replace('~&([a-z]{1,2})(acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i', '$1', $fileName);
        $fileName = html_entity_decode($fileName, ENT_QUOTES, 'UTF-8');
        $fileName = preg_replace(array('~[^0-9a-z]~i', '~[ -]+~'), ' ', $fileName);

        return trim($fileName, ' -');
    }
}
