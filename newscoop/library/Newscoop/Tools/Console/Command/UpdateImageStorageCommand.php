<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Tools\Console\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console;

/**
 * Update Image Storage Command
 */
class UpdateImageStorageCommand extends Console\Command\Command
{
    /**
     * @see Console\Command\Command
     */
    protected function configure()
    {
        $this
            ->setName('image:update-storage')
            ->setDescription('Move images into subfolders.')
            ->setHelp('Move images from images/ folder into subfolders like images/a/, images/b/ etc.');
    }

    /**
     * @see Console\Command\Command
     */
    protected function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output)
    {
        $this->getApplication()->getKernel()->getContainer()->getService('image.update_storage')->updateStorage();
    }
}
