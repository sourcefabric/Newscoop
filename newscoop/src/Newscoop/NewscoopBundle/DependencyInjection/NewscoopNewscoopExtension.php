<?php

namespace Newscoop\NewscoopBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class NewscoopNewscoopExtension extends Extension implements PrependExtensionInterface
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
    }

    public function prepend(ContainerBuilder $container)
    {
        global $Campsite;

        $this->mergeParameters($container);
        $containerParameters = $container->getParameterBag()->all();

        $config_file = APPLICATION_PATH . '/../conf/database_conf.php';
        if (is_readable($config_file)) {
            require_once $config_file;
        }

        $doctrine_dbal_config = array(
            'default_connection' => 'default',
            'connections' => array(
                'default' => array(
                    'dbname' => $Campsite['DATABASE_NAME'],
                    'host' => $Campsite['DATABASE_SERVER_ADDRESS'],
                    'port' => $Campsite['DATABASE_SERVER_PORT'],
                    'user' => $Campsite['DATABASE_USER'],
                    'password' => $Campsite['DATABASE_PASSWORD'],
                    'driver' => 'pdo_mysql',
                    'charset' => 'UTF8',
                    'mapping_types' => array(
                        'enum' => 'string',
                        'point' => 'string',
                        'geometry' => 'string',
                    ),
                ),
            ),
        );

        $doctrine_orm_config = array(
            'auto_generate_proxy_classes' => $containerParameters['doctrine']['proxy']['autogenerate'],
            'proxy_namespace' => $containerParameters['doctrine']['proxy']['namespace'],
            'proxy_dir' =>  $this->truepath($containerParameters['doctrine']['proxy']['dir']),
            'default_entity_manager' => 'default',
            'entity_managers' => array(
                'default' => array(
                    # The name of a DBAL connection (the one marked as default is used if not set)
                    'connection' => 'default',
                    'auto_mapping' => false,
                    'mappings' => array(
                        'newscoop_entity' => array(
                            'mapping' => 'true',
                            'type' => 'annotation',
                            'dir' => $this->truepath($containerParameters['doctrine']['entity']['dir']),
                            'is_bundle' => false,
                            'prefix' => 'Newscoop\Entity'
                        ),
                        'newscoop_package' => array(
                            'mapping' => 'true',
                            'type' => 'annotation',
                            'dir' => $this->truepath($containerParameters['doctrine']['entity']['dir']),
                            'is_bundle' => false,
                            'prefix' => 'Newscoop\Package'
                        ),
                        'newscoop_image' => array(
                            'mapping' => 'true',
                            'type' => 'annotation',
                            'dir' => $this->truepath($containerParameters['doctrine']['entity']['dir']),
                            'is_bundle' => false,
                            'prefix' => 'Newscoop\Image'
                        ),
                        'newscoop_subscription' => array(
                            'mapping' => 'true',
                            'type' => 'annotation',
                            'dir' => $this->truepath($containerParameters['doctrine']['entity']['dir']),
                            'is_bundle' => false,
                            'prefix' => 'Newscoop\Subscription'
                        )
                    ),
                    # All cache drivers have to be array, apc, xcache or memcache
                    'metadata_cache_driver' => $containerParameters['doctrine']['cache'],
                    'query_cache_driver' => $containerParameters['doctrine']['cache'],
                    'dql' => array(
                        'numeric_functions' => $containerParameters['doctrine']['functions']
                    ),
                ),
            ),
        );

        $container->prependExtensionConfig('doctrine', array(
            'dbal' => $doctrine_dbal_config,
            'orm' => $doctrine_orm_config
        ));
    }

    /**
     * Merge parameters for env's.
     */
    public function mergeParameters($container) 
    {
        $container->setParameter('application_path', APPLICATION_PATH);

        /**
         * Allways load config for env
         */
        $this->loader = new YamlFileLoader($container, new FileLocator(APPLICATION_PATH . '/configs/'));
        $this->loader->load(APPLICATION_PATH . '/configs/parameters/parameters.yml');

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

    /**
     * This function is to replace PHP's extremely buggy realpath().
     * @param string The original path, can be relative etc.
     * @return string The resolved path, it might not exist.
     */
    public function truepath($path){
        $path = str_replace('%application_path%', APPLICATION_PATH, $path);

        // whether $path is unix or not
        $unipath=strlen($path)==0 || $path{0}!='/';
        // attempts to detect if path is relative in which case, add cwd
        if(strpos($path,':')===false && $unipath)
            $path=getcwd().DIRECTORY_SEPARATOR.$path;
        // resolve path parts (single dot, double dot and double delimiters)
        $path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
        $parts = array_filter(explode(DIRECTORY_SEPARATOR, $path), 'strlen');
        $absolutes = array();
        foreach ($parts as $part) {
            if ('.'  == $part) continue;
            if ('..' == $part) {
                array_pop($absolutes);
            } else {
                $absolutes[] = $part;
            }
        }
        $path=implode(DIRECTORY_SEPARATOR, $absolutes);
        // put initial separator that could have been lost
        $path=!$unipath ? '/'.$path : $path;
        return $path;
    }
}
