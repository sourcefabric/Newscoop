<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * @Acl(ignore="1")
 */
class Admin_ApplicationController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $this->_forward('help');
    }

    public function helpAction()
    {
        $newscoop = new CampVersion;
        $this->view->version = $newscoop->getVersion();
        $this->view->date = $newscoop->getReleaseDate();
    }
}

