<?php

/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Service;

/**
 * Provides base generic services for entities.
 */

interface IEntityBaseService
{

	/**
	 * Provides the entity that has the provided id.
	 *
	 * @param mixed $id
	 *		The id to be searched, not null, not empty.
	 *
	 * @return Newscoop\Entity
	 *		The entity, not null.
	 * @throws InvalidArgumentException
	 * 		If for the provided id no entity could be found.
	 */
	function getById($id);

	/**
	 * Provides the entity that has the provided id.
	 *
	 * @param mixed $id
	 *		The id to be searched, not null, not empty.
	 *
	 * @return Newscoop\Entity
	 *		The entity, null if no entities could be found for the provided id.
	 */
	function findById($id);

}