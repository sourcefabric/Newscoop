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
 * Log maintenance command
 */
class UpdateNewscoopCommand extends Console\Command\Command
{
    /**
     * @see Console\Command\Command
     */
    protected function configure()
    {
        $this
        ->setName('newscoop:update')
        ->setDescription('Update Newscoop with migrations.');
    }

    /**
     * @see Console\Command\Command
     */
    protected function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output)
    {
        $updateLog = __DIR__ . '/../../../../../cache/update_log.txt';
        $migrationConf = __DIR__ . '/../../../../../application/configs/migrations.yml';
        $newscoopConsole = __DIR__ . '/../../../../../scripts/newscoop.php';
        $filesystem = new Filesystem();

        $process = new Process\Process('php ' . $newscoopConsole . ' migrations:migrate --no-interaction --configuration="' . $migrationConf . '"');
        $process->setTimeout(3600);
        $process->run(function($type, $buffer) use ($updateLog) {
            $fh = fopen($updateLog, 'a');
            fwrite($fh, $buffer);
            fclose($fh);
        });

        $vendors = new Process\Process('php ' . $newscoopConsole . ' vendors:update');
        $vendors->setTimeout(3600);
        $vendors->run(function($type, $buffer)  use ($updateLog) {
            $fh = fopen($updateLog, 'a');
            fwrite($fh, $buffer);
            fclose($fh);
        });
        
        // create file for stop ajax checking (usefull when live console preview will work again)
        $filesystem->touch(__DIR__ . '/../../../../../cache/end_update');
    }
}
