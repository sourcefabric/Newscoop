<?php
/**
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @package Newscoop
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\EventDispatcher\Events;

use Symfony\Component\EventDispatcher\GenericEvent as SymfonyGenericEvent;
use Symfony\Component\HttpFoundation\Response;

/**
 * PluginHooksEvent class.
 *
 * Collect Response objects from plugins admin iterface hooks.
 */
class PluginHooksEvent extends SymfonyGenericEvent
{
	/**
	 * Array with Response objects from hooks
	 * @var array
	 */
    public $hooksResponses = array();

    /**
     * Add Response object to event
     *
     * @param Response $response
     */
    public function addHookResponse(Response $response)
    {
        $this->hooksResponses[] = $response;
    }

    /**
     * Override responses array
     *
     * @param array $response
     */
    public function setHookResponse(array $hooksResponses)
    {
        $this->hooksResponses = $hooksResponses;
    }

    /**
     * Get all stored Response objects from event
     *
     * @return array
     */
    public function getHooksResponses()
    {
        return $this->hooksResponses;
    }
}
