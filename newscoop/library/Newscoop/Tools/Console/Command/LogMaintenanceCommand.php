<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Tools\Console\Command;

use Symfony\Component\Console;

/**
 * Log maintenance command
 */
class LogMaintenanceCommand extends Console\Command\Command
{
    /**
     * @see Console\Command\Command
     */
    protected function configure()
    {
        $this
        ->setName('log:maintenance')
        ->setDescription('Simple log maintenance (Log and audit_event tables in db).')
        ->setHelp(<<<EOT
Log maintenance.
EOT
        );
    }

    /**
     * @see Console\Command\Command
     */
    protected function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output)
    {
        $audit = $this->getApplication()->getKernel()->getContainer()->getService('audit.maintenance');
        $audit->flush();

        if ($input->getOption('verbose')) {
            $output->writeln('Log data processed.');
        }
    }
}
