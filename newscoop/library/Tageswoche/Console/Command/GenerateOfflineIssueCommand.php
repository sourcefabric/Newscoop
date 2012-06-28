<?php
/**
 * @package Tageswoche
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Tageswoche\Console\Command;

use Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Console\Input\InputOption,
    Symfony\Component\Console;

/**
 */
class GenerateOfflineIssueCommand extends Console\Command\Command
{
    /**
     */
    protected function configure()
    {
        $this->setName('oi:generate')
            ->setDescription('Generate offline issue');
    }

    /**
     */
    protected function execute($input, $output)
    {
        $issue = $this->getHelper('container')->getService('mobile.issue')->find('current');
        $this->getHelper('container')->getService('mobile.issue.offline')->generateIssue($issue);
    }
}
