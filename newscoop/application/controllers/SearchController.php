<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 */
class SearchController extends Zend_Controller_Action
{
    public function indexAction()
    {
        if ($this->_getParam('language')) {
            $gimme = CampTemplate::singleton()->context();
            $gimme->language = MetaLanguage::createFromCode($this->_getParam('language'));
        }
    }
}
