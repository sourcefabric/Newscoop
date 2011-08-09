<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Omnibox controller
 */
class OmniboxController extends Zend_Controller_Action
{
    public function init()
    {
		
    }

    public function indexAction()
    {
		$this->view->gimme = $this->_getParam('gimme');
	}
}
