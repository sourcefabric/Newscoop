<?php
/**
 * @package Newscoop
 * @copyright 2014 Sourcefabric z.ú.
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Interfaces;

/**
 * Jobby interface to help manage Cron jobs.
 */
interface IJobbyService
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
     * Add cron job which will be executed later to Jobby library
     *
     * @param string $jobName Job name
     * @param array  $config  Array with job configuration
     *
     * @return void
     */
    public function addJobbyJob($jobName, array $config);

    /**
     * Run Jobby manager which will start all enabled cronjobs
     *
     * @return void
     */
    public function run();
}
