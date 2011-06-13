<?php

namespace Newscoop\Acl\Assertions;

use Zend_Acl_Assert_Interface, Zend_Acl, Zend_Acl_Role_Interface;

class SaasAssertion implements Zend_Acl_Assert_Interface
{
	/**
	 *
	 * @param Zend_Acl $acl
	 * @param Zend_Acl_Role_Interface $role
	 * @param Zend_Acl_Resource_Interface $resource
	 * @param string $privilege
	 */
	public function assert(Zend_Acl $acl,
                           Zend_Acl_Role_Interface $role = null,
                           Zend_Acl_Resource_Interface $resource = null,
                           $privilege = null)
	{
		var_dump( 'got here' );
		return false;
	}
}
?>