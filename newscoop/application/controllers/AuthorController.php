<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 */
class AuthorController extends Zend_Controller_Action
{
    public function profileAction()
    {
        if ($this->_getParam('author') === null) {
            throw new InvalidArgumentException();
        }

        $this->view->author = new MetaAuthor($this->_getParam('author'));
    }
}
