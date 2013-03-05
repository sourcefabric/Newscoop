<?php
/**
 * @package Newscoop
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */


namespace Newscoop\NewscoopBundle\Routing;
 
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Newscoop\Services\PluginsManagerService;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Loader\YamlFileLoader;
 
class PluginsLoader implements LoaderInterface
{
    private $loaded = false;

    /**
     * @var PluginsManagerService
     */
    private $pluginsManager;

    public function __construct($pluginsManager) {
        $this->pluginsManager = $pluginsManager;
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

        if (count($dirs) > 0) {
            $locator = new FileLocator($dirs);
            $loader = new YamlFileLoader($locator);
            $routes = $loader->load('routing.yml');
        }
 
        return $routes;
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