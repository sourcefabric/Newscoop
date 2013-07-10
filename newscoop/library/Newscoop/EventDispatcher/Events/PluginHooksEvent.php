<?php
/**
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @package Newscoop
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\EventDispatcher\Events;

use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Response;

/**
 * PluginHooksEvent class.
 *
 * Collect Response objects from plugins admin iterface hooks.
 */
class PluginHooksEvent extends GenericEvent
{
    public $hooksResponses = array();

    public function addHookResponse(Response $response)
    {   
        $this->hooksResponses[] = $response;
    }

    public function getHooksResponses()
    {
        return $this->hooksResponses;
    }
}