<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop;

/**
 * Value Object
 */
abstract class ValueObject
{
    /**
     * @param array $values
     */
    public function __construct($values = null)
    {
        if (is_array($values)) {
            foreach ($values as $key => $val) {
                if (property_exists($this, $key) && $val !== null) {
                    $this->$key = $val;
                }
            }
        }
    }
}
