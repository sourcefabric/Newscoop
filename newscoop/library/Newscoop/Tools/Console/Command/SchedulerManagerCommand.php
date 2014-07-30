<?php
/**
 * @package   Newscoop
 * @author    Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2014 Sourcefabric z.ú.
 * @license   http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Tools\Console\Command;

use Symfony\Component\Console;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

/**
 * Runs all defined, enabled cron jobs
 */
class SchedulerManagerCommand extends ContainerAwareCommand
{
    /**
     */
    protected function configure()
    {
        $this
            ->setName('scheduler:run')
            ->setDescription('Runs all defined, enabled cron jobs');
    }

    /**
     */
    protected function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output)
    {
        $systemPreferences = $this->getContainer()->getService('system_preferences_service');
        $schedulerService = $this->getContainer()->getService('newscoop.scheduler');
        $cacheService = $this->getContainer()->getService('newscoop.cache');
        $em = $this->getContainer()->getService('em');

        $jobsCount = $em->getRepository('Newscoop\Entity\CronJob')
            ->createQueryBuilder('j')
            ->select('count(j)')
            ->where('j.enabled = true')
            ->getQuery()
            ->getSingleScalarResult();

        $jobs = array();
        $cacheKey = $cacheService->getCacheKey(array('jobs_count', $jobsCount), 'cronjobs');
        if ($cacheService->contains($cacheKey)) {
            $jobs = $cacheService->fetch($cacheKey);
        } else {
            $jobs = $em->getRepository('Newscoop\Entity\CronJob')
                ->createQueryBuilder('j')
                ->where('j.enabled = true')
                ->getQuery()
                ->getArrayResult();

            $cacheService->save($cacheKey, $jobs);
        }

        try {
            foreach ($jobs as $job) {
                unset($job['id']);
                unset($job['createdAt']);
                $schedulerService->addSchedulerJob($job['name'], array_filter($job));
            }

            $schedulerService->run();
        } catch (\Exception $e) {
            $output->writeln("<error>" . $e->getMessage() . "</error>");
        }
    }
}
