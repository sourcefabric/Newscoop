<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Search;

use Newscoop\ValueObject;

/**
 * Query
 */
class Query extends ValueObject
{
    /**
     * @var string
     */
    public $core;

    /**
     * @var string
     */
    public $q;

    /**
     * @var string
     */
    public $fq;

    /**
     * @var string
     */
    public $sort;

    /**
     * @var int
     */
    public $start = 0;

    /**
     * @var int
     */
    public $rows = 10;

    /**
     * @var string
     */
    public $fl = 'number';

    /**
     * @var string
     */
    public $df = 'title';

    /**
     * @var string
     */
    public $wt = 'json';

    /**
     * @var string
     */
    public $defType = 'edismax';

    /**
     * @var string
     */
    public $qf;
}
