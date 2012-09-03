<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Tools\Console\Helper;

use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\DependencyInjection\Container;

/**
 * Service Container Helper
 */
class ServiceContainerHelper extends Helper
{
    /** @var Symfony\Component\DependencyInjection\Container */
    protected $container;

    /**
     * @param Symfony\Component\DependencyInjection\Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Get service
     *
     * @param string $name
     * @return mixed
     */
    public function getService($name)
    {
        return $this->container->getService($name);
    }

    /**
     * @see Helper
     */
    public function getName()
    {
        return 'serviceContainer';
    }
}
