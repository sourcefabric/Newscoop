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
        $librariesCheck = $this->librariesCheck();
        $requirementsOk = $phpFunctionsCheck && $sysCheck && $librariesCheck;
        $this->phpRecommendedOptions();
        $this->phpIniSettings();

        $template = CampTemplate::singleton();

        $template->assign('php_req_ok', $requirementsOk);
        $template->assign('php_functions', $this->m_lists['phpFunctions']);
        $template->assign('sys_requirements', $this->m_lists['sysRequirements']);
        $template->assign('library_requirements', $this->m_lists['libraryRequirements']);
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

    private function librariesCheck()
    {
        $success = true;
        $libraryRequirements = array();

        // autoloader for dependencies
        $inclPath = explode(PATH_SEPARATOR, get_include_path());
        $autoloadCallback = function($p_class) use ($inclPath)
        {
            foreach ($inclPath as $path) {
                $fn = DIR_SEP.trim($path, DIR_SEP).DIR_SEP.str_replace("_", DIR_SEP, $p_class).".php";
                if (file_exists($fn)) {
                    require_once $fn;
                    return true;
                }
            }
            return true;
        };

        $autoloader = Zend_Loader_Autoloader::getInstance();
        $autoloader->pushAutoloader($autoloadCallback);


        $pear = CampInstallationViewHelper::CheckPear();
        $success = ($pear == 'Yes') ? $success : false;
        $libraryRequirements[] = array(
			'tag' => 'PEAR',
			'exists' => $pear
		);

		$pearDate = CampInstallationViewHelper::CheckPearDate();
        $success = ($pearDate == 'Yes') ? $success : false;
        $libraryRequirements[] = array(
			'tag' => 'PEAR/Date',
			'exists' => $pearDate
		);

		$pearArchiveTar = CampInstallationViewHelper::CheckPearArchiveTar();
        $success = ($pearArchiveTar == 'Yes') ? $success : false;
        $libraryRequirements[] = array(
			'tag' => 'PEAR/Archive',
			'exists' => $pearArchiveTar
		);

		$pearEventDispatcher = CampInstallationViewHelper::CheckPearEventDispatcher();
        $success = ($pearEventDispatcher == 'Yes') ? $success : false;
        $libraryRequirements[] = array(
			'tag' => 'PEAR/Event Dispatcher',
			'exists' => $pearEventDispatcher
		);

		$pearMail = CampInstallationViewHelper::CheckPearMail();
        $success = ($pearMail == 'Yes') ? $success : false;
        $libraryRequirements[] = array(
			'tag' => 'PEAR/Mail',
			'exists' => $pearMail
		);

		$pearMailMime = CampInstallationViewHelper::CheckPearMailMime();
        $success = ($pearMailMime == 'Yes') ? $success : false;
        $libraryRequirements[] = array(
			'tag' => 'PEAR/Mail_mime',
			'exists' => $pearMailMime
		);

		$pearXmlSerializer = CampInstallationViewHelper::CheckPearXmlSerializer();
        $success = ($pearXmlSerializer == 'Yes') ? $success : false;
        $libraryRequirements[] = array(
			'tag' => 'PEAR/XML_Serializer',
			'exists' => $pearXmlSerializer
		);

		$pearXmlParser = CampInstallationViewHelper::CheckPearXmlParser();
        $success = ($pearXmlParser == 'Yes') ? $success : false;
        $libraryRequirements[] = array(
			'tag' => 'PEAR/XML_Parser',
			'exists' => $pearXmlParser
		);

		$pearHtmlCommon = CampInstallationViewHelper::CheckPearHtmlCommon();
        $success = ($pearHtmlCommon == 'Yes') ? $success : false;
        $libraryRequirements[] = array(
			'tag' => 'PEAR/HTML_Common',
			'exists' => $pearHtmlCommon
		);

        $zendFramework = CampInstallationViewHelper::CheckZendFramework();
        $success = ($zendFramework == 'Yes') ? $success : false;
        $libraryRequirements[] = array(
			'tag' => 'Zend Framework',
			'exists' => $zendFramework
		);

		$adoDb = CampInstallationViewHelper::CheckAdoDb();
		$success = ($adoDb == 'Yes') ? $success : false;
        $libraryRequirements[] = array(
			'tag' => 'AdoDB',
			'exists' => $adoDb
		);

		$smarty = CampInstallationViewHelper::CheckSmarty();
        $success = ($smarty == 'Yes') ? $success : false;
        $libraryRequirements[] = array(
			'tag' => 'Smarty',
			'exists' => $smarty
		);

        $this->m_lists['libraryRequirements'] = $libraryRequirements;

        // removing that autoloader
        $autoloader->removeAutoloader($autoloadCallback);

        return $success;
    } // fn librariesCheck


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
    public static function CheckPear()
    {
        return (class_exists('System')||class_exists('PEAR')||class_exists('PEAR_Common')) ? 'Yes' : 'No';
    } // fn checkPear

    public static function CheckPearDate()
    {
        return (class_exists('Date_Calc')) ? 'Yes' : 'No';
    } // fn checkPearDate

    public static function CheckPearArchiveTar()
    {
        return (class_exists('Archive_Tar')) ? 'Yes' : 'No';
    } // fn checkPearArchiveTar

	public static function CheckPearEventDispatcher()
    {
        return (class_exists('Event_Dispatcher')) ? 'Yes' : 'No';
    } // fn checkPearEventDispatcher

    public static function CheckPearMail()
    {
        return (class_exists('Mail_mail')) ? 'Yes' : 'No';
    } // fn checkPearMail

    public static function CheckPearMailMime()
    {
        return (class_exists('Mail_mime')) ? 'Yes' : 'No';
    } // fn checkPearMailMime

    public static function CheckPearXmlSerializer()
    {
        return (class_exists('XML_Serializer')) ? 'Yes' : 'No';
    } // fn checkPearXmlSerializer

    public static function CheckPearXmlParser()
    {
        return (class_exists('XML_Parser')) ? 'Yes' : 'No';
    } // fn checkPearXmlParser

    public static function CheckPearHtmlCommon()
    {
        return (class_exists('HTML_Common')) ? 'Yes' : 'No';
    } // fn checkPearHtmlCommon

    public static function CheckZendFramework()
    {
        return (class_exists('Zend_Application')) ? 'Yes' : 'No';
    } // fn checkZendFramework

    public static function CheckAdoDb()
    {
        return (class_exists('ADOFieldObject')) ? 'Yes' : 'No';
    } // fn checkAdoDb

    public static function CheckSmarty()
    {
        return (class_exists('Smarty')) ? 'Yes' : 'No';
    } // fn checkSmarty

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
