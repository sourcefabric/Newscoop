<?php
/**
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\DependencyInjection;

use Symfony\Component\DependencyInjection\Container as SymfonyContainer;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

/**
 * ContainerBuilder class.
 *
 * Provides wrapper methods for symfony's Container.
 */
class ContainerBuilder extends SymfonyContainer
{
    /**
    * Construct object.
    *
    * @param array $options The default options
    */
    public function __construct($options = array())
    {
        parent::__construct(new ParameterBag($options));
    }

    /**
    * Sets a service.
    *
    * Wrapper for ContainerBuilder::set method.
    *
    * @param string $id The service identifier
    * @param object $service The service instance
    */
    public function setService($id, $service)
    {
        parent::set($id, $service);
    }

    /**
    * Returns true if the given service is defined.
    *
    * Wrapper for ContainerBuilder::has method.
    *
    * @param string $id The service identifier
    * @return boolean True if the service is defined, false otherwise
    */
    public function hasService($id)
    {
        return parent::has($id);
    }

    /**
    * Gets a service.
    *
    * Wrapper for ContainerBuilder::get method.
    *
    * @param string  $id The service identifier
    * @return object The associated service
    */
    public function getService($id)
    {
        return parent::get($id);
    }

    /**
    * Gets all service ids.
    *
    * Wrapper for ContainerBuilder::getServiceIds method.
    *
    * @return array An array of all defined service ids
    */
    public function getServiceIds()
    {
        return parent::getServiceIds();
    }

    /**
    * Sets an alias for an existing service.
    *
    * Wrapper for ContainerBuilder::setAlias method.
    *
    * @param string $alias The alias to create
    * @param mixed  $id The service to alias
    */
    public function setAlias($alias, $id)
    {
        parent::setAlias($alias, $id);
    }

    /**
    * Gets all defined aliases.
    *
    * Wrapper for ContainerBuilder::getAliases method.
    *
    * @return array An array of aliases
    */
    public function getAliases()
    {
        return parent::getAliases();
    }

    /**
    * Registers a service definition.
    *
    * Wrapper for ContainerBuilder::getAliases method.
    * This methods allows for simple registration of service definition
    * with a fluid interface.
    *
    * @param string $id The service identifier
    * @param string $class The service class
    */
    public function register($id, $class = null)
    {
        return parent::register($id, $class);
    }
}
