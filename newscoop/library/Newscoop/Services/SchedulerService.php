<?php
/**
 * @package Newscoop
 * @copyright 2014 Sourcefabric z.ú.
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

use Doctrine\ORM\EntityManager;
use Newscoop\SchedulerServiceInterface;
use Newscoop\Entity\CronJob;
use Jobby\Jobby;
use Cron\CronExpression;

/**
 * Scheduler Service to handle Jobby which is a PHP cron job manager
 */
class SchedulerService implements SchedulerServiceInterface
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
        try {
            foreach (array("command", "schedule") as $field) {
                if (empty($config[$field])) {
                    throw new \Exception("'$field' is required for '$jobName' job");
                }
            }

            $jobByCommand = $this->em->getRepository('Newscoop\Entity\CronJob')->findOneByCommand($config['command']);
            if (!$jobByCommand) {
                $cronJob = new CronJob();
                foreach ($config as $key => $value) {
                    $setter = "set" . ucfirst($key);
                    $cronJob->{$setter}($value);
                }

                $cronJob->setName($jobName);
                $this->em->persist($cronJob);
                $this->em->flush($cronJob);
            }
        } catch (\Exception $e) {
            throw new \Exception("Could not register job: '$jobName'");
        }
    }

    /**
     * {@inheritDoc}
     */
    public function removeJob($jobName, array $config)
    {
        foreach (array("command", "schedule") as $field) {
            if (empty($config[$field])) {
                throw new \Exception("'$field' is required for '$jobName' job");
            }
        }

        $job = $this->em->getRepository('Newscoop\Entity\CronJob')->findOneBy(array(
            'command' => $config['command'],
            'schedule' => $config['schedule'],
            'name' => $jobName,
        ));

        if ($job) {
            $this->em->remove($job);
            $this->em->flush();
        }
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
