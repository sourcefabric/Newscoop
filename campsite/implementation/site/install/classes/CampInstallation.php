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

/**
 * Includes
 *
 * We indirectly reference the DOCUMENT_ROOT so we can enable
 * scripts to use this file from the command line, $_SERVER['DOCUMENT_ROOT']
 * is not defined in these cases.
 */
$g_documentRoot = $_SERVER['DOCUMENT_ROOT'];

require_once($g_documentRoot.'/template_engine/classes/CampTemplate.php');


/**
 * Class CampInstallation
 */
final class CampInstallation
{
    /**
     * @var integer
     */
    private $m_step = null;

    /**
     * @var string
     */
    private $m_defaultStep = 'precheck';

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
                             'finish' => array('tplfile' => 'finish.tpl',
                                               'title' => 'Finish',
                                               'order' => 5)
                             );

    /**
     * @var string
     */
    private $m_title = 'Campsite Web Installer';


    /**
     * Class constructor.
     */
    public function __construct()
    {
        $template = CampTemplate::singleton();
        $template->setTemplateDir(CAMP_INSTALL_DIR.DIR_SEP.'templates');
    } // fn __construct


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
    } // fn dispatch


    /**
     *
     */
    public function render()
    {
        $tpl = CampTemplate::singleton();

        $tpl->assign('current_step', $this->m_step);
        $tpl->assign('current_step_title', $this->m_steps[$this->m_step]['title']);
        $tpl->assign('step_titles', $this->m_steps);

        $tpl->display($this->getTemplateName());
    } // fn render


    private function getTemplateName()
    {
        return $this->m_steps[$this->m_step]['tplfile'];
    } // fn getTemplateName


    /**
     *
     */
    public function getDefaultStep()
    {
        return $this->m_defaultStep;
    } // fn getDefaultStep


    /**
     *
     */
    private function requirementsCheck()
    {

    } // fn requirementsCheck

} // class CampInstallation

?>