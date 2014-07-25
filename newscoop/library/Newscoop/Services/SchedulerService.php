<?php
/**
 * @package Newscoop
 * @copyright 2014 Sourcefabric z.ú.
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

use Doctrine\ORM\EntityManager;
use Newscoop\Interfaces\ISchedulerService;
use Newscoop\Entity\CronJob;
use Jobby\Jobby;
use Cron\CronExpression;

/**
 * Scheduler Service to handle Jobby which is a PHP cron job manager
 */
class SchedulerService implements ISchedulerService
{
    protected $em;

    protected $jobby;

    /**
     * Construct
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->jobby = new Jobby();
    }

    /**
     * {@inheritDoc}
     */
    public function registerJob($jobName, array $config)
    {
        foreach (array("command", "schedule") as $field) {
            if (empty($config[$field])) {
                throw new \Exception("'$field' is required for '$jobName' job");
            }
        }

        $cronJob = new CronJob();
        foreach ($config as $key => $value) {
            $setter = "set" . ucfirst($key);
            $cronJob->{$setter}($value);
        }

        $cronJob->setName($jobName);
        $this->em->persist($cronJob);
        $this->em->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function addSchedulerJob($jobName, array $config)
    {
        $this->jobby->add($jobName, $config);
    }

    /**
     * {@inheritDoc}
     */
    public function run()
    {
        $this->jobby->run();
    }

    /**
     * {@inheritDoc}
     */
    public function getNextRunDate($schedule, $currentTime = 'now', $nth = 0, $allowCurrentDate = false)
    {
        $cron = CronExpression::factory($schedule);

        return $cron->getNextRunDate($currentTime, $nth, $allowCurrentDate)->format('Y-m-d H:i:s');
    }

    /**
     * {@inheritDoc}
     */
    public function getPreviousRunDate($schedule, $currentTime = 'now', $nth = 0, $allowCurrentDate = false)
    {
        $cron = CronExpression::factory($schedule);

        return $cron->getPreviousRunDate($currentTime, $nth, $allowCurrentDate)->format('Y-m-d H:i:s');
    }
}
