<?php
/**
 * @package Newscoop
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services\Plugins;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\ExpressionBuilder;
use Doctrine\ORM\EntityManager;
use Newscoop\EventDispatcher\EventDispatcher;
use Newscoop\EventDispatcher\Events\PluginHooksEvent;
use Newscoop\EventDispatcher\Events\CollectObjectsDataEvent;

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
     * @var Doctrine\ORM\EntityManager 
     */
    private $em;

    /**
     * Avaiable plugins
     * @var Collection
     */
    private $avaiablePlugins;

    /**
     * @param Newscoop\EventDispatcher\EventDispatcher $dispatcher
     * @param Doctrine\ORM\EntityManager $em
     */
    public function __construct($dispatcher, EntityManager $em)
    {
        $this->dispatcher = $dispatcher;
        $this->em = $em; 
    }

    public function getAllAvailablePlugins()
    {   
        if ($this->avaiablePlugins) {
            return $this->avaiablePlugins;
        }

        return $this->avaiablePlugins = $this->em->getRepository('Newscoop\Entity\Plugin')->findAll();
    }

    public function getEnabledPlugins()
    {
        $eb = new ExpressionBuilder();
        $expr = $eb->eq('enabled', true);
        $criteria = new Criteria($expr);

        $avaiablePlugins = new ArrayCollection($this->getAllAvailablePlugins());
        
        return $avaiablePlugins->matching($criteria);
    }

    public function getPluginByCriteria($criteria, $id)
    {
        $eb = new ExpressionBuilder();
        $expr = $eb->eq($criteria, intval($id));
        $criteria = new Criteria($expr);

        $avaiablePlugins = new ArrayCollection($this->getAllAvailablePlugins());
        
        return $avaiablePlugins->matching($criteria);
    }

    /**
     * Dispatch hook event and render collected Response objects
     * 
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

    /**
     * Dispatch event for list objects registration
     * 
     * @param  mixed $subject
     * @param  array $options
     * 
     * @return string
     */
    public function collectListObjects($subject = null, $options = array())
    {
        $collectedData = array('listObjects' => array(), 'objectTypes' => array());
        $listObjectsRegistration = $this->dispatcher->dispatch('newscoop.listobjects.register', new CollectObjectsDataEvent($subject, $options));

        foreach ($listObjectsRegistration->getListObjects() as $key => $object) {
            $collectedData['listObjects'][$key] = $object;
        }

        foreach ($listObjectsRegistration->getObjectTypes() as $key => $object) {
            $collectedData['objectTypes'][$key] = $object;
        }

        return $collectedData;
    }

    public function isEnabled($pluginName)
    {
        $plugin = $pluginService->getPluginByCriteria('name', $pluginName)->first();
        if ($plugin) {
            if ($plugin->getEnabled()) {
                return true;
            }
        }

        return false;
    }

    public function getPluginsDir()
    {
        $pluginsDir = __DIR__ . '/../../../../plugins';

        if (file_exists($pluginsDir)) {
            return realpath($pluginsDir);
        }

        return $pluginsDir;
    }
}