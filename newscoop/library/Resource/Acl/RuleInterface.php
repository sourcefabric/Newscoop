<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Resource\Acl;

/**
 * Acl rule interface
 */
interface RuleInterface
{
    /**
     * Get type
     *
     * @return string
     */
    public function getType();

    /**
     * Get resource
     *
     * @return mixed
     */
    public function getResource();

    /**
     * Get action
     *
     * @return mixed
     */
    public function getAction();
}
