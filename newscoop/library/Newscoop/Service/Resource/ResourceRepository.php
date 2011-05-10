<?php

/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Service\Resource;

use Newscoop\Utils\Tools;
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

	/** @var array  */
	private $repositoryProperties = NULL;

	/** @var array  */
	private $serviceCache = NULL;

	private function __construct() {}

	/* --------------------------------------------------------------- */

	/**
	 * Provides the properties of the repository.
	 *
	 * @return array
	 *		The array containing the properties.
	 */
	public function getRepositoryProperties()
	{
		if ($this->repositoryProperties === NULL) {
			$this->repositoryProperties = $this->getResourceFor(new ResourceId(__CLASS__, ResourceId::TYPE_PROPERTIES));
		}
		return $this->repositoryProperties;
	}

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
	public function getResourceFor(ResourceId $resourceId)
	{		
		Validation::notEmpty($resourceId, 'resourceId');
		//TODO: add implementation for visitors that can provide custom resources (ResourceLocator).

		if($resourceId->getType() == ResourceId::TYPE_SERVICE){
			$simpleClassName = $this->extractSimpleClassName($resourceId->getId());

			$properties = $this->getRepositoryProperties();
			$implClass = $properties['service.'.$simpleClassName];

			if(!isset($implClass)){
				throw new \Exception("No default implementation set in the property file for '$simpleClassName'.");
			}

			// Instantiating the service
			$service = new $implClass($resourceId);
			$service = $this->configureService($service);
			
			return $this->cacheService($resourceId->getId(), $service);
		}
		else if($resourceId->getType() == ResourceId::TYPE_PROPERTIES){
			$simpleClassName = $this->extractSimpleClassName($resourceId->getId());
			return $this->loadProperties($simpleClassName);
		}
		throw new \Exception("Cannot locate the resource '.$resourceId->getFullId().' of type '.$resourceId->getType().'.");
	}

	/* --------------------------------------------------------------- */

	/**
	 * Configures the provided service instance with all configurations available in the properties of the repository.
	 * This configurations works as folows:
	 *  - if in the repository properties there is a property that has the format [simple class name].[field name]
	 *    it will automatically assigne the value found to the service field.
	 *  - the [simple class name] represents the service implementation simple class name (ex: 'ThemeServiceLocalFileSystem')
	 *  - the [field name] represents the field name inside the instance that will be assigned the value for that property.
	 *
	 *  ex: ThemeServiceLocalFileSystem.path=\usr\local
	 *
	 *  This will configure the field path in the service instance with the provided value.
	 *
	 * @param mixed $service
	 * 		The service to be configured, not null.
	 *
	 * @return mixed
	 *		The configured service instance.
	 */
	protected function configureService($service)
	{
		$prefix = 'config.'.$this->extractSimpleClassName(get_class($service)).'.';
		$length = strlen($prefix);

		$properties = $this->getRepositoryProperties();
		foreach ($properties as $key => $val){
			if(strncmp($key, $prefix, $length) == 0){
				$fieldName = substr($key, $length);
				$service->$fieldName = $val;
			}
		}

		return $service;
	}

	/**
	 * Find the service instance cached under the specified key.
	 *
	 * @param string $key
	 * 		The key to search by, *(not null not empty).
	 */
	protected function findInServiceCache($key){
		if ($this->serviceCache !== NULL) {
			$service = $this->serviceCache[$key];

			if(isset($service)){
				return $service;
			}
		}
		return NULL;
	}

	/**
	 * Provides to the cache the serice instance.
	 *
	 * @param string $key
	 * 		The key to be used for caching the provided service instance, *(not null not empty).
	 * @param mixed $service
	 * 		The service instance to cache, *(not null).
	 * @return mixed
	 *		The cached service instance.
	 */
	protected function cacheService($key, $service)
	{
		if ($this->serviceCache === NULL) {
			$this->serviceCache = array();
		}
		$this->serviceCache[$key] = $service;
		return $service;
	}

	/* --------------------------------------------------------------- */


	/**
	 * Load the property file at the specified path.
	 *
	 * @param string
	 *		The path where the property file is located, not null not, empty.
	 *
	 * @return array
	 *		The array containing the loaded properties.
	 */
	protected function loadProperties($path)
	{
		Validation::notEmpty($path, 'path');
		return parse_ini_file($this->processConfigurationsPath($path));
	}

	/**
	 * Provides the configuration path, where all property files are located.
	 *
	 * @param string $className
	 *		The class name for which to locate the property file, this done by convention
	 *		the class name can contain alaso the namespace which will be removed, the property file
	 *		will be considered as the simple class name with the 'properties' extension.
	 * @return string
	 *		The config path based on the properties convention for the provided class name.
	 */
	protected function processConfigurationsPath($className)
	{
		Validation::notEmpty($className, 'className');

		$propertiesFolder = __DIR__;
		//Removing the 'Resources' from the dir path strlen('Resources')=9
		$propertiesFolder = substr($propertiesFolder, 0, -9);

		$simpleClassName = $this->extractSimpleClassName($className);
		return $propertiesFolder.DIR_SEP.'configs'.DIR_SEP.$simpleClassName.'.properties';
	}

	/**
	 * Provides the simple class name based on the provided full class name (namespace + simple class name).
	 *
	 * @param string $className
	 *		The class name from where to extract the simple class name.
	 * @return string
	 *		The config path.
	 */
	protected function extractSimpleClassName($className)
	{
		Validation::notEmpty($className, 'className');

		$pos = strrpos($className, '\\');
		if ($pos !== false) {
			return substr($className, $pos + 1);
		}
		return $className;
	}

}