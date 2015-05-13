<?php
/**
 * @package Newscoop
 * @copyright 2015 Sourcefabric o.p.s.
 * @author Paweł Mikołąjczuk <pawel.mikolajczuk@sourcefabric.org>
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop;

/**
 * Cache key
 */
class CacheKey extends ValueObject
{
    public $key;

    public function __toString()
    {
        return $this->key;
    }
}