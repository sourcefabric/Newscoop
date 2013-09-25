<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\Entity\User;

/**
 */
class Admin_Form_UserCriteria extends Zend_Form
{
    public function init()
    {   
        $translator = \Zend_Registry::get('container')->getService('translator');
        
        $this->addElement('text', 'q');

        $this->addElement('select', 'status', array(
            'multioptions' => array(
                User::STATUS_ACTIVE => $translator->trans('active', array(), 'users'),
                User::STATUS_INACTIVE => $translator->trans('pending', array(), 'users'),
                User::STATUS_DELETED => $translator->trans('deleted', array(), 'users'),
            ),
        ));

        $this->addElement('select', 'groups', array(
            'multioptions' => array(
                null => $translator->trans('Any group', array(), 'users'),
            ),
        ));
    }
}
