<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Tools\Console\Command;

use Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Console\Input\InputOption,
    Symfony\Component\Console;

/**
 * Log maintenance command
 */
class UpdateAutoloadCommand extends Console\Command\Command
{
    /**
     * @see Console\Command\Command
     */
    protected function configure()
    {
        $this
        ->setName('newscoop:autoload:update')
        ->setDescription('Update managed by composer autoload.');
    }

    /**
     * @see Console\Command\Command
     */
    protected function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output)
    {
        exec('cd newscoop && php composer.phar dump-autoload && echo "Found" || echo "Not Found"', $output);
        if ( $output[0] == "Not Found" ) {
            exec('cd newscoop && curl -s https://getcomposer.org/installer | php');
            exec('cd newscoop && php composer.phar dump-autoload');
            $output->writeln('Autload dumped.');
        }
    }
}
