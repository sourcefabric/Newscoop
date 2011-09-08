<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Service container action helper
 */
class Action_Helper_Service extends Zend_Controller_Action_Helper_Abstract
{
    /** @var sfServiceContainerInterface */
    private $container;

    /**
     */
    public function init()
    {
        $this->container = $this->getActionController()
            ->getInvokeArg('bootstrap')
            ->getResource('container');
    }

    /**
     * Get service
     *
     * @param string $service
     * @return mixed
     */
    public function getService($service)
    {
        if (!$this->container->hasService($service)) {
            throw new InvalidArgumentException("Service '$service' not found");
        }

        return $this->container->getService($service);
    }

    /**
     * Direct get service
     *
     * @param string $service
     * @return mixed
     */
    public function direct($service)
    {
        return $this->getService($service);
    }
}
