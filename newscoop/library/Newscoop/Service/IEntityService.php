<?php

/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Service;

use Newscoop\Service\Model\Search\Search;

/**
 * Provides generic services for entities.
 */

interface IEntityService {

	/**
	 * Provides the service representing entity that has the provided id.
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
	 * Provides the service representing entity that has the provided id.
	 *
	 * @param mixed $id
	 *		The id to be searched, not null, not empty.
	 *
	 * @return Newscoop\Entity
	 *		The entity, null if no entities coyuld be found for the provided id.
	 */
	function findById($id);

	/* --------------------------------------------------------------- */

	/**
	 * Provides the count of the entities that can be associated with the provided search.
	 *
	 * @param Newscoop\Service\Model\Search\Search $search
	 *		The search criteria, not null.
	 *
	 * @return int
	 *		The entities count.
	 */
	function getCount(Search $search = NULL);

	/**
	 * Provides the all the entities that can be associated with the provided search
	 *
	 * @param Newscoop\Service\Model\Search\Search $search
	 *		The search criteria, not null.
	 *
	 * @param int|0 $offset
	 *	 	The offset from where to retrieve the entities.
	 *
	 * @param int|-1 $limit
	 *		The limit of entities to fetch, negaive value will fetch all entities found.
	 *
	 * @return array
	 *		The array of entities found.
	 */
	function getEntities(Search $search = NULL, $offset = 0, $limit = -1);

}