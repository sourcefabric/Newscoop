<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop;

/**
 * Random
 */
class Random
{
    /**
     * @var array
     */
    protected $chars = array();

    /**
     * @var int
     */
    protected $chars_count;

    /**
     */
    public function __construct()
    {
        $this->chars = array_merge(range(0, 9), range('a', 'z'));
        $this->chars_count = count($this->chars);
    }

    /**
     * Generate random string
     *
     * @param int $length
     * @return string
     */
    public function getRandomString($length)
    {
        if (empty($length)) {
            throw new \InvalidArgumentException("Length can't be empty.");
        }

        $random = array();
        for ($i = 0; $i < $length; $i++) {
            $random[] = $this->chars[mt_rand(0, $this->chars_count - 1)];
        }

        return implode('', $random);
    }
}
