<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Tools\Console\Command;

use Symfony\Component\Console;

/**
 * Autoload map update command
 */
class UpdateAutoloadCommand extends Console\Command\Command
{
    /**
     * @see Console\Command\Command
     */
    protected function configure()
    {
        $this
        ->setName('autoload:update')
        ->setDescription('Update composer autoload map.');
    }

    /**
     * @see Console\Command\Command
     */
    protected function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output)
    {
        exec('php composer.phar dump-autoload && echo "Found" || echo "Not Found"', $out);
        if ( $out[0] == "Not Found" ) {
            exec('curl -s https://getcomposer.org/installer | php');
            exec('php composer.phar dump-autoload');
        }

        $output->writeln('Autoload dumped.');
    }
}
