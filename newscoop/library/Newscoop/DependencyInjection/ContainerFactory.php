<?php
/**
 * @package Newscoop
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\DependencyInjection;

use Newscoop\DependencyInjection\ContainerBuilder;
use Newscoop\DependencyInjection\Compiler\RegisterListenersPass;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;

/**
 * Create depenedency injection container object.
 */
class ContainerFactory {
    private $container;
    private $loader;

    /**
     * Set container to factory.
     * @param object $container Must implement Symfony\Component\DependencyInjection\ContainerBuilder
     */
    public function setContainer($container) 
    {
        $this->container = $container;
    }

    /**
     * Get container from factory.
     * @return Object ContainerBuilder
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Build container for Newscoop.
     * @return Object ContainerBuilder
     */
    public function buildContainer() 
    {
        $file = APPLICATION_PATH . '/../cache/container.php';

        /**
         * Use cached container if exists, and env is set up on production
         */
        if (APPLICATION_ENV === 'production' && file_exists($file) && php_sapi_name() !== 'cli') {
            require_once $file;
            $this->container = new \NewscoopCachedContainer();
        } else {
            $this->container = new ContainerBuilder();
            $this->container->addCompilerPass(new RegisterListenersPass());
            $this->mergeParameters();

            $this->container->compile();

            if (APPLICATION_ENV === 'production') {
                $dumper = new PhpDumper($this->container);
                file_put_contents($file, $dumper->dump(array(
                    'class' => 'NewscoopCachedContainer',
                    'base_class' => 'Newscoop\DependencyInjection\ContainerBuilder'
                )));
            }
        }

        return $this->container;
    }

    /**
     * Merge parameters for env's.
     */
    public function mergeParameters() 
    {
        $this->loader = new YamlFileLoader($this->container, new FileLocator(APPLICATION_PATH . '/configs/'));

        /**
         * Allways load config for env
         */
        $this->loader->load(APPLICATION_PATH . '/configs/parameters/parameters.yml');
        $this->container->setParameter('application_path', APPLICATION_PATH);

        if (APPLICATION_ENV !== 'production') {
            $tempContainer = new ContainerBuilder();
            $tempLoader = new YamlFileLoader($tempContainer, new FileLocator(APPLICATION_PATH . '/configs/'));        
        
            if (file_exists($file = APPLICATION_PATH . '/configs/parameters/parameters_' . APPLICATION_ENV . '.yml')) {
                $tempLoader->load($file);
            }

            $containerParameters = $this->container->getParameterBag()->all();
            $tempContainerParameters = $tempContainer->getParameterBag()->all();
            $parameters = $this->array_merge_recursive_distinct($containerParameters, $tempContainerParameters);

            foreach ($parameters as $key => $value) {
                $this->container->setParameter($key, $value);
            }
        }

        $this->container->setParameter('storage', array(
            \Zend_Cloud_StorageService_Adapter_FileSystem::LOCAL_DIRECTORY => APPLICATION_PATH . '/..',
        ));

        /**
         * Load all configs from services directory.
         */
        $services = glob(APPLICATION_PATH . '/configs/services/*.yml');
        foreach ($services as $service) {
            $this->loader->load($service);
        }
    }

    /**
     * array_merge_recursive does indeed merge arrays, but it converts values with duplicate
     * keys to arrays rather than overwriting the value in the first array with the duplicate
     * value in the second array, as array_merge does. I.e., with array_merge_recursive,
     * this happens (documented behavior):
     * 
     * array_merge_recursive(array('key' => 'org value'), array('key' => 'new value'));
     *     => array('key' => array('org value', 'new value'));
     * 
     * array_merge_recursive_distinct does not change the datatypes of the values in the arrays.
     * Matching keys' values in the second array overwrite those in the first array, as is the
     * case with array_merge, i.e.:
     * 
     * array_merge_recursive_distinct(array('key' => 'org value'), array('key' => 'new value'));
     *     => array('key' => 'new value');
     * 
     * Parameters are passed by reference, though only for performance reasons. They're not
     * altered by this function.
     * 
     * @param array $array1
     * @param mixed $array2
     * @author daniel@danielsmedegaardbuus.dk
     * @return array
     */
    private function &array_merge_recursive_distinct(array &$array1, &$array2 = null)
    {
        $merged = $array1;
     
        if (is_array($array2)){
            foreach ($array2 as $key => $val) {
                if (is_array($array2[$key])) {
                    $mergedKey = !empty($merged[$key]) ? $merged[$key] : null;
                    $arrayKey = $array2[$key];
                    $merged[$key] = is_array($mergedKey) ? $this->array_merge_recursive_distinct($mergedKey, $arrayKey) : $arrayKey;
                } else {
                    $merged[$key] = $val;
                }
            }
        }
     
        return $merged;
    }
}
