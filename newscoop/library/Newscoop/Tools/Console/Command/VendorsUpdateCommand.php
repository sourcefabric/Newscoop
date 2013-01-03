<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Tools\Console\Command;

use Symfony\Component\Console;
use Symfony\Component\Process;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Update newscoop vendors with composer install
 */
class VendorsUpdateCommand extends Console\Command\Command
{
    /**
     * @see Console\Command\Command
     */
    protected function configure()
    {
        $this
        ->setName('vendors:update')
        ->setDescription('Update Newscoop vendors with composer.');
    }

    /**
     * @see Console\Command\Command
     */
    protected function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output)
    {
        $echoBuffer = function($type, $buffer) {
            if ($type == 'out') {
                echo $buffer;
            }
        };

        echo "\n \n";

        $installVendors = new Process\Process('cd '.__DIR__ . '/../../../../../../ && php composer.phar install');
        $installVendors->setTimeout(3600);

        $filesystem = new Filesystem();
        if (!$filesystem->exists(__DIR__ . '/../../../../../../composer.phar')) {
            $installComposer = new Process\Process('cd '.__DIR__ . '/../../../../../../ && curl -s https://getcomposer.org/installer | php');
            $installComposer->setTimeout(3600);
            $installComposer->run($echoBuffer);

            if (!$installComposer->isSuccessful()) {
                throw new \Exception("Error with installing new vendors", 1);
            }

            $installVendors->run($echoBuffer);
        } else {
            $installVendors->run($echoBuffer);
        }
    }
}
