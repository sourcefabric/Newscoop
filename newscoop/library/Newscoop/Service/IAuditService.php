<?php

/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Service;

use Newscoop\Entity\Entity;

/**
 * Provides audit services for the database entities
 */
interface IAuditService
{

	/**
	 * Provides the class name as a constant. 
	 */
	const NAME = __CLASS__;
	
	/* --------------------------------------------------------------- */
	
	/**
	 * Audit the creation of a new entity.
	 * The audit should be called after the persistance of the entity.
	 *
	 * @param Entity $entity
	 * 		The entity to be audited, not null.
	 */
	function created(Entity $entity);

	/**
	 * Audit the change of a entity.
	 * The audit should be called before the persistance of the entity.
	 *
	 * @param Entity $entity
	 * 		The entity to be audited, not null.
	 */
	function changed(Entity $entity);

	/**
	 * Audit the remove of a entity.
	 * The audit should be called before the removal of the entity.
	 *
	 * @param Entity $entity
	 * 		The entity to be audited, not null.
	 */
	function removed(Entity $entity);
	
}