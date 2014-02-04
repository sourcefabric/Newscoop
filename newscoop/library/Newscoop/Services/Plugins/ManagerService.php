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
    private $em;

    /**
     * @var Newscoop\EventDispatcher\EventDispatcher
     */
    private $dispatcher;

    /**
     * Plugins service
     * @var Newscoop\Services\Plugins\PluginsService
     */
    private $pluginsService;

    /**
     * Logger
     * @var Symfony\Bridge\Monolog\Logger
     */
    private $logger;

    /**
     * Newscoop root directory
     * @var string
     */
    private $newsoopDir;

    /**
     * Plugins directory
     * @var string
     */
    public $pluginsDir;

    /**
     * @param Doctrine\ORM\EntityManager $em
     * @param Newscoop\EventDispatcher\EventDispatcher $dispatcher
     */
    public function __construct(EntityManager $em, $dispatcher, $pluginsService, Logger $logger)
    {
        $this->em = $em;
        $this->dispatcher = $dispatcher;
        $this->pluginsService = $pluginsService;
        $this->logger = $logger;
        $this->newsoopDir = __DIR__ . '/../../../../';
        $this->pluginsDir = $this->newsoopDir . 'plugins';
    }

    /**
     * Install plugin inside Newscoop - it's a wrapper for all tasks connected with plugin installation
     * 
     * @param  string           $pluginName 
     * @param  string           $version    
     * @param  OutputInterface  $output     
     * @param  boolean          $notify     
     */
    public function installPlugin($pluginName, $version, $output, $notify = true)
    {
        $this->installComposer();
        $this->prepareCacheDir();

        $pluginMeta = explode('/', $pluginName);
        if(count($pluginMeta) !== 2) {
            throw new \Exception("Plugin name is invalid, try \"vendor/plugin-name\"", 1);
        }

        $process = new Process('cd ' . $this->newsoopDir . ' && php composer.phar require --no-update ' . $pluginName .':' . $version .' && php composer.phar update ' . $pluginName .'  --prefer-dist --no-dev -n');

        $process->setTimeout(3600);
        $process->run(function ($type, $buffer) use ($output) {
            if ('err' === $type) {
                $output->write('<error>'.$buffer.'</error>');
            } else {
                $output->write('<info>'.$buffer.'</info>');
            }
        });

        if (!$process->isSuccessful()) {
            throw new \Exception("Error with installing plugin", 1);
        }

        $cachedPluginMeta = $this->newsoopDir.'/plugins/cache/add_'.str_replace('/', '-', $pluginName).'_package.json';
        if (file_exists($cachedPluginMeta)) {
            $pluginMeta = json_decode(file_get_contents($cachedPluginMeta), true);
            $pluginDetails = file_get_contents($this->pluginsDir.'/'.$pluginMeta['targetDir'].'/composer.json');
            $this->em->getRepository('Newscoop\Entity\Plugin')->addPlugin($pluginMeta, $pluginDetails);

            // clear cache files
            $filesystem = new Filesystem();
            $filesystem->remove($cachedPluginMeta);
        }

        $this->saveAvaiablePluginsToCacheFile();
        $this->clearCache($output);

        if ($notify) {
            $process = new Process('cd ' . $this->newsoopDir . ' && php application/console plugins:dispatch ' . $pluginName.' install');
            $process->setTimeout(3600);
            $process->run(function ($type, $buffer) use ($output) {
                if ('err' === $type) {
                    $output->write('<error>'.$buffer.'</error>');
                } else {
                    $output->write('<info>'.$buffer.'</info>');
                }
            });

            if (!$process->isSuccessful()) {
                throw new \Exception("Error with dispatching install event", 1);
            }
        }

        $output->writeln('<info>Plugin '.$pluginName.' is installed!</info>');
    }

    /**
     * Dispatch events for plugins
     * @param  string $pluginName 
     * @param  string $eventName  
     * @param  mixed $output                  
     */
    public function dispatchEventForPlugin($pluginName, $eventName, $output = null)
    {
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

    /**
     * Remove plugin from newscoop (composer+database+cleaning)
     * 
     * @param  string          $pluginName 
     * @param  OutputInterface $output     
     * @param  boolean         $notify     
     */
    public function removePlugin($pluginName, OutputInterface $output, $notify = true)
    {
        $this->installComposer();
        $this->prepareCacheDir();

        /*if (!$this->isInstalled($pluginName)) {
            $output->writeln('<info>Plugin "'.$pluginName.'" is not installed yet</info>');

            return;
        }*/

        $composerFile = $this->newsoopDir . 'composer.json';
        $composerDefinitions = json_decode(file_get_contents($composerFile), true);
        
        foreach ($composerDefinitions['require'] as $package => $version) {
            if ($package == $pluginName) {

                if ($notify) {
                    $process = new Process('cd ' . $this->newsoopDir . ' && php application/console plugins:dispatch ' . $pluginName.' remove');
                    $process->setTimeout(3600);
                    $process->run(function ($type, $buffer) use ($output) {
                        if ('err' === $type) {
                            $output->write('<error>'.$buffer.'</error>');
                        } else {
                            $output->write('<info>'.$buffer.'</info>');
                        }
                    });

                    if (!$process->isSuccessful()) {
                        throw new \Exception("Error with dispatching remove event", 1);
                    }
                }

                $output->writeln('<info>Remove "'.$pluginName.'" from composer.json file</info>');
                unset($composerDefinitions['require'][$package]);

                file_put_contents($composerFile, \Newscoop\Gimme\Json::indent(json_encode($composerDefinitions)));

                $process = new Process('cd ' . $this->newsoopDir . ' && php composer.phar update --no-dev ' . $pluginName);
                $process->setTimeout(3600);
                $process->run(function ($type, $buffer) use ($output) {
                    if ('err' === $type) {
                        $output->write('<error>'.$buffer.'</error>');
                    } else {
                        $output->write('<info>'.$buffer.'</info>');
                    }
                });

                if (!$process->isSuccessful()) {
                    throw new \Exception("Error with removing plugin", 1);
                }
            }
        }

        $cachedPluginMeta = $this->newsoopDir.'/plugins/cache/uninstall_'.str_replace('/', '-', $pluginName).'_package.json';

        if (file_exists($cachedPluginMeta)) {
            $pluginMeta = json_decode(file_get_contents($cachedPluginMeta), true);

            $this->em->getRepository('Newscoop\Entity\Plugin')->removePlugin($pluginName);

            // clear cache files
            $filesystem = new Filesystem();
            $filesystem->remove($cachedPluginMeta);
            $filesystem->remove($this->pluginsDir.'/'.$pluginMeta['targetDir'].'/');
        }

        $this->saveAvaiablePluginsToCacheFile();
        $this->clearCache($output);

        $output->writeln('<info>Plugin '.$pluginName.' is removed!</info>');
    }

    /**
     * Update installed plugin
     * 
     * @param  string          $pluginName 
     * @param  string          $version    
     * @param  OutputInterface $output     
     * @param  boolean         $notify     
     */
    public function updatePlugin($pluginName, $version, OutputInterface $output, $notify = true)
    {
        $this->installComposer();

        $output->writeln('<info>Update "'.$pluginName.'"</info>');sleep(10);
        $process = new Process('cd ' . $this->newsoopDir . ' && php composer.phar update --prefer-dist --no-dev ' . $pluginName);
        $process->setTimeout(3600);
        $process->run(function ($type, $buffer) use ($output) {
            if ('err' === $type) {
                $output->write('<error>'.$buffer.'</error>');
            } else {
                $output->write('<info>'.$buffer.'</info>');
            }
        });

        if (!$process->isSuccessful()) {
            throw new \Exception("Error with updating plugin", 1);
        }

        $this->saveAvaiablePluginsToCacheFile();

        $this->clearCache($output);
        $this->prepareCacheDir();

        if ($notify) {
            $process = new Process('cd ' . $this->newsoopDir . ' && php application/console plugins:dispatch ' . $pluginName.' update');
            $process->setTimeout(3600);
            $process->run(function ($type, $buffer) use ($output) {
                if ('err' === $type) {
                    $output->write('<error>'.$buffer.'</error>');
                } else {
                    $output->write('<info>'.$buffer.'</info>');
                }
            });

            if (!$process->isSuccessful()) {
                throw new \Exception("Error with dispatching update event", 1);
            }
        }

        $cachedPluginMeta = $this->newsoopDir.'/plugins/cache/update_'.str_replace('/', '-', $pluginName).'_package.json';

        if (file_exists($cachedPluginMeta)) {
            $pluginMeta = json_decode(file_get_contents($cachedPluginMeta), true);
            $pluginDetails = file_get_contents($this->pluginsDir.'/'.$pluginMeta['target']['targetDir'].'/composer.json');

            $this->em->getRepository('Newscoop\Entity\Plugin')->updatePlugin($pluginMeta['target'], $pluginDetails);

            // clear cache files
            $filesystem = new Filesystem();
            $filesystem->remove($cachedPluginMeta);
        }

        $output->writeln('<info>Plugin '.$pluginName.' is updated!</info>');
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
    public function upgrade(OutputInterface $output)
    {
        $this->clearCache($output);

        $allPlugins = $this->pluginsService->getAllAvailablePlugins();
        $require = array();
        $update = array();
        foreach ($allPlugins as $key => $value) {
            // work only with modern packages
            if (strpos($value->getName(), '/') !== false) {
                $require[] = $value->getName() . ' ' . $value->getVersion();
                $update[] = $value->getName();

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

        $require = implode(' ', $require);
        $update = implode(' ', $update);

        $process = new Process('cd ' . $this->newsoopDir . ' && php composer.phar require ' . $require.' --no-update && php composer.phar update ' . $update.' --no-dev');
        $output->writeln('<info>require ' . $require.' --no-update</info>');
        $output->writeln('<info>update ' . $update.' --no-dev</info>');
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
        $this->clearCache($output);
    }

    /**
     * Get installed plugins
     * @return array Array with installed plugins info
     */
    public function getInstalledPlugins()
    {
        $cachedAvailablePlugins = $this->pluginsDir . '/avaiable_plugins.json';
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
        $installedPlugins = $this->getInstalledPlugins();
    }

    /**
     * Clear cache after plugin installation
     * 
     * @param  OutputInterface $output
     */
    private function clearCache($output)
    {   
        $output->writeln('<info>remove '.realpath($this->newsoopDir.'cache/').'/*</info>');
        $process = new Process('rm -rf '.realpath($this->newsoopDir.'cache/').'/*');
        $process->setTimeout(3600);
        $process->run(function ($type, $buffer) use ($output) {
            if ('err' === $type) {
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
                throw new \Exception("Error with installing composer", 1);
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

        file_put_contents($this->pluginsDir . '/avaiable_plugins.json', json_encode($plugins));
    }

    private function prepareCacheDir()
    {
        if (!file_exists($this->newsoopDir.'/cache/prod')) {
            $filesystem = new Filesystem();
            $filesystem->mkdir($this->newsoopDir.'/cache/prod');
            $filesystem->mkdir($this->newsoopDir.'/cache/dev');
        }
    }
}
