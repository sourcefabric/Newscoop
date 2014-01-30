<?php
/**
 * @package Newscoop
 * @copyright 2014 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Search;

/**
 * Query
 */
interface QueryInterface
{
    // TODO: Add basic stuff here


    public function encodeParameters();

    public function decodeParameters();

}
