<?php
/**
 * @package Newscoop
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Installer\Services;

use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;
use Symfony\Component\Filesystem\Filesystem;
use Newscoop\Entity\User;
use Newscoop\SchedulerServiceInterface;
use Crontab\Crontab;
use Crontab\Job;
use Symfony\Component\Filesystem\Exception\IOException;

/**
 * Finish Newscoop installation tasks
 */
class FinishService
{
    protected $newscoopDir;
    protected $filesystem;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->newscoopDir = __DIR__ . '/../../../..';
        $this->filesystem = new Filesystem();
    }

    /**
     * Generate proxies for entities
     */
    public function generateProxies()
    {
        $phpFinder = new PhpExecutableFinder();
        $phpPath = $phpFinder->find();
        if (!$phpPath) {
            throw new \RuntimeException('The php executable could not be found, add it to your PATH environment variable and try again');
        }

        exec('rm -rf '.$this->newscoopDir.'/cache/*', $output, $code);

        $php = escapeshellarg($phpPath);
        $doctrine = escapeshellarg($this->newscoopDir.'/scripts/doctrine.php');
        $generateProxies = new Process("$php $doctrine orm:generate-proxies", null, null, null, 300);
        $generateProxies->run();
        if (!$generateProxies->isSuccessful()) {
            throw new \RuntimeException($generateProxies->getErrorOutput());
        }

        exec('rm -rf '.$this->newscoopDir.'/cache/*', $output, $code);
    }

    /**
     * Reload themes reditions in datbase
     */
    public function reloadRenditions()
    {
        $phpFinder = new PhpExecutableFinder();
        $phpPath = $phpFinder->find();
        if (!$phpPath) {
            throw new \RuntimeException('The php executable could not be found, add it to your PATH environment variable and try again');
        }

        $php = escapeshellarg($phpPath);
        $newscoopConsole = escapeshellarg($this->newscoopDir.'/application/console');
        $reloadRenditions = new Process("$php $newscoopConsole renditions:reload", null, null, null, 300);
        $reloadRenditions->run();
        if (!$reloadRenditions->isSuccessful()) {
            throw new \RuntimeException('An error occurred when executing the Reload renditions command.');
        }
    }

    /**
     * Install bundle assets
     */
    public function installAssets()
    {
        $phpFinder = new PhpExecutableFinder();
        $phpPath = $phpFinder->find();
        if (!$phpPath) {
            throw new \RuntimeException('The php executable could not be found, add it to your PATH environment variable and try again');
        }

        $php = escapeshellarg($phpPath);
        $newscoopConsole = escapeshellarg($this->newscoopDir.'/application/console');
        $assetsInstall = new Process("$php $newscoopConsole assets:install $this->newscoopDir/public", null, null, null, 300);
        $assetsInstall->run();
        if (!$assetsInstall->isSuccessful()) {
            throw new \RuntimeException($assetsInstall->getErrorOutput());
        }
    }

    /**
     * Save newscoop cronjobs in user cronjob file
     *
     * @param  SchedulerServiceInterface $scheduler Cron job scheduler service
     * @return boolean
     */
    public function saveCronjobs(SchedulerServiceInterface $scheduler)
    {
        $binDirectory = realpath($this->newscoopDir.'/bin');
        $appDirectory = realpath($this->newscoopDir.'/application/console');

        $scheduler->registerJob("Autopublish pending issues and articles", array(
            'command' => $binDirectory.'/newscoop-autopublish',
            'schedule' => '* * * * *',
        ));

        $scheduler->registerJob("Runs Newscoop Indexer - articles indexing", array(
            'command' => $binDirectory.'/newscoop-indexer --silent',
            'schedule' => '0 */4 * * *',
        ));

        $scheduler->registerJob("Send Newscoop subscriptions notifications", array(
            'command' => $binDirectory.'/subscription-notifier',
            'schedule' => '0 */8 * * *',
        ));

        $scheduler->registerJob("Send Newscoop events notifications", array(
            'command' => $binDirectory.'/events-notifier',
            'schedule' => '*/2 * * * *',
        ));

        $scheduler->registerJob("Remove old statistics from Newscoop database", array(
            'command' => $binDirectory.'/newscoop-statistics',
            'schedule' => '0 */4 * * *',
        ));

        $scheduler->registerJob("Send Newscoop stats to Sourcefabric", array(
            'command' => $binDirectory.'/newscoop-stats',
            'schedule' => '0 5 * * *',
        ));

        $scheduler->registerJob("Remove obsolete pending users data", array(
            'command' => $appDirectory.' user:garbage',
            'schedule' => '30 0 * * *',
        ));

        $scheduler->registerJob("Display the last 7 days logged actions when going to Configure -> Logs. All the rest are stored in newscoop-audit.log.", array(
            'command' => $appDirectory.' log:maintenance',
            'schedule' => '30 1 * * *',
            'enabled' => false,
            'detailsUrl' => 'http://sourcefabric.booktype.pro/newscoop-42-for-journalists-and-editors/log-file-maintenance/'
        ));

        $crontab = new Crontab();

        $job = new Job();
        $job->setMinute('*')->setHour('*')->setDayOfMonth('*')->setMonth('*')->setDayOfWeek('*')
            ->setCommand('php '.$appDirectory.' scheduler:run');
        $crontab->addJob($job);
        $crontab->write();

        return true;
    }

    /**
     * Save instance config (to files and database)
     *
     * @param array      $config
     * @param Connection $connection
     */
    public function saveInstanceConfig($config, $connection)
    {
        // Set site title
        $sql = "UPDATE SystemPreferences SET value = ? WHERE varname = 'SiteTitle'";
        $stmt = $connection->prepare($sql);
        $stmt->bindValue(1, $config['site_title']);
        $stmt->execute();

        $sql = "UPDATE SystemPreferences SET value = ? WHERE varname = 'EmailFromAddress' OR varname = 'EmailContact'";
        $stmt = $connection->prepare($sql);
        $stmt->bindValue(1, $config['user_email']);
        $stmt->execute();

        // Set admin user
        $user = new User();
        $salt = $user->generateRandomString();
        $password = implode(User::HASH_SEP, array(
            User::HASH_ALGO,
            $salt,
            hash(User::HASH_ALGO, $salt . $config['recheck_user_password']),
        ));

        $sql = "UPDATE liveuser_users SET Password = ?, EMail = ?, time_updated = NOW(), time_created = NOW(), status = '1', is_admin = '1' WHERE id = 1";
        $stmt = $connection->prepare($sql);
        $stmt->bindValue(1, $password);
        $stmt->bindValue(2, $config['user_email']);
        $stmt->execute();

        $sql = "UPDATE SystemPreferences SET value = ? WHERE varname = 'SiteSecretKey'";
        $stmt = $connection->prepare($sql);
        $stmt->bindValue(1, sha1($config['site_title'] . mt_rand()));
        $stmt->execute();

        $sql = "INSERT INTO SystemPreferences (`varname`, `value`, `last_modified`) VALUES ('installation_id', ?, NOW())";
        $stmt = $connection->prepare($sql);
        $stmt->bindValue(1, sha1($config['site_title'] . mt_rand()));
        $stmt->execute();

        $result = $this->setupHtaccess();
        if (!empty($result)) {
            throw new IOException(implode(" ", $result) . " Most likely it's caused by wrong permissions.");
        }
    }

    /**
     * Makes backup of current .htaccess file and copy the latest one
     *
     * @return array
     */
    public function setupHtaccess()
    {
        $htaccess = '/.htaccess';
        $errors = array();

        try {
            if ($this->filesystem->exists($this->newscoopDir . $htaccess)) {
                $this->filesystem->copy(realpath($this->newscoopDir . $htaccess), realpath($this->newscoopDir) . '/htaccess.bak');
            }
        } catch (IOException $e) {
            $errors[] = $e->getMessage();

            return $errors;
        }

        try {
            $this->filesystem->copy(realpath($this->newscoopDir . '/htaccess.dist'), realpath($this->newscoopDir) . $htaccess, true);
        } catch (IOException $e) {
            $errors[] = $e->getMessage();
        }

        return $errors;
    }
}
