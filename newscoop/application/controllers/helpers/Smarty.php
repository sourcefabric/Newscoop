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

        if (!in_array($request->getParam('module'), $this->modules)) {
            return;
        }

        $controller->view = new Newscoop\SmartyView();
        $controller->view
            ->addScriptPath(APPLICATION_PATH . '/views/scripts/');

        $controller->getHelper('viewRenderer')
            ->setView($controller->view)
            ->setViewScriptPathSpec(':controller_:action.:suffix')
            ->setViewSuffix('tpl');

        $controller->getHelper('layout')
            ->disableLayout();
    }
}
