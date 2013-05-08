<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 */
class Action_Helper_Smarty extends Zend_Controller_Action_Helper_Abstract
{
    /** @var array */
    private $modules = array(
        'default',
    );

    /**
     */
    public function preDispatch()
    {
        $controller = $this->getActionController();
        $GLOBALS['controller'] = $controller;

        $request = $this->getRequest();

        $format = $request->getParam('format', null);
        if (isset($format) && $format == "json") {
            return;
        }

        if (!in_array($request->getParam('module', 'default'), $this->modules) || !array_key_exists('module', $request->getParams())) {
            return;
        }

        $uri = CampSite::GetURIInstance();
        $themePath = $uri->getThemePath();

        $controller->view = new Newscoop\SmartyView();
        $controller->view
            ->addScriptPath(APPLICATION_PATH . '/views/scripts/')
            ->addScriptPath(realpath(APPLICATION_PATH . "/../themes/$themePath"));

        $controller->view->addPath(realpath(APPLICATION_PATH . "/../themes/$themePath"));

        $controller->getHelper('viewRenderer')
            ->setView($controller->view)
            ->setViewScriptPathSpec(':controller_:action.:suffix')
            ->setViewSuffix('tpl');

        $controller->getHelper('layout')
            ->disableLayout();

        // reverse templates directory order
        \CampTemplate::singleton()->setTemplateDir(array_reverse(\CampTemplate::singleton()->getTemplateDir()));
    }
}
