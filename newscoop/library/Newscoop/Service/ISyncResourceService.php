<?php

/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Service;

use Newscoop\Entity\Resource;

use Newscoop\Entity\Output;
use Newscoop\Service\IEntityService;

/**
 * Provides the services for the Outputs.
 */
interface ISyncResourceService extends IEntityService
{

	/**
	 * Provides the class name as a constant. 
	 */
	const NAME = __CLASS__;
	
	/* --------------------------------------------------------------- */
	
	/**
	 * Provides the synchronized Resource based on the provided resource.
	 * The synchronization of a resource means the association of that resource with the database.
	 *
	 * @param Resource $resource
	 *		The Resource to be synchronized, not null, not empty.
	 *
	 * @return Resource
	 *		The synchronized Resource.
	 */
	function getSynchronized(Resource $resource);
}