<?php

/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Service\Resource;

use Newscoop\Utils\Validation;
use Newscoop\Service\Resource\ResourceId;

/**
 * Provides the repository containing the resources required for linking the layers.
 * The resources can be services, configurations ...
 */
class ResourceRepository {

	/** @var Newscoop\Service\Resource\ResourceRespository  */
	private static $instance = NULL;

	/**
	 * Provides the singletone of the resource repository.
	 *
	 * @return Newscoop\Service\Resource\ResourceRespository
	 *		The singletone.
	 */
	public static function getInstance()
	{
		if (self::$instance === NULL) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/* --------------------------------------------------------------- */

	private function __construct() { }

	/* --------------------------------------------------------------- */

	/**
	 * Provides the resource for the provided resource id.
	 * The resource is located by using ResourceLocator (future).
	 *
	 * @param Newscoop\Service\Resource\ResourceId $resourceId
	 *		The resource id used for obtaining the assigned resource, must not be null or empty.
	 *
	 * @return mixed
	 *		The resource coresponding to the resource id.
	 */
	public function getResourceFor($resourceId)
	{
		Validation::notEmpty($resourceId, 'resourceId');
		//TODO: add implementation for visitors that can provide custom resources (ResourceLocator).

		if($resourceId->getType() == ResourceId::TYPE_SERVICE){
			// If the requested resource is a service and has not been provided by other means
			// we will try to located by using the id of the resource to treansform it into a
			// implementation class name based on the convention:
			// - the name of the service interface always start with 'I' (ex: IThemeService)
			// - the class name of the default implementation of the provided interface name
			//   has to be in the name space of the interface with '\Implementation\' and the name has to end
			//   with 'Default'
			// ex: 'Newscoop\Services\IThemeService' - > 'Newscoop\Services\Implementation\ThemeServiceDefault'
			
			//Remove the 'I' from the interface name and appending 'Default'.
			
			$pos = strrpos($resourceId->getId(), '\\');
			if ($pos === false) {
				$className = '\\Implementation\\'.substr($resourceId->getId(), 1);
			} else {
				$namespace = substr($resourceId->getId(), 0, $pos);
				$className = substr($resourceId->getId(), $pos + 1);
				$className = $namespace.'\\Implementation\\'.substr($className, 1);
			}
			
			$className = $className.'Default';
			// Instantiating the service
			return new $className($resourceId);
		}
		throw new \InvalidArgumentException("Cannot locate the resource '.$resourceId->getFullId().' of type '.$resourceId->getType().'.");
	}

}