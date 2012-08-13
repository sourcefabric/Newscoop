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
        $request = $this->getRequest();

        if ($request->getParam('format') === 'json') {
            return;
        }

        if (!in_array($request->getParam('module', 'default'), $this->modules)
            || !array_key_exists('module', $request->getParams())) {
            return;
        }

        $this->setSmartyView();
    }

    /**
     * Set smarty view as view helper
     *
     * @return void/
     */
    public function setSmartyView()
    {
        $GLOBALS['controller'] = $controller = $this->getActionController();

        $controller->view = new Newscoop\SmartyView();
        $controller->view->addScriptPath(APPLICATION_PATH . '/views/scripts/');
        $controller->view->addScriptPath($controller->getHelper('theme')->getFilePath('_views'));
        $controller->view->addPath($controller->getHelper('theme')->getPath());
        $controller->view->addPath($controller->getHelper('theme')->getFilePath('_views'));

        $controller->getHelper('viewRenderer')
            ->setView($controller->view)
            ->setViewScriptPathSpec(':controller_:action.:suffix')
            ->setViewSuffix('tpl');

        $controller->getHelper('layout')
            ->disableLayout();
    }
}

