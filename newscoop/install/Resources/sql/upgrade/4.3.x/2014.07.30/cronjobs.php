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
            $crontab->removeJob($job);
        }
    }
}

$job = new Job();
$job->setMinute('*')->setHour('*')->setDayOfMonth('*')->setMonth('*')->setDayOfWeek('*')
    ->setCommand('php '.$newscoopRealPath . '/application/console scheduler:run');
$crontab->addJob($job);

mysqli_query($connection, "INSERT INTO `cron_jobs`(`name`, `command`, `schedule`, `is_active`, `created_at`, `sendMail`) VALUES ('Remove obsolete pending users data','" . $newscoopRealPath . "/application/console user:garbage','30 0 * * *', 1, NOW(), 0)");
mysqli_query($connection, "INSERT INTO `cron_jobs`(`name`, `command`, `schedule`, `is_active`, `created_at`, `sendMail`) VALUES ('Autopublish pending issues and articles','" . $newscoopRealPath . "/bin/newscoop-autopublish','* * * * *', 1, NOW(), 0)");
mysqli_query($connection, "INSERT INTO `cron_jobs`(`name`, `command`, `schedule`, `is_active`, `created_at`, `sendMail`) VALUES ('Runs Newscoop Indexer - articles indexing','" . $newscoopRealPath . "/bin/newscoop-indexer','0 */4 * * *', 1, NOW(), 0)");
mysqli_query($connection, "INSERT INTO `cron_jobs`(`name`, `command`, `schedule`, `is_active`, `created_at`, `sendMail`) VALUES ('Send Newscoop subscriptions notifications','" . $newscoopRealPath . "/bin/subscription-notifier','0 */8 * * *', 1, NOW(), 0)");
mysqli_query($connection, "INSERT INTO `cron_jobs`(`name`, `command`, `schedule`, `is_active`, `created_at`, `sendMail`) VALUES ('Send Newscoop events notifications','" . $newscoopRealPath . "/bin/events-notifier','*/2 * * * *', 1, NOW(), 0)");
mysqli_query($connection, "INSERT INTO `cron_jobs`(`name`, `command`, `schedule`, `is_active`, `created_at`, `sendMail`) VALUES ('Remove old statistics from Newscoop database','" . $newscoopRealPath . "/bin/newscoop-statistics','0 */4 * * *', 1, NOW(), 0)");
mysqli_query($connection, "INSERT INTO `cron_jobs`(`name`, `command`, `schedule`, `is_active`, `created_at`, `sendMail`) VALUES ('Send Newscoop stats to Sourcefabric','" . $newscoopRealPath . "/bin/newscoop-stats','0 5 * * *', 1, NOW(), 0)");
mysqli_query($connection, "INSERT INTO `cron_jobs`(`name`, `command`, `schedule`, `is_active`, `created_at`, `sendMail`, `detailsUrl`) VALUES ('Display the last 7 days logged actions when going to Configure -> Logs. All the rest are stored in newscoop-audit.log.','".$newscoopRealPath . "/application/console log:maintenance','30 1 * * *', 0, NOW(), 0, 'http://sourcefabric.booktype.pro/newscoop-42-for-journalists-and-editors/log-file-maintenance/')");

$crontab->write();
