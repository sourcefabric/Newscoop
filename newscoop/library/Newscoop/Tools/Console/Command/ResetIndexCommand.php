<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Tools\Console\Command;

use Symfony\Component\Console;

/**
 * Index Reset Command
 */
class ResetIndexCommand extends Console\Command\Command
{
    /**
     * @see Console\Command\Command
     */
    protected function configure()
    {
        $this
            ->setName('index:reset')
            ->setDescription('Forces re-indexing of articles.')
            ->setHelp('');
    }

    /**
     * @see Console\Command\Command
     */
    protected function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output)
    {
        $indexer = $this->getHelper('container')->getService('search.indexer.article');
        $indexer->reset();
    }
}
