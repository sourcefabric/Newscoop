<?php
/**
 * @package Newscoop
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services\Plugins;

use Newscoop\EventDispatcher\EventDispatcher;
use Newscoop\EventDispatcher\Events\PluginHooksEvent;

/**
 * Plugins Service
 *
 * Service for plugins stuff
 */
class PluginsService
{
    /**
     * @var Newscoop\EventDispatcher\EventDispatcher
     */
    private $dispatcher;

    /**
     * @param Newscoop\EventDispatcher\EventDispatcher $dispatcher
     */
    public function __construct($dispatcher)
    {
        $this->dispatcher = $dispatcher;    
    }

    /**
     * Dispatch hook event and render collected Response objects
     * @param  string $eventName
     * @param  mixed $subject
     * @param  array $options
     * 
     * @return string
     */
    public function renderPluginHooks($eventName, $subject = null, $options = null)
    {
        $content = '';
        $pluginHooksEvent = $this->dispatcher->dispatch($eventName, new PluginHooksEvent($subject, $options));

        foreach ($pluginHooksEvent->getHooksResponses() as $key => $response) {
            $content .= $response->getContent();
        }

        return $content;
    }
}