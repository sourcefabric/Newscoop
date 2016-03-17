<?php
/**
 * @package Newscoop
 * @copyright 2015 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Tools\Console\Command;

use Symfony\Component\Console;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;

/**
 * Update composer.json with plugins after Newscoop upgrade.
 */
class InfoPluginsCommand extends Console\Command\Command
{
    /**
     * @see Console\Command\Command
     */
    protected function configure()
    {
        $this
            ->setName('plugins:info')
            ->setDescription('Lists all installed plugins and version.')
            ->addArgument('format', InputArgument::OPTIONAL, 'Output format for the information.')
            ->addOption('all-data', null, InputOption::VALUE_NONE, 'List all data for table and nice formats.')
            ->addOption('no-version', null, InputOption::VALUE_NONE, 'Hide version from output, not for nice or table format.')
            ->addOption('silent', null, InputOption::VALUE_NONE, 'Don\'t give any output if there is nothing to report.');
    }

    /**
     * @see Console\Command\Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $pluginsManager = $this->getApplication()->getKernel()->getContainer()->getService('newscoop.plugins.manager');
        $plugins = $pluginsManager->getPluginData();
        $format = $input->getArgument('format');
        $showVersion = $input->getOption('no-version');
        $outputString = '';

        if (count($plugins) > 0) {

            switch ($format) {
                case 'short':
                case 'oneline':
                case 'composer':
                    $outputList = array();
                    $seperator = ($format=='composer') ? ':' : ' ';
                    foreach ($plugins as $plugin) {
                        $outputList[] = sprintf('%s%s%s', $plugin->getName(), $seperator, ($showVersion ? '' : $plugin->getVersion()));
                    }
                    $seperator = ($format == 'short') ? "\n" : ' ';
                    $outputString = implode($seperator, $outputList);
                break;
                case 'nice':
                case 'table':
                default:
                    $allData = $input->getOption('all-data');
                    $tableData = array();
                    $tableHeaders = array();
                    if ($allData) {
                        $tableHeaders = array('name', 'version', 'enabled', 'author', 'license', 'installed', 'updated', 'description');
                    } else {
                        $tableHeaders = array('name', 'version', 'enabled', 'author', 'license');
                    }

                    foreach ($plugins as $plugin) {
                        $tmpData = array(
                            $plugin->getName(),
                            $plugin->getVersion(),
                            ($plugin->getEnabled()) ? 'enabled' : 'disabled',
                            $plugin->getAuthor(),
                            $plugin->getLicense()
                        );
                        if ($allData) {
                            $tmpData = array_merge($tmpData, array(
                                date_format($plugin->getInstalledAt(), 'd-m-Y H:i:s'),
                                date_format($plugin->getUpdatedAt(), 'd-m-Y H:i:s'),
                                $plugin->getDescription()
                            ));
                        }
                        $tableData[] = $tmpData;
                    }
                    $table = new Table($output);
                    $table
                        ->setHeaders($tableHeaders)
                        ->setRows($tableData)
                    ;
                    $table->render();
                break;
            }
        } else {
            if ($input->getOption('silent') || in_array($format, array('oneline', 'composer'))) {
                $outputString = '';
            } else {
                $outputString = 'There are no plugins installed.';
            }
        }
        if (trim($outputString) != '') {
            $output->writeln($outputString);
        }
    }
}
