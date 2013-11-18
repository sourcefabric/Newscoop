<?php
/**
 * @package Newscoop
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Installer\Services;

use Doctrine\DBAL\DriverManager;

class DatabaseService
{	
	private $logger;
	public $errorQueries = array();

	public function __construct($logger){
		$this->logger = $logger;
	}

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

	public function fillNewscoopDatabase($connection)
	{
		// import database from sql file
		$sqlFile =  __DIR__ . '/../../../../install/Resources/sql/campsite_core.sql';
		$errors = $this->importDB($sqlFile, $connection);

		if ($errors > 0) {
            return false;
        }

        // TODO: load geonames

        $db_versions = array_map('basename', glob(__DIR__ . '/../../../../install/Resources/sql/upgrade/[2-9].[0-9]*'));
        if (!empty($db_versions)) {
            usort($db_versions, array($this, 'camp_version_compare'));
            $db_last_version = array_pop($db_versions);
            $db_last_version_dir = __DIR__ . '/../../../../install/Resources/sql/upgrade/'.$db_last_version.'/';
            $db_last_roll = '';
            $db_rolls = $this->camp_search_db_rolls($db_last_version_dir, '');
            if (!empty($db_rolls)) {
                $db_last_roll_info = array_slice($db_rolls, -1, 1, true);
                $db_last_roll_info_keys = array_keys($db_last_roll_info);
                $db_last_roll = $db_last_roll_info_keys[0];
            }

            $this->camp_save_database_version($connection, $db_last_version, $db_last_roll);
            $this->logger->addInfo('Last db version:"'.$db_last_version.'", last db roll: "'.$db_last_roll.'"');
        }

        return true;
	}

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

	public function installSampleData($connection, $host)
	{
		$sqlFile =  __DIR__ . '/../../../../install/Resources/sql/campsite_demo_tables.sql';
		$errors = $this->importDB($sqlFile, $connection);

		$sqlFile =  __DIR__ . '/../../../../install/Resources/sql/campsite_demo_prepare.sql';
		$errors = $this->importDB($sqlFile, $connection);

		$sqlFile =  __DIR__ . '/../../../../install/Resources/sql/campsite_demo_data.sql';
		$errors = $this->importDB($sqlFile, $connection);
		
        $connection->executeQuery('UPDATE Aliases SET Name=?', array($host));
	}

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

	public function importDB($sqlFile, $connection)
    {
        if(!($sqlFile = file_get_contents($sqlFile))) {
            return false;
        }

        $queries = $this->splitSQL($sqlFile);

        $errors = 0;
        foreach($queries as $query) {
            $query = trim($query);
            if (!empty($query) && $query{0} != '#') {
            	try {
					$connection->executeQuery($query);
				} catch (\Exception $e) {
					$errors++;
                    $this->errorQueries[] = $query;
                    $this->logger->addDebug('Error with query "'.$query.'"');
				}
            }
        }

        return $errors;
    }

	private function withMysqlAllIsOk($mysql_client_command)
	{
	    if (!file_exists($mysql_client_command)) {
	        return false;
	    }

	    if ((!is_file($mysql_client_command)) && (!is_link($mysql_client_command))) {
	        return false;
	    }

	    if (!is_executable($mysql_client_command)) {
	        return false;
	    }

	    return true;
	}

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

    protected function renderFile($template, $target, $parameters)
    {
        if (!is_dir(dirname($target))) {
            mkdir(dirname($target), 0777, true);
        }

        return file_put_contents($target, $this->renderTwigTemplate($template, $parameters));
    }

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
	 * Compares versions of Newscoop for upgrades
	 * 3.1.0 before 3.1.x, 3.5.2 before 3.5.11
	 * @param $p_version1
	 * @param $p_version2
	 * @return int
	 */
	private function camp_version_compare($p_version1, $p_version2)
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

	private function camp_search_db_rolls($roll_base_dir, $last_db_roll)
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

	private function camp_save_database_version($connection, $version, $roll)
	{
	    $version = str_replace(array('"', '\''), array('_', '_'), $version);
	    $roll = str_replace(array('"', '\''), array('_', '_'), $roll);

	    $connection->executeQuery('INSERT INTO Versions (ver_name, ver_value) VALUES ("last_db_version", "' . $version . '") ON DUPLICATE KEY UPDATE ver_value = "' . $version . '"');
	    $connection->executeQuery('INSERT INTO Versions (ver_name, ver_value) VALUES ("last_db_roll", "' . $roll . '") ON DUPLICATE KEY UPDATE ver_value = "' . $roll . '"');

        return true;
	}
}