<?php
/**
 * @package Campsite
 *
 * @author Holman Romero <holman.romero@gmail.com>
 * @copyright 2007 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @version $Revision$
 * @link http://www.sourcefabric.org
 */

/**
 * Class CampInstallationView
 */
final class CampInstallationView
{
    /**
     * @var string
     */
    private $m_step = null;

    /**
     * @var array
     */
    private $m_lists = array();


    /**
     * Class constructor.
     *
     * @param string $p_step
     */
    public function __construct($p_step)
    {
        $this->m_step = $p_step;
        $this->controller();
    } // fn __construct


    private function controller()
    {
        switch($this->m_step) {
        case 'precheck':
            $this->preInstallationCheck();
            break;
        case 'license':
            break;
        case 'database':
            $this->databaseConfiguration();
            break;
        case 'mainconfig':
            break;
        case 'loaddemo':
            break;
        case 'finish':
            break;
        }
    } // fn controller


    private function preInstallationCheck()
    {
        $phpFunctionsCheck = $this->phpFunctionsCheck();
        $sysCheck = $this->sysCheck();
        $requirementsOk = $phpFunctionsCheck && $sysCheck;
        $this->phpRecommendedOptions();
        $this->phpIniSettings();

        $template = CampTemplate::singleton();

        $template->assign('php_req_ok', $requirementsOk);
        $template->assign('php_functions', $this->m_lists['phpFunctions']);
        $template->assign('sys_requirements', $this->m_lists['sysRequirements']);
        $template->assign('php_recommended', $this->m_lists['phpRecommendedOptions']);
        $template->assign('php_settings', $this->m_lists['phpIniSettings']);
    } // fn preInstallationCheck


    private function databaseConfiguration()
    {
        $template = CampTemplate::singleton();
    } // fn databaseConfiguration


    private function sysCheck()
    {
        $success = true;

        $to_check = array(
            CS_PATH_SITE => 'Document Root Writable',
            CS_INSTALL_DIR.DIR_SEP.'cron_jobs' => 'Cron Jobs Writable',
            CS_PATH_CONFIG => 'Configuration Files Writable',
            CS_PATH_TEMPLATES => 'Templates Folder Writable',
            CS_PATH_SITE . '/cache' => 'Cache Folder Writable',
            CS_PATH_SITE.DIR_SEP.'plugins' => 'Plugins Folder Writable',
            CS_PATH_SITE.DIR_SEP.'backup' => 'Backup Folder Writable',
            );

        foreach ($to_check as $path => $tag) {
            $isWritable = CampInstallationViewHelper::CheckDirWritable($path);
            $success = ($isWritable == 'Yes') ? $success : false;
            $sysRequirements[] = array(
                                   'tag' => $tag,
                                   'exists' => $isWritable,
                                   'path' => $path
                                   );
        }

        $this->m_lists['sysRequirements'] = $sysRequirements;

        return $success;
    } // fn sysCheck


    /**
     *
     */
    private function phpFunctionsCheck()
    {
        $success = true;

        $isPHP5 = CampInstallationViewHelper::CheckPHPVersion();
        $success = ($isPHP5 == 'Yes') ? $success : false;
        $phpFunctions[] = array(
                                'tag' => 'PHP >= 5.2',
                                'exists' => $isPHP5
                                );

        $hasMySQL = CampInstallationViewHelper::CheckPHPMySQL();
        $success = ($hasMySQL == 'Yes') ? $success : false;
        $phpFunctions[] = array(
                                'tag' => 'MySQL Support',
                                'exists' => $hasMySQL
                                );

        $execEnabled = CampInstallationViewHelper::CheckExec();
        $success = ($execEnabled == 'Yes') ? $success : false;
        $phpFunctions[] = array(
                                'tag' => 'Exec() function enabled',
                                'exists' => $execEnabled
                                );

        $systemEnabled = CampInstallationViewHelper::CheckSystem();
        $success = ($systemEnabled == 'Yes') ? $success : false;
        $phpFunctions[] = array(
                                'tag' => 'System() function enabled',
                                'exists' => $systemEnabled
                                );

        $hasGD = CampInstallationViewHelper::CheckPHPGD();
        $success = ($hasGD == 'Yes') ? $success : false;
        $phpFunctions[] = array(
                                'tag' => 'GD Image Functions Support',
                                'exists' => $hasGD
                                );

        $hasSession = CampInstallationViewHelper::CheckPHPSession();
        $success = ($hasSession == 'Yes') ? $success : false;
        $phpFunctions[] = array(
                                'tag' => 'Session Handling Support',
                                'exists' => $hasSession
                                );

        $hasRewriteModule = CampInstallationViewHelper::CheckRewriteModule();
        $success = ($hasRewriteModule == 'Yes' || $hasRewriteModule == 'Cannot be checked') ? $success : false;
        $phpFunctions[] = array(
                                'tag' => 'Rewrite Module',
                                'exists' => $hasRewriteModule
                                );

        $this->m_lists['phpFunctions'] = $phpFunctions;
        return $success;
    } // fn phpFunctionsCheck


    private function phpRecommendedOptions()
    {
        $hasCLI = CampInstallationViewHelper::CheckCLI();
        $phpOptions[] = array(
                                'tag' => '<span class="optional">PHP CLI (Command Line)</span>',
                                'exists' => $hasCLI
                                );

        $hasAPC = CampInstallationViewHelper::CheckPHPAPC();
        $phpOptions[] = array(
                                'tag' => '<span class="optional">APC (PHP Cache) Support</span>',
                                'exists' => $hasAPC
                                );

        $this->m_lists['phpRecommendedOptions'] = $phpOptions;
    }


    /**
     *
     */
    private function phpIniSettings()
    {
        $safeModeState = (ini_get('safe_mode') == '1') ? 'On' : 'Off';
        $phpSettings[] = array(
                              'tag' => 'Safe Mode',
                              'rec_state' => 'Off',
                              'cur_state' => $safeModeState
                              );
		$regGlobalsState = (ini_get('register_globals') == '1') ? 'On' : 'Off';
		$phpSettings[] = array(
			      'tag' => 'Register Globals',
			      'rec_state' => 'Off',
			      'cur_state' => $regGlobalsState
			      );
        $fileUploadsState = (ini_get('file_uploads') == '1') ? 'On' : 'Off';
        $phpSettings[] = array(
                              'tag' => 'File Uploads',
                              'rec_state' => 'On',
                              'cur_state' => $fileUploadsState
                              );
        $sessionAutoState = (ini_get('session.auto_start_') == '1') ? 'On' : 'Off';
        $phpSettings[] = array(
                              'tag' => 'Session Auto Start',
                              'rec_state' => 'Off',
                              'cur_state' => $sessionAutoState
                              );

        $this->m_lists['phpIniSettings'] = $phpSettings;
    } // fn phpIniSettings

} // class CampInstallationView


final class CampInstallationViewHelper
{
    public static function CheckPHPVersion()
    {
        return (phpversion() > '5.2') ? 'Yes' : 'No';
    } // fn checkPHPVersion


    public static function CheckPHPMySQL()
    {
        return (function_exists('mysql_connect')) ? 'Yes' : 'No';
    } // fn CheckPHPMySQL


    public static function CheckRewriteModule()
    {
        if (!function_exists('apache_get_modules')) {
            return 'Cannot be checked';
        }
        return array_search('mod_rewrite', apache_get_modules()) !== FALSE ? 'Yes' : 'No';
    }


    public static function CheckExec()
    {
    	$disabledFunctions = explode(', ', ini_get('disable_functions'));
    	$execEnabled = !in_array('exec', $disabledFunctions);
		return $execEnabled ? 'Yes' : 'No';
    }


    public static function CheckSystem()
    {
    	$disabledFunctions = explode(', ', ini_get('disable_functions'));
    	$systemEnabled = !in_array('system', $disabledFunctions);
		return $systemEnabled ? 'Yes' : 'No';
    }


    public static function CheckCLI()
    {
    	$response = exec('which php', $o, $r);
        return ($r == 0) ? 'Yes' : 'No';
    }


    public static function CheckPHPAPC()
    {
        return (ini_get('apc.enabled') && function_exists('apc_store')) ? 'Yes' : 'No';
    } // fn CheckPHPAPC


    public static function CheckPHPGD()
    {
        return (function_exists('gd_info')) ? 'Yes' : 'No';
    } // fn CheckPHPGD


    public static function CheckPHPSession()
    {
        return (function_exists('session_start')) ? 'Yes' : 'No';
    } // fn CheckPHPSession


    public static function CheckDirWritable($p_directory)
    {
        return (is_writable($p_directory)) ? 'Yes' : 'No';
    } // fn CheckConfigDirRights

} //

?>
