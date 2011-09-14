<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 */
abstract class MetaEntity
{
    /**
     * Call method via attribute
     * Provides backward compatibility for callbacks called as property
     *
     * @param string $property
     */
    public function __get($property)
    {
        if (method_exists($this, $property)) {
            return $this->$property();
        }

        throw new \InvalidArgumentException("Property '$property' not found");
    }
}
