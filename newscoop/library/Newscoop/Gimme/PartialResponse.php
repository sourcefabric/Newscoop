<?php
/**
 * @package Newscoop\Gimme
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Gimme;

/**
 * Gimme Pagination object.
 */
class PartialResponse {
    /**
     * Fields property - string with comma separated fields
     * @var string
     */
    protected $fields = null;

    /**
     * Set fields
     * @param string $fields string with comma separated fields
     */
    public function setFields($fields)
    {
        $this->fields = explode(',', $fields);

        return $this;
    }

    /**
     * Get Fields
     * @return string string with comma separated fields
     */
    public function getFields()
    {
        return $this->fields;
    }
}