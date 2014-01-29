<?php
/**
 * @package Newscoop
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop;

/**
 * Base Newscoop Criteria class.
 */
class Criteria
{
    /**
     * @var int
     */
    public $firstResult = 0;

    /**
     * @var int
     */
    public $maxResults = 25;

    /**
     * @var array
     */
    public $orderBy = array();

    /**
     * Criteria parameters operators chars
     * @var array
     */
    public $perametersOperators = array();
}
