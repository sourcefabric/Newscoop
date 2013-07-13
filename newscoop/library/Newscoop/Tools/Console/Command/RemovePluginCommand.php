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
class RemovePluginCommand extends Console\Command\Command
{
    /**
     * @see Console\Command\Command
     */
    protected function configure()
    {
        $this
            ->setName('plugins:remove')
            ->setDescription('Remove Newscoop Plugin.')
            ->addArgument('name', InputArgument::REQUIRED, 'Composer package name "vendor/plugin-name"');
    }

    /**
     * @see Console\Command\Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $pluginsManager = $this->getApplication()->getKernel()->getContainer()->getService('newscoop.plugins.manager');
        $name = $input->getArgument('name');

        $pluginsManager->removePlugin($name, $output);
    }
}
