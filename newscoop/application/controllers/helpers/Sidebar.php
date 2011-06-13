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
     * @param bool $prepend
     * @return void
     */
    public function addSidebar(array $config, $prepend = false)
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

        ob_start();
        echo !empty($config['label']) ? '<h3 class="label">' . $config['label'] . "</h3>\n" : '';
        echo $view->action($config['action'], $config['controller'], $config['module'], $config);
        $content = ob_get_clean();

        $method = $prepend ? 'prepend' : 'append';
        $view->placeholder(self::SIDEBAR)->$method($content);
    }

    /**
     * Direct strategy
     *
     * @param array $config
     * @param bool $prepend
     * @return void
     */
    public function direct(array $config, $prepend = false)
    {
        $this->addSidebar($config, $prepend);
    }
}
