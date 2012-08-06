<?php
/**
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\EventDispatcher\Events;

use Symfony\Component\EventDispatcher\GenericEvent as SymfonyGenericEvent;

/**
 * GenericEvent class.
 *
 * Provides wrapper methods for symfony's GenericEvent.
 */
class GenericEvent extends SymfonyGenericEvent
{

    /**
    * Construct GenericEvent object.
    *
    * @param Object $subject The subject of the event
    * @param array $arguments The arguments passed to event listener.
    */
    public function __construct($subject = null, array $arguments = array())
    {
        parent::__construct($subject, $arguments);
    }
}