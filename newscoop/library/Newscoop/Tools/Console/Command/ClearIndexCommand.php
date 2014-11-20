<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Tools\Console\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console;

/**
 * Index clear command
 */
class ClearIndexCommand extends AbstractIndexCommand
{
    /**
     * @see Console\Command\Command
     */
    protected function configure()
    {
        $this
        ->setName('index:clear')
        ->setDescription('Clear search index.')
        ->addArgument('type', InputArgument::OPTIONAL, 'Types to clear index for', 'all')
        ->setHelp("");
    }

    /**
     * @see Console\Command\Command
     */
    protected function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output)
    {
        global $g_ado_db;
        $container = $this->getApplication()->getKernel()->getContainer();
        $g_ado_db = $container->get('doctrine.adodb');

        $type = $input->getArgument('type');
        $indexers = $this->getIndexers();

        if ($type !== 'all' && !array_key_exists($type, $indexers)) {

            $output->writeln('<error>Invalid value for parameter type specified.</error> Valid values are: all, '
                . implode(', ', array_keys($indexers)));
        } else {

            if ($type === 'all') {
                foreach ($indexers as $name => $indexer) {
                    $output->writeln('Clearing index on '.$name.'.');
                    $indexer->clearAll();
                }
            } else {
                $output->writeln('Clearing index on '.$type.'.');
                $indexers[$type]->clearAll();
            }

            $output->writeln('Search index cleared.');
        }
    }
}
