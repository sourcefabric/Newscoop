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
class ListObjectsEvent extends SymfonyGenericEvent
{	
	/**
	 * Array with list object registration array
	 * @var array
	 */
    public $objects = array();

    /**
     * Add list objects registration array
     * @param array $name
     * @param array $listObject
     */
    public function registerListObject($name, array $listObject)
    {   
        $this->objects[$name] = $listObject;
    }

    /**
     * Get all list objects registrations array
     * @return array
     */
    public function getObjects()
    {
        return $this->objects;
    }
}