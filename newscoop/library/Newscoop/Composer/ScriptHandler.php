<?php
/**
 * @package Newscoop
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Composer;

use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

/**
 */
class ScriptHandler
{
    const TIMEOUT = 300;

    public static function generateOrmProxies($event)
    {
        $io = $event->getIO();
        $io->write('Generating ORM proxies');

        $phpFinder = new PhpExecutableFinder();
        $phpPath = $phpFinder->find();
        if (!$phpPath) {
            throw new \RuntimeException('The php executable could not be found, add it to your PATH environment variable and try again');
        }

        $php = escapeshellarg($phpPath);
        $doctrine = escapeshellarg(__DIR__ . '/../../../scripts/doctrine.php');
        $process = new Process("$php $doctrine orm:generate-proxies", null, null, null, self::TIMEOUT);
        $process->run();
        if (!$process->isSuccessful()) {
            throw new \RuntimeException('An error occurred when executing the Generating ORM proxies command.');
        }
    }
}
