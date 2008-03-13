<?php
/**
 * @package Campsite
 *
 * @author Holman Romero <holman.romero@gmail.com>
 * @author Mugur Rus <mugur.rus@gmail.com>
 * @copyright 2007 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @version $Revision$
 * @link http://www.campware.org
 */

/**
 * Includes
 *
 * We indirectly reference the DOCUMENT_ROOT so we can enable
 * scripts to use this file from the command line, $_SERVER['DOCUMENT_ROOT']
 * is not defined in these cases.
 */
$g_documentRoot = $_SERVER['DOCUMENT_ROOT'];

require_once($g_documentRoot.'/template_engine/classes/CampSession.php');
require_once($g_documentRoot.'/template_engine/classes/CampVersion.php');
require_once($g_documentRoot.'/install/classes/CampTemplate.php');
require_once($g_documentRoot.'/install/classes/CampInstallationBase.php');
require_once($g_documentRoot.'/install/classes/CampInstallationView.php');


/**
 * Class CampInstallation
 */
final class CampInstallation extends CampInstallationBase
{
    /**
     * @var array
     */
    private $m_steps = array(
                             'precheck' => array('tplfile' => 'precheck.tpl',
                                                 'title' => 'Pre-installation Check',
                                                 'order' => 1),
                             'license' => array('tplfile' => 'license.tpl',
                                                'title' => 'License',
                                                'order' => 2),
                             'database' => array('tplfile' => 'database.tpl',
                                                 'title' => 'Database Settings',
                                                 'order' => 3),
                             'mainconfig' => array('tplfile' => 'mainconfig.tpl',
                                                   'title' => 'Main Configuration',
                                                   'order' => 4),
                             'loaddemo' => array('tplfile' => 'loaddemo.tpl',
                                                 'title' => 'Sample Site',
                                                 'order' => 5),
                             'finish' => array('tplfile' => 'finish.tpl',
                                               'title' => 'Finish',
                                               'order' => 6)
                             );

    /**
     * @var array
     */
    private $m_lists = array();

    /**
     * @var string
     */
    private $m_title = null;


    /**
     * Class constructor.
     */
    public function __construct()
    {
        $this->m_os = self::GetHostOS();
    } // fn __construct


    public function execute()
    {
        parent::execute();
        return $this->m_step;
    } // fn execute


    /**
     *
     */
    public function dispatch($p_step)
    {
        if (array_key_exists($p_step, $this->m_steps)) {
            $this->m_step = $p_step;
        } else {
            $this->m_step = $this->m_defaultStep;
        }

        $cVersion = new CampVersion();
        $this->m_title = $cVersion->getPackage().' '.$cVersion->getRelease();
        $this->m_title .= (strlen($cVersion->getDevelopmentStatus()) > 0) ? '-'.$cVersion->getDevelopmentStatus() : '';
        $this->m_title .= (!is_null($cVersion->getCodeName())
                               && $cVersion->getCodeName() != 'undefined') ?
                          ' [ '.$cVersion->getCodeName().' ]' : '';
        $this->m_title .= ' Installer';
    } // fn dispatch


    /**
     *
     */
    public function initSession()
    {
        $session = CampSession::singleton();
    } // fn initSession


    /**
     *
     */
    public function render()
    {
        $tpl = CampTemplate::singleton();

        $tpl->assign('site_title', $this->m_title);
        $tpl->assign('message', $this->m_message);

        $tpl->assign('current_step', $this->m_step);
        $tpl->assign('current_step_title', $this->m_steps[$this->m_step]['title']);
        $tpl->assign('step_titles', $this->m_steps);

        $session = CampSession::singleton();
        $config_db = $session->getData('config.db', 'installation');

        if (!empty($config_db)) {
            $tpl->assign('db', $config_db);
        } else {
            $tpl->assign('db',
                         array('hostname'=>'localhost',
                               'username'=>'root',
                               'database'=>'campsite')
                        );
        }

        $config_site = $session->getData('config.site', 'installation');
        if (!empty($config_site)) {
            $tpl->assign('mc', $config_site);
        }

        $config_demo = $session->getData('config.demo', 'installation');
        if (!empty($config_demo)) {
            $tpl->assign('dm', $config_demo);
        }

        $view = new CampInstallationView($this->m_step);

        $tpl->display($this->getTemplateName());
    } // fn render


    /**
     *
     */
    public static function GetHostOS()
    {
        if (strtoupper(PHP_OS) === 'LINUX') {
            $os = 'linux';
        } elseif (strtoupper(PHP_OS) === 'FREEBSD') {
            $os = 'freebsd';
        } elseif (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $os = 'windows';
        } else {
            $os = 'unsupported';
        }

        return $os;
    } // fn GetHostOS


    private function getTemplateName()
    {
        return $this->m_steps[$this->m_step]['tplfile'];
    } // fn getTemplateName

} // class CampInstallation

?>