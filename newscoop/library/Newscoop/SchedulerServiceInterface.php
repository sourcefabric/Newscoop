<?php
/**
 * @package Newscoop
 * @copyright 2014 Sourcefabric z.ú.
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop;

/**
 * Scheduler interface to help manage Cron jobs using Jobby library.
 */
interface SchedulerServiceInterface
{
    /**
     * Add cron job details to database
     *
     * @param string $jobName Cron job name
     * @param array  $config  Array with job configuration
     *                        - string  $command      The job to run (either a shell command or anonymous PHP function)
     *                        - string  $schedule     Crontab schedule format (`man -s 5 crontab`)
     *                        - boolean $enabled      Run this job at scheduled times
     *                        - boolean $debug        Send `jobby` internal messages to 'debug.log'
     *                        - string  $dateFormat   Format for dates on `jobby` log messages
     *                        - string  $output       Redirect `stdout` and `stderr` to this file
     *                        - string  $runOnHost    Run jobs only on this hostname
     *                        - string  $environment  Development environment for this job
     *                        - string  $runAs        Run as this user, if crontab user has `sudo` privileges
     *
     * @return void
     */
    public function registerJob($jobName, array $config);

    /**
     * Add cron job to Jobby library directly, where job will be started based on given configuration parameters
     *
     * @param string $jobName Job name
     * @param array  $config  Array with job configuration
     *
     * @return void
     */
    public function addSchedulerJob($jobName, array $config);

    /**
     * Run Jobby manager which will start all enabled cronjobs
     *
     * @return void
     */
    public function run();

    /**
     * Get a next run date relative to the current date or a specific date
     *
     * @param string          $schedule         Cron job schedule expression
     * @param string|DateTime $currentTime      (optional) Relative calculation date
     * @param int             $nth              (optional) Number of matches to skip before returning a
     *                                          matching next run date.  0, the default, will return the current
     *                                          date and time if the next run date falls on the current date and
     *                                          time.  Setting this value to 1 will skip the first match and go to
     *                                          the second match.  Setting this value to 2 will skip the first 2
     *                                          matches and so on.
     * @param bool            $allowCurrentDate (optional) Set to TRUE to return the
     *                                          current date if it matches the cron expression
     *
     * @return string
     * @throws RuntimeExpression on too many iterations
     */
    public function getNextRunDate($schedule, $currentTime = 'now', $nth = 0, $allowCurrentDate = false);

    /**
     * Get a previous run date relative to the current date or a specific date
     *
     * @param string          $schedule         Cron job schedule expression
     * @param string|DateTime $currentTime      (optional) Relative calculation date
     * @param int             $nth              (optional) Number of matches to skip before returning
     * @param bool            $allowCurrentDate (optional) Set to TRUE to return the
     *                                          current date if it matches the cron expression
     *
     * @return string
     * @throws RuntimeExpression on too many iterations
     */
    public function getPreviousRunDate($schedule, $currentTime = 'now', $nth = 0, $allowCurrentDate = false);
}
