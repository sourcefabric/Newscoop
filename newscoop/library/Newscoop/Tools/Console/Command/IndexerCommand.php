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


/**
 * Index newscoop articles command
 */
class IndexerCommand extends Console\Command\Command
{
    /**
     * @see Console\Command\Command
     */
    protected function configure()
    {
        $this
        ->setName('newscoop:indexer:run')
        ->setDescription('Run Newscoop Indexer')
        ->addOption('time-limit', null, InputOption::VALUE_OPTIONAL, 'Interrupt the indexing after the specified number of seconds.', null);
    }

    /**
     * @see Console\Command\Command
     */
    protected function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output)
    {
        $res = \ArticleIndex::RunIndexer($input->getOption('time-limit'));
        if (\PEAR::isError($res)) {
            if ($input->getOption('verbose')) {
                $output->writeln($res->getMessage());
            }

            return;
        }

        if ($input->getOption('verbose')) {
            $output->writeln($res['articles'] . ' out of ' . $res['total articles'] . ' articles were indexed with a total of ' . $res['words'] . ' words.');
            $output->writeln('Total index time was ' . sprintf("%.3f", $res['total time'] . ' seconds.'));
            $output->writeln('Average article index time was ' . sprintf("%.3f", $res['article time']) . ' seconds.');
        }
    }
}
