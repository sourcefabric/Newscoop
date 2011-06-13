<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Utils;

/**
 * Newscoop exception used in error_handler - can change file and line
 */
class Exception extends \Exception
{
    /**
     * Set file
     *
     * @param string $file
     * @return void
     */
    public function setFile($file)
    {
        $file = realpath($file);
        if ($file) {
            $this->file = $file;
        }
    }

    /**
     * Set line
     *
     * @param int $line
     * @return void
     */
    public function setLine($line)
    {
        $this->line = (int) $line;
    }
}
