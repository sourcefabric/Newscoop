<?php
/**
 * @package Newscoop
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services\Plugins;

use Composer\Package\PackageInterface\PackageInterface;
use Doctrine\ORM\EntityManager;
use Newscoop\Entity\Plugin;
use Newscoop\EventDispatcher\EventDispatcher;
use Newscoop\EventDispatcher\Events\GenericEvent;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Process;

/**
 * Plugins Manager Service
 *
 * Manage plugins installation, status and more...
 */
class ManagerService
{
    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var Newscoop\EventDispatcher\EventDispatcher
     */
    protected $dispatcher;

    /**
     * Plugins service
     * @var Newscoop\Services\Plugins\PluginsService
     */
    protected $pluginsService;

    /**
     * Logger
     * @var Symfony\Bridge\Monolog\Logger
     */
    protected $logger;

    /**
     * Newscoop root directory
     * @var string
     */
    protected $newsoopDir;

    /**
     * Plugins directory
     * @var string
     */
    public $pluginsDir;

    /**
     * Config array
     * @var array
     */
    protected $config = array();

    /**
     * @param Doctrine\ORM\EntityManager $em
     * @param Newscoop\EventDispatcher\EventDispatcher $dispatcher
     */
    public function __construct(EntityManager $em, $dispatcher, $pluginsService, Logger $logger, array $config)
    {
        $this->em = $em;
        $this->dispatcher = $dispatcher;
        $this->pluginsService = $pluginsService;
        $this->logger = $logger;
        $this->newsoopDir = __DIR__ . '/../../../../';
        $this->pluginsDir = $this->newsoopDir . 'plugins';
        $this->handleConfig($config);
    }

    /**
     * Install plugin inside Newscoop - it's a wrapper for all tasks connected with plugin installation
     *
     * @param array            $plugins
     * @param OutputInterface  $output
     * @param boolean          $notify
     * @param boolean          $clearCache
     */
    public function installPlugin($plugins, $output, $notify = true, $clearCache = true)
    // public function installPlugin($pluginName, $version, $output, $notify = true)
    {
        $this->installComposer();
        $this->prepareCacheDir();

        $composerList = array();
        $eventList = array();
        foreach ($plugins as $plugin) {
            if (!$this->isInstalled($plugins)) {
                $composerList[] = sprintf('%s:%s', $plugin['name'], $plugin['version']);
                $eventList[] = $plugin['name'];
            } else {
                $output->writeln('<info>The plugin '.$plugin['name'].' is already installed.</info>');
            }
        }

        if (count($composerList) == 0) {
            $output->writeln('<info>No plugins to install.</info>');
            return;
        }

        $composerList = implode(' ', $composerList);
        $eventList = implode(' ', $eventList);

        // Require deps via composer
        $process = new Process(sprintf(
            'cd %s && php -d memory_limit=%s composer.phar require %s --update-no-dev --update-with-dependencies --prefer-dist -n',
            $this->newsoopDir,
            $this->config['internal_memory_limit'],
            $composerList
        ));
        $process->setTimeout(3600);
        $process->run(function ($type, $buffer) use ($output) {
            if (Process::ERR === $type) {
                $output->write('<error>'.$buffer.'</error>');
            } else {
                $output->write('<info>'.$buffer.'</info>');
            }
        });

        if (!$process->isSuccessful()) {
            throw new \Exception("Error with installing plugin(s).", 1);
        }

        foreach ($plugins as $plugin) {
            $cachedPluginMeta = $this->newsoopDir.'/plugins/cache/add_'.str_replace('/', '-', $plugin['name']).'_package.json';
            if (file_exists($cachedPluginMeta)) {
                $pluginMeta = json_decode(file_get_contents($cachedPluginMeta), true);
                $pluginDetails = file_get_contents($this->pluginsDir.'/'.$pluginMeta['targetDir'].'/composer.json');
                $this->em->getRepository('Newscoop\Entity\Plugin')->addPlugin($pluginMeta, $pluginDetails);

                // clear cache files
                $filesystem = new Filesystem();
                $filesystem->remove($cachedPluginMeta);
            }
        }

        $this->saveAvaiablePluginsToCacheFile();
        if ($clearCache) {
            $this->clearCache($output);
        }

        if ($notify) {
            $process = new Process(sprintf(
                'cd %s && php -d memory_limit=%s application/console plugins:dispatch "%s" install',
                $this->newsoopDir,
                $this->config['internal_memory_limit'],
                $eventList
            ));
            $process->setTimeout(3600);
            $process->run(function ($type, $buffer) use ($output) {
                if (Process::ERR === $type) {
                    $output->write('<error>'.$buffer.'</error>');
                } else {
                    $output->write('<info>'.$buffer.'</info>');
                }
            });

            if (!$process->isSuccessful()) {
                throw new \Exception("Error with dispatching install event(s).", 1);
            }
        }

        // TODO: make sure this works with multiple plugins
        $output->writeln('<info>Plugin(s) installed: '.$eventList.'</info>');
    }

    /**
     * Dispatch event for plugin
     * @param  string $pluginName
     * @param  string $eventName
     * @param  mixed  $output
     */
    public function dispatchEventForPlugin($pluginName, $eventName, $output = null)
    {
        $this->dispatchEventForPlugins(array($pluginName), $eventName, $output);
    }

    /**
     * Dispatch events for plugins
     * @param  array  $plugins
     * @param  string $eventName
     * @param  mixed  $output
     */
    public function dispatchEventForPlugins($plugins, $eventName, $output = null)
    {
        foreach ($plugins as $pluginName) {
            $this->dispatcher->dispatch('plugin.'.$eventName, new GenericEvent($this, array(
                'plugin_name' => $pluginName
            )));

            if ($output) {
                $output->writeln('<info>We just fired: "plugin.'.$eventName.'" event</info>');
            }

            $this->dispatcher->dispatch(
                'plugin.'.$eventName.'.'.str_replace('-', '_', str_replace('/', '_', $pluginName)),
                new GenericEvent($this, array(
                    'plugin_name' => $pluginName
                ))
            );

            if ($output) {
                $output->writeln('<info>We just fired: "plugin.'.$eventName.'.'.str_replace('-', '_', str_replace('/', '_', $pluginName)).'" event</info>');
            }
        }
    }

    /**
     * Remove plugin from newscoop (composer+database+cleaning)
     *
     * @param  string          $pluginName
     * @param  OutputInterface $output
     * @param  boolean         $notify
     */
    public function removePlugin($plugins, OutputInterface $output, $notify = true)
    {
        $this->installComposer();
        $this->prepareCacheDir();

        $nameList = array();
        foreach ($plugins as $plugin) {
            if ($this->isInstalled($plugin)) {
                $nameList[] = $plugin;
            } else {
                $output->writeln("<info>The plugin $plugin is not installed.</info>");
            }
        }

        if (count($nameList) == 0) {
            $output->writeln('<info>None of the specified plugins are installed.</info>');
            return;
        }

        if ($notify) {
            $process = new Process(sprintf(
                'cd %s && php -d memory_limit=%s application/console plugins:dispatch "%s" remove',
                $this->newsoopDir,
                $this->config['internal_memory_limit'],
                implode(' ', $nameList)
            ));
            $process->setTimeout(3600);
            $process->run(function ($type, $buffer) use ($output) {
                if (Process::ERR === $type) {
                    $output->write('<error>'.$buffer.'</error>');
                } else {
                    $output->write('<info>'.$buffer.'</info>');
                }
            });

            if (!$process->isSuccessful()) {
                throw new \Exception('Error with dispatching remove event(s).', 1);
            }
        }

        $process = new Process(sprintf(
            'cd %s && php -d memory_limit=%s composer.phar remove %s --update-no-dev -n',
            $this->newsoopDir,
            $this->config['internal_memory_limit'],
            implode(' ', $nameList)
        ));
        $process->setTimeout(3600);
        $process->run(function ($type, $buffer) use ($output) {
            if (Process::ERR === $type) {
                $output->write('<error>'.$buffer.'</error>');
            } else {
                $output->write('<info>'.$buffer.'</info>');
            }
        });

        if (!$process->isSuccessful()) {
            throw new \Exception('Error with removing plugin(s).', 1);
        }

        foreach ($nameList as $plugin) {
            $cachedPluginMeta = $this->newsoopDir.'/plugins/cache/uninstall_'.str_replace('/', '-', $plugin).'_package.json';

            if (file_exists($cachedPluginMeta)) {
                $pluginMeta = json_decode(file_get_contents($cachedPluginMeta), true);

                $this->em->getRepository('Newscoop\Entity\Plugin')->removePlugin($plugin);

                // clear cache files
                $filesystem = new Filesystem();
                $filesystem->remove($cachedPluginMeta);
                $filesystem->remove($this->pluginsDir.'/'.$pluginMeta['targetDir'].'/');
            }
        }

        $this->saveAvaiablePluginsToCacheFile();
        $this->clearCache($output);

        $output->writeln('<info>Plugin(s) removed: '.implode(', ', $nameList).'</info>');
    }

    /**
     * Update installed plugin
     *
     * @param  array           $plugins
     * @param  OutputInterface $output
     * @param  boolean         $notify
     * @param  boolean         $clearCache
     */
    public function updatePlugin($plugins, OutputInterface $output, $notify = true, $clearCache = true)
    // public function updatePlugin($pluginName, $version, OutputInterface $output, $notify = true)
    {
        $this->installComposer();

        $composerList = array();
        $eventList = array();
        foreach ($plugins as $plugin) {
            if ($this->isInstalled($plugin['name'])) {
                $composerList[] = sprintf('%s:%s', $plugin['name'], $plugin['version']);
                $eventList[] = $plugin['name'];
            } else {
                $output->writeln('<info>The plugin '.$plugin['name'].' is not installed.</info>');
            }
        }

        if (count($composerList) == 0) {
            $output->writeln('<info>None of the specified plugins are installed.</info>');
            return;
        }

        $composerList = implode(' ', $composerList);
        $eventList = implode(' ', $eventList);
        $printList = str_replace(' ', ', ', $eventList);

        $output->writeln('<info>Updating: '.$printList.'</info>');

        // Require deps via composer
        $process = new Process(sprintf(
            'cd %s && php -d memory_limit=%s composer.phar require %s --update-no-dev --update-with-dependencies --prefer-dist -n',
            $this->newsoopDir,
            $this->config['internal_memory_limit'],
            $composerList
        ));
        $process->setTimeout(3600);
        $process->run(function ($type, $buffer) use ($output) {
            if (Process::ERR === $type) {
                $output->write('<error>'.$buffer.'</error>');
            } else {
                $output->write('<info>'.$buffer.'</info>');
            }
        });

        if (!$process->isSuccessful()) {
            throw new \Exception('Error with updating plugin(s).', 1);
        }

        $this->saveAvaiablePluginsToCacheFile();

        if ($clearCache) {
            $this->clearCache($output);
        }

        if ($notify) {
            $process = new Process(sprintf(
                'cd %s && php -d memory_limit=%s application/console plugins:dispatch "%s" update',
                $this->newsoopDir,
                $this->config['internal_memory_limit'],
                $eventList
            ));
            $process->setTimeout(3600);
            $process->run(function ($type, $buffer) use ($output) {
                if (Process::ERR === $type) {
                    $output->write('<error>'.$buffer.'</error>');
                } else {
                    $output->write('<info>'.$buffer.'</info>');
                }
            });

            if (!$process->isSuccessful()) {
                throw new \Exception('Error with dispatching update event(s).', 1);
            }
        }

        foreach ($plugins as $plugin) {
            $cachedPluginMeta = $this->newsoopDir.'/plugins/cache/update_'.str_replace('/', '-', $plugin['name']).'_package.json';
            if (file_exists($cachedPluginMeta)) {
                $pluginMeta = json_decode(file_get_contents($cachedPluginMeta), true);

                $pluginDetails = file_get_contents($this->pluginsDir.'/'.$pluginMeta['target']['targetDir'].'/composer.json');
                $this->em->getRepository('Newscoop\Entity\Plugin')->updatePlugin($pluginMeta['target'], $pluginDetails);

                // clear cache files
                $filesystem = new Filesystem();
                $filesystem->remove($cachedPluginMeta);
            }
        }

        $output->writeln('<info>Plugin(s) updated: '.$printList.'</info>');
    }

    /**
     * Enable plugin
     *
     * @param  Plugin $plugin
     */
    public function enablePlugin(Plugin $plugin)
    {
        $this->dispatcher->dispatch('plugin.enable', new GenericEvent($this, array(
            'plugin_name' => $plugin->getName(),
            'plugin' => $plugin
        )));
    }

    /**
     * Disable plugin
     * @param  Plugin $plugin
     */
    public function disablePlugin(Plugin $plugin)
    {
        $this->dispatcher->dispatch('plugin.disable', new GenericEvent($this, array(
            'plugin_name' => $plugin->getName(),
            'plugin' => $plugin
        )));
    }

    /**
     * Reinstall plugins after Newscoop upgrade (re-add them to composer)
     */
    public function upgrade(OutputInterface $output, $doUpdate = false)
    {
        if ($doUpdate) {
            $this->clearCache($output);
        }

        $allPlugins = $this->pluginsService->getAllAvailablePlugins();
        $require = array();
        $update = array();
        foreach ($allPlugins as $key => $value) {
            // work only with modern packages
            if (strpos($value->getName(), '/') !== false) {
                $require[] = $value->getName() . ' ' . $value->getVersion();
                $update[] = $value->getName();

                if ($doUpdate) {
                    $details = json_decode($value->getDetails(), true);
                    if (array_key_exists('targetDir', $details)) {
                        $filesystem = new Filesystem();
                        if (!is_writable($this->pluginsDir.$details['targetDir'].'/')) {
                            throw new Exception("Plugins directory must be writable: ".$this->pluginsDir, 1);
                        }

                        $filesystem->remove($this->pluginsDir.$details['targetDir'].'/');
                    }
                }
            }
        }

        $require = implode(' ', $require);
        $update = implode(' ', $update);

        if ($doUpdate) {
            $doUpdate = ' && php -d memory_limit='.$this->config['internal_memory_limit'].' composer.phar update ' . $update . ' --no-dev';
        }

        $process = new Process('cd ' . $this->newsoopDir . ' && php -d memory_limit='.$this->config['internal_memory_limit'].' composer.phar require ' . $require.' --no-update'.$doUpdate);
        $output->writeln('<info>require ' . $require.' --no-update</info>');
        if ($doUpdate) {
            $output->writeln('<info>update ' . $update.'</info>');
        }
        $process->setTimeout(3600);
        $process->run(function ($type, $buffer) use ($output) {
            if ('err' === $type) {
                $output->write('<error>'.$buffer.'</error>');
            } else {
                $output->write('<info>'.$buffer.'</info>');
            }
        });

        if (!$process->isSuccessful()) {
            throw new \Exception("Error with reverting plugins", 1);
        }

        $this->saveAvaiablePluginsToCacheFile();
        if ($doUpdate) {
            $this->clearCache($output);
        }
    }

    /**
     * Reinstall plugins after Newscoop upgrade (re-add them to composer)
     */
    public function getPluginData()
    {
        $newPlugins = array();
        $allPlugins = $this->pluginsService->getAllAvailablePlugins();
        foreach ($allPlugins as $plugin) {
            if (strpos($plugin->getName(), '/') !== false) {
                $newPlugins[] = $plugin;
            }
        }
        return $newPlugins;
    }

    /**
     * Get installed plugins
     * @return array Array with installed plugins info
     */
    public function getInstalledPlugins()
    {
        $cachedAvailablePlugins = $this->pluginsDir . '/available_plugins.json';
        if (!file_exists($cachedAvailablePlugins)) {
            return array();
        }

        return $plugins = json_decode(file_get_contents($cachedAvailablePlugins));
    }

    /**
     * Check if plugin is installed
     * TODO
     *
     * @param  string  $pluginName
     * @return boolean
     */
    public function isInstalled($pluginName)
    {
        return $this->pluginsService->isInstalled($pluginName);
    }

    /**
     * Clear cache after plugin installation
     *
     * @param  OutputInterface $output
     */
    private function clearCache($output)
    {
        $output->writeln('<info>removing '.realpath($this->newsoopDir.'cache/').'/*</info>');
        $process = new Process('rm -rf '.realpath($this->newsoopDir.'cache/').'/*');
        $process->setTimeout(3600);
        $process->run(function ($type, $buffer) use ($output) {
            if (Process::ERR === $type) {
                $output->write('<error>'.$buffer.'</error>');
            } else {
                $output->write('<info>'.$buffer.'</info>');
            }
        });

        if ($process->isSuccessful()) {
            $output->writeln('<info>Cache cleared</info>');
        }

        $this->prepareCacheDir();
    }

    /**
     * Install composer
     */
    public function installComposer()
    {
        $filesystem = new Filesystem();
        if (!$filesystem->exists($this->newsoopDir . 'composer.phar')) {
            $installComposer = new Process('cd ' . $this->newsoopDir . ' && curl -s https://getcomposer.org/installer | php');
            $installComposer->setTimeout(3600);
            $installComposer->run();

            if (!$installComposer->isSuccessful()) {
                throw new \Exception("Error with installing composer.", 1);
            }
        }
    }

    /**
     * Find avaiable plugins
     * @return array array('plugin/name' => \Class\Name)
     */
    public function findAvaiablePlugins()
    {
        $plugins = array();
        $finder = new Finder();
        $elements = $finder->directories()->depth('== 0')->in($this->pluginsDir);
        if (count($elements) > 0) {
            foreach ($elements as $element) {
                $vendorName = $element->getFileName();
                $secondFinder = new Finder();
                $directories = $secondFinder->directories()->depth('== 0')->in($element->getPathName());
                foreach ($directories as $directory) {
                    $pluginName = $directory->getFileName();
                    $className = $vendorName . '\\' .$pluginName . '\\' . $vendorName . $pluginName;
                    $pos = strpos($pluginName, 'Bundle');
                    if ($pos !== false) {
                        $plugins[] = $className;
                    }
                }
            }
        }

        return $plugins;
    }

    private function saveAvaiablePluginsToCacheFile()
    {
        $plugins = $this->findAvaiablePlugins();

        file_put_contents($this->pluginsDir . '/available_plugins.json', json_encode($plugins));
    }

    private function prepareCacheDir()
    {
        if (!file_exists($this->newsoopDir.'/cache/prod')) {
            $filesystem = new Filesystem();
            $filesystem->mkdir($this->newsoopDir.'/cache/prod');
            $filesystem->mkdir($this->newsoopDir.'/cache/dev');
        }
    }

    private function handleConfig(array $config)
    {
        foreach ($config as $key => $value) {
            if (trim($value) == '') {
                unset($config[$key]);
            }
        }

        // Make sure internal_memory_limit is always set
        if (!isset($config['internal_memory_limit'])) {
            $config['internal_memory_limit'] = ini_get('memory_limit');
        }

        $this->config = array_merge($this->config, $config);
    }

    /**
     * Convert a (string) list of plugins to an array
     * Supported formats:
     *     <vendor>/<plugin-name>:<version>
     *     <vendor>/<plugin-name>
     *
     * Multiple plugin statements should be seperated by a space. Both formats
     * can be used mixed.
     *
     * @param  string $pluginList List of plugins
     *
     * @return array
     */
    public function stringToArray($pluginList, $withVersion = true)
    {
        $pluginsInput = explode(' ', $pluginList);
        $pluginsOutput = array();
        foreach ($pluginsInput as $plugin) {
            if ($withVersion) {
                if (strpos($plugin, ':') !== false) {
                    list($name, $version) = explode(':', $plugin);
                } else {
                    $name = $plugin;
                    $version = '*';
                }
            } else {
                $name = $plugin;
            }
            $pluginMeta = explode('/', $name);
            if (count($pluginMeta) !== 2) {
                throw new \Exception("Plugin name $name is invalid, try \"vendor/plugin-name\"", 1);
            }
            if ($withVersion) {
                $pluginsOutput[] = array('name' => $name, 'version' =>$version);
            } else {
                $pluginsOutput[] = $name;
            }
        }

        return $pluginsOutput;
    }
}
