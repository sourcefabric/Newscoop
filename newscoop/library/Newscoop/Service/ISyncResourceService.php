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
     * 		The Resource to be synchronized, not null, not empty.
     *
     * @return Resource
     * 		The synchronized Resource.
     */
    function getSynchronized(Resource $resource);

    /**
     * Provides the synchronized Resource based on the provided resource.
     * The synchronization of a resource means the association of that resource with the database.
     *
     * @param string $path
     * 		The Resource Path to be synchronized, not null, not empty.
     *
     * @return Resource
     * 		The synchronized Resource.
     */
    function findByPath($path);

    /**
     * Provides the synchronized Resource based on the provided resource.
     * The synchronization of a resource means the association of that resource with the database.
     *
     * @param string|int $pathOrID
     * 		The Resource Path or Id to be synchronized, not null, not empty.
     *
     * @return Resource
     * 		The synchronized Resource.
     */
    function findByPathOrId($pathOrId);
    
    /**
     * Provides the synchronized Resource based on the provided name and path.
     * The synchronization of a resource means the association of that resource with the database.
     *
     * @param string $name
     * 		The name of the Resource to be synchronized, not null, not empty.
     *
     * @param string $path
     * 		The path of the Resource to be synchronized, not null, not empty.
     *
     * @return Resource
     * 		The synchronized Resource.
     */
    function getResource($name, $path);

    /**
     * Provides the synchronized Resource based on the provided theme path.
     * The synchronization of a resource means the association of that resource with the database.
     *
     * @param str $themePath
     * 		The theme path to be synchronized, not null, not empty.
     *
     * @return Resource
     * 		The synchronized Resource.
     */
    function getThemePath($themePath);

    /* --------------------------------------------------------------- */

    /**
     * Clears all resources that are prefixed by the provided path.
     * @param str $path
     * 		The path for which all resources should be cleared has to end with a '/'.
     */
    function clearAllFor($path);
}