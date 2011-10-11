<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Tools\Console\Command;

use Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Console\Input\InputOption,
    Symfony\Component\Console;

/**
 * Ingest updated command
 */
class UpdateIngestCommand extends Console\Command\Command
{
    /**
     * @see Console\Command\Command
     */
    protected function configure()
    {
        $this
        ->setName('ingest:update')
        ->setDescription('Update Ingest Feeds.')
        ->setHelp(<<<EOT
Update Ingest feeds.
EOT
        );
    }

    /**
     * @see Console\Command\Command
     */
    protected function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output)
    {
        $ingest = $this->getHelper('container')->getService('ingest');
        $ingest->updateSDA();
        $ingest->updateSwissinfo();
        $output->writeln('Ingest Feeds updated.');
    }
}
