<?php
/**
 * @package Newscoop
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */


namespace Newscoop\NewscoopBundle\Routing;
 
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\Routing\RouteCollection;
use Newscoop\Services\Plugins\ManagerService;
 
class PluginsLoader implements LoaderInterface
{
    protected $loaded = false;

    /**
     * @var ManagerService
     */
    protected $pluginsManager;

    protected $container;

    public function __construct($pluginsManager, $container) {
        $this->pluginsManager = $pluginsManager;
        $this->container = $container;
    }
 
    public function load($resource, $type = null)
    {
        if (true === $this->loaded) {
            throw new \RuntimeException('Do not add this loader twice');
        }

        $routes = new \Symfony\Component\Routing\RouteCollection();

        $availablePlugins = $this->pluginsManager->getInstalledPlugins();
        $dirs = array();
        foreach ($availablePlugins as $plugin) {
            $pluginPath = explode('\\', $plugin);
            $directoryPath = realpath(__DIR__ . '/../../../../plugins/'.$pluginPath[0].'/'.$pluginPath[1].'/Resources/config/routing.yml');
            if ($directoryPath) {
                $dirs[] = realpath(__DIR__ . '/../../../../plugins/'.$pluginPath[0].'/'.$pluginPath[1].'/Resources/config/');
            }
        }

        $routesCollection = new \Symfony\Component\Routing\RouteCollection();
        foreach ($dirs as $directory) {
            $routeLoader = $this->container->get('routing.loader');
            $routesCollection->addCollection($routeLoader->load($directory.'/routing.yml'));
        }
 
        return $routesCollection;
    }
 
    public function supports($resource, $type = null)
    {
        return 'plugins' === $type;
    }
 
    public function getResolver()
    {}
 
    public function setResolver(LoaderResolverInterface $resolver)
    {}
}