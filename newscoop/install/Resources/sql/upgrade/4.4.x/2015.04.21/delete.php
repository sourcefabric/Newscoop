<?php

$newscoopDir = realpath(dirname(__FILE__).'/../../../../../../').'/';
$currentDir = dirname(__FILE__).'/';
$diffFile = 'diff.txt';
$upgradeErrors = array();

require_once $newscoopDir.'vendor/autoload.php';

use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;
use Monolog\Logger;

$app = new Silex\Application();
$app->register(new Silex\Provider\MonologServiceProvider(), array(
    'monolog.logfile' => $newscoopDir.'log/upgrade.log',
    'monolog.level' => Logger::NOTICE,
    'monolog.name' => 'upgrade',
));

$logger = $app['monolog'];

// Remove files
$finder = new Finder();
$filesystem = new Filesystem();
$finder->files()->in($currentDir)->name($diffFile);

$contents = null;
foreach ($finder as $file) {
    $contents = $file->getContents();
}

$filesToBeDeleted =  array_filter(preg_split('/\r\n|\n|\r/', $contents));
$folderToBeChecked = array();
if (count($filesToBeDeleted) > 0) {
    foreach ($filesToBeDeleted as $file) {
        if (strpos($file, 'newscoop/') === false) {
            continue;
        }

        $fullPath = $newscoopDir.substr($file, 9);

        if (is_file($fullPath) && is_readable($fullPath)) {
            try {
                $filesystem->remove(array($fullPath));
            } catch (IOException $e) {
                $msg = 'Could not remove file '.str_replace($newscoopDir, '', $fullPath).', please remove it manually.';
                $logger->addError($msg);
                $upgradeErrors[] = $msg;
            } catch (\Exception $e) {
                $msg = 'Could not remove file '.str_replace($newscoopDir, '', $fullPath).', please remove it manually.';
                $logger->addError($msg);
                $upgradeErrors[] = $msg;
            }
        }

        if (strpos($file, '/') !== false) {
            $folderToBeChecked[] = dirname($fullPath);
        }
    }
}

$folderToBeChecked = array_unique($folderToBeChecked);
arsort($folderToBeChecked);

foreach ($folderToBeChecked as $folder) {
    if (is_dir($folder)) {
        $finder = new Finder();
        $finder->in($folder);

        if (iterator_count($finder->files()) == 0) {
            try {
                // Remove subdirectories
                foreach ($finder->directories()->sortByName() as $subFolder) {
                    if (is_dir($subFolder)) {
                        $filesystem->remove(array($subFolder));
                    }
                }
            } catch (\Exception $e) {
                $msg = 'Could not remove or check subdirectories of '.str_replace($newscoopDir, '', $fullPath).'. Please see newscoop/install/Resources/sql/upgrade/4.4.x/2015.04.21/diff.txt and remove the empty directories manually.';
                $logger->addError($msg);
                $upgradeErrors[] = $msg;
            }

            try {
                // Remove parent directory
                $filesystem->remove(array($folder));
            } catch (\Exception $e) {
                $msg = 'Could not remove folder '.str_replace($newscoopDir, '', $folder).', please remove it manually.';
                $logger->addError($msg);
                $upgradeErrors[] = $msg;
            }
        }
    }
}

if (count($upgradeErrors) > 0) {
    $msg = "Some files or directories could not automatically be removed. This is "
        ."most likely caused by permissions. \n"
        ."You can either remove the files manually (see ${newscoopDir}install/Resources/sql/upgrade/4.4.x/2015.04.21/diff.txt) "
        ."or execute this file with root permissions, e.g.: \n"
        ."sudo php ${newscoopDir}install/Resources/sql/upgrade/4.4.x/2015.04.21/delete.php";
    $logger->addError($msg);
    array_splice($upgradeErrors, 0, 0, array($msg));
}