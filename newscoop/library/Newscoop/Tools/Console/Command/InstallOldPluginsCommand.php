<?php
/**
 * @package Newscoop
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Tools\Console\Command;

use Symfony\Component\Console;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Convert old Newscoop translations Command
 */
class InstallOldPluginsCommand extends Console\Command\Command
{
    /**
     * @see Console\Command\Command
     */
    protected function configure()
    {
        $this
            ->setName('oldplugins:install')
            ->setDescription('Installs Debate, Poll, Soundcloud, reCaptcha plugins.');
    }

    /**
     * @see Console\Command\Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $source = __DIR__.'/../../../../../../plugins';
            $dest = __DIR__.'/../../../../../plugins';
            $this->copy($source, $dest, null, $output);

            $debate = __DIR__.'/../../../../../plugins/debate/admin-files/translations';
            $dest = __DIR__.'/../../../../../src/Newscoop/NewscoopBundle/Resources/translations';
            $this->copy($debate, $dest, 'Debate', $output);
            $this->removeDirectory($debate);

            $poll = __DIR__.'/../../../../../plugins/poll/admin-files/translations';
            $this->copy($poll, $dest, 'Poll', $output);
            $this->removeDirectory($poll);

            $soundcloud = __DIR__.'/../../../../../plugins/soundcloud/admin-files/translations';
            $this->copy($soundcloud, $dest, 'Soundcloud', $output);
            $this->removeDirectory($soundcloud);

            $recaptcha = __DIR__.'/../../../../../plugins/recaptcha/admin-files/translations';
            $this->copy($recaptcha, $dest, 'reCaptcha', $output);
            $this->removeDirectory($recaptcha);


        } catch (\Exception $e) {
            throw new \Exception('Something went wrong!');
        }
    }

    /**
     * Removes old translations directory
     *
     * @param string          $dir    Path to old translation files
     * @param OutputInterface $output Console output
     *
     * @return void
     */
    private function removeDirectory($dir) {

        if (!file_exists($dir)){ 
            return true;
        }

        if (!is_dir($dir)) {
            return unlink($dir);
        }

        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }

            if (!$this->removeDirectory($dir.DIRECTORY_SEPARATOR.$item)) {
                return false;
            }
        }

        return rmdir($dir);
    }

    /**
     * Copy translations directory
     *
     * @param string          $source    Directory source
     * @param string          $dest      Destination directory
     * @param string          $name      Plugin name
     * @param OutputInterface $output Console output
     *
     * @return void
     */
    private function copy($source, $dest, $name, $output){ 

        foreach ($iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS), \RecursiveIteratorIterator::SELF_FIRST) as $item
        ) {
            if ($item->isDir()) {
                mkdir($dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
            } else {
                copy($item, $dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
            }
        }

        if($name) {
            $output->writeln('<info>'.$name.' plugin successfully installed.</info>');
        }
    } 
}