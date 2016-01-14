<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Tools\Console\Command;

use Symfony\Component\Console;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

/**
 * Update Image Storage Command
 */
class AutopublishCommand extends ContainerAwareCommand
{
    /**
     * @see Console\Command\Command
     */
    protected function configure()
    {
        $this
            ->setName('newscoop:autopublish')
            ->setDescription('Autopublish pending issues and articles')
            ->setHelp('Modifies the status of issues and articles scheduled for certain actions.');
    }

    /**
     * @see Console\Command\Command
     */
    protected function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output)
    {
        $issueActions = \IssuePublish::DoPendingActions();
        $articleActions = \ArticlePublish::DoPendingActions();
        if ($issueActions > 0 || $articleActions > 0) {
            fopen($this->getContainer()->getParameter('kernel.cache_dir') .'/reset_cache', 'w');
        }

        if ($input->getOption('verbose')) {
            $output->writeln('Published issues: <info>'.$issueActions.'</info>.');
            $output->writeln('Published articles: <info>'.$articleActions.'</info>.');
        }
    }
}
