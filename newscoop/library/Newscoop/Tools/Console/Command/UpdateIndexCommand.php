<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Tools\Console\Command;

use Symfony\Component\Console;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Index Update Command
 */
class UpdateIndexCommand extends Console\Command\Command
{
    /**
     * @see Console\Command\Command
     */
    protected function configure()
    {
        $this
            ->setName('index:update')
            ->setDescription('Update Search Index.')
            ->addArgument('limit', InputArgument::OPTIONAL, 'Articles batch size limit', 10);
    }

    /**
     * @see Console\Command\Command
     */
    protected function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output)
    {
        if ($this->getApplication()->getKernel()->getContainer()->hasService('search.indexer.article')) {
            $indexer = $this->getApplication()->getKernel()->getContainer()->getService('search.indexer.article');
            $result = $indexer->updateIndex($input->getArgument('limit'));

            $output->writeln(sprintf('Indexed %s articles.', $result));
        } else {
            $output->writeln('Indexer is not configured.');
        }
    }
}
