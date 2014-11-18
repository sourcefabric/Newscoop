<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Tools\Console\Command;

use Symfony\Component\Console;

/**
 * Ingest update command
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
        ->setDescription('Update Ingest.')
        ->setHelp(<<<EOT
Updates all configured feeds.
EOT
        );
    }

    /**
     * @see Console\Command\Command
     */
    protected function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output)
    {
        $ingest = $this->getApplication()->getKernel()->getContainer()->getService('ingest');
        $ingest->updateSTX();
    }
}
