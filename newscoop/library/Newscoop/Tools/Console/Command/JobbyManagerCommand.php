<?php
/**
 * @package   Newscoop
 * @author    Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2014 Sourcefabric o.p.s.
 * @license   http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Tools\Console\Command;

use Symfony\Component\Console;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

/**
 * Runs all defined, enabled cron jobs
 */
class JobbyManagerCommand extends ContainerAwareCommand
{
    /**
     */
    protected function configure()
    {
        $this
            ->setName('jobby:run')
            ->setDescription('Runs all defined, enabled cron jobs');
    }

    /**
     */
    protected function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output)
    {
        $systemPreferences = $this->getContainer()->getService('system_preferences_service');
        $jobbyService = $this->getContainer()->getService('newscoop.jobby.service');
        $cacheService = $this->getContainer()->getService('newscoop.cache');
        $em = $this->getContainer()->getService('em');

        $jobs = $em->getRepository('Newscoop\Entity\CronJob')
            ->createQueryBuilder('j')
            ->where('j.enabled = true')
            ->getQuery()
            ->getArrayResult();

        try {

            foreach ($jobs as $job) {
                unset($job['id']);
                unset($job['createdAt']);
                $jobbyService->addJobbyJob($job['name'], array_filter($job));
            }

            $jobbyService->run();
        } catch (\Exception $e) {
            $output->writeln("<error>" . $e->getMessage() . "</error>");
        }
    }
}
