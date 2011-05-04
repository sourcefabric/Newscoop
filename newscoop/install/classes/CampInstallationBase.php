<?php
/**
 * @package Campsite
 *
 * @author Holman Romero <holman.romero@gmail.com>
 * @author Mugur Rus <mugur.rus@gmail.com>
 * @copyright 2007 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @version $Revision$
 * @link http://www.sourcefabric.org
 */

global $g_db;

/**
 * Includes
 */
require_once($GLOBALS['g_campsiteDir'].'/conf/install_conf.php');
require_once('adodb/adodb.inc.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Input.php');
require_once($GLOBALS['g_campsiteDir'].'/template_engine/classes/CampRequest.php');
require_once($GLOBALS['g_campsiteDir'].'/install/classes/CampInstallationView.php');


/**
 * Class CampInstallation
 */
class CampInstallationBase
{
    /**
     * @var array
     */
    protected $m_config = array();

    /**
     * @var string
     */
    protected $m_step = null;

    /**
     * @var string
     */
    protected $m_defaultStep = 'precheck';

    /**
     * @var string
     */
    protected $m_sampleSiteName = 'classic';

    /**
     * @var string
     */
    protected $m_message = null;

    /**
     * @var string
     */
    protected $m_os = null;

    /**
     * @var boolean
     */
    protected $m_overwriteDb = false;


    /**
     *
     */
    protected function execute()
    {
        $input = CampRequest::GetInput('post');
        $session = CampSession::singleton();

        $this->m_step = (!empty($input['step'])) ? $input['step'] : $this->m_defaultStep;

        switch($this->m_step) {
        case 'precheck':
            break;
        case 'license':
            $session->unsetData('config.db', 'installation');
            $session->unsetData('config.site', 'installation');
            $session->unsetData('config.demo', 'installation');
            $this->preInstallationCheck();
            break;
        case 'database':
            $this->license();
            break;
        case 'mainconfig':
            $prevStep = (isset($input['this_step'])) ? $input['this_step'] : '';
            if ($prevStep != 'loaddemo'
                    && $this->databaseConfiguration($input)) {
                $session->setData('config.db', $this->m_config['database'], 'installation', true);
            }
            break;
        case 'loaddemo':
            $prevStep = (isset($input['this_step'])) ? $input['this_step'] : '';
            if ($prevStep != 'loaddemo'
                    && $this->generalConfiguration($input)) {
                $session->setData('config.site', $this->m_config['mainconfig'], 'installation', true);
            }
            break;
        case 'cronjobs':
            if (isset($input['install_demo'])) {
                $session->setData('config.demo', array('loaddemo' => $input['install_demo']), 'installation', true);
                if ($input['install_demo'] != '0') {
                    if (!$this->loadDemoSite()) {
                        break;
                    }
                }
            }
            break;
        case 'finish':
            if (isset($input['install_demo'])) {
                $session->setData('config.demo', array('loaddemo' => $input['install_demo']), 'installation', true);
                if ($input['install_demo'] != '0') {
                    if (!$this->loadDemoSite()) {
                        break;
                    }
                }
            }
            $this->saveCronJobsScripts();
            if ($this->finish()) {
                $this->saveConfiguration();
                self::InstallPlugins();

                require_once($GLOBALS['g_campsiteDir'].'/classes/SystemPref.php');
                SystemPref::DeleteSystemPrefsFromCache();

                // clear all cache
                require_once($GLOBALS['g_campsiteDir'].'/classes/CampCache.php');
                CampCache::singleton()->clear('user');
                CampCache::singleton()->clear();
                CampTemplate::singleton()->clearCache();
            }
            break;
        }
    } // fn execute


    private function preInstallationCheck() {}


    private function license() {}


    /**
     *
     */
    private function databaseConfiguration($p_input)
    {
        global $g_db;

        if (file_exists(CS_PATH_SITE . DIR_SEP . '.htaccess')) {
        	if (!file_exists(CS_PATH_SITE . DIR_SEP . '.htaccess-default')) {
        		@copy(CS_PATH_SITE . DIR_SEP . '.htaccess', CS_PATH_SITE . DIR_SEP . '.htaccess-default');
        	}
        	@unlink(CS_PATH_SITE . DIR_SEP . '.htaccess');
        }

        $session = CampSession::singleton();

        $db_hostname = Input::Get('db_hostname', 'text');
        $db_hostport = Input::Get('db_hostport', 'int');
        $db_username = Input::Get('db_username', 'text');
        $db_userpass = Input::Get('db_userpass', 'text');
        $db_database = Input::Get('db_database', 'text');
        $db_overwrite = Input::Get('db_overwrite', 'int', 0);

        $dbhost = $db_hostname;
        if (empty($db_hostport)) {
            $db_hostport = 3306;
        } else { // add port to hostname
            $dbhost .= ':' . $db_hostport;
        }

        if (empty($db_hostname) || empty($db_hostport)
                || empty($db_username) || empty($db_database)) {
            $this->m_step = 'database';
            $this->m_message = 'Error: Please input the requested data';
            return false;
        }

        $error = false;
        $g_db = ADONewConnection('mysql');
        $g_db->SetFetchMode(ADODB_FETCH_ASSOC);
        @$g_db->Connect($dbhost, $db_username, $db_userpass);
        if (!$g_db->isConnected()) {
            $error = true;
        } else {
            $isDbEmpty = TRUE;
            $selectDb = $g_db->SelectDB($db_database);
            if ($selectDb) {
                $dbTables = $g_db->GetAll('SHOW TABLES');
                $isDbEmpty = empty($dbTables) ? TRUE : FALSE;
            }
            if (!$isDbEmpty && !$db_overwrite) {
                $this->m_step = 'database';
                $this->m_overwriteDb = true;
                $this->m_message = '<p>There is already a database named <i>' . $db_database . '</i>.</p><p>If you are sure to overwrite it, check <i>Yes</i> for the option below. If not, just change the <i>Database Name</i> and continue.</p>';
                $this->m_config['database'] = array(
                    'hostname' => $db_hostname,
                    'hostport' => $db_hostport,
                    'username' => $db_username,
                    'userpass' => $db_userpass,
                    'database' => $db_database
                );
                $session->unsetData('config.db', 'installation');
                $session->setData('config.db', $this->m_config['database'], 'installation', true);
                return false;
            }
        }

        if (!$error && !$selectDb) {
            $dict = NewDataDictionary($g_db);
            $sql = $dict->CreateDatabase($db_database, array('MYSQL'=>'CHARACTER SET utf8'));
            // ExecuteSQLArray() returns:
            // 0 if failed,
            // 1 if executed all but with errors,
            // 2 if executed successfully
            if ($dict->ExecuteSQLArray($sql) != 2) {
                $error = true;
            }
            $g_db->SelectDB($db_database);
        }

        if ($error == true) {
            $this->m_step = 'database';
            $this->m_message = 'Error: Database parameters invalid. Could not '
                . 'connect to database server.';
            return false;
        }

        $sqlFile = CS_INSTALL_DIR.DIR_SEP.'sql'.DIR_SEP.'campsite_core.sql';
        $errors = CampInstallationBaseHelper::ImportDB($sqlFile, $errorQueries);
        if ($errors > 0) {
            $this->m_step = 'database';
            $this->m_message = 'Error: Importing Database';
            foreach ($errorQueries as $query) {
                $this->m_message .= "<br>$query";
            }
            return false;
        }

        // load geonames
        set_time_limit(0);
        foreach (array('CityNames', 'CityLocations') as $table) {
            $g_db->Execute("TRUNCATE `$table`");
            $g_db->Execute("ALTER TABLE `$table` DISABLE KEYS");
            $csvFile = CS_INSTALL_DIR.DIR_SEP.'sql'.DIR_SEP."$table.csv";
            $csvFile = str_replace("\\", "\\\\", $csvFile);
            $g_db->Execute("LOAD DATA LOCAL INFILE '$csvFile' INTO TABLE $table FIELDS TERMINATED BY ';' ENCLOSED BY '\"'");
            $g_db->Execute("ALTER TABLE `$table` ENABLE KEYS");
        }

        $this->m_config['database'] = array(
                                            'hostname' => $db_hostname,
                                            'hostport' => $db_hostport,
                                            'username' => $db_username,
                                            'userpass' => $db_userpass,
                                            'database' => $db_database
                                            );

        return true;
    } // fn databaseConfiguration


    /**
     *
     */
    private function generalConfiguration($p_input)
    {
        $mc_sitetitle = Input::Get('Site_Title', 'text');
        $mc_adminpsswd = Input::Get('Admin_Password', 'text');
        $mc_admincpsswd = Input::Get('Confirm_Password', 'text');
        $mc_adminemail = Input::Get('Admin_Email', 'text');

        if (empty($mc_sitetitle) || empty($mc_adminpsswd)
                || empty($mc_admincpsswd) || empty($mc_adminemail)) {
            $this->m_step = 'mainconfig';
            $this->m_message = 'Error: Please input the requested data';
            return false;
        }

        $psswd_validation = CampInstallationBaseHelper::ValidatePassword($mc_adminpsswd, $mc_admincpsswd);
        if ($psswd_validation['result'] == FALSE) {
            $this->m_step = 'mainconfig';
            $this->m_message = 'Error: ' . $psswd_validation['message'];
            return false;
        }

        $this->m_config['mainconfig'] = array(
                                              'sitetitle' => $mc_sitetitle,
                                              'adminemail' => $mc_adminemail,
                                              'adminpsswd' => $mc_adminpsswd
                                              );

        return true;
    } // fn generalConfiguration


    /**
     *
     */
    private function loadDemoSite()
    {
        global $g_db;
        $session = CampSession::singleton();
        $template_name = $session->getData('config.demo', 'installation');
        $isWritable = true;
        $directories = array();
        $templatesDir = CS_PATH_TEMPLATES;
        $cssDir = $templatesDir.DIR_SEP.'css';
        $imagesDir = $templatesDir.DIR_SEP.'img';
        if (!is_dir($templatesDir) || !is_writable($templatesDir)) {
            $directories[] = $templatesDir;
            $isWritable = false;
        }
        if ((!is_dir($cssDir) && !mkdir($cssDir)) || !is_writable($cssDir)) {
            $directories[] = $cssDir;
            $isWritable = false;
        }
        if ((!is_dir($imagesDir) && !mkdir($imagesDir)) || !is_writable($imagesDir)) {
            $directories[] = $imagesDir;
            $isWritable = false;
        }

        if (!$isWritable) {
            $dirList = implode('<br />', $directories);
            $this->m_step = 'loaddemo';
            $this->m_message = 'Error: Templates directories'
                . '<div class="nonwritable"><em>'.$dirList
                . '</font></em></div>'
                . 'are not writable, please set the appropiate '
                . 'permissions in order to install the demo site files.';
            return false;
        }

        require_once($GLOBALS['g_campsiteDir'].'/bin/cli_script_lib.php');
        camp_remove_dir(CS_PATH_TEMPLATES.DIR_SEP.'*', null, array('system_templates'));

        // copies template files to corresponding directory
        $source = CS_INSTALL_DIR.DIR_SEP.'sample_templates'.DIR_SEP.$template_name['loaddemo'].DIR_SEP.'templates';
        $target = CS_PATH_TEMPLATES;
        if (CampInstallationBaseHelper::CopyFiles($source, $target) == false) {
            $this->m_step = 'loaddemo';
            $this->m_message = 'Error: Copying sample site files';
            return false;
        }

        // copies template data files to corresponding directories.
        // data files are article images and article attachments
        $source = CS_INSTALL_DIR.DIR_SEP.'sample_data';
        $target = CS_PATH_SITE;
        if (CampInstallationBaseHelper::CopyFiles($source, $target) == false) {
            $this->m_step = 'loaddemo';
            $this->m_message = 'Error: Copying sample site data files';
            die('failed');
            return false;
        }

        if (CampInstallationBaseHelper::ConnectDB() == false) {
            $this->m_step = 'loaddemo';
            $this->m_message = 'Error: Database parameters invalid. Could not '
                . 'connect to database server.';
            return false;
        }

        $sqlFile = CS_INSTALL_DIR.DIR_SEP.'sql'.DIR_SEP.'campsite_demo_tables.sql';
        $errors = CampInstallationBaseHelper::ImportDB($sqlFile, $errorQueries);
        if ($errors > 0) {
            $this->m_step = 'loaddemo';
            $this->m_message = 'Error: Importing Database: demo tables';
            foreach ($errorQueries as $query) {
                $this->m_message .= "<br>$query";
            }
            return false;
        }

        $sqlFile = CS_INSTALL_DIR.DIR_SEP.'sql'.DIR_SEP.'campsite_demo_prepare.sql';
        $errors = CampInstallationBaseHelper::ImportDB($sqlFile, $errorQueries);
        if ($errors > 0) {
            $this->m_step = 'loaddemo';
            $this->m_message = 'Error: Importing Database: demo prepare file';
            foreach ($errorQueries as $query) {
                $this->m_message .= "<br>$query";
            }
            return false;
        }

        $sqlFile = CS_INSTALL_DIR.DIR_SEP.'sql'.DIR_SEP.'campsite_demo_data.sql';
        $errors = CampInstallationBaseHelper::ImportDB($sqlFile, $errorQueries);
        if ($errors > 0) {
            $this->m_step = 'loaddemo';
            $this->m_message = 'Error: Importing Database: demo data';
            foreach ($errorQueries as $query) {
                $this->m_message .= "<br>$query";
            }
            return false;
        }

        return true;
    } // fn loadDemoSite


    /**
     *
     */
    private function finish()
    {
        $session = CampSession::singleton();
        $dbData = $session->getData('config.db', 'installation');
        $mcData = $session->getData('config.site', 'installation');

        if (is_array($mcData) && isset($mcData['sitetitle'])
        && !CampInstallationBaseHelper::SetSiteTitle($mcData['sitetitle'])) {
            $this->m_step = 'mainconfig';
            $this->m_message = 'Error: Could not update the site title.';
            return false;
        }
        if (is_array($mcData) && isset($mcData['adminemail'])
        && !CampInstallationBaseHelper::CreateAdminUser($mcData['adminemail'], $mcData['adminpsswd'])) {
            $this->m_step = 'mainconfig';
            $this->m_message = 'Error: Could not update the admin user credentials.';
            return false;
        }

		if (!file_exists(CS_PATH_SITE . DIR_SEP . '.htaccess')
        && !copy(CS_PATH_SITE . DIR_SEP . 'htaccess', CS_PATH_SITE . DIR_SEP . '.htaccess')) {
            $this->m_step = 'mainconfig';
            $this->m_message = 'Error: Could not create the htaccess file.';
            return false;
        }

        if (file_exists(CS_PATH_SITE . DIR_SEP . 'upgrade.php')) {
            @unlink(CS_PATH_SITE . DIR_SEP . 'upgrade.php');
        }

        return true;
    } // fn finish


    /**
     *
     */
    private function saveCronJobsScripts()
    {
        global $g_db;

        $cronJobs = array('newscoop_autopublish',
                          'newscoop_indexer',
                          'newscoop_notifyendsubs',
                          'newscoop_notifyevents',
                          'newscoop_statistics');

        $template = CampTemplate::singleton();
        $campsiteBinDir = CS_PATH_SITE.DIR_SEP.'bin';
        $template->assign('CAMPSITE_BIN_DIR', CS_PATH_SITE.DIR_SEP.'bin');

        $cmd = 'crontab -l';
        $external = true;
        exec($cmd, $output, $result);
        if ($result != 0) {
            $cmd = 'crontab -';
            exec($cmd, $output, $result);
            if ($result != 0) {
                $external = false;
                if (CampInstallationBaseHelper::ConnectDB() == false) {
                    $this->m_step = 'cronjobs';
                    $this->m_message = 'Error: Database parameters invalid. Could not '
                    . 'connect to database server.';
                    return false;
                }
                $sqlQuery = "UPDATE SystemPreferences SET value = 'N' "
                    ."WHERE varname = 'ExternalCronManagement'";
                $g_db->Execute($sqlQuery);
            }
        }

        $cronJobsDir = CS_INSTALL_DIR.DIR_SEP.'cron_jobs';
        $allAtOnceFile = $cronJobsDir.DIR_SEP.'all_at_once';
        if (file_exists($allAtOnceFile)) {
            unlink($allAtOnceFile);
        }
        $alreadyInstalled = false;
        foreach ($output as $cronLine) {
            if (!file_put_contents($allAtOnceFile, "$cronLine\n", FILE_APPEND)) {
                $error = true;
            }
            if (strstr($cronLine, $campsiteBinDir)) {
                $alreadyInstalled = true;
            }
        }
        if ($alreadyInstalled) {
            return true;
        }

        $buffer = '';
        $isFileWritable = is_writable($cronJobsDir);
        $error = false;
        foreach ($cronJobs as $cronJob) {
            $buffer = $template->fetch('_'.$cronJob.'.tpl');
            $cronJobFile = $cronJobsDir.DIR_SEP.$cronJob;
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
            $this->m_step = 'cronjobs';
            $this->m_message = 'Error: Could not save cron job files. '
                .'Apache user must have write permissions on <em>install/cron_jobs/</em> directory.';
            return false;
        }

        if ($external && file_exists($allAtOnceFile)) {
            $cmd = 'crontab '.escapeshellarg($allAtOnceFile);
            system($cmd);
        }

        return true;
    } // fn saveCronJobsScripts


    /**
     *
     */
    private function saveConfiguration()
    {
        $session = CampSession::singleton();
        $dbData = $session->getData('config.db', 'installation');

        $template = CampTemplate::singleton();
        $template->assign('DATABASE_SERVER_ADDRESS', $dbData['hostname']);
        $template->assign('DATABASE_SERVER_PORT', $dbData['hostport']);
        $template->assign('DATABASE_NAME', $dbData['database']);
        $template->assign('DATABASE_USER', $dbData['username']);
        $template->assign('DATABASE_PASSWORD', $dbData['userpass']);

        $buffer1 = $template->fetch('_configuration.tpl');
        $buffer1 = preg_replace('/#{#{/', '{{', $buffer1);
        $buffer1 = preg_replace('/#}#}/', '}}', $buffer1);

        $buffer2 = $template->fetch('_database_conf.tpl');

        $path1 = CS_PATH_CONFIG.DIR_SEP.'configuration.php';
        $path2 = CS_PATH_CONFIG.DIR_SEP.'database_conf.php';
        if (file_exists($path1) && file_exists($path2)) {
            $isConfigWritable = is_writable($path1);
            $isDBConfigWritable = is_writable($path2);
        } else {
            $isConfigWritable = is_writable(CS_PATH_CONFIG);
            $isDBConfigWritable = $isConfigWritable;
        }

        if (!$isConfigWritable || !$isDBConfigWritable) {
            $this->m_step = 'mainconfig';
            $this->m_message = 'Error: Could not write the configuration file.';
            return false;
        }

        file_put_contents($path1, $buffer1);
        file_put_contents($path2, $buffer2);
        @chmod($path2, 0600);

        // create images and files directories
        CampInstallationBase::CreateDirectory($GLOBALS['g_campsiteDir'].DIR_SEP.'images');
        CampInstallationBase::CreateDirectory($GLOBALS['g_campsiteDir'].DIR_SEP.'images'.DIR_SEP.'thumbnails');
        CampInstallationBase::CreateDirectory($GLOBALS['g_campsiteDir'].DIR_SEP.'files');

        return true;
    } // fn saveConfiguration


    /**
     * Creates the given directory; verifies if it already exists of if
     * another file with the same name exists.
     *
     * @param string $p_directoryPath
     */
    public static function CreateDirectory($p_directoryPath)
    {
        if (file_exists($p_directoryPath) && !is_dir($p_directoryPath)) {
            unlink($p_directoryPath);
        }
        if (!is_dir($p_directoryPath)) {
            mkdir($p_directoryPath);
        }
    } // fn CreateDirectory


    private static function InstallPlugins()
    {
        require_once($GLOBALS['g_campsiteDir'].'/include/campsite_constants.php');
        require_once(dirname(dirname(dirname(__FILE__))) . DIR_SEP . 'db_connect.php');
        require_once dirname(dirname(dirname(__FILE__))) . '/classes/CampPlugin.php';

        foreach (CampPlugin::GetPluginsInfo() as $info) {
            $CampPlugin = new CampPlugin($info['name']);
            $CampPlugin->create($info['name'], $info['version']);
            $CampPlugin->install();
            if ($CampPlugin->isEnabled()) {
                $CampPlugin->enable();
            } else {
                $CampPlugin->disable();
            }

            if (function_exists("plugin_{$info['name']}_addPermissions")) {
                call_user_func("plugin_{$info['name']}_addPermissions");
            }
        }
    }

} // fn ClassInstallationBase


/**
 * Class CampInstallationBaseHelper
 */
class CampInstallationBaseHelper
{
    const PASSWORD_MINLENGTH = 5;

    /**
     *
     */
    public static function ConnectDB()
    {
        global $g_db;

        if (is_a($g_db, 'ADONewConnection')) {
            return true;
        }

        $session = CampSession::singleton();
        $dbData = $session->getData('config.db', 'installation');

        if (empty($dbData)) {
            return false;
        }

        $dbhost = $dbData['hostname'];
        if (!empty($dbData['hostport'])) { // add port to hostname
            $dbhost .= ':' . $dbData['hostport'];
        }

        $g_db = ADONewConnection('mysql');
        $g_db->SetFetchMode(ADODB_FETCH_ASSOC);
        return @$g_db->Connect($dbhost, $dbData['username'],
                               $dbData['userpass'], $dbData['database']);
    } // fn ConnectDB


    /**
     *
     */
    public static function CreateAdminUser($p_email, $p_password)
    {
        global $g_db;

        if (self::ConnectDB() == false) {
            return false;
        }

        $sqlQuery1 = 'UPDATE liveuser_users SET '
            ."Password = SHA1('".$g_db->Escape($p_password)."'), "
            ."EMail = '".$g_db->Escape($p_email)."' "
            .'WHERE Id = 1';
        $sqlQuery2 = 'UPDATE phorum_users SET '
            ."password = SHA1('".$g_db->Escape($p_password)."'), "
            ."email = '".$g_db->Escape($p_email)."' "
            .'WHERE user_id = 1';
        if (!$g_db->Execute($sqlQuery1) || !$g_db->Execute($sqlQuery2)) {
            return false;
        }

        return true;
    } // fn CreateAdminUser


    /**
     *
     */
    public static function ImportDB($p_sqlFile, &$errorQueries)
    {
        global $g_db;

        if(!($sqlFile = file_get_contents($p_sqlFile))) {
            return false;
        }

        $queries = self::SplitSQL($sqlFile);

        $errors = 0;
        $errorQueries = array();
        foreach($queries as $query) {
            $query = trim($query);
            if (!empty($query) && $query{0} != '#') {
                if ($g_db->Execute($query) == false) {
                    $errors++;
                    $errorQueries[] = $query;
                }
            }
        }

        return $errors;
    } // fn ImportDB


    public static function SetSiteTitle($p_title)
    {
        global $g_db;

        if (self::ConnectDB() == false) {
            return false;
        }

        $p_title = $g_db->escape($p_title);
        $sqlQuery = "UPDATE SystemPreferences SET value = '$p_title' "
        ."WHERE varname = 'SiteTitle'";
        return $g_db->Execute($sqlQuery);
    }


    /**
     *
     */
    public static function SplitSQL($p_sqlFile)
    {
        $p_sqlFile = trim($p_sqlFile);
        $p_sqlFile = preg_replace("/\n\#[^\n]*/", '', "\n".$p_sqlFile);
        $buffer = array ();
        $return = array ();
        $inString = false;

        for ($i = 0; $i < strlen($p_sqlFile) - 1; $i ++) {
            if ($p_sqlFile[$i] == ";" && !$inString) {
                $return[] = substr($p_sqlFile, 0, $i);
                $p_sqlFile = substr($p_sqlFile, $i +1);
                $i = 0;
            }

            if ($inString && ($p_sqlFile[$i] == $inString)
                    && $buffer[1] != "\\") {
                $inString = false;
            } elseif (!$inString && ($p_sqlFile[$i] == '"'
                                     || $p_sqlFile[$i] == "'")
                          && (!isset ($buffer[0]) || $buffer[0] != "\\")) {
                $inString = $p_sqlFile[$i];
            }
            if (isset($buffer[1])) {
                $buffer[0] = $buffer[1];
            }

            $buffer[1] = $p_sqlFile[$i];
        }

        if (!empty($p_sqlFile)) {
            $return[] = $p_sqlFile;
        }

        return $return;
    } // fn SplitSQL


    /**
     *
     */
    public static function ValidatePassword($p_password1, $p_password2)
    {
        $validator = array('result' => TRUE);
        if (strlen($p_password1) < self::PASSWORD_MINLENGTH) {
            $validator['result'] = FALSE;
            $validator['message'] = 'Password must be at least ' . self::PASSWORD_MINLENGTH . ' characters long.';
        }

        if ($validator['result'] == TRUE && ($p_password1 !== $p_password2)) {
            $validator['result'] = FALSE;
            $validator['message'] = 'Passwords do not match each other.';
        }

        return $validator;
    } // fn ValidatePassword


    /**
     *
     */
    public static function CopyFiles($p_source, $p_target)
    {
        if (is_dir($p_source)) {
            @mkdir($p_target);
            $d = dir($p_source);

            while(($entry = $d->read()) !== false) {
                if ($entry == '.' || $entry == '..' || $entry == '.svn') {
                    continue;
                }

                $Entry = $p_source . DIR_SEP . $entry;
                if (is_dir($Entry)) {
                    self::CopyFiles($Entry, $p_target . DIR_SEP . $entry);
                    continue;
                }
                @copy($Entry, $p_target . DIR_SEP . $entry);
            }

            $d->close();
        } else {
            @copy($p_source, $p_target);
        }

        return true;
    } // fn CopyFiles

} // class CampInstallationBaseHelper

?>
