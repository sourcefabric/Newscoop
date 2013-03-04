<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

use Doctrine\ORM\EntityManager;
use Newscoop\Entity\Plugin;
use Symfony\Component\Process\Process;
use Symfony\Component\Filesystem\Filesystem;

/**
 */
class PluginsManagerService
{
    /** @var Doctrine\ORM\EntityManager */
    private $em;

    /**
     * @param Doctrine\ORM\EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function installPlugin($pluginName, $version, $output = null) {
        $this->installComposer();

        $pluginMeta = explode('/', $pluginName);
        if(count($pluginMeta) !== 2) {
            throw new \Exception("Plugin name is invalid, try \"vendor/plugin-name\"", 1);
        }

        $process = new Process('cd ' . __DIR__ . '/../../../ && php composer.phar require ' . $pluginName .':' . $version);
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
    }

    public function removePlugin($pluginName, $output) {
        $this->installComposer();

        $composerFile = __DIR__ . '/../../../composer.json';
        $composerDefinitions = json_decode(file_get_contents($composerFile), true);
        
        foreach ($composerDefinitions['require'] as $package => $version) {
            if ($package == $pluginName) {
                $output->writeln('<info>Remove "'.$pluginName.'" from composer.json file</info>');
                unset($composerDefinitions['require'][$package]);

                file_put_contents($composerFile, \Newscoop\Gimme\Json::indent(json_encode($composerDefinitions)));

                $process = new Process('cd ' . __DIR__ . '/../../../ && php composer.phar update ' . $pluginName);
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
        // fetch composer.json file
        // search plugin name in require section
        // unset that key
        // save new version of file
        // run composer install
    }

    public function updatePlugin($pluginName) {

    }

    public function enablePlugin(Plugin $plugin) {

    }

    public function disablePlugin(Plugin $plugin) {

    }

    public function upgrade() {
        //add and install all plugins from avaiable_plugins.json after newscoop upgrade
    }

    public function getInstalledPlugins() {
        $cachedAvailablePlugins = __DIR__ . '/../../../plugins/avaiable_plugins.json';
        if (!file_exists($cachedAvailablePlugins)) {
            return array();
        }

        return $plugins = json_decode(file_get_contents($cachedAvailablePlugins));
    }

    public function installComposer(){
        $filesystem = new Filesystem();
        if (!$filesystem->exists(__DIR__ . '/../../../composer.phar')) {
            $installComposer = new Process('cd '.__DIR__ . '/../../../ && curl -s https://getcomposer.org/installer | php');
            $installComposer->setTimeout(3600);
            $installComposer->run();

            if (!$installComposer->isSuccessful()) {
                throw new \Exception("Error with installing composer", 1);
            }
        }
    }
}
