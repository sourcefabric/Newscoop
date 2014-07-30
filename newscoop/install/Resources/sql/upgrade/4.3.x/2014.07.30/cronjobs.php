<?php

require_once __DIR__ . '/../../../../../../conf/database_conf.php';

$newscoopDir = dirname(__FILE__).'/../../../../../../';
require_once $newscoopDir . 'vendor/yzalis/crontab/src/Crontab/Crontab.php';
require_once $newscoopDir . 'vendor/yzalis/crontab/src/Crontab/BaseJob.php';
require_once $newscoopDir . 'vendor/yzalis/crontab/src/Crontab/Job.php';
require_once $newscoopDir . 'vendor/yzalis/crontab/src/Crontab/CrontabFileHandler.php';
require_once $newscoopDir . 'vendor/symfony/symfony/src/Symfony/Component/Process/Process.php';
require_once $newscoopDir . 'vendor/symfony/symfony/src/Symfony/Component/Process/ProcessPipes.php';

use Crontab\Crontab;
use Crontab\Job;

$crontab = new Crontab();

$newscoopJobs = array(
    'user:garbage',
    'newscoop-autopublish',
    'newscoop-indexer',
    'subscription-notifier',
    'events-notifier',
    'newscoop-statistics',
    'newscoop-stats',
);

$connection = mysqli_connect($Campsite['db']['host'], $Campsite['db']['user'], $Campsite['db']['pass'], $Campsite['db']['name']);
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

foreach ($crontab->getJobs() as $key => $job) {
    foreach ($newscoopJobs as $key => $value) {
        if (strpos($job->getCommand(), $value) !== false) {
            $schedule = $job->getMinute(). ' ' .$job->getHour(). ' ' .$job->getDayOfMonth(). ' ' .$job->getMonth(). ' ' .$job->getDayOfWeek();
            $result = mysqli_query($connection, "SELECT * FROM `cron_jobs` WHERE command = '".strip_tags($job->getCommand())."'");
            if (is_null(mysqli_fetch_array($result))) {
                mysqli_query($connection, "INSERT INTO `cron_jobs`(`name`, `command`, `schedule`, `is_active`, `created_at`) VALUES ('".strip_tags(substr($job->getCommand(), strrpos($job->getCommand(), '/' )+1))."','".strip_tags($job->getCommand())."','". strip_tags($schedule)."', 1, NOW())");
            }

            $crontab->removeJob($job);
        }
    }

    if (strpos($job->getCommand(), 'scheduler:run') === false) {
        $job = new Job();
        $job->setMinute('*')->setHour('*')->setDayOfMonth('*')->setMonth('*')->setDayOfWeek('*')
            ->setCommand('php '.realpath(__DIR__ . '/../../../../../../application') . '/console scheduler:run');
        $crontab->addJob($job);
    }
}

$crontab->write();
