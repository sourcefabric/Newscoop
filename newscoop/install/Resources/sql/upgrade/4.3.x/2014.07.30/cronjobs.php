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

$newscoopRealPath = realpath($newscoopDir);
$newscoopJobs = array(
    $newscoopRealPath . '/application/console user:garbage',
    $newscoopRealPath . '/bin/newscoop-autopublish',
    $newscoopRealPath . '/bin/newscoop-indexer',
    $newscoopRealPath . '/bin/subscription-notifier',
    $newscoopRealPath . '/bin/events-notifier',
    $newscoopRealPath . '/bin/newscoop-statistics',
    $newscoopRealPath . '/bin/newscoop-stats',
    $newscoopRealPath . '/scripts/newscoop.php',
    $newscoopRealPath . '/application/console log:maintenance',
);

$connection = mysqli_connect($Campsite['db']['host'], $Campsite['db']['user'], $Campsite['db']['pass'], $Campsite['db']['name']);
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

foreach ($crontab->getJobs() as $key => $job) {
    foreach ($newscoopJobs as $key => $value) {
        if (strpos($job->getCommand(), $value) !== false) {
            $schedule = $job->getMinute(). ' ' .$job->getHour(). ' ' .$job->getDayOfMonth(). ' ' .$job->getMonth(). ' ' .$job->getDayOfWeek();
            $result = mysqli_query($connection, "SELECT * FROM `cron_jobs` WHERE command = '".strip_tags(str_replace('php ', '', $job->getCommand()))."'");
            if (is_null(mysqli_fetch_array($result))) {
                mysqli_query($connection, "INSERT INTO `cron_jobs`(`name`, `command`, `schedule`, `is_active`, `created_at`, `sendMail`) VALUES ('".strip_tags(substr($job->getCommand(), strrpos($job->getCommand(), '/' )+1))."','".strip_tags(strip_tags(str_replace('php ', '', $job->getCommand())))."','". strip_tags($schedule)."', 1, NOW(), 0)");
            }

            $crontab->removeJob($job);
        }
    }

    if (strpos($job->getCommand(), $newscoopRealPath . '/application/console scheduler:run') === false) {
        $job = new Job();
        $job->setMinute('*')->setHour('*')->setDayOfMonth('*')->setMonth('*')->setDayOfWeek('*')
            ->setCommand('php '.$newscoopRealPath . '/application/console scheduler:run');
        $crontab->addJob($job);
    }
}

$result = mysqli_query($connection, "SELECT * FROM `cron_jobs` WHERE command = '".$newscoopRealPath ."/application/console log:maintenance' OR command = '".$newscoopRealPath ."/scripts/newscoop.php log:maintenance'");
if (is_null(mysqli_fetch_array($result))) {
    mysqli_query($connection, "INSERT INTO `cron_jobs`(`name`, `command`, `schedule`, `is_active`, `created_at`, `sendMail`, `detailsUrl`) VALUES ('Display the last 7 days logged actions when going to Configure -> Logs. All the rest are stored in newscoop-audit.log.','".$newscoopRealPath . "/application/console log:maintenance','30 1 * * *', 0, NOW(), 0, 'http://sourcefabric.booktype.pro/newscoop-42-for-journalists-and-editors/log-file-maintenance/')");
}

$crontab->write();
