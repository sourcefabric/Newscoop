<?php
/**
 * @package Newscoop
 * @copyright 2014 Sourcefabric o.p.s.
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

/**
 * Placeholder service
 */
class PlaceholdersService
{
    /**
     * Set given value for given property
     *
     * @param string $property Given property
     * @param string $value    Value for given property
     *
     * @return void
     */
    public function set($property, $value)
    {
        $this->$property = $value;
    }

    /**
     * Get value for given property
     *
     * @param string $property Given property
     *
     * @return string
     */
    public function get($property)
    {
        return $this->$property;
    }
}
