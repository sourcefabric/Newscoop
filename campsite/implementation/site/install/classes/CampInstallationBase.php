<?php
/**
 * @package Campsite
 *
 * @author Holman Romero <holman.romero@gmail.com>
 * @copyright 2007 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @version $Revision$
 * @link http://www.campware.org
 */

global $g_db;

/**
 * Includes
 *
 * We indirectly reference the DOCUMENT_ROOT so we can enable
 * scripts to use this file from the command line, $_SERVER['DOCUMENT_ROOT']
 * is not defined in these cases.
 */
$g_documentRoot = $_SERVER['DOCUMENT_ROOT'];

require_once($g_documentRoot.'/include/adodb/adodb.inc.php');
require_once($g_documentRoot.'/classes/Input.php');
require_once($g_documentRoot.'/install/classes/CampInstallationView.php');


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


    protected function execute()
    {
        $input = CampRequest::GetInput('post');
        $session = CampSession::singleton();

        $this->m_step = (!empty($input['step'])) ? $input['step'] : $this->m_defaultStep;
        switch($this->m_step) {
        case 'precheck':
            $session->unsetData('config.db', 'installation');
            $session->unsetData('config.site', 'installation');
            break;
        case 'license':
            $this->preInstallationCheck();
            break;
        case 'database':
            $this->license();
            break;
        case 'mainconfig':
            if ($this->databaseConfiguration($input)) {
                $session->setData('config.db', $this->m_config['database'], 'installation', true);
            }
            break;
        case 'finish':
            if ($this->generalConfiguration($input)) {
                $session->setData('config.site', $this->m_config['mainconfig'], 'installation', true);
            }
            if ($this->finish()) {
                $this->saveConfiguration();
            }
            break;
        }
    } // fn execute


    private function preInstallationCheck() {}


    private function license() {}


    private function databaseConfiguration($p_input)
    {
        global $g_db;

        $db_hostname = Input::Get('db_hostname', 'text');
        $db_hostport = Input::Get('db_hostport', 'int');
        $db_username = Input::Get('db_username', 'text');
        $db_userpass = Input::Get('db_userpass', 'text');
        $db_database = Input::Get('db_database', 'text');

        if (empty($db_hostport)) {
            $db_hostport = 3306;
        }

        if (empty($db_hostname) || empty($db_hostport) || empty($db_username)
                || empty($db_userpass) || empty($db_database)) {
            $this->m_step = 'database';
            $this->m_message = 'Error: Please input the requested data';
            return false;
        }

        $error = false;
        $g_db = ADONewConnection('mysql');
        $g_db->SetFetchMode(ADODB_FETCH_ASSOC);
        @$g_db->Connect($db_hostname, $db_username, $db_userpass);
        if (!$g_db->isConnected()) {
            $error = true;
        } else {
            $selectDb = $g_db->SelectDB($db_database);
        }

        if (!$error && !$selectDb) {
            $dict = NewDataDictionary($g_db);
            $sql = $dict->CreateDatabase($db_database);
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

        $sqlFile = CAMP_INSTALL_DIR.DIR_SEP.'sql'.DIR_SEP.'campsite_core.sql';
        $errors = CampInstallationBaseHelper::ImportDB($sqlFile);
        if ($errors > 0) {
            $this->m_step = 'database';
            $this->m_message = 'Error: Importing Database';
            return false;
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


    private function generalConfiguration($p_input)
    {
        $mc_sitename = Input::Get('mc_sitename', 'text');
        $mc_adminpsswd = Input::Get('mc_adminpsswd', 'text');
        $mc_admincpsswd = Input::Get('mc_admincpsswd', 'text');
        $mc_adminemail = Input::Get('mc_adminemail', 'text');

        if (empty($mc_sitename) || empty($mc_adminpsswd)
                || empty($mc_admincpsswd) || empty($mc_adminemail)) {
            $this->m_step = 'mainconfig';
            $this->m_message = 'Error: Please input the requested data';
            return false;
        }

        if (!CampInstallationBaseHelper::ValidatePassword($mc_adminpsswd,
                                                          $mc_admincpsswd)) {
            $this->m_step = 'mainconfig';
            $this->m_message = 'Error: Passwords do not match each other.';
            return false;
        }

        $this->m_config['mainconfig'] = array(
                                              'sitename' => $mc_sitename,
                                              'adminemail' => $mc_adminemail,
                                              'adminpsswd' => $mc_adminpsswd
                                              );

        return true;
    } // fn generalConfiguration


    private function finish()
    {
        $session = CampSession::singleton();
        $dbData = $session->getData('config.db', 'installation');
        $mcData = $session->getData('config.site', 'installation');

        if (!CampInstallationBaseHelper::CreateAdminUser($mcData['adminemail'],
                                                         $mcData['adminpsswd'])) {
            $this->m_step = 'mainconfig';
            $this->m_message = 'Error: Could not save the configuration.';
            return false;
        }

        return true;
    } // fn finish


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

        $buffer = $template->fetch('configuration.tpl');
        $buffer = preg_replace('/#{#{/', '{{', $buffer);
        $buffer = preg_replace('/#}#}/', '}}', $buffer);

        $path = CS_PATH_SITE.DIR_SEP.'template_engine'.DIR_SEP.'configuration.php';
        if (file_exists($path)) {
            $isWritable = is_writable($path);
        } else {
            $isWritable = is_writable(CS_PATH_SITE.DIR_SEP.'template_engine');
        }

        if (!$isWritable) {
            $this->m_step = 'mainconfig';
            $this->m_message = 'Error: Could not write the configuration file.';
            return false;
        }

        file_put_contents($path, $buffer);

        return true;
    } // fn saveConfiguration

} // fn ClassInstallationBase


/**
 * Class CampInstallationBaseHelper
 */
class CampInstallationBaseHelper
{
    public static function ConnectDB()
    {
        global $g_db;

        $session = CampSession::singleton();
        $dbData = $session->getData('config.db', 'installation');

        if (empty($dbData)) {
            return false;
        }

        $g_db = ADONewConnection('mysql');
        $g_db->SetFetchMode(ADODB_FETCH_ASSOC);
        return @$g_db->Connect($dbData['hostname'], $dbData['username'],
                               $dbData['userpass'], $dbData['database']);
    } // fn ConnectDB


    public static function CreateAdminUser($p_email, $p_password)
    {
        global $g_db;

        if (self::ConnectDB() == false) {
            return false;
        }

        $sqlQuery1 = 'UPDATE liveuser_users SET '
            ."Password = MD5('".$g_db->Escape($p_password)."'), "
            ."EMail = '".$g_db->Escape($p_email)."' "
            .'WHERE Id = 1';

        $sqlQuery2 = 'UPDATE phorum_users SET '
            ."password = MD5('".$g_db->Escape($p_password)."'), "
            ."email = '".$g_db->Escape($p_email)."' "
            .'WHERE user_id = 1';
        if (!$g_db->Execute($sqlQuery1)
                || !$g_db->Execute($sqlQuery2)) {
            return false;
        }

        return true;
    } // fn CreateAdminUser


    public static function ImportDB($p_sqlFile)
    {
        global $g_db;

        if(!($sqlFile = file_get_contents($p_sqlFile))) {
            return false;
        }

        $queries = self::SplitSQL($sqlFile);

        $errors = 0;
        foreach($queries as $query) {
            $query = trim($query);
            if (!empty($query) && $query{0} != '#') {
                if ($g_db->Execute($query) == false) {
                    $errors++;
                }
            }
        }

        return $errors;
    } // fn ImportDB


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


    public static function ValidatePassword($p_password1, $p_password2)
    {
        return ($p_password1 == $p_password2);
    } // fn ValidatePassword


} // class CampInstallationBaseHelper

?>