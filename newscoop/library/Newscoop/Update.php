<?php
/**
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Migrations\Configuration\YamlConfiguration;

/**
 * Newscoop Update
 * 
 * Manage Newscoom updates with Doctrine Migrations
 */
class Update
{ 
    /**
     * DBAL Connection
     * @var Connection
     */
    private $conn;

    /**
     * Migrantions configurtation file path
     * @var string
     */
    private $configurationPath;

    /**
     * Configuration object
     * @var YamlConfiguration
     */
    private $configuration;

    public function __construct(Connection $conn, $configurationPath)
    {
        $this->conn = $conn;
        $this->configurationPath = $configurationPath;

        $this->configuration = new YamlConfiguration($this->conn);
        $this->configuration->load($this->configurationPath);
    }

    /**
     * Get migrations status
     * @return array Migrations details
     */
    public function getStatus()
    {
        $currentVersion = $this->configuration->getCurrentVersion();
        $latestVersion = $this->configuration->getLatestVersion();

        $executedMigrations = $this->configuration->getMigratedVersions();
        $availableMigrations = $this->configuration->getAvailableVersions();
        $executedUnavailableMigrations = array_diff($executedMigrations, $availableMigrations);
        $numExecutedUnavailableMigrations = count($executedUnavailableMigrations);
        $newMigrations = count($availableMigrations) - count($executedMigrations);

        return array(
            'codeVersion' => \Newscoop\Version::VERSION,
            'currentVersion' => $currentVersion,
            'latestVersion' => $latestVersion,
            'executedMigrations' => $executedMigrations,
            'availableMigrations' => $availableMigrations,
            'executedUnavailableMigrations' => $executedUnavailableMigrations,
            'numExecutedUnavailableMigrations' => $numExecutedUnavailableMigrations,
            'newMigrations' => $newMigrations
        );
    }

    /**
     * Get migrations
     * @return array  Array with migratedVersions and notMigratedVersions
     */
    public function getUpdates()
    {
        $migratedVersions = array();
        $notMigratedVersions = array();

        if ($migrations = $this->configuration->getMigrations()) {
            $migratedVersions = $this->configuration->getMigratedVersions();
            foreach ($migrations as $version) {
                $isMigrated = in_array($version->getVersion(), $migratedVersions);

                if ($isMigrated) {
                  $migratedVersions[$version->getVersion()] = $version;
                } else {
                    $notMigratedVersions[$version->getVersion()] = $version;
                }
            }
        }

        return array(
            'migratedVersions' => $migratedVersions,
            'notMigratedVersions' => $notMigratedVersions
        );
    }
}