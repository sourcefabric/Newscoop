<?php
/**
 * @package Newscoop
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Installer\Services;

use Newscoop\Installer\Services;

/**
 * Upgrade service
 */
class UpgradeService
{
    private $newscoopDir;
    private $connection;
    private $monolog;

    /**
     * @param Connection $connection
     * @param object     $logger
     */
    public function __construct($connection, $logger)
    {
        $this->newscoopDir = __DIR__ . '/../../../..';
        $this->connection = $connection;
        $this->logger = $logger;
    }

    /**
     * Get info about instance database versions
     *
     * @return array Database verions
     */
    public function getDBVersion()
    {
        $version = $this->connection->fetchAll('SELECT ver_value FROM Versions WHERE ver_name = "last_db_version"');
        $version = $version[0]['ver_value'];
        $roll = $this->connection->fetchAll('SELECT ver_value FROM Versions WHERE ver_name = "last_db_roll"');
        $roll = $roll[0]['ver_value'];

        if (!$version) {
            $version = '[unknown]';
        }

        $dbInfo = $version;
        if (!in_array($roll, array('', '.'))) {
            $dbInfo .= ', roll ' . $roll;
        }

        return array(
            'version' => $version,
            'roll' => $roll,
            'dbInfo' => $dbInfo
        );
    }

    /**
     * Upgrade database
     *
     * @param array   $versionsArray
     * @param boolean $silent
     * @param boolean $showRolls
     *
     * @return boolean
     */
    public function upgradeDatabase($versionsArray, $silent = false, $showRolls = false)
    {
        $databaseService = new Services\DatabaseService($this->monolog);
        $lockFileName = __FILE__;
        $lockFile = fopen($lockFileName, "r");
        if ($lockFile === false) {
            return "Unable to create single process lock control!";
        }
        if (!flock($lockFile, LOCK_EX | LOCK_NB)) {
            // do an exclusive lock
            return "The upgrade process is already running.";
        }

        // keeping the last imported version throughout the upgrade process
        $last_db_version = $versionsArray['version'];
        // keeping the last imported roll throughout the upgrade process
        $last_db_roll = $versionsArray['roll'];

        $first = true;
        $skipped = array();
        $sqlVersions = array_map('basename', glob($this->newscoopDir . '/install/Resources/sql/upgrade/[2-9].[0-9]*'));
        usort($sqlVersions, array($databaseService, 'versionCompare'));

        foreach ($sqlVersions as $index => $db_version) {
            if (-1 == $databaseService->versionCompare($db_version, $last_db_version)) {
                continue;
            }

            $last_db_version = $db_version;
            $last_db_roll = '';

            $cur_old_roll = ''; // the roll of the running version that was imported before the upgrade ($old_roll or '')
            if ($first) {
                $last_db_roll = $versionsArray['roll'];
                $cur_old_roll = $last_db_roll;
                if (!$silent) {
                    $db_ver_roll_info = "$db_version";
                    if (!in_array($last_db_roll, array('', '.'))) {
                        $db_ver_roll_info .= ", roll $last_db_roll";
                    }

                    $this->logger->addNotice('* Upgrading the database from version '. $db_ver_roll_info .'...');
                }
                $first = false;
            }
            $output = array();

            $upgrade_base_dir = $this->newscoopDir . "/install/Resources/sql/upgrade/$db_version/";
            $rolls = $databaseService->searchDbRolls($upgrade_base_dir, $cur_old_roll);

            // run upgrade scripts
            $sql_scripts = array("tables.sql", "data-required.sql", "data-optional.sql", "tables-post.sql");

            foreach ($rolls as $upgrade_dir_roll => $upgrade_dir_path) {
                $upgrade_dir = $upgrade_dir_path . DIRECTORY_SEPARATOR;
                $last_db_roll = $upgrade_dir_roll;

                if ($showRolls || (!$silent)) {
                    $this->logger->addNotice('* importing database roll '. $last_db_version .' / '. $last_db_roll);
                }

                foreach ($sql_scripts as $index => $script) {
                    if (!is_file($upgrade_dir . $script)) {
                        continue;
                    }

                    $error_queries = array();
                    $errorsCount = $databaseService->importDB($upgrade_dir.$script, $this->connection, $this->logger);

                    if ($errorsCount) {
                        $this->logger->addError('* '.$script.' ('.$db_version.') errors');
                    }
                }

                $saveResult = $databaseService->saveDatabaseVersion($this->connection, $last_db_version, $last_db_roll);

                if ($saveResult) {
                    $this->logger->addNotice('* version is updated to '. $last_db_version .'/'. $last_db_roll);
                }
            }
        }

        if (!$silent) {
            $this->logger->addNotice('* importing database is done');
        }

        flock($lockFile, LOCK_UN); // release the lock

        if ($errorsCount) {
            return $databaseService->errorQueries;
        }

        return true;
    }
}
