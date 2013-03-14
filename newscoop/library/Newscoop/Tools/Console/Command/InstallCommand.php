<?php
/**
 * @package Newscoop
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Tools\Console\Command;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Component\Console;
use Symfony\Component\Console\Input\InputOption;
use Newscoop\Filesystem;
use Newscoop\Install\InstallConfig;
use Newscoop\Install\ConfigWriter;
use Newscoop\Install\ConnectionManager;
use Newscoop\Install\SchemaLoader;

/**
 * Install Newscoop Command
 */
class InstallCommand extends Console\Command\Command
{
    const ADMIN_PASSWORD = 'admin-password';
    const ADMIN_EMAIL = 'admin-email';
    const SITE_ALIAS = 'site-alias';
    const TEMPLATE_SET = 'theme-set';
    const DB_OVERWRITE = 'overwrite-db';
    const DB_NAME = 'db-name';
    const DB_HOST = 'db-host';
    const DB_PORT = 'db-port';
    const DB_USER = 'db-user';
    const DB_PASSWORD = 'db-password';

    /**
     * @see Console\Command\Command
     */
    protected function configure()
    {
        $this->setName('install');
        $this->setDescription('Install Newscoop');
        $this->addOption(self::ADMIN_PASSWORD, null, InputOption::VALUE_OPTIONAL, 'Admin password', 'admin');
        $this->addOption(self::ADMIN_EMAIL, null, InputOption::VALUE_OPTIONAL, 'Admin email', 'admin@localhost');
        $this->addOption(self::SITE_ALIAS, null, InputOption::VALUE_OPTIONAL, 'Site alias', 'localhost');
        $this->addOption(self::TEMPLATE_SET, null, InputOption::VALUE_OPTIONAL, 'Theme set', 'set_the_new_custodian');
        $this->addOption(self::DB_OVERWRITE, null, InputOption::VALUE_OPTIONAL, 'Overwrite database', false);
        $this->addOption(self::DB_NAME, null, InputOption::VALUE_OPTIONAL, 'Database name', 'newscoop');
        $this->addOption(self::DB_HOST, null, InputOption::VALUE_OPTIONAL, 'Database host', 'localhost');
        $this->addOption(self::DB_PORT, null, InputOption::VALUE_OPTIONAL, 'Database port', 3306);
        $this->addOption(self::DB_USER, null, InputOption::VALUE_OPTIONAL, 'Database user', 'root');
        $this->addOption(self::DB_PASSWORD, null, InputOption::VALUE_OPTIONAL, 'Database password', '');
    }

    /**
     * @see Console\Command\Command
     */
    protected function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output)
    {
        $config = new InstallConfig();
        $config->admin_password = $input->getOption(self::ADMIN_PASSWORD);
        $config->admin_email = $input->getOption(self::ADMIN_EMAIL);
        $config->alias = $input->getOption(self::SITE_ALIAS);
        $config->template_set = $input->getOption(self::TEMPLATE_SET);
        $config->overwrite_database = $input->getOption(self::DB_OVERWRITE);
        $config->db['dbname'] = $input->getOption(self::DB_NAME);
        $config->db['user'] = $input->getOption(self::DB_USER);
        $config->db['password'] = $input->getOption(self::DB_PASSWORD);
        $config->db['host'] = $input->getOption(self::DB_HOST);
        $config->db['port'] = $input->getOption(self::DB_PORT);

        $this->connection = ConnectionManager::getConnection($config->db, $config->overwrite_database);
        $this->schemaLoader = new SchemaLoader($this->connection);

        $this->loadSchema();
        $this->writeConfig($config);
        $this->loadSampleTemplates($config->template_set);
        $this->setAdmin($config);
        $this->setAlias($config);
        $this->finish();
    }

    /**
     * Finish install
     */
    private function finish()
    {
        $upgrade = APPLICATION_PATH . '/../conf/upgrading.php';
        if (file_exists($upgrade)) {
            unlink($upgrade);
        }
    }

    /**
     * Set admin user email/password
     *
     * @param object $config
     */
    private function setAdmin($config)
    {
        $sql = "UPDATE liveuser_users
                SET Password = SHA1(:password),
                    EMail = :email,
                    time_updated = NOW(),
                    time_created = NOW(),
                    status = :status,
                    is_admin = :is_admin
                WHERE id = :id";

        $this->connection->executeUpdate(
            $sql,
            array(
                'password' => $config->admin_password,
                'email' => $config->admin_email,
                'status' => 1,
                'is_admin' => 1,
                'id' => 1,
            )
        );
    }

    /**
     * Set site alias
     *
     * @param object $config
     */
    private function setAlias($config)
    {
        $sql = "UPDATE Aliases SET Name = :alias LIMIT 1";
        $this->connection->executeUpdate(
            $sql,
            array('alias' => $config->alias)
        );
    }

    /**
     * Load core schema
     */
    private function loadSchema()
    {
        $this->schemaLoader->load(APPLICATION_PATH . '/../install/sql/campsite_core.sql');
    }

    /**
     * Load sample template set
     *
     * @param string $set
     */
    private function loadSampleTemplates($set)
    {
        $this->schemaLoader->load(APPLICATION_PATH . '/../install/sql/campsite_demo_tables.sql');
        $this->schemaLoader->load(APPLICATION_PATH . '/../install/sql/campsite_demo_prepare.sql');
        $this->schemaLoader->load(APPLICATION_PATH . '/../install/sql/campsite_demo_data.sql');

        $this->clearAssignedTemplates();
        $this->copyData();

        $resourceId = new \Newscoop\Service\Resource\ResourceId(__CLASS__);
        $themeService = $resourceId->getService(\Newscoop\Service\IThemeManagementService::NAME_1);
        $publicationService = $resourceId->getService(\Newscoop\Service\IPublicationService::NAME);

        foreach ($themeService->getUnassignedThemes() as $theme) {
            if (basename($theme->getPath()) !== $set) {
                continue;
            }

        	foreach ($publicationService->getEntities() as $publication) {
        		$themeService->assignTheme($theme, $publication);
        	}
        }

        $this->getHelper('container')->getService('image.rendition')->reloadRenditions();
    }

    /**
     * Clear assigned templates
     */
    private function clearAssignedTemplates()
    {
        foreach (glob(APPLICATION_PATH . '/../themes/publication_*') as $path) {
            Filesystem::unlinkr($path);
        }
    }

    /**
     * Copy sample data
     */
    private function copyData()
    {
        foreach (array('files', 'images') as $dir) {
            $source = APPLICATION_PATH . '/../install/sample_data/' . $dir;
            $dest = APPLICATION_PATH . '/../' . $dir;
            Filesystem::copyr($source, $dest);
        }
    }

    /**
     * Write config
     *
     * @param object $config
     */
    private function writeConfig($config)
    {
        $configWriter = new ConfigWriter();
        $configWriter->write($config, APPLICATION_PATH . '/../conf/database_conf.php');
    }
}
