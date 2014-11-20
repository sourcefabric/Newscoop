<?php
/**
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @package Newscoop
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\EventDispatcher\Events;

use Symfony\Component\EventDispatcher\GenericEvent as SymfonyGenericEvent;

/**
 * ListObjectsEvent class.
 *
 * Collect list objects registrations array
 */
class CollectObjectsDataEvent extends SymfonyGenericEvent
{	
	/**
	 * Array with list object registration data
	 * @var array
	 */
    public $listObjects = array();

    /**
     * Array with object type registration data
     * @var array
     */
    public $objectTypes = array();

    /**
     * Add list objects registration data
     * @param string $name
     * @param array $listObject
     */
    public function registerListObject($name, array $listObject)
    {
        $this->listObjects[$name] = $listObject;
    }

    /**
     * Get all list objects registrations data
     * @return array
     */
    public function getListObjects()
    {
        return $this->listObjects;
    }

    /**
     * Add object type registration data
     * @param string $name
     * @param array $objectType
     */
    public function registerObjectTypes($name, array $objectType)
    {   
        $this->objectTypes[$name] = $objectType;
    }

    /**
     * Get all list objects registrations data
     * @return array
     */
    public function getObjectTypes()
    {
        return $this->objectTypes;
    }
}
