<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Resource\Acl;

/**
 * Acl storage interface
 */
interface StorageInterface
{
    /**
     * Get rules for role
     *
     * @param Zend_Acl_Role_Interface $role
     * @return array
     */
    public function getRules(\Zend_Acl_Role_Interface $role);

    /**
     * Get stored resources
     *
     * @return array
     */
    public function getResources();
}
