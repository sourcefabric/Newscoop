<?php
/**
 * @package Newscoop
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Installer\Services;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Connection;

/**
 * Service with functions related to database jobs
 */
class DatabaseService
{
    protected $logger;
    public $errorQueries = array();

    /**
     * @param object $logger
     */
    public function __construct($logger)
    {
        $this->logger = $logger;
    }

    /**
     * Create Newscoop Database
     *
     * @param Connection $connection
     */
    public function createNewscoopDatabase($connection)
    {
        $params = $connection->getParams();
        if (isset($params['master'])) {
            $params = $params['master'];
        }
        unset($params['dbname']);
        $tmpConnection = DriverManager::getConnection($params);

        $name = $tmpConnection->getDatabasePlatform()->quoteSingleIdentifier($connection->getDatabase());
        $tmpConnection->getSchemaManager()->createDatabase($name);

        unset($tmpConnection);

        $this->logger->addInfo('Database '.$name.' created');
    }

    /**
     * Fill database with default data and run all not applied upgrades
     *
     * @param Connection $connection
     *
     * @return boolean
     */
    public function fillNewscoopDatabase($connection)
    {
        // import database from sql file
        $sqlFile =  __DIR__ . '/../../../../install/Resources/sql/campsite_core.sql';

        try {
            $connection->exec(file_get_contents($sqlFile));
        } catch (\Exception $e) {
            return false;
        }

        $dbVersions = array_map('basename', glob(__DIR__ . '/../../../../install/Resources/sql/upgrade/[2-9].[0-9]*'));
        if (!empty($dbVersions)) {
            usort($dbVersions, array($this, 'versionCompare'));
            $dbLastVersion = array_pop($dbVersions);
            $dbLastVersionDir = __DIR__ . '/../../../../install/Resources/sql/upgrade/'.$dbLastVersion.'/';
            $dbLastRoll = '';
            $dbRolls = $this->searchDbRolls($dbLastVersionDir, '');
            if (!empty($dbRolls)) {
                $dbLastRollInfo = array_slice($dbRolls, -1, 1, true);
                $dbLastRollInfoKeys = array_keys($dbLastRollInfo);
                $dbLastRoll = $dbLastRollInfoKeys[0];
            }

            $this->saveDatabaseVersion($connection, $dbLastVersion, $dbLastRoll);
            $this->logger->addInfo('Last db version:"'.$dbLastVersion.'", last db roll: "'.$dbLastRoll.'"');
        }

        return true;
    }

    /**
     * Save database configuration to file
     * @param Connection $connection
     */
    public function saveDatabaseConfiguration($connection)
    {
        $this->renderFile('_database_conf.twig', __DIR__ . '/../../../../conf/database_conf.php', array(
            'database_name' => $connection->getDatabase(),
            'database_server_host' => $connection->getHost(),
            'database_server_port' => $connection->getPort(),
            'database_user' => $connection->getUsername(),
            'database_password' => $connection->getPassword()
        ));

        $this->renderFile('_configuration.twig', __DIR__ . '/../../../../conf/configuration.php', array());
    }

    /**
     * Fill database with sample data
     *
     * @param Connection $connection
     */
    public function installSampleData($connection)
    {
        $sqlFile =  __DIR__ . '/../../../../install/Resources/sql/campsite_demo_data.sql';
        $errors = $this->importDB($sqlFile, $connection);
    }

    /**
     * Install database schema
     *
     * @param Connection $connection
     * @param string     $host
     */
    public function installDatabaseSchema($connection, $host = null, $publicationName = null)
    {
        $sqlFile =  __DIR__ . '/../../../../install/Resources/sql/campsite_demo_tables.sql';
        $errors = $this->importDB($sqlFile, $connection);

        $connection->executeQuery('INSERT IGNORE INTO Aliases VALUES (2,?,1)', array($host));
        $connection->executeQuery("INSERT IGNORE INTO Publications VALUES (1,?,1,'D',0.00,0.00,'',0,0,2,2,NULL,1,1,0,0,1,0,0,'','',NULL,'a:1:{s:4:\"name\";s:2:\"on\";}')", array($publicationName));
    }

    /**
     * Fill datatabase with geodata
     *
     * @param Connection $connection
     *
     * @return boolean|null
     */
    public function loadGeoData($connection)
    {
        $which_output = '';
        $which_ret = '';
        @exec('which mysql', $which_output, $which_ret);

        if (is_array($which_output) && (isset($which_output[0]))) {
            $mysql_client_command = $which_output[0];

            if (!$this->withMysqlAllIsOk($mysql_client_command)) {
                return false;
            }

            $last_dir = getcwd();
            $work_dir = __DIR__ . '/../../../../install/Resources/sql';
            chdir($work_dir);

            $db_host = $connection->getHost();
            $db_port = $connection->getPort();
            $db_user = $connection->getUsername();
            $db_pass = $connection->getPassword();
            $db_name = $connection->getDatabase();

            $access_params = '';
            $access_params .= ' -h ' . escapeshellarg($db_host);
            if (!empty($db_port)) {
                $access_params .= ' -P ' . escapeshellarg('' . $db_port);
            }
            $access_params .= ' -u ' . escapeshellarg($db_user);
            if (!empty($db_pass)) {
                $access_params .= ' -p' . escapeshellarg($db_pass);
            }
            $access_params .= ' -D ' . escapeshellarg($db_name);

            $cmd_string = escapeshellcmd($mysql_client_command) . $access_params . ' --local-infile=1 < ' . 'geonames.sql';
            $cmd_output = array();
            $cmd_retval = 0;
            exec($cmd_string, $cmd_output, $cmd_retval);

            chdir($last_dir);
            if (!empty($cmd_retval)) {
                return false;
            }

            return true;
        }
    }

    /**
     * Import sql file to databas
     * @param string     $sqlFilePath
     * @param Connection $connection
     * @param mixed      $logger
     *
     * @return integer
     */
    public function importDB($sqlFilePath, $connection, $logger = null)
    {
        if (!($sqlFile = file_get_contents($sqlFilePath))) {
            return false;
        }

        if ($logger == null) {
            $logger = $this->logger;
        }

        $queries = $this->splitSQL($sqlFile);

        $errors = 0;
        foreach ($queries as $query) {
            $query = trim($query);
            if (!empty($query) && $query{0} != '#' && (0 !== strpos($query, "--"))) {
                if (0 !== strpos(strtolower($query), "system")) {
                    try {
                        $connection->executeQuery($query);
                    } catch (\Exception $e) {
                        $errors++;
                        $this->errorQueries[] = $query;
                        $logger->addError('Error with query "'.$query.'"');

                        continue;
                    }
                }

                // if it started via the system command
                $command_parts = array();
                foreach (explode(" ", $query) as $query_part) {
                    $query_part = trim($query_part);
                    if ("" != $query_part) {
                        $command_parts[] = $query_part;
                    }
                }

                $command_script = "";
                $command_known = false;
                if (3 == count($command_parts)) {
                    if ("php" == strtolower($command_parts[1])) {
                        $command_known = true;
                        $command_script = trim($command_parts[2], ";");
                    }
                }

                if (!$command_known) {
                    continue;
                }

                $command_path = dirname($sqlFilePath);
                $command_path = $this->combinePaths($command_path, $command_script);

                require_once($command_path);

                if (isset($upgradeErrors) && is_array($upgradeErrors) && count($upgradeErrors) > 0) {
                    foreach ($upgradeErrors as $upgradeError) {
                        $errors++;
                        $this->errorQueries[] = $upgradeError;
                    }
                }
            }
        }

        return $errors;
    }

    /**
     * Puts together two paths, usually an absolute one (directory), plus a relative one (filename)
     *
     * @param string $dirFirst
     * @param string $dirSecond
     *
     * @return string
     */
    private function combinePaths($dirFirst, $dirSecond)
    {
        if (0 === strpos(strtolower($dirSecond), "/")) {
            return $dirSecond;
        }

        if (0 === strpos(strtolower($dirSecond), "./")) {
            $dirSecond = substr($dirSecond, 2);

            return $dirFirst . DIRECTORY_SEPARATOR . $dirSecond;
        }

        while (0 === strpos(strtolower($dirSecond), "../")) {
            $dirFirst = dirname($dirFirst);
            $dirSecond = substr($dirSecond, 3);
        }

        return $dirFirst . DIRECTORY_SEPARATOR . $dirSecond;
    }

    /**
     * Check if current mysql instance is ok for Newscoop
     * @param string $mysqlClientCommand
     *
     * @return boolean
     */
    private function withMysqlAllIsOk($mysqlClientCommand)
    {
        if (!file_exists($mysqlClientCommand)) {
            return false;
        }

        if ((!is_file($mysqlClientCommand)) && (!is_link($mysqlClientCommand))) {
            return false;
        }

        if (!is_executable($mysqlClientCommand)) {
            return false;
        }

        return true;
    }

    /**
     * render Twig template
     *
     * @param string $template
     * @param array  $parameters
     *
     * @return string
     */
    protected function renderTwigTemplate($template, $parameters)
    {
        $twig = new \Twig_Environment(
            new \Twig_Loader_Filesystem(__DIR__ . '/../../../../install/Resources/templates/'),
            array(
                'debug'            => true,
                'cache'            => false,
                'strict_variables' => true,
                'autoescape'       => false,
            )
        );

        return $twig->render($template, $parameters);
    }

    /**
     * Save TWIG template to file
     * @param string $template
     * @param string $target
     * @param array  $parameters
     *
     * @return integer
     */
    public function renderFile($template, $target, $parameters)
    {
        if (!is_dir(dirname($target))) {
            mkdir(dirname($target), 0777, true);
        }

        return file_put_contents($target, $this->renderTwigTemplate($template, $parameters));
    }

    /**
     * Split big sql to array fo queries
     *
     * @param string $sqlFile
     *
     * @return array
     */
    private function splitSQL($sqlFile)
    {
        $sqlFile = trim($sqlFile);
        $sqlFile = preg_replace("/\n\#[^\n]*/", '', "\n".$sqlFile);
        $buffer = array ();
        $return = array ();
        $inString = false;

        for ($i = 0; $i < strlen($sqlFile) - 1; $i ++) {
            if ($sqlFile[$i] == ";" && !$inString) {
                $return[] = substr($sqlFile, 0, $i);
                $sqlFile = substr($sqlFile, $i +1);
                $i = 0;
            }

            if ($inString && ($sqlFile[$i] == $inString)
                    && $buffer[1] != "\\") {
                $inString = false;
            } elseif (!$inString && ($sqlFile[$i] == '"'
                                     || $sqlFile[$i] == "'")
                          && (!isset ($buffer[0]) || $buffer[0] != "\\")) {
                $inString = $sqlFile[$i];
            }
            if (isset($buffer[1])) {
                $buffer[0] = $buffer[1];
            }

            $buffer[1] = $sqlFile[$i];
        }

        if (!empty($sqlFile)) {
            $return[] = $sqlFile;
        }

        return $return;
    }

    /**
     * Compares versions of Newscoop for upgrades 3.1.0 before 3.1.x, 3.5.2 before 3.5.11
     *
     * @param string $p_version1
     * @param string $p_version2
     *
     * @return int
     */
    public function versionCompare($p_version1, $p_version2)
    {
        $version1 = "" . $p_version1;
        $version2 = "" . $p_version2;

        $ver1_arr = explode(".", $version1);
        $ver2_arr = explode(".", $version2);
        $ver1_len = count($ver1_arr);
        $ver2_len = count($ver2_arr);

        $ver_len = $ver1_len;
        if ($ver2_len < $ver_len) {$ver_len = $ver2_len;}

        for ($ind = 0; $ind < $ver_len; $ind++) {
            if ($ver1_arr[$ind] < $ver2_arr[$ind]) {return -1;}
            if ($ver1_arr[$ind] > $ver2_arr[$ind]) {return 1;}
        }

        if ($ver1_len < $ver2_len) {return -1;}
        if ($ver1_len > $ver2_len) {return 1;}

        return 0;
    }

    /**
     * Search for db roll in rols directory
     *
     * @param string $roll_base_dir
     * @param string $last_db_roll
     *
     * @return array
     */
    public function searchDbRolls($roll_base_dir, $last_db_roll)
    {
        $rolls = array(); // roll_name => roll_path

        $roll_dir_names = scandir($roll_base_dir);
        if (empty($roll_dir_names)) {
            return $rolls;
        }

        $avoid_starts = array('.', '_');
        $some_top_files = false;
        foreach ($roll_dir_names as $one_rol_dir) {
            $cur_rol_path = $roll_base_dir . DIRECTORY_SEPARATOR . $one_rol_dir;
            if (is_file($cur_rol_path) && ('sql' == pathinfo($cur_rol_path, PATHINFO_EXTENSION))) {
                $some_top_files = true;
            }

            if ((!is_dir($cur_rol_path)) || (in_array(substr($one_rol_dir, 0, 1), $avoid_starts))) {
                continue;
            }

            if ((!empty($last_db_roll)) && ($one_rol_dir <= $last_db_roll)) {
                continue;
            }

            $rolls[$one_rol_dir] = $cur_rol_path;
        }

        ksort($rolls);

        if (empty($last_db_roll)) {
            if ($some_top_files) {
                $rolls = array_merge(array('.' => $roll_base_dir), $rolls);
            }
        }

        return $rolls;
    }

    /**
     * Sava new database version in Versions table
     *
     * @param Connection $connection
     * @param string     $version
     * @param string     $roll
     *
     * @return boolean
     */
    public function saveDatabaseVersion($connection, $version, $roll)
    {
        $version = str_replace(array('"', '\''), array('_', '_'), $version);
        $roll = str_replace(array('"', '\''), array('_', '_'), $roll);

        $connection->executeQuery('INSERT INTO Versions (ver_name, ver_value) VALUES ("last_db_version", :version) ON DUPLICATE KEY UPDATE ver_value = :version', array(
            'version' => $version
        ));

        $connection->executeQuery('INSERT INTO Versions (ver_name, ver_value) VALUES ("last_db_roll", :roll) ON DUPLICATE KEY UPDATE ver_value = :roll', array(
            'roll' => $roll
        ));

        return true;
    }
}
