<?php
/**
 * @package Campsite
 * 
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @author Holman Romero <holman.romero@gmail.com>
 * @author Mugur Rus <mugur.rus@gmail.com>
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.sourcefabric.org
 */

use Newscoop\Entity\Resource;
use Newscoop\Service\IThemeManagementService;
use Newscoop\Service\IPublicationService;
use Newscoop\Service\Implementation\ThemeManagementServiceLocal;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

global $g_ado_db;

/**
 * Includes
 */
require_once($GLOBALS['g_campsiteDir'].'/conf/install_conf.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Input.php');
require_once($GLOBALS['g_campsiteDir'].'/template_engine/classes/CampRequest.php');
require_once($GLOBALS['g_campsiteDir'].'/install/classes/CampInstallationView.php');
require_once($GLOBALS['g_campsiteDir'].'/install/scripts/SQLImporting.php');

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
    protected $m_message = null;

    /**
     * @var string
     */
    protected $m_os = null;

    /**
     * @var boolean
     */
    protected $m_overwriteDb = false;

    protected function execute()
    {
        // test ado db connection
        //require_once($GLOBALS['g_campsiteDir'].'/db_connect.php');

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
                    $this->saveConfiguration();
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
                $this->installEmptyTheme();
                $this->saveCronJobsScripts();
                if ($this->finish()) {
                    self::InstallPlugins();
                    $this->initRenditions();

                    require_once($GLOBALS['g_campsiteDir'].'/classes/SystemPref.php');
                    SystemPref::DeleteSystemPrefsFromCache();

                    require_once($GLOBALS['g_campsiteDir'].'/classes/CampCache.php');
                    CampCache::singleton()->clear('user');
                    CampCache::singleton()->clear();
                    CampTemplate::singleton()->clearCache();
                }
                break;

        }
    }


    private function preInstallationCheck() {}

    private function license()
    {
        $license_agreement = Input::Get('license_agreement', 'int', 0);
        if ($license_agreement < 1) {
            $this->m_step = 'license';
            $this->m_message = 'You must accept the terms of the License Agreement!';

            return false;
        } else {
            return true;
        }
    }

    private function databaseConfiguration($p_input)
    {
        global $g_ado_db;

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
        }

        if (empty($db_hostname) || empty($db_hostport) || empty($db_username) || empty($db_database)) {
            $this->m_step = 'database';
            $this->m_message = 'Error: Please input the requested data';

            return false;
        }

        $error = false;

        $connectionParams = array(
            'dbname' => $db_database,
            'user' => $db_username,
            'password' => $db_userpass,
            'host' => $db_hostname,
            'port' => $db_hostport,
        );

        $g_ado_db = $this->getTestDbConnection($connectionParams);

        $isConnected = false;

        try {
            $isConnected = $g_ado_db->isConnected(true);
        } catch (Exception $e) {
            if ($e->getCode() == 1045) {
                $error = true;
            }
        }

        if (!$isConnected) {
            $error = true;
        } else {
            $isDbEmpty = TRUE;

            $selectDb = $g_ado_db->hasDatabase($db_database);
            if ($selectDb) {
                $g_ado_db = $this->getDbConnection($connectionParams);
                $dbTables = $g_ado_db->GetAll('SHOW TABLES');
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
            try {
                $g_ado_db->createDatabase($db_database);
                $g_ado_db = $this->getDbConnection($connectionParams);
            } catch (\Exception $e) {
                $error = true;
            }
        }

        if ($error == true) {
            $this->m_step = 'database';
            $this->m_message = 'Error: Database parameters invalid. Could not connect to database server.';
            return false;
        }

        $g_ado_db = $this->getDbConnection($connectionParams);

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
            $conn_specs = 'mysql:host='.$db_hostname.';';
            if (!empty($db_hostport)) {
                $conn_specs .= 'port='.$db_hostport.';';
            }
            $conn_specs .= 'dbname='.$db_database.'';

            $l_db = new PDO(
                $conn_specs,
                $db_username,
                $db_userpass,
                array(
                    PDO::MYSQL_ATTR_LOCAL_INFILE => 1,
                    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
                )
            );

            $l_db->exec("TRUNCATE `$table`");
            $l_db->exec("ALTER TABLE `$table` DISABLE KEYS");
            $csvFile = CS_INSTALL_DIR.DIR_SEP.'sql'.DIR_SEP."$table.csv";
            $csvFile = str_replace("\\", "\\\\", $csvFile);
            $l_db->exec("LOAD DATA LOCAL INFILE '$csvFile' INTO TABLE $table FIELDS TERMINATED BY ';' ENCLOSED BY '\"'");
            $l_db->exec("ALTER TABLE `$table` ENABLE KEYS");
        }

        require_once($GLOBALS['g_campsiteDir'].'/bin/cli_script_lib.php');
        if (!camp_geodata_loaded($g_ado_db)) {
            $which_output = '';
            $which_ret = '';
            @exec('which mysql', $which_output, $which_ret);

            if (is_array($which_output) && (isset($which_output[0]))) {
                $mysql_client_command = $which_output[0];
                if (0 < strlen($mysql_client_command)) {
                    $db_conf = array(
                        'host' => $db_hostname,
                        'port' => $db_hostport,
                        'user' => $db_username,
                        'pass' => $db_userpass,
                        'name' => $db_database,
                    );
                    camp_load_geodata($mysql_client_command, $db_conf);
                }
            }
        }

        require_once($GLOBALS['g_campsiteDir'].'/bin/cli_script_lib.php');
        if (!camp_geodata_loaded($g_ado_db)) {
            $which_output = '';
            $which_ret = '';
            @exec('which mysql', $which_output, $which_ret);

            if (is_array($which_output) && (isset($which_output[0]))) {
                $mysql_client_command = $which_output[0];
                if (0 < strlen($mysql_client_command)) {
                    $db_conf = array(
                        'host' => $db_hostname,
                        'port' => $db_hostport,
                        'user' => $db_username,
                        'pass' => $db_userpass,
                        'name' => $db_database,
                    );
                    camp_load_geodata($mysql_client_command, $db_conf);
                }
            }
        }

        { // installing the stored function for 'point in polygon' checking
            $sqlFile = CS_INSTALL_DIR . DIR_SEP . 'sql' . DIR_SEP . "checkpp.sql";
            importSqlStoredProgram($g_ado_db, $sqlFile);
        }

        $this->m_config['database'] = array(
            'hostname' => $db_hostname,
            'hostport' => $db_hostport,
            'username' => $db_username,
            'userpass' => $db_userpass,
            'database' => $db_database
        );

        require_once($GLOBALS['g_campsiteDir'].'/bin/cli_script_lib.php');
        camp_remove_dir(CS_PATH_TEMPLATES.DIR_SEP.'*', null, array('system_templates', 'unassigned'));

        // this is copyied form upgrade.php file - code duplication
        $db_versions = array_map('basename', glob($GLOBALS['g_campsiteDir'] . '/install/sql/upgrade/[2-9].[0-9]*'));
        if (!empty($db_versions)) {
            usort($db_versions, 'camp_version_compare');
            $db_last_version = array_pop($db_versions);
            $db_last_version_dir = $GLOBALS['g_campsiteDir'] . "/install/sql/upgrade/$db_last_version/";
            $db_last_roll = '';
            $db_rolls = camp_search_db_rolls($db_last_version_dir, '');
            if (!empty($db_rolls)) {
                $db_last_roll_info = array_slice($db_rolls, -1, 1, true);
                $db_last_roll_info_keys = array_keys($db_last_roll_info);
                $db_last_roll = $db_last_roll_info_keys[0];
            }
            camp_save_database_version($g_ado_db, $db_last_version, $db_last_roll);
        }

        return true;
    }

    /**
     * Get database connection
     *
     * @return Newscoop\Doctrine\AdoDbAdapter
     */
    private function getDbConnection(array $params)
    {
        $params = array_merge($params, array(
            'driver' => 'pdo_mysql',
            'charset' => 'UTF8',
        ));

        $config = new \Doctrine\DBAL\Configuration();
        $connection = \Doctrine\DBAL\DriverManager::getConnection($params, $config);
        return new \Newscoop\Doctrine\AdoDbAdapter($connection);
    }

    /**
     * Get test database connection
     *
     * @return Newscoop\Doctrine\AdoDbAdapter
     */
    private function getTestDbConnection(array $params)
    {
        unset($params['dbname']);

        return $this->getDbConnection($params);
    }

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
    }


    private function installEmptyTheme()
    {
        global $g_ado_db;
        $templatesDir = CS_PATH_TEMPLATES;
        $unassignedTemplatesDir = $templatesDir.DIR_SEP.ThemeManagementServiceLocal::FOLDER_UNASSIGNED;
        $emptyThemeDir = $unassignedTemplatesDir.DIR_SEP."empty";
        $isWritable = true;

        if ((!is_dir($unassignedTemplatesDir) && !mkdir($unassignedTemplatesDir)) || !is_writable($unassignedTemplatesDir)) {
            $isWritable = false;
        }

        if ((!is_dir($emptyThemeDir) && !mkdir($emptyThemeDir)) || !is_writable($emptyThemeDir)) {
            $isWritable = false;
        }

        if(!$isWritable) {
            return false;
        }

        // creating theme xml
        $themeXml = <<<XML
<theme name="Empty" designer="default" version="1.0" require="3.6">
    <description>This is an empty theme</description>
    <presentation-img src="preview-front.jpg" name="Front page"/>
    <presentation-img src="preview-section.jpg" name="Section page"/>
    <presentation-img src="preview-article.jpg" name="Article page"/>
    <output name="Web">
        <frontPage src="front.tpl"/>
        <sectionPage src="section.tpl"/>
        <articlePage src="article.tpl"/>
        <errorPage src="404.tpl"/>
    </output>
</theme>
XML;
        $sxml = new SimpleXMLElement($themeXml);
        $sxml->asXML($emptyThemeDir.DIR_SEP."theme.xml");

        // creating preview images
        $preview = @imagecreatetruecolor(210, 130);
        $logoPoints = array( 159, 9,   113, 34,   86, 99,   150, 121,   203, 99,   138, 78 );
        $textColor = imagecolorallocate($preview, 191, 191, 191);
        imagefill($preview, 0, 0, imagecolorallocate($preview, 255, 255, 255));
        imagefilledpolygon($preview, $logoPoints, 6, imagecolorallocate($preview, 239, 239, 239));
        imagestring($preview, 5, 10, 100,  'Empty Theme', $textColor);
        imagejpeg($preview, $emptyThemeDir.DIR_SEP."preview-front.jpg",100);
        imagejpeg($preview, $emptyThemeDir.DIR_SEP."preview-article.jpg",100);
        imagejpeg($preview, $emptyThemeDir.DIR_SEP."preview-section.jpg",100);
        imagedestroy($preview);

        // put empty templates in theme
        file_put_contents($emptyThemeDir.DIR_SEP."front.tpl", "<!-- Front page template -->");
        file_put_contents($emptyThemeDir.DIR_SEP."section.tpl", "<!-- Section page template -->");
        file_put_contents($emptyThemeDir.DIR_SEP."article.tpl", "<!-- Article page template -->");
        file_put_contents($emptyThemeDir.DIR_SEP."404.tpl", "<!-- Error page template -->");

    }

    /**
     *
     */
    private function loadDemoSite()
    {
        global $g_ado_db;

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

        if (is_dir(CS_PATH_TEMPLATES.DIR_SEP.ThemeManagementServiceLocal::FOLDER_UNASSIGNED)) {
            CampInstallationBaseHelper::CopyFiles(CS_PATH_TEMPLATES.DIR_SEP.ThemeManagementServiceLocal::FOLDER_UNASSIGNED, CS_INSTALL_DIR.DIR_SEP.'temp');
        }

        camp_remove_dir(CS_PATH_TEMPLATES.DIR_SEP.'*', null, array('system_templates'));

        // copies template files to corresponding directory
        $source = CS_INSTALL_DIR.DIR_SEP.'sample_templates'.DIR_SEP.$template_name['loaddemo'].DIR_SEP.'templates';
        $target = CS_PATH_TEMPLATES.DIR_SEP.ThemeManagementServiceLocal::FOLDER_UNASSIGNED;

        if (CampInstallationBaseHelper::CopyFiles($source, $target) == false) {
            $this->m_step = 'loaddemo';
            $this->m_message = 'Error: Copying sample site files';

            return false;
        }

        // copies template data files to corresponding directories.
        // data files are article images and article attachments
        $sourceFiles = CS_INSTALL_DIR.DIR_SEP.'sample_data/files';
        $targetFiles = CS_PATH_SITE.DIR_SEP.'public'.DIR_SEP.'files';
        $sourceImages = CS_INSTALL_DIR.DIR_SEP.'sample_data/images';
        $targetImages = CS_PATH_SITE.DIR_SEP.'images';

        if (
            CampInstallationBaseHelper::CopyFiles($sourceFiles, $targetFiles) == false ||
            CampInstallationBaseHelper::CopyFiles($sourceImages, $targetImages) == false 
        ) {
            $this->m_step = 'loaddemo';
            $this->m_message = 'Error: Copying sample site data files';
            die('failed');

            return false;
        }

        if (CampInstallationBaseHelper::ConnectDB() == false) {
            $this->m_step = 'loaddemo';
            $this->m_message = 'Error: Database parameters invalid. Could not connect to database server.';

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

        $resourceId = new Newscoop\Service\Resource\ResourceId(__CLASS__);
        $themeService = $resourceId->getService(IThemeManagementService::NAME_1);
        $publicationService = $resourceId->getService(IPublicationService::NAME);
        foreach ($themeService->getUnassignedThemes() as $theme) {
            foreach ($publicationService->getEntities() as $publication) {
                $themeService->assignTheme($theme, $publication);
            }
        }

        if (is_dir(CS_INSTALL_DIR.DIR_SEP.'temp')) {
            CampInstallationBaseHelper::CopyFiles(CS_INSTALL_DIR.DIR_SEP.'temp', CS_PATH_TEMPLATES.DIR_SEP.ThemeManagementServiceLocal::FOLDER_UNASSIGNED);
            camp_remove_dir(CS_INSTALL_DIR.DIR_SEP.'temp');
        }

        // set publication alias
        global $g_ado_db;
        $sql = 'UPDATE `Aliases` SET ' . $g_ado_db->escapeKeyVal('Name', $_SERVER['HTTP_HOST']);
        $g_ado_db->executeUpdate($sql);

        return true;
    }

    private function finish()
    {
        $session = CampSession::singleton();
        $dbData = $session->getData('config.db', 'installation');
        $mcData = $session->getData('config.site', 'installation');

        if (is_array($mcData) && isset($mcData['sitetitle']) && !CampInstallationBaseHelper::SetSiteTitle($mcData['sitetitle'])) {
            $this->m_step = 'mainconfig';
            $this->m_message = 'Error: Could not update the site title.';

            return false;
        }

        if (is_array($mcData) && isset($mcData['adminemail']) && !CampInstallationBaseHelper::CreateAdminUser($mcData['adminemail'], $mcData['adminpsswd'])) {
            $this->m_step = 'mainconfig';
            $this->m_message = 'Error: Could not update the admin user credentials.';

            return false;
        }

        if (!file_exists(CS_PATH_SITE . DIR_SEP . '.htaccess') && !copy(CS_PATH_SITE . DIR_SEP . 'htaccess', CS_PATH_SITE . DIR_SEP . '.htaccess')) {
            $this->m_step = 'mainconfig';
            $this->m_message = 'Error: Could not create the htaccess file.';

            return false;
        }

        if (file_exists(CS_PATH_SITE . DIR_SEP . 'conf' . DIR_SEP . 'installation.php')) {
            @unlink(CS_PATH_SITE . DIR_SEP . 'conf' . DIR_SEP . 'installation.php');
        }

        // Set the site EmailFromAddress to the adminemail as default
        // Set the site EmailContact to the sitetitle as default
        require_once($GLOBALS['g_campsiteDir'].'/classes/SystemPref.php');
        SystemPref::Set('EmailFromAddress', $mcData['adminemail']);
        SystemPref::Set('EmailContact', $mcData['adminemail']);

        return true;
    }


    /**
     *
     */
    private function saveCronJobsScripts()
    {
        global $g_ado_db;

        $cronJobs = array(
            'newscoop_autopublish',
            'newscoop_indexer',
            'newscoop_notifyendsubs',
            'newscoop_notifyevents',
            'newscoop_statistics',
            'newscoop_stats'
        );

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
                    $this->m_message = 'Error: Database parameters invalid. Could not connect to database server.';

                    return false;
                }
                $sqlQuery = "UPDATE SystemPreferences SET value = 'N' WHERE varname = 'ExternalCronManagement'";
                $g_ado_db->Execute($sqlQuery);
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
    }

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

        @chmod($path2, 0777);

        $phpFinder = new PhpExecutableFinder();
        $phpPath = $phpFinder->find();
        if (!$phpPath) {
            throw new \RuntimeException('The php executable could not be found, add it to your PATH environment variable and try again');
        }

        $php = escapeshellarg($phpPath);
        $doctrine = escapeshellarg($GLOBALS['g_campsiteDir'].DIR_SEP.'scripts'.DIR_SEP.'doctrine.php');
        $generateProxies = new Process("$php $doctrine orm:generate-proxies", null, null, null, 300);
        $generateProxies->run();
        if (!$generateProxies->isSuccessful()) {
            throw new \RuntimeException('An error occurred when executing the Generating ORM proxies command.');
        }

        $env = '';
        $console = escapeshellarg($GLOBALS['g_campsiteDir'].'/application/console');
        if (APPLICATION_ENV == 'production') {
            $env = '--env=prod';
        }

        $clearCache = new Process("$php $console cache:clear $env ", null, null, null, 300);
        $clearCache->run();
        if (!$clearCache->isSuccessful()) {
            throw new \RuntimeException('An error occurred when clearing cache.');
        }

        @chmod($path2, 0600);

        require_once $path2; // load saved db config for next steps

        // create images and files directories
        CampInstallationBase::CreateDirectory($GLOBALS['g_campsiteDir'].DIR_SEP.'images');
        CampInstallationBase::CreateDirectory($GLOBALS['g_campsiteDir'].DIR_SEP.'images'.DIR_SEP.'thumbnails');
        CampInstallationBase::CreateDirectory($GLOBALS['g_campsiteDir'].DIR_SEP.'public'.DIR_SEP.'files');

        return true;
    }


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
    }


    private static function InstallPlugins()
    {
        require_once($GLOBALS['g_campsiteDir'].'/include/campsite_constants.php');
        require_once(dirname(dirname(dirname(__FILE__))) . DIR_SEP . 'db_connect.php');
        require_once dirname(dirname(dirname(__FILE__))) . '/classes/CampPlugin.php';

        foreach (CampPlugin::GetPluginsInfo() as $info) {
            $CampPlugin = new CampPlugin($info['name']);

            $to_enable = true;
            if (isset($info['enabled_by_default'])) {
                $to_enable = (in_array($info['enabled_by_default'], array(true, 1, 'Y')) ? true : false);
            }

            $CampPlugin->create($info['name'], $info['version'], $to_enable);
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

    /**
     * Init renditions
     *
     * @return void
     */
    protected function initRenditions()
    {
        global $application;
        $application->bootstrap('container');
        $application->getBootstrap()->getResource('container')->getService('image.rendition')->reloadRenditions();
    }
}

/**
 * Class CampInstallationBaseHelper
 */
class CampInstallationBaseHelper
{
    const PASSWORD_MINLENGTH = 5;

    public static function ConnectDB()
    {
        global $g_ado_db;

        $config_file = APPLICATION_PATH . '/../conf/database_conf.php';
        if (is_readable($config_file)) {
            $container = Zend_Registry::get('container');
            $g_ado_db = $container->getService('doctrine.adodb');
        }

        if (!$g_ado_db->isConnected(true)) {
            return false;
        }

        return $g_ado_db;
    }

    public static function CreateAdminUser($p_email, $p_password)
    {
        global $g_ado_db;

        if (self::ConnectDB() == false) {
            return false;
        }

        $sqlQuery1 = "UPDATE liveuser_users SET
            Password = SHA1(".$g_ado_db->Escape($p_password)."),
            EMail = ".$g_ado_db->Escape($p_email).",
            time_updated = NOW(),
            time_created = NOW(),
            status = '1',
            is_admin = '1'
            WHERE id = 1";

        if (!$g_ado_db->Execute($sqlQuery1)) {
            return false;
        }

        return true;
    }

    public static function ImportDB($p_sqlFile, &$errorQueries)
    {
        global $g_ado_db;

        if (self::ConnectDB() == false) {
            return false;
        }

        if(!($sqlFile = file_get_contents($p_sqlFile))) {
            return false;
        }

        $queries = self::SplitSQL($sqlFile);

        $errors = 0;
        $errorQueries = array();
        foreach($queries as $query) {
            $query = trim($query);
            if (!empty($query) && $query{0} != '#') {
                if ($g_ado_db->Execute($query) == false) {
                    $errors++;
                    $errorQueries[] = $query;
                }
            }
        }

        return $errors;
    }

    public static function SetSiteTitle($p_title)
    {
        global $g_ado_db;

        if (self::ConnectDB() == false) {
            return false;
        }

        $p_title = $g_ado_db->escape($p_title);
        $sqlQuery = "UPDATE SystemPreferences SET value = $p_title WHERE varname = 'SiteTitle'";
        return $g_ado_db->Execute($sqlQuery);
    }

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
    }

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
    }

    public static function CopyFiles($p_source, $p_target)
    {
        if (is_dir($p_source)) {
            @mkdir($p_target);
            $direcotry = dir($p_source);

            while(($entry = $direcotry->read()) !== false) {
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

            $direcotry->close();
        } else {
            @copy($p_source, $p_target);
        }

        return true;
    }
}
