<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Tools\Console\Command;

use Symfony\Component\Console;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Update composer.json with plugins after Newscoop upgrade.
 */
class UpgradePluginsCommand extends Console\Command\Command
{
    /**
     * @see Console\Command\Command
     */
    protected function configure()
    {
        $this
            ->setName('plugins:upgrade')
            ->setDescription('Update composer.json with plugins after Newscoop upgrade.')
            ->addOption('update', null, InputOption::VALUE_OPTIONAL, 'Run composer update for reverted plugins.');
    }

    /**
     * @see Console\Command\Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $pluginsManager = $this->getApplication()->getKernel()->getContainer()->getService('newscoop.plugins.manager');

        $pluginsManager->upgrade($output, $input->getOption('update'));
    }
}
