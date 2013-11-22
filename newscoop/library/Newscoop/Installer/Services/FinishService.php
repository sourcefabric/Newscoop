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

class FinishService
{
    private $newscoopDir;
    private $filesystem;

	public function __construct(){
        $this->newscoopDir = __DIR__ . '/../../../..';
        $this->filesystem = new Filesystem();
	}

	public function generateProxies()
	{
        exec('rm -rf '.$this->newscoopDir.'/cache/*', $output = array(), $code);
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
            throw new \RuntimeException('An error occurred when executing the Generating ORM proxies command.');
        }

        exec('rm -rf '.$this->newscoopDir.'/cache/*', $output = array(), $code);
	}

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

    public function saveCronjobs($connection)
    {
        $cronJobs = array(
            'newscoop_autopublish',
            'newscoop_indexer',
            'newscoop_notifyendsubs',
            'newscoop_notifyevents',
            'newscoop_statistics',
            'newscoop_stats'
        );

        $binDirectory = $this->newscoopDir.'/bin';
        $cronJobsTemplatesDir = realpath($this->newscoopDir.'/install/Resources/templates/cron_jobs/');
        $cronJobsTempDir = realpath($this->newscoopDir.'/install/Resources/templates/cron_jobs/tmp/');
        $allAtOnceFile = $cronJobsTempDir.'/all_at_once';

        $cmd = 'crontab -l';
        $external = true;
        exec($cmd, $output, $result);
        if ($result != 0) {
            $cmd = 'crontab -';
            exec($cmd, $output, $result);
            if ($result != 0) {
                $external = false;

                $query = "UPDATE SystemPreferences SET value = 'N' WHERE varname = 'ExternalCronManagement'";
                $connection->executeQuery($query);
            }
        }

        if (file_exists($allAtOnceFile)) {
            unlink($allAtOnceFile);
        }

        $alreadyInstalled = false;
        foreach ($output as $cronLine) {
            if (!file_put_contents($allAtOnceFile, "$cronLine\n", FILE_APPEND)) {
                $error = true;
            }
            if (strstr($cronLine, $binDirectory)) {
                $alreadyInstalled = true;
            }
        }

        if ($alreadyInstalled) {
            return true;
        }

        $buffer = '';
        $isFileWritable = is_writable($cronJobsTempDir);
        $error = false;
        $twig = new \Twig_Environment(
            new \Twig_Loader_Filesystem(__DIR__ . '/../../../../install/Resources/templates/cron_jobs/'), 
            array('debug' => true, 'cache' => false, 'strict_variables' => true,'autoescape' => false)
        );

        foreach ($cronJobs as $cronJob) {
            $buffer = $twig->render('_'.$cronJob.'.twig', array('bin_directory' => $binDirectory));

            $cronJobFile = $cronJobsTempDir.'/'.$cronJob;
            if (file_exists($cronJobFile)) {
                $isFileWritable = is_writable($cronJobFile);
            }

            if (!$isFileWritable) {
                // try to unlink existing file
                $isFileWritable = @unlink($cronJobFile);

            }

            if (!$isFileWritable) {
                $error = true;
                continue;
            }

            if (file_put_contents($cronJobFile, $buffer)) {
                $buffer .= "\n";
                if (!file_put_contents($allAtOnceFile, $buffer, FILE_APPEND)) {
                    $error = true;
                }
            } else {
                $error = true;
            }
        }

        if ($error) {
            return false;
        }

        if ($external && file_exists($allAtOnceFile)) {
            $cmd = 'crontab '.escapeshellarg($allAtOnceFile);
            exec($cmd, $output, $result);
        }

        return true;
    }

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