<?php

/**
 * Sidebar placeholder action helper
 */
class Action_Helper_Sidebar extends Zend_Controller_Action_Helper_Abstract
{
    const SIDEBAR = 'sidebar';

    /**
     * Add sidebar
     *
     * @param array $config
     * @return void
     */
    public function addSidebar(array $config)
    {
        // init
        $controller = $this->getActionController();
        $view = $controller->view;
        $acl = $controller->getHelper('acl');

        // get config
        $params = $this->getRequest()->getParams();
        $config = array_merge($params, $config);

        if (!empty($config['resource'])) { // check acl
            if (!$acl->isAllowed($config['resource'], $config['privilege'])) {
                return;
            }
        }

        // render sidebar to placeholder
        $view->placeholder(self::SIDEBAR)->captureStart();
        echo $view->action($config['action'], $config['controller'], $config['module'], $config);
        $view->placeholder(self::SIDEBAR)->captureEnd();
    }

    /**
     * Direct strategy
     *
     * @param array $config
     * @return void
     */
    public function direct(array $config)
    {
        $this->addSidebar($config);
    }
}
