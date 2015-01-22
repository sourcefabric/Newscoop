<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Tools\Console\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console;
use Newscoop\Tools\Console\Command\AbstractIndexCommand;

/**
 * Index update command
 */
class UpdateIndexCommand extends AbstractIndexCommand
{
    /**
     * @see Console\Command\Command
     */
    protected function configure()
    {
        $this
        ->setName('index:update')
        ->setDescription('Update search index.')
        ->addArgument('type', InputArgument::OPTIONAL, 'Types to index', 'all')
        ->addArgument('limit', InputArgument::OPTIONAL, 'Items batch limit', 50)
        ->setHelp('');
    }

    /**
     * @see Console\Command\Command
     */
    protected function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output)
    {
        // This is needed to surpress STRICT errors, else everything will FAIL :'(
        error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE & ~E_STRICT);

        global $g_ado_db;
        $container = $this->getApplication()->getKernel()->getContainer();
        $g_ado_db = $container->get('doctrine.adodb');

        $type = $input->getArgument('type');
        $indexers = $this->getIndexers();

        if ($type !== 'all' && !array_key_exists($type, $indexers)) {

            $output->writeln(
                sprintf(
                    '<error>Invalid value for parameter type specified.</error> Valid values are: all, %s',
                    implode(', ', array_keys($indexers))
                )
            );
        } else {

            $output->writeln('Indexing started.');

            if ($type === 'all') {
                foreach ($indexers as $name => $indexer) {
                    $indexer->update($input->getArgument('limit'));
                }
            } else {
                $indexers[$type]->update($input->getArgument('limit'));
            }

            $output->writeln('Indexer is done.');
        }
    }
}
