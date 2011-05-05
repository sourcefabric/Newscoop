<?php

/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Service\Resource;

use Newscoop\Utils\Validation;

/**
 * Provides the id for resource identification.
 */
class ResourceId {

	const TYPE_ROOT = 'root';
	const TYPE_SERVICE = 'service';
	
	/* --------------------------------------------------------------- */
	
	/** @var Newscoop\Service\Resource\ResourceId  */
	private $parent;

	/** @var string  */
	private $id;
	
	/** @var string  */
	private $type;

	/**
	 * Construct a resource id for the provided id.
	 * The id should not be provided as a plain string it should be the actuall simple
	 * class name of the top class (Controller) that creates the resource.
	 *
	 * @param string $id
	 *		The id of the theme, must not be null or empty.
	 */
	public function __construct($id) 
	{
		Validation::notEmpty($id, 'id');
		$this->id = $id;
		$this->type = ResourceId::TYPE_ROOT;
		$this->parent = NULL;
	}
	
	/* --------------------------------------------------------------- */

	/**
	 * Provides the id of the resource, attention this is not the full id is just this resource id.
	 *
	 * @return string
	 *		The id of the resource.
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Provides the full id of the resource, this id will be full path of the requesting layer components
	 * separated by ':'.
	 *
	 * @return string
	 *		The full id of the resource.
	 */
	public function getFullId()
	{
		if($this->parent != NULL){
			return $this->parent->getFullId().':'.$this->id;
		}
		return $this->id;
	}

	/* --------------------------------------------------------------- */
	
	/**
	 * Provides the type of the resource, please check the contstants on this class to see what are the posible types.
	 *
	 * @return string
	 *		The type of the resource.
	 */
	public function getType()
	{
		return $this->type;
	}
	
	/* --------------------------------------------------------------- */

	/**
	 * Provides the service for the requested name.
	 * The id should not be provided as a plain string it should be the actuall simple
	 * class name ot the service API beeing requested. As a convention
	 * this id should be obtain from the NAME contstant of a interface (ex: IThemeService::NAME),
	 * if apllicable.
	 *
	 * @param string $serviceName
	 *		The simple class name of the interface beeing requested, must not be null or empty.
	 *
	 * @return mixed
	 *		The resource id obtained for this service request.
	 */
	public function service($serviceName)
	{
		Validation::notEmpty($serviceName, "serviceName");
		$serviceId = new ResourceId($serviceName);
		$serviceId->type = ResourceId::TYPE_SERVICE;
		$serviceId->parent = $this;
		return ResourceRepository::getInstance()->getResourceFor($serviceId);
	}

}