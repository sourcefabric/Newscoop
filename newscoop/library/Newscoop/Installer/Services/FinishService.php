<?php
/**
 * @package Newscoop
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Installer\Services;

use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;
use Newscoop\Entity\User;
use Crontab\Crontab;
use Crontab\Job;

/**
 * Finish Newscoop installation tasks
 */
class FinishService
{
    private $newscoopDir;
    private $filesystem;

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
            throw new \RuntimeException('An error occurred when executing the assets install command.');
        }
    }

    /**
     * Save newscoop cronjobs in user cronjob file
     *
     * @return bolean
     */
    public function saveCronjobs()
    {
        $binDirectory = realpath($this->newscoopDir.'/bin');

        $crontab = new Crontab();

        $job = new Job();
        $job->setMinute('*')->setHour('*')->setDayOfMonth('*')->setMonth('*')->setDayOfWeek('*')
            ->setCommand($binDirectory.'/newscoop-autopublish');
        $crontab->addJob($job);

        $job = new Job();
        $job->setMinute('0')->setHour('*/4')->setDayOfMonth('*')->setMonth('*')->setDayOfWeek('*')
            ->setCommand($binDirectory.'/newscoop-indexer --silent');
        $crontab->addJob($job);

        $job = new Job();
        $job->setMinute('0')->setHour('*/8')->setDayOfMonth('*')->setMonth('*')->setDayOfWeek('*')
            ->setCommand($binDirectory.'/subscription-notifier');
        $crontab->addJob($job);

        $job = new Job();
        $job->setMinute('*/2')->setHour('*')->setDayOfMonth('*')->setMonth('*')->setDayOfWeek('*')
            ->setCommand($binDirectory.'/events-notifier');
        $crontab->addJob($job);

        $job = new Job();
        $job->setMinute('0')->setHour('*/4')->setDayOfMonth('*')->setMonth('*')->setDayOfWeek('*')
            ->setCommand($binDirectory.'/newscoop-statistics');
        $crontab->addJob($job);

        $job = new Job();
        $job->setMinute('0')->setHour('5')->setDayOfMonth('*')->setMonth('*')->setDayOfWeek('*')
            ->setCommand($binDirectory.'/newscoop-autopublish');
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

        $this->filesystem->copy($this->newscoopDir.'/htaccess', $this->newscoopDir.'/.htaccess');

        if (file_exists($this->newscoopDir.'/conf/installation.php')) {
            $this->filesystem->remove($this->newscoopDir.'/conf/installation.php');
        }

        if (file_exists($this->newscoopDir.'/conf/upgrading.php')) {
            $this->filesystem->remove($this->newscoopDir.'/conf/upgrading.php');
        }
    }
}
