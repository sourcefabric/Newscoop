<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Tools\Console\Command;

use Symfony\Component\Console;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Index Reset Command
 */
class DispatchEventForPluginCommand extends Console\Command\Command
{
    /**
     * @see Console\Command\Command
     */
    protected function configure()
    {
        $this
            ->setName('plugins:dispatch')
            ->setDescription('Dispatch instalation event for new plugin in new request. For dispatching the same event to multiple plugins, wrap the names in double quotes. E.g.: "<plugin-1> <plugin-2>" <event-name>')
            ->addArgument('name', InputArgument::REQUIRED, 'Composer package name, ex. "vendor/plugin-name"')
            ->addArgument('eventName', InputArgument::REQUIRED, 'Event name');
    }

    /**
     * @see Console\Command\Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $pluginsManager = $this->getApplication()->getKernel()->getContainer()->getService('newscoop.plugins.manager');
        $name = $input->getArgument('name');
        $eventName = $input->getArgument('eventName');

        $plugins = $pluginsManager->stringToArray($name, false);
        $pluginsManager->dispatchEventForPlugins($plugins, $eventName, $output);
    }
}
