<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

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

        if (!empty($config['resource'])) { // check acl
            $acl = $controller->getHelper('acl');
            if (!$acl->isAllowed($config['resource'], $config['privilege'])) {
                return;
            }
        }

        // get config
        $params = $this->getRequest()->getParams();
        $config = array_merge($params, $config);

        // render sidebar to placeholder
        $view->placeholder(self::SIDEBAR)->captureStart();
        echo !empty($config['label']) ? '<h3 class="label">' . $config['label'] . "</h3>\n" : '';
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
