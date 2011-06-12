<?php

/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Service;

use Newscoop\Entity\Output;
use Newscoop\Service\IEntityService;

/**
 * Provides the services for the Outputs.
 */
interface IOutputService extends IEntityService
{

	/**
	 * Provides the class name as a constant. 
	 */
	const NAME = __CLASS__;
	
	/* --------------------------------------------------------------- */
	
	/**
	 * Provides the Output that has the provided name.
	 *
	 * @param string $name
	 *		The name to be searched, not null, not empty.
	 *
	 * @return Newscoop\Entity\Output
	 *		The Output, null if no Output could be found for the provided name.
	 */
	function findByName($name);
}