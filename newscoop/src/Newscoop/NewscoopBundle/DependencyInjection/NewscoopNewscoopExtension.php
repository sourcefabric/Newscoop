<?php

namespace Newscoop\NewscoopBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class NewscoopNewscoopExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $this->mergeParameters($container);
    }

    /**
     * Merge parameters for env's.
     */
    public function mergeParameters($container) 
    {
        $this->loader = new YamlFileLoader($container, new FileLocator(APPLICATION_PATH . '/configs/'));

        /**
         * Allways load config for env
         */
        $this->loader->load(APPLICATION_PATH . '/configs/parameters/parameters.yml');
        $container->setParameter('application_path', APPLICATION_PATH);

        if (APPLICATION_ENV !== 'production') {
            $tempContainer = new ContainerBuilder();
            $tempLoader = new YamlFileLoader($tempContainer, new FileLocator(APPLICATION_PATH . '/configs/'));        
        
            if (file_exists($file = APPLICATION_PATH . '/configs/parameters/parameters_' . APPLICATION_ENV . '.yml')) {
                $tempLoader->load($file);
            }

            $containerParameters = $container->getParameterBag()->all();
            $tempContainerParameters = $tempContainer->getParameterBag()->all();
            $parameters = $this->array_merge_recursive_distinct($containerParameters, $tempContainerParameters);

            foreach ($parameters as $key => $value) {
                $container->setParameter($key, $value);
            }
        }

        $container->setParameter('storage', array(
            \Zend_Cloud_StorageService_Adapter_FileSystem::LOCAL_DIRECTORY => APPLICATION_PATH . '/..',
        ));

        // load custom instalation parameter
        if (file_exists($file = APPLICATION_PATH . '/configs/parameters/custom_parameters.yml')) {
            $this->loader->load($file);
        }

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
