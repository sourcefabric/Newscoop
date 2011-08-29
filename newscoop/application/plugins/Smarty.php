<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

require_once APPLICATION_PATH . '/controllers/helpers/Smarty.php';

/**
 */
class Application_Plugin_Smarty extends Zend_Controller_Plugin_Abstract
{
    /**
     * @param Zend_Controller_Request_Abstract $request
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        Zend_Controller_Action_HelperBroker::addHelper(new Action_Helper_Smarty());
    }
}
