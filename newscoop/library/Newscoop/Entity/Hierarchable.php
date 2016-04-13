<?php
/**
 * @package Newscoop
 * @copyright 2016 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity;

interface Hierarchable
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @return Hierarchable
     */
    public function getParent();
}
